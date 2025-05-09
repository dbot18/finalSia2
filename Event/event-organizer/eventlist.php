<?php
include('../api/shared.php');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['calendar_fd_user']['type'])) {
    $_SESSION['calendar_fd_user']['type'] = 'admin'; // TEMP: remove in prod
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['eventId'])) {
    $eventId = (int) $_POST['eventId'];
    switch ($_POST['action']) {
        case 'approve':
            $stmt = $conn->prepare("UPDATE events SET status = 'APPROVED' WHERE id = ?");
            break;
        case 'deny':
            $stmt = $conn->prepare("UPDATE events SET status = 'DENIED' WHERE id = ?");
            break;
        case 'delete':
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            break;
        default:
            header("Location: eventlist.php?error=Invalid action");
            exit;
    }
    $stmt->bind_param("i", $eventId);
    if ($stmt->execute()) {
        $status = $_POST['action'] === 'delete' ? 'deleted' : $_POST['action'];
        header("Location: eventlist.php?status=$status");
    } else {
        header("Location: eventlist.php?error=Failed to {$_POST['action']} event");
    }
    exit;
}
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$events = getEventRecords($limit, $offset);
$utype = ($_SESSION['calendar_fd_user']['type'] ?? '') === 'admin' ? 'on' : 'off';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f4f6f9;
        padding-top: 56px; /* Adjusted for fixed navbar */
    }

    #sidebar {
        position: fixed;
        top: 56px;
        left: 0;
        height: calc(100% - 56px);
        width: 250px;
        background-color: #6a00ff; /* Purple background */
        color: white;
        padding-top: 20px;
    }

    #sidebar a {
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        display: block;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }

    #sidebar a:hover {
        background-color:rgb(0, 26, 255); /* Cyan hover effect */
    }

    .content-wrapper {
        margin-left: 250px;
        padding: 20px;
    }

    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: #6a00ff; /* Custom Purple navbar */
        z-index: 1000;
    }

    .navbar .navbar-nav {
        flex-grow: 0;
    }

    .footer {
        background-color: #fff;
        padding: 15px 0;
        text-align: center;
        border-top: 1px solid #dee2e6;
    }

    .emergency {
        background-color: #dc3545;
        color: white;
        padding: 6px 10px;
        border: none;
        border-radius: 4px;
        font-size: 13px;
        text-decoration: none;
    }

    .emergency:hover {
        background-color: #c82333;
        text-decoration: none;
        color: white;
    }

    .btn-sm {
        margin-right: 4px;
    }

    .navbar-toggler-icon {
        background-color: white;
    }

    .navbar .navbar-brand {
        color: white;
        font-weight: bold;
    }
    thead {
    background: linear-gradient(45deg, #6a00ff, #00aaff); /* Purple to Cyan gradient */
    color: white;
}

th {
    padding: 15px;
    text-align: left;
    font-weight: bold;
}

/* Optional: Hover effect for the rows */
.table-hover tbody tr:hover {
    background-color: #f1eaff; /* Light purple hover */
}


    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">📅 Event Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <?php if (isset($_SESSION['calendar_fd_user'])): ?>
                    <a class="nav-link text-white" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link text-white" href="login.php">Login</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>

<!-- Sidebar -->
<div id="sidebar">
    <a href="index.php">📅 Event Calendar</a>
    <a href="eventlist.php">📋 Event List</a>
    <a href="addevent.php">➕ Add Event</a>
</div>

<!-- Main Content -->
<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <h2 class="mb-4">Event Booking Details</h2>

        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-success"><?= ucfirst($_GET['status']) ?> successfully.</div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Event Name</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Attendees</th>
                        <th>Status</th>
                        <?php if ($utype === 'on'): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php $idx = $offset + 1; foreach ($events as $event): ?>
                    <?php
                        $id = $event['id'];
                        $name = strtoupper($event['name']);
                        $location = $event['location'];
                        $date = date('F j, Y', strtotime($event['date']));
                        $attendees = $event['attendees'];
                        $status = $event['status'] ?? 'PENDING';
                        $labelClass = match ($status) {
                            'APPROVED' => 'success',
                            'DENIED' => 'danger',
                            default => 'warning',
                        };
                    ?>
                    <tr>
                        <td><?= $idx++ ?></td>
                        <td><a href="eventdetails.php?id=<?= $id ?>"><?= $name ?></a></td>
                        <td><?= $location ?></td>
                        <td><?= $date ?></td>
                        <td><?= $attendees ?></td>
                        <td><span class="badge badge-<?= $labelClass ?>"><?= $status ?></span></td>
                        <?php if ($utype === 'on'): ?>
                        <td>
                            <?php if ($status === "PENDING"): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="eventId" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this event?')">Approve</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="eventId" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="deny">
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Deny this event?')">Deny</button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="eventId" value="<?= $id ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?')">Delete</button>
                            </form>
                            <?php if ($status !== "DENIED"): ?>
                                <a href="emergency.php?event_id=<?= $id ?>" class="emergency">🚨 Emergency</a>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php
                $totalRecords = getTotalEventCount();
                $totalPages = ceil($totalRecords / $limit);
                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i === $page) ? 'active' : '';
                    echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div>

    <!-- Footer -->
    <div class="footer mt-4">
        <p>&copy; <?= date("Y") ?> Event Management System</p>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

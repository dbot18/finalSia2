<?php
// Fetch events from API or database as you were doing earlier
// Sample data for demonstration purposes:
$events = [
    ['id' => 1, 'name' => 'Event 1', 'location' => 'Location 1', 'date' => '2025-05-10', 'attendees' => 50],
    ['id' => 2, 'name' => 'Event 2', 'location' => 'Location 2', 'date' => '2025-05-12', 'attendees' => 60],
    // Add more events as needed
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Sidebar styling */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }

        #sidebar a {
            color: white;
            padding: 10px;
            text-decoration: none;
            display: block;
        }

        #sidebar a:hover {
            background-color: #575757;
        }

        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
        }

        .approved-event {
            background-color: green !important;
            color: white !important;
            border-color: green !important;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div id="sidebar">
    <h4 class="text-center text-white">Event Management</h4>
    <a href="index.php">Event Calendar</a>
    <a href="eventlist.php">Event List</a>
    <a href="addevent.php">Add Event</a>
    <!-- Add more links as needed -->
</div>

<!-- Main Content Area -->
<div class="content-wrapper">
    <h2>Event Booking Details</h2>

    <!-- Alert messages (Success/Error) -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'approved'): ?>
        <div class="alert alert-success">Event approved successfully.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Event Name</th>
            <th>Location</th>
            <th>Date</th>
            <th>Attendees</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $idx = 1;
        foreach ($events as $event):
            $status = 'PENDING'; // Example status
            $labelClass = match ($status) {
                'APPROVED' => 'success',
                'DENIED' => 'danger',
                default => 'warning',
            };
        ?>
        <tr>
            <td><?= $idx++ ?></td>
            <td><a href="userlist.php?id=<?= $event['id'] ?>"><?= strtoupper($event['name']) ?></a></td>
            <td><?= $event['location'] ?></td>
            <td><?= date('F j, Y', strtotime($event['date'])) ?></td>
            <td><?= $event['attendees'] ?></td>
            <td><span class="badge badge-<?= $labelClass ?>"><?= $status ?></span></td>
            <td>
                <a href="edit.php?id=<?= $event['id'] ?>">Edit</a> /
                <a href="delete.php?id=<?= $event['id'] ?>" onclick="return confirm('Delete this event?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> Event Management | <a href="index.php">Back to Calendar</a></p>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>

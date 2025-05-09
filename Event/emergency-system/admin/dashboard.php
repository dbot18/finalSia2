<?php
// Fetch emergencies
$emergencyResponse = @file_get_contents('http://localhost/Event/api/emergencies.php');
$emergencies = json_decode($emergencyResponse, true)['data'] ?? [];

// Fetch events
$eventResponse = @file_get_contents('http://localhost/Event/api/events.php');
$events = json_decode($eventResponse, true)['data'] ?? [];

// Map event_id => event name for easy lookup
$eventNames = [];
foreach ($events as $event) {
    $eventNames[$event['id']] = $event['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles.css" />
</head>
<body>
<div class="container dashboard-page">

    <!-- Header -->
    <div class="top-container">
        <h1>Emergency System Admin Dashboard</h1>
        <a href="../index.php" class="btn">Public View</a>
    </div>

    <!-- Emergencies Table -->
    <div class="dashboard-panel">
        <h2>Active Emergencies</h2>
        <table>
            <thead>
            <tr>
                <th>Location</th>
                <th>Description</th>
                <th>Status</th>
                <th>Event Name</th> 
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($emergencies as $emergency): ?>
                <tr>
                    <td>
                        <a href="emergencydetails.php?id=<?= $emergency['id'] ?>" class="no-underline">
                            <?= htmlspecialchars($emergency['location']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($emergency['description']) ?></td>
                    <td>
                        <span class="status-badge status-<?= htmlspecialchars(strtolower(str_replace(' ', '_', $emergency['status']))) ?>">
                            <?= htmlspecialchars($emergency['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $eventId = $emergency['event_id'] ?? $emergency['event'] ?? null;
                        if ($eventId && isset($eventNames[$eventId])) {
                            // Link to the event details page
                            echo "<a href='emergencydetails.php?id=$eventId' class='no-underline'>" . htmlspecialchars($eventNames[$eventId]) . "</a>";
                        } elseif ($eventId) {
                            // Event ID found but no matching event
                            echo "<span style='color:red;'>Event not found (ID: " . htmlspecialchars($eventId) . ")</span>";
                        } else {
                            // No event assigned
                            echo "<span style='color:gray;'>No Event Assigned</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="emergencydetails.php?id=<?= $emergency['id'] ?>" class="btn info">View</a>
                            <a href="delete.php?id=<?= $emergency['id'] ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this emergency?');">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Events Table -->
    <div class="dashboard-panel active-events-panel">
        <h2>Active Events</h2>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Date</th>
                <th>Attendees</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['name']) ?></a></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= htmlspecialchars($event['date']) ?></td>
                    <td><?= htmlspecialchars($event['attendees']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>

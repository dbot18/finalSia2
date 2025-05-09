<?php
require_once('../api/shared.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $attendees = $_POST['attendees'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validate input
    if (!$id || !$name || !$location || !$date || !$attendees) {
        die("Missing required fields.");
    }

    // Format the date and time
    $dateTime = $date . ' ' . $time;

    // Update the event in the database
    $stmt = $conn->prepare("UPDATE events SET name = ?, location = ?, date = ?, attendees = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssssis", $name, $location, $dateTime, $attendees, $description, $id);

    if ($stmt->execute()) {
        // Redirect to the event details page after a successful update
        header("Location: eventdetails.php?id=" . urlencode($id));
        exit;
    } else {
        die("Error updating event: " . $conn->error);
    }
}
?>

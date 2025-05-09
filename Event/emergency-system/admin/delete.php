<?php
// Ensure 'id' is present in the URL query parameter
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];

// Handle the form submission (POST request) for deleting the emergency
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = ['id' => $id];

    // Make the DELETE request to the API
    $ch = curl_init('http://localhost/Event/api/emergencies.php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send the ID as JSON
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
    ]);

    // Execute the request
    $result = curl_exec($ch);
    $response = json_decode($result, true);
    curl_close($ch);

    // If the delete operation is successful, redirect
    if (!empty($response['success'])) {
        header("Location: dashboard.php?deleted=1");
        exit;
    } else {
        $error = "Error deleting emergency: " . ($response['message'] ?? 'Unknown error');
        header("Location: dashboard.php?error=" . urlencode($error));
        exit;
    }
}

// Fetch the emergency data for confirmation (to display the details)
$emergency_url = 'http://localhost/Event/api/emergencies.php?id=' . $id;
$emergency = json_decode(file_get_contents($emergency_url), true)['data'] ?? null;

// If emergency not found, redirect back to the dashboard
if (!$emergency) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Delete Emergency</title>
    <link rel="stylesheet" href="../styles.css" />
</head>
<body>
<div class="container index-page">

    <!-- Card container -->
    <div class="dashboard-panel">
        <!-- Header -->
        <div class="top-container" style="margin-bottom: 0;">
            <h1>Delete Emergency</h1>
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>

        <!-- Warning Alert -->
        <div class="alert warning" style="margin-top: 32px;">
            <p>Are you sure you want to delete this emergency?</p>
            <p><strong>Location:</strong> <?= htmlspecialchars($emergency['location']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($emergency['description']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($emergency['status']) ?></p>
        </div>

        <!-- Confirmation Form -->
        <form method="POST" style="margin-top: 24px;">
            <div class="admin-actions">
                <button type="submit" class="btn danger">Confirm Delete</button>
                <a href="dashboard.php" class="btn">Cancel</a>
            </div>
        </form>
    </div>

</div>
</body>
</html>

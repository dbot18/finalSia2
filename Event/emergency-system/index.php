<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the form data
    $data = [
        'location' => $_POST['location'],
        'description' => $_POST['description'],
        'media' => $_POST['media'] ?? null
    ];

    // Prepare the request to send the data to the API
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents('http://localhost/Event/api/emergencies.php', false, $context);

    if ($result === false) {
        $error = "Error connecting to the API.";
    } else {
        $response = json_decode($result, true);
        if ($response && $response['success']) {
            header("Location: index.php?success=1");
            exit;
        } else {
            $error = "Error reporting emergency: " . ($response['message'] ?? 'Unknown error.');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Emergency Reporting System</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<div class="container index-page">

    <!-- Card container for Emergency Reporting System -->
    <div class="dashboard-panel">
        <!-- Header and Admin Dashboard button -->
        <div class="top-container" style="margin-bottom: 0;">
            <h1>Emergency Reporting System</h1>
            <a href="admin/dashboard.php" class="btn">Admin Dashboard</a>
        </div>

        <!-- Alerts -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Emergency reported successfully!</div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="emergency-form" style="margin-top:32px;">
            <div class="form-group">
                <label for="location">Emergency Location:</label>
                <input type="text" id="location" name="location" required />
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group" style="margin-top:40px;">
                <label for="media">Media (optional):</label>
                <input type="text" id="media" name="media" placeholder="URL to image or video" />
            </div>
            <button type="submit" class="btn emergency">Report Emergency</button>
        </form>
    </div>

</div>
</body>
</html>

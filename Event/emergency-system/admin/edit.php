<?php
// In a real app, you would have proper authentication here

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];

// Fetch the emergency to edit
$emergency_url = 'http://localhost/Event/api/emergencies.php?id=' . $id;
$response = file_get_contents($emergency_url);
$emergency = json_decode($response, true)['data'] ?? null;

// Debugging: Check if the emergency data exists
if (!$emergency) {
    echo "Error: No emergency data found for ID $id.";
    echo "<pre>";
    var_dump($response); // Debug the response
    echo "</pre>";
    exit;
}

// Handle form submission to update emergency
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => $id,
        'location' => $_POST['location'],
        'description' => $_POST['description'],
        'status' => $_POST['status']
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'PUT',
            'content' => json_encode($data),
        ],
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost/Event/api/emergencies.php', false, $context);
    $response = json_decode($result, true);
    
    if ($response['success']) {
        header("Location: dashboard.php?success=1");
        exit;
    } else {
        $error = "Error updating emergency: " . $response['message'];
    }
}

// Fetch events for reference
$events_url = 'http://localhost/Event/api/events.php';
$events = json_decode(file_get_contents($events_url), true)['data'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Emergency</title>
    <link rel="stylesheet" href="../styles.css" />
</head>
<body>
<div class="container index-page">

    <!-- Card container -->
    <div class="dashboard-panel">
        <!-- Header with back button -->
        <div class="top-container" style="margin-bottom: 0;">
            <h1>Edit Emergency</h1>
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>

        <!-- Error alert -->
        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Edit form -->
        <form method="POST" style="margin-top: 32px;">
            <div class="form-group">
                <label for="location">Location:</label>
                <input
                    type="text"
                    id="location"
                    name="location"
                    value="<?= htmlspecialchars($emergency['location'] ?? '') ?>"
                    required
                />
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea
                    id="description"
                    name="description"
                    required
                ><?= htmlspecialchars($emergency['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="pending" <?= isset($emergency['status']) && $emergency['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_progress" <?= isset($emergency['status']) && $emergency['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= isset($emergency['status']) && $emergency['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </div>

            <div class="form-group">
                <label>Associated Event:</label>
                <?php if (isset($emergency['event_id']) && $emergency['event_id']): ?>
                    <?php 
                        $event = array_filter($events, function($e) use ($emergency) {
                            return $e['id'] == $emergency['event_id'];
                        });
                        $event = reset($event);
                        echo htmlspecialchars($event['name'] ?? 'Unknown') . ' (ID: ' . htmlspecialchars($emergency['event_id']) . ')';
                    ?>
                <?php else: ?>
                    <p>No associated event</p>
                <?php endif; ?>
            </div>

            <div class="admin-actions" style="margin-top: 20px;">
                <button type="submit" class="btn emergency">Update Emergency</button>
            </div>
        </form>
    </div>

</div>
</body>
</html>

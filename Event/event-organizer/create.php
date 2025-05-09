<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'location' => $_POST['location'],
        'date' => $_POST['date'],
        'attendees' => $_POST['attendees']
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost/Event/api/events.php', false, $context);    $response = json_decode($result, true);
    
    if ($response['success']) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Error creating event: " . $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Create New Event</h1>
        <a href="index.php" class="btn">Back to Events</a>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Event Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>
            
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="datetime-local" id="date" name="date" required>
            </div>
            
            <div class="form-group">
                <label for="attendees">Number of Attendees:</label>
                <input type="number" id="attendees" name="attendees" required>
            </div>
            
            <button type="submit" class="btn">Create Event</button>
        </form>
    </div>
</body>
</html>
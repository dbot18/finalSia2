<?php
if (!isset($_GET['event_id'])) {
    header("Location: index.php");
    exit;
}

$event_id = htmlspecialchars($_GET['event_id']);
$location = '';
$media = null;
$error = '';
$success = false;

// Allowed media types
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/quicktime'];

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = htmlspecialchars(trim($_POST['location']));
    $description = htmlspecialchars(trim($_POST['description']));

    // Handle file upload (media)
    if (isset($_FILES['media']) && $_FILES['media']['error'] === 0) {
        $fileType = $_FILES['media']['type'];
        $fileSize = $_FILES['media']['size'];
        $maxSize = 50 * 1024 * 1024; // 50MB limit
        $target_dir = "uploads/";

        // Check type and size
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Unsupported media type.";
        } elseif ($fileSize > $maxSize) {
            $error = "File exceeds maximum size of 50MB.";
        } else {
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $uniqueName = time() . "_" . basename($_FILES["media"]["name"]);
            $target_file = $target_dir . $uniqueName;

            if (move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
                $media = $target_file;
            } else {
                $error = "Error uploading media file.";
            }
        }
    }

    // If there's no error, send data to the API
    if (empty($error)) {
        $data = [
            'location' => $location,
            'description' => $description,
            'event_id' => $event_id,
            'media' => $media
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents('http://localhost/Event/api/emergencies.php', false, $context);

        if ($result === false) {
            $error = "Failed to connect to API.";
        } else {
            $response = json_decode($result, true);
            if (isset($response['success']) && $response['success']) {
                $success = true;
                $location = '';
            } else {
                $error = "Error reporting emergency: " . ($response['message'] ?? "Unknown error.");
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Emergency</title>
    <link rel="stylesheet" href="styles.css">
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 650px;
    margin: 40px auto;
    background-color: #ffffff;
    padding: 30px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-radius: 12px;
    border-top: 5px solid #6a00ff;
}

h1 {
    text-align: center;
    color: #6a00ff;
    margin-bottom: 25px;
    font-weight: bold;
}

.btn {
    display: inline-block;
    background: linear-gradient(135deg, #6a00ff, #00aaff);
    color: white;
    padding: 10px 18px;
    text-decoration: none;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn:hover {
    opacity: 0.85;
}

.btn.emergency {
    background: linear-gradient(135deg, #e60023, #ff4b5c);
}

.btn.emergency:hover {
    background: linear-gradient(135deg, #c3001b, #e63946);
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #444;
}

input[type="text"],
textarea,
input[type="file"] {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

.alert.error {
    background-color: #f8d7da;
    color: #842029;
    padding: 12px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
    border: 1px solid #f5c2c7;
}

.alert.success {
    background-color: #d1e7dd;
    color: #0f5132;
    padding: 12px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
    border: 1px solid #badbcc;
}
    </style>
</head>
<body>
<div class="container">
    <h1>Report Emergency</h1>
    <a href="index.php" class="btn">Back to Events</a>

    <?php if (!empty($error)): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success">Emergency reported successfully!</div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="emergencyForm">
        <div class="form-group">
            <label for="location">Emergency Location:</label>
            <input type="text" id="location" name="location" required value="<?= htmlspecialchars($location) ?>">
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>

        <div class="form-group">
            <label for="media">Media (optional):</label>
            <input type="file" id="media" name="media" accept="image/*,video/*">
        </div>

        <button type="submit" class="btn emergency">Report Emergency</button>
    </form>
</div>

<script>
<?php if ($success): ?>
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById('description').value = "";
    document.getElementById('media').value = "";
    setTimeout(() => {
        window.location.href = "index.php";
    }, 3000);
});
<?php endif; ?>
</script>
</body>
</html>

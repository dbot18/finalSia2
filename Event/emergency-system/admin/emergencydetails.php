<?php
require_once '../../api/shared.php';

if (!isset($_GET['id'])) {
    die('Emergency ID is required.');
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM emergencies WHERE id = ?");
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$emergency = $result->fetch_assoc();

if (!$emergency) {
    die('Emergency not found.');
}

// Build correct image path
$imageURL = null;
if (!empty($emergency['media'])) {
    // Remove any accidental leading slashes
    $mediaPath = ltrim($emergency['media'], '/');
    $imageURL = "http://localhost/Event/event-organizer/" . $mediaPath;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Emergency Details</title>
    <link rel="stylesheet" href="../styles.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f2f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 25px;
        }

        p {
            font-size: 16px;
            color: #444;
            margin-bottom: 15px;
        }

        strong {
            color: #222;
        }

        img {
            display: block;
            max-width: 100%;
            margin-top: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background-color: #6a00ff;
            color: #fff;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #5500cc;
        }

        .btn-right {
            float: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Emergency Details</h1>
        <p><strong>Location:</strong> <?= htmlspecialchars($emergency['location']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($emergency['description']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($emergency['status']) ?></p>

        <?php if ($imageURL): ?>
            <p><strong>Image:</strong></p>
            <img src="<?= $imageURL ?>" alt="Emergency Image">
        <?php else: ?>
            <p><em>No image uploaded.</em></p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn">Back to Dashboard</a>
        
        <!-- Edit button with the same style and aligned to the right -->
        <a href="edit.php?id=<?= $id ?>" class="btn btn-right">Edit Emergency</a>
    </div>
</body>
</html>

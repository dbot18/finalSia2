<?php
require_once('../api/shared.php');

$id = $_GET['id'] ?? '';

if (!$id) {
    die("No event ID provided.");
}

// Fetch event
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die("Event not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $attendees = $_POST['attendees'] ?? '';
    $status = $_POST['status'] ?? '';

    if (!$name || !$location || !$date || !$attendees || !$status) {
        die("Missing required fields.");
    }

    $dateTime = $date . ' ' . $time;

    $stmt = $conn->prepare("UPDATE events SET name = ?, location = ?, date = ?, attendees = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $location, $dateTime, $attendees, $status, $id);

    if ($stmt->execute()) {
        header("Location: eventdetails.php?id=" . urlencode($id));
        exit;
    } else {
        die("Error updating event: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            padding: 2rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        h1 {
            color: #6a00ff;
            margin-bottom: 1.5rem;
        }

        a.btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: transparent;
            border: 2px solid #6a00ff;
            color: #6a00ff;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        a.btn:hover {
            background: #6a00ff;
            color: white;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        input[type="text"],
        input[type="datetime-local"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        button.btn {
            background: #00aaff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button.btn:hover {
            background: #0077cc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>
        <a href="index.php" class="btn">Back to Events</a>

        <form method="POST">
            <div class="form-group">
                <label for="name">Event Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($event['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
            </div>

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="datetime-local" id="date" name="date" value="<?= htmlspecialchars(str_replace(' ', 'T', substr($event['date'], 0, 16))) ?>" required>
            </div>

            <div class="form-group">
                <label for="attendees">Number of Attendees:</label>
                <input type="number" id="attendees" name="attendees" value="<?= htmlspecialchars($event['attendees']) ?>" required>
            </div>


            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="PENDING" <?= strtoupper($event['status']) === 'PENDING' ? 'selected' : '' ?>>Pending</option>
                    <option value="APPROVED" <?= strtoupper($event['status']) === 'APPROVED' ? 'selected' : '' ?>>Approved</option>
                    <option value="DENIED" <?= strtoupper($event['status']) === 'DENIED' ? 'selected' : '' ?>>Deny</option>
                </select>
            </div>

            <button type="submit" class="btn">Update Event</button>
        </form>
    </div>
</body>
</html>

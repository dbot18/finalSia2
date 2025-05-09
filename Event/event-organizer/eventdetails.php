<?php
require_once('../api/shared.php');

$id = $_GET['id'] ?? '';

if (!$id) {
    die("No event ID provided.");
}

// Fetch event from database
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die("Event not found.");
}

// Format date and time
$dateFormatted = '';
$timeFormatted = '';
if (!empty($event['date'])) {
    $timestamp = strtotime($event['date']);
    $dateFormatted = date('Y-m-d', $timestamp);
    $timeFormatted = date('H:i', $timestamp);
}

// Set badge color based on status
$statusColor = match (strtoupper($event['status'] ?? '')) {
    'APPROVED' => 'success',
    'DENIED' => 'danger',
    default => 'secondary', // For 'PENDING' or unknown
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
    }

    .event-card {
      border-radius: 0.75rem;
      overflow: hidden;
    }

    .card-header {
      background: linear-gradient(45deg, #6a00ff, #00aaff);
    }

    .btn-outline-primary {
      border-color: #6a00ff;
      color: #6a00ff;
    }

    .btn-outline-primary:hover {
      background: linear-gradient(45deg, #6a00ff, #00aaff);
      color: white;
    }

    .badge-success {
      background-color: #198754;
    }

    .badge-danger {
      background-color: #dc3545;
    }

    .badge-secondary {
      background-color: #6c757d;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="card shadow event-card">
      <div class="card-header text-white">
        <h3 class="mb-0">Event Details</h3>
      </div>
      <div class="card-body bg-white">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><strong>ID:</strong> <?= htmlspecialchars($event['id']) ?></li>
          <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($event['name']) ?></li>
          <li class="list-group-item"><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></li>
          <li class="list-group-item"><strong>Date:</strong> <?= $dateFormatted ?: 'Not Provided' ?></li>
          <li class="list-group-item"><strong>Time:</strong> <?= $timeFormatted ?: 'Not Provided' ?></li>
          <li class="list-group-item"><strong>Attendees:</strong> <?= htmlspecialchars($event['attendees']) ?></li>
          <li class="list-group-item">
            <strong>Status:</strong>
            <span class="badge text-white badge-<?= $statusColor ?>"><?= htmlspecialchars($event['status']) ?></span>
          </li>
          <li class="list-group-item"><strong>Description:</strong><br><?= nl2br(htmlspecialchars($event['description'] ?? 'No description provided.')) ?></li>
        </ul>

        <!-- Action buttons -->
        <div class="mt-4 d-flex justify-content-between">
          <a href="index.php" class="btn btn-outline-primary">← Back to Calendar</a>
          <div>
            <a href="edit.php?id=<?= urlencode($event['id']) ?>" class="btn btn-outline-warning">✏️ Edit Event</a>
            <a href="delete.php?id=<?= urlencode($event['id']) ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this event?');">🗑 Delete Event</a>
          </div>
        </div>
      </div>
    </div>
    <footer class="text-center mt-5 text-muted">
      &copy; <?= date("Y"); ?> Event Management System
    </footer>
  </div>
</body>
</html>

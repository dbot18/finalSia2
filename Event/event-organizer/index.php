<?php
// Fetch events from API
$api_url = 'http://localhost/Event/api/events.php';
$response = file_get_contents($api_url);
$events = $response ? json_decode($response, true)['data'] ?? [] : [];

$formattedEvents = [];
foreach ($events as $event) {
    $formattedEvents[] = [
        'id' => $event['id'],
        'title' => $event['name'],
        'start' => $event['date'],
        'end' => $event['end_date'] ?? $event['date'],
        'location' => $event['location'],
        'attendees' => $event['attendees'],
        'status' => $event['status'] ?? 'PENDING',
    ];
}
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Event Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet"/>
  <style>
    body { background-color: #f4f6f9; }
    #sidebar {
        position: fixed;
        top: 56px;
        left: 0;
        height: calc(100% - 56px);
        width: 250px;
        background-color: #6a00ff;
        color: white;
        padding-top: 20px;
    }
    #sidebar a {
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        display: block;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }
    #sidebar a:hover { background-color: #00aaff; }
    .content-wrapper {
        margin-left: 250px;
        margin-top: 65px;
        padding: 20px;
    }
    .approved-event {
        background-color: #198754 !important;
        color: white !important;
        border-color: #198754 !important;
    }
    .denied-event {
        background-color: #dc3545 !important;
        color: white !important;
        border-color: #dc3545 !important;
    }
    .footer {
        background-color: #f8f9fa;
        padding: 10px;
        text-align: center;
    }
    .card-header {
        background: linear-gradient(45deg, #6a00ff, #00aaff);
        color: white;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .btn-success {
        background-color: #6a00ff;
        border-color: #6a00ff;
    }
    .btn-success:hover {
        background-color: #4e00b3;
        border-color: #4e00b3;
    }
    .btn-outline-secondary:hover { background-color: #dee2e6; }
    .navbar-dark.bg-dark { background-color: #6a00ff !important; }
    .navbar-brand { font-weight: bold; color: white; }
    .navbar .btn-outline-light {
        color: white;
        border-color: #00aaff;
    }
    .navbar .btn-outline-light:hover {
        background-color: #00aaff;
        border-color: #00aaff;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">📅 Event Management</span>
    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="text-white">Welcome, <?= $_SESSION['username'] ?? 'Guest' ?></span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<div id="sidebar">
  <a href="index.php">📆 Event Calendar</a>
  <a href="eventlist.php">📋 Event List</a>
  <a href="addevent.php">➕ Add Event</a>
</div>

<!-- Content -->
<div class="content-wrapper">
  <div class="row">
    <!-- Calendar -->
    <div class="col-lg-8 mb-4">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">Event Calendar</h5></div>
        <div class="card-body"><div id="calendar"></div></div>
      </div>
    </div>

   
    </div>
  </div>

  <!-- Footer -->
  <div class="footer mt-4 shadow-sm rounded">
    <p class="mb-0">&copy; <?= date("Y") ?> Event Management | <a href="index.php">Back to Calendar</a></p>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
    initialView: 'dayGridMonth',
    selectable: true,
    editable: false,
    events: <?= json_encode($formattedEvents); ?>,
    dateClick: function(info) {
      const datetimeInput = document.getElementById('datetime');
      datetimeInput.value = info.dateStr + "T00:00";
    },
    eventClick: function(info) {
      const event = info.event.extendedProps;
      const params = new URLSearchParams({
        id: info.event.id,
        name: info.event.title,
        location: event.location || '',
        date: info.event.startStr,
        attendees: event.attendees || ''
      });
      window.location.href = `eventdetails.php?${params.toString()}`;
    },
    eventDidMount: function(info) {
      const status = info.event.extendedProps.status;
      if (status === 'APPROVED') {
        info.el.classList.add('approved-event');
      } else if (status === 'DENIED') {
        info.el.classList.add('denied-event');
      }
    }
  });

  calendar.render();

  document.getElementById('eventForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const eventData = {
      name: formData.get('name'),
      location: formData.get('location'),
      date: formData.get('datetime'),
      attendees: formData.get('attendees')
    };

    axios.post('http://localhost/Event/api/events.php', JSON.stringify(eventData), {
      headers: { 'Content-Type': 'application/json' }
    })
    .then(res => {
      if (res.data.success) {
        alert('Event saved!');
        location.reload();
      } else {
        console.error('Error saving event:', res.data.message); // Log the error message
      }
    })
    .catch(err => {
      console.error('Error details:', err.response ? err.response.data : err); // Log the detailed error to the console
    });
  });
});

function clearForm() {
  document.getElementById('eventForm').reset();
  document.getElementById('eventId').value = '';
}
</script>

</body>
</html>

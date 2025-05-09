<?php
// Fetch existing holidays using cURL
$api_url = 'http://localhost/Event/api/process.php';

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);  // Timeout after 30 seconds

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

// Decode JSON response
$holidays = $response ? json_decode($response, true)['data'] ?? [] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Holidays</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-section {
      border-right: 1px solid #ddd;
    }
  </style>
</head>
<body>
<div class="container-fluid py-5">
  <div class="row">
    <!-- Add Holiday Form -->
    <div class="col-lg-5 form-section pe-4">
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Add New Holiday</h5>
        </div>
        <div class="card-body">
          <form id="addHolidayForm" class="needs-validation" novalidate>
            <div class="mb-3">
              <label for="holidayDate" class="form-label">Holiday Date</label>
              <input type="date" class="form-control" id="holidayDate" name="date" required>
              <div class="invalid-feedback">Please enter a valid holiday date.</div>
            </div>
            <div class="mb-3">
              <label for="holidayReason" class="form-label">Holiday Reason</label>
              <input type="text" class="form-control" id="holidayReason" name="reason" minlength="8" required>
              <div class="invalid-feedback">Reason must be at least 8 characters.</div>
            </div>
            <button type="submit" class="btn btn-success w-100">Add Holiday</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Holiday List -->
    <div class="col-lg-7">
      <div class="card shadow">
        <div class="card-header bg-secondary text-white">
          <h5 class="mb-0">Holiday List</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($holidays)): ?>
            <div class="table-responsive">
              <table id="holidayTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($holidays as $index => $holiday): ?>
                    <tr>
                      <td><?= $index + 1 ?></td>
                      <td><?= htmlspecialchars($holiday['date']) ?></td>
                      <td><?= htmlspecialchars($holiday['reason']) ?></td>
                      <td>
                        <a href="javascript:deleteHoliday('<?= $holiday['id'] ?>');" class="btn btn-sm btn-danger">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-info">No holidays found.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Add Holiday Form Submission
  document.getElementById('addHolidayForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var date = document.getElementById('holidayDate').value;
    var reason = document.getElementById('holidayReason').value;

    var holidayData = {
      date: date,
      reason: reason
    };

    fetch('http://localhost/Event/api/process.php', {
      method: 'POST',
      body: JSON.stringify(holidayData),
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        var newHoliday = data.holiday;

        // Ensure the table and tbody exist
        var table = document.getElementById('holidayTable');
        if (table) {
          var tbody = table.getElementsByTagName('tbody')[0];
          if (tbody) {
            // Insert a new row dynamically
            var newRow = tbody.insertRow(tbody.rows.length);
            newRow.innerHTML = `
              <td>${tbody.rows.length}</td>
              <td>${newHoliday.date}</td>
              <td>${newHoliday.reason}</td>
              <td><a href="javascript:deleteHoliday('${newHoliday.id}');" class="btn btn-sm btn-danger">Delete</a></td>
            `;
          }
        }

        // Clear the form fields
        document.getElementById('holidayDate').value = '';
        document.getElementById('holidayReason').value = '';
      } else {
        alert('Failed to add holiday.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('There was an error adding the holiday.');
    });
  });
  
  // Function to delete holiday (if needed)
  function deleteHoliday(hid) {
    if (confirm('Are you sure you want to delete this holiday?')) {
      window.location.href = 'http://localhost/Event/api/process.php?cmd=deleteHoliday&id=' + hid;
    }
  }
</script>

</body>
</html>

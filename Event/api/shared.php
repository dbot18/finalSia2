<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = ""; // Use your actual password if needed
$dbname = "event_emergency_db"; // Make sure this matches your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize input data
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        global $conn;
        return mysqli_real_escape_string($conn, trim($input));
    }
}

// Register a user using MySQLi
function registerUser($name, $email, $username, $password, $confirmPassword) {
    global $conn;

    if ($password !== $confirmPassword) {
        return ['success' => false, 'message' => 'Passwords do not match.'];
    }

    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters.'];
    }

    // Check if user exists
    $checkQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Username or email already exists.'];
    }
    $stmt->close();

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $insertQuery = "INSERT INTO users (name, email, username, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $name, $email, $username, $hashedPassword);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Registration successful.'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

// Function to fetch event records with optional pagination
function getEventRecords($limit = 10, $offset = 0) {
    global $conn;
    $events = [];

    if (is_numeric($limit) && is_numeric($offset)) {
        $stmt = $conn->prepare("SELECT * FROM events ORDER BY date DESC LIMIT ?, ?");
        $stmt->bind_param("ii", $offset, $limit);
    } else {
        $stmt = $conn->prepare("SELECT * FROM events ORDER BY date DESC");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    $stmt->close();
    return $events;
}

// Function to count total number of events
function getTotalEventCount() {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM events";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    return 0;
}

// Function to send JSON responses
if (!function_exists('jsonResponse')) {
    function jsonResponse($success, $message, $data = null) {
        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];
        echo json_encode($response);
        exit();
    }
}
?>

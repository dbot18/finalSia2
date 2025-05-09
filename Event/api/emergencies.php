emergencies.php

<?php
require_once 'shared.php'; // Ensure the connection and jsonResponse() are set up

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");



$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = sanitizeInput($_GET['id']);
            $result = $conn->query("SELECT * FROM emergencies WHERE id = $id");
            $emergency = $result->fetch_assoc();
            if ($emergency) {
                jsonResponse(true, 'Emergency fetched', $emergency);
            } else {
                jsonResponse(false, 'Emergency not found');
            }
        } else {
            $result = $conn->query("SELECT * FROM emergencies");
            $emergencies = [];
            while ($row = $result->fetch_assoc()) {
                $emergencies[] = $row;
            }
            jsonResponse(true, 'Emergencies retrieved', $emergencies);
        }
        break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
        $location = sanitizeInput($data['location']);
        $description = sanitizeInput($data['description']);
        $event_id = isset($data['event_id']) ? sanitizeInput($data['event_id']) : null;
        $media = isset($data['media']) ? sanitizeInput($data['media']) : null;
        $status = 'pending'; // default status
        
        $stmt = $conn->prepare("INSERT INTO emergencies (location, description, event_id, media, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $location, $description, $event_id, $media, $status);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Emergency reported', ['id' => $stmt->insert_id]);
        } else {
            jsonResponse(false, 'Error reporting emergency');
        }
        break;

    case 'PUT':
        // Handle PUT request (update emergency)
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['id']) || !isset($data['location']) || !isset($data['description'])) {
            jsonResponse(false, 'Missing required fields');
        }

        $id = sanitizeInput($data['id']);
        $location = sanitizeInput($data['location']);
        $description = sanitizeInput($data['description']);
        $status = sanitizeInput($data['status']);

        $stmt = $conn->prepare("UPDATE emergencies SET location = ?, description = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssi", $location, $description, $status, $id);

        if ($stmt->execute()) {
            jsonResponse(true, 'Emergency updated successfully');
        } else {
            jsonResponse(false, 'Failed to update emergency');
        }
        break;

        case 'DELETE':
            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['id'])) {
                jsonResponse(false, 'Missing emergency ID');
            }
        
            $id = sanitizeInput($data['id']);
            $stmt = $conn->prepare("DELETE FROM emergencies WHERE id = ?");
            $stmt->bind_param("i", $id);
        
            if ($stmt->execute()) {
                jsonResponse(true, 'Emergency deleted successfully');
            } else {
                jsonResponse(false, 'Failed to delete emergency');
            }
            break;
}
?>

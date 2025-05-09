<?php
require_once 'shared.php'; // Includes DB connection and utility functions

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");



$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = sanitizeInput($_GET['id']);
            $stmt = $conn->prepare("SELECT id, name, location, date, attendees, status FROM events WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
            $stmt->close();

            if ($event) {
                jsonResponse(true, 'Event retrieved', $event);
            } else {
                jsonResponse(false, 'Event not found');
            }
        } else {
            $result = $conn->query("SELECT id, name, location, date, attendees, status FROM events ORDER BY date DESC");
            $events = [];
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
            jsonResponse(true, 'Events retrieved', $events);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['name'], $data['location'], $data['datetime'], $data['attendees'])) {
            $name = sanitizeInput($data['name']);
            $location = sanitizeInput($data['location']);
            $date = sanitizeInput($data['datetime']); // Updated field
            $attendees = (int)$data['attendees'];

            $stmt = $conn->prepare("INSERT INTO events (name, location, date, attendees) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $name, $location, $date, $attendees);

            if ($stmt->execute()) {
                $insertedId = $stmt->insert_id;
                $stmt->close();
                jsonResponse(true, 'Event created', ['id' => $insertedId]);
            } else {
                $error = $stmt->error;
                $stmt->close();
                jsonResponse(false, "Error creating event: $error");
            }
        } else {
            jsonResponse(false, 'Missing required fields');
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['id'], $input['name'], $input['location'], $input['datetime'], $input['attendees'], $input['status'])) {
            jsonResponse(false, 'Missing or invalid input');
        }

        $id = (int)$input['id'];
        $name = sanitizeInput($input['name']);
        $location = sanitizeInput($input['location']);
        $date = sanitizeInput($input['datetime']); // Updated field
        $attendees = (int)$input['attendees'];
        $status = sanitizeInput($input['status']);

        $stmt = $conn->prepare("UPDATE events SET name = ?, location = ?, date = ?, attendees = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssisi", $name, $location, $date, $attendees, $status, $id);

        if ($stmt->execute()) {
            $stmt->close();
            jsonResponse(true, 'Event updated successfully');
        } else {
            $error = $stmt->error;
            $stmt->close();
            jsonResponse(false, "Update failed: $error");
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $id = (int)$data['id'];

            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $stmt->close();
                jsonResponse(true, 'Event deleted');
            } else {
                $error = $stmt->error;
                $stmt->close();
                jsonResponse(false, "Delete failed: $error");
            }
        } else {
            jsonResponse(false, 'Missing event ID');
        }
        break;

    default:
        http_response_code(405);
        jsonResponse(false, 'Method not allowed');
}
?>

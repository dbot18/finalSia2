<?php
require_once 'shared.php';  // Ensure shared.php is included here

ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");

$method = $_SERVER['REQUEST_METHOD'];

if (!isset($db)) {
    die('Database connection failed.');
}

if ($method === 'GET' && isset($_GET['cmd']) && $_GET['cmd'] === 'listHolidays') {
    try {
        $stmt = $db->query("SELECT * FROM holidays_list ORDER BY date ASC");
        $holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $holidays]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if (isset($data->date) && isset($data->reason)) {
        try {
            $query = "INSERT INTO holidays_list (date, reason) VALUES (:date, :reason)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':date', $data->date);
            $stmt->bindParam(':reason', $data->reason);

            if ($stmt->execute()) {
                $newHolidayId = $db->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'holiday' => [
                        'id' => $newHolidayId,
                        'date' => $data->date,
                        'reason' => $data->reason
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to insert holiday.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
    exit;
}

if ($method === 'GET' && isset($_GET['cmd']) && $_GET['cmd'] === 'deleteHoliday' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $db->prepare("DELETE FROM holidays_list WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: http://localhost/Event/holiday.php");
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Default error
http_response_code(405);
echo json_encode(['error' => 'Invalid request.']);

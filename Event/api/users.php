<?php
require_once('shared.php');
header('Content-Type: application/json');

// Helper to read JSON body
$input = json_decode(file_get_contents('php://input'), true);

// GET: /api/users.php or /api/users.php?id=1
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT id, name, email, username FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        jsonResponse(true, "User fetched", $result);
    } else {
        $result = $conn->query("SELECT id, name, email, username FROM users ORDER BY id DESC");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        jsonResponse(true, "User list fetched", $users);
    }
}

// POST: Create user
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($input['name'] ?? '');
    $email = sanitizeInput($input['email'] ?? '');
    $username = sanitizeInput($input['username'] ?? '');
    $password = $input['password'] ?? '';
    $confirmPassword = $input['confirm_password'] ?? '';

    $response = registerUser($name, $email, $username, $password, $confirmPassword);
    jsonResponse($response['success'], $response['message']);
}

// PUT: Update user
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isset($_GET['id'])) {
        jsonResponse(false, "User ID required for update.");
    }

    $id = intval($_GET['id']);
    $name = sanitizeInput($input['name'] ?? '');
    $email = sanitizeInput($input['email'] ?? '');
    $username = sanitizeInput($input['username'] ?? '');

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, username = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $username, $id);

    if ($stmt->execute()) {
        jsonResponse(true, "User updated successfully.");
    } else {
        jsonResponse(false, "Failed to update user.");
    }
}

// DELETE: Delete user
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) {
        jsonResponse(false, "User ID required for deletion.");
    }

    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        jsonResponse(true, "User deleted successfully.");
    } else {
        jsonResponse(false, "Failed to delete user.");
    }
}

else {
    jsonResponse(false, "Unsupported request method.");
}

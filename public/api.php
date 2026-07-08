<?php
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'save-cv' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    if (json_decode($body) === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
    if (file_put_contents('cv.json', $body) !== false) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to write cv.json. Make sure directory permissions are writable.']);
    }
} elseif ($action === 'save-avatar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    if (file_put_contents('avatar.jpg', $body) !== false) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to write avatar.jpg. Make sure directory permissions are writable.']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
}

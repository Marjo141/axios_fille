<?php
header('Content-Type: application/json');

session_start();

function respond(array $data, int $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'POST only'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    respond(['success' => false, 'message' => 'Invalid JSON body'], 400);
}

$username = trim($body['username'] ?? '');
$password = trim($body['password'] ?? '');

if ($username === '' || $password === '') {
    respond(['success' => false, 'message' => 'Username and password are required'], 400);
}

$users = [
    'admin' => 'password',
    'user' => '1234'
];

if (!isset($users[$username]) || $users[$username] !== $password) {
    respond(['success' => false, 'message' => 'Invalid username or password'], 401);
}

$token = bin2hex(random_bytes(16));
$_SESSION['token'] = $token;
$_SESSION['username'] = $username;

respond(['success' => true, 'token' => $token]);

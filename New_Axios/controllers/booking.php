<?php
header('Content-Type: application/json');

session_start();

function respond(array $data, int $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

if (!isset($_SESSION['bookings']) || !is_array($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [];
}

$bookings = &$_SESSION['bookings'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!is_array($body)) {
        respond(['success' => false, 'message' => 'Invalid JSON body'], 400);
    }

    $firstname = trim($body['firstname'] ?? '');
    $lastname = trim($body['lastname'] ?? '');
    $checkin = trim($body['bookingdates']['checkin'] ?? '');
    $checkout = trim($body['bookingdates']['checkout'] ?? '');
    $additionalneeds = trim($body['additionalneeds'] ?? '');

    if ($firstname === '' || $lastname === '' || $checkin === '' || $checkout === '') {
        respond(['success' => false, 'message' => 'Firstname, lastname, checkin and checkout are required'], 400);
    }

    $newBooking = [
        'id' => count($bookings) + 1,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'totalprice' => $body['totalprice'] ?? 0,
        'depositpaid' => $body['depositpaid'] ?? false,
        'bookingdates' => [
            'checkin' => $checkin,
            'checkout' => $checkout,
        ],
        'additionalneeds' => $additionalneeds,
    ];

    $bookings[] = $newBooking;
    $_SESSION['bookings'] = $bookings;

    respond(['success' => true, 'booking' => $newBooking]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['bookingID']) ? intval($_GET['bookingID']) : 0;
    if ($id <= 0) {
        respond(['success' => false, 'message' => 'bookingID is required'], 400);
    }

    foreach ($bookings as $booking) {
        if ($booking['id'] === $id) {
            respond(['success' => true, 'booking' => $booking]);
        }
    }

    respond(['success' => false, 'message' => 'Booking not found'], 404);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!is_array($body) || !isset($body['id'])) {
        respond(['success' => false, 'message' => 'Invalid JSON body or missing id'], 400);
    }

    $id = intval($body['id']);
    if ($id <= 0) {
        respond(['success' => false, 'message' => 'Invalid booking id'], 400);
    }

    foreach ($bookings as $index => $booking) {
        if ($booking['id'] === $id) {
            $firstname = trim($body['firstname'] ?? $booking['firstname']);
            $lastname = trim($body['lastname'] ?? $booking['lastname']);
            $checkin = trim($body['bookingdates']['checkin'] ?? $booking['bookingdates']['checkin']);
            $checkout = trim($body['bookingdates']['checkout'] ?? $booking['bookingdates']['checkout']);
            $additionalneeds = trim($body['additionalneeds'] ?? $booking['additionalneeds']);

            if ($firstname === '' || $lastname === '' || $checkin === '' || $checkout === '') {
                respond(['success' => false, 'message' => 'Firstname, lastname, checkin and checkout are required'], 400);
            }

            $bookings[$index] = [
                'id' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'totalprice' => $body['totalprice'] ?? $booking['totalprice'],
                'depositpaid' => $body['depositpaid'] ?? $booking['depositpaid'],
                'bookingdates' => [
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                ],
                'additionalneeds' => $additionalneeds,
            ];

            $_SESSION['bookings'] = $bookings;
            respond(['success' => true, 'booking' => $bookings[$index]]);
        }
    }

    respond(['success' => false, 'message' => 'Booking not found'], 404);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['bookingID']) ? intval($_GET['bookingID']) : 0;
    if ($id <= 0) {
        respond(['success' => false, 'message' => 'bookingID is required'], 400);
    }

    foreach ($bookings as $index => $booking) {
        if ($booking['id'] === $id) {
            array_splice($bookings, $index, 1);
            $_SESSION['bookings'] = $bookings;
            respond(['success' => true, 'message' => 'Booking deleted']);
        }
    }

    respond(['success' => false, 'message' => 'Booking not found'], 404);
}

respond(['success' => false, 'message' => 'Unsupported request method'], 405);

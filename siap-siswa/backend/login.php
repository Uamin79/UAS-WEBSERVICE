<?php
require 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password required']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Verify additional data based on role
    $validUser = false;

    switch ($user['role']) {
        case 'admin':
            $admin = getOne('admins', 'user_id = ?', [$user['id']]);
            $validUser = !empty($admin);
            break;
        case 'guru':
            $guru = getOne('teachers', 'user_id = ?', [$user['id']]);
            $validUser = !empty($guru);
            break;
        case 'orangtua':
            $orangtua = getOne('parents', 'user_id = ?', [$user['id']]);
            $validUser = !empty($orangtua);
            break;
    }

    if ($validUser) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid user profile']);
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}
?>

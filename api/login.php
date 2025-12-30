<?php
require_once '../config.php';
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $phone = $data['phone_number'];
    $password = $data['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone_number = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        unset($user['password']);
        echo json_encode(['success' => true, 'message' => 'Login berhasil!', 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nomor HP atau Password salah.']);
    }
}
?>
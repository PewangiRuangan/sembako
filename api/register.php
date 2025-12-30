<?php
require_once '../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $fullName = $data['full_name'];
    $phone = $data['phone_number'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $residenceType = $data['residence_type'];

    if (empty($fullName) || empty($phone) || empty($data['password']) || empty($residenceType)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, phone_number, password, residence_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullName, $phone, $password, $residenceType]);
        echo json_encode(['success' => true, 'message' => 'Registrasi berhasil. Silakan login.']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Nomor HP sudah terdaftar.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mendaftar: ' . $e->getMessage()]);
        }
    }
}
?>
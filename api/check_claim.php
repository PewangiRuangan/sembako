<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$phone = isset($input['phone']) ? preg_replace('/[^0-9]/', '', $input['phone']) : '';

if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Nomor HP wajib diisi']);
    exit;
}

try {
    // 1. Check if user exists
    $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE phone_number = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'status' => 'not_found', 'message' => 'Nomor HP tidak terdaftar']);
        exit;
    }

    // 2. Check if already claimed
    $stmt = $pdo->prepare("SELECT id FROM claims WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($claim) {
        echo json_encode([
            'success' => true,
            'status' => 'claimed',
            'user_id' => $user['id'],
            'full_name' => $user['full_name'],
            'message' => 'Anda sudah memiliki tiket.'
        ]);
        exit;
    }

    // 3. Check Quota (Limit 50)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM claims");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    if ($total >= 50) {
        echo json_encode(['success' => false, 'status' => 'full', 'message' => 'Mohon maaf, kuota sembako sudah habis.']);
        exit;
    }

    // 4. Available
    echo json_encode([
        'success' => true,
        'status' => 'available',
        'user_id' => $user['id'],
        'full_name' => $user['full_name'],
        'message' => 'Kuota tersedia. Silakan ambil tiket.'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
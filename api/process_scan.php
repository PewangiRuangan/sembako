<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $input['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Data QR tidak valid']);
    exit;
}

try {
    // 1. Check if user has a claim
    $stmt = $pdo->prepare("SELECT id, collected_at, claimed_at FROM claims WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$claim) {
        echo json_encode(['success' => false, 'message' => 'User belum melakukan booking tiket!']);
        exit;
    }

    // 2. Check if already collected
    if ($claim['collected_at']) {
        echo json_encode(['success' => false, 'message' => 'Gagal: Sembako SUDAH DIANTARKAN sebelumnya.']);
        exit;
    }

    // 3. Mark as collected
    $stmt = $pdo->prepare("UPDATE claims SET collected_at = NOW() WHERE id = ?");
    $stmt->execute([$claim['id']]);

    // Get User Name
    $stmtUser = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $user = $stmtUser->fetch();
    $name = $user['full_name'] ?? 'Peserta';

    echo json_encode([
        'success' => true,
        'message' => "SUKSES! Sembako untuk $name berhasil diverifikasi."
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>
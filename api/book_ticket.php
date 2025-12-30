<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = isset($input['user_id']) ? intval($input['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid User ID']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Final Quota Check (Locking for concurrency safety is better, but simple count for now)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM claims");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    if ($total >= 50) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal klaim: Kuota baru saja habis.']);
        exit;
    }

    // 2. Check duplicate
    $stmt = $pdo->prepare("SELECT id FROM claims WHERE user_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Anda sudah mengambil tiket sebelumnya.']);
        exit;
    }

    // 3. Insert Claim
    $stmt = $pdo->prepare("INSERT INTO claims (user_id) VALUES (?)");
    $stmt->execute([$user_id]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Berhasil klaim tiket!']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
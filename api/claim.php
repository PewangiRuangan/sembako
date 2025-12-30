<?php
require_once '../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'];

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Invalid User ID']);
        exit;
    }

    // 1. Check User details (Eligibility)
    $stmt = $pdo->prepare("SELECT residence_type FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User tidak ditemukan.']);
        exit;
    }

    if ($user['residence_type'] === 'asrama') {
        echo json_encode(['success' => false, 'message' => 'Maaf, Anak Asrama tidak berhak mendapatkan sembako.']);
        exit;
    }

    // 2. Check if already claimed
    $stmtClaim = $pdo->prepare("SELECT * FROM claims WHERE user_id = ?");
    $stmtClaim->execute([$userId]);
    $existingClaim = $stmtClaim->fetch();

    if ($existingClaim) {
        echo json_encode(['success' => false, 'message' => 'User ini SUDAH mengambil sembako pada ' . $existingClaim['claimed_at']]);
        exit;
    }

    // 3. Process Claim
    try {
        $stmtInsert = $pdo->prepare("INSERT INTO claims (user_id) VALUES (?)");
        $stmtInsert->execute([$userId]);
        echo json_encode(['success' => true, 'message' => 'Sembako BERHASIL diambil!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal memproses klaim.']);
    }
}
?>
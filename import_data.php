<?php
require_once 'config.php';

// File CSV
$csvFile = 'BIODATA KORBAN - Form Responses 1.csv';

if (!file_exists($csvFile)) {
    die("File CSV tidak ditemukan.");
}

$file = fopen($csvFile, 'r');
$header = fgetcsv($file); // Skip header

$count = 0;
$success = 0;
$failed = 0;

echo "<pre>";
echo "Mulai Import Data...\n";

while (($row = fgetcsv($file)) !== false) {
    // Mapping Column based on your CSV structure
    // 1: Nama
    // 4: Nomor Handphone
    // 9: Pilihan Tempat Tinggal

    $name = $row[1];
    $phone = cleanPhone($row[4]);
    $residenceRaw = strtolower($row[9]);

    // Determine Status
    $status = 'kos'; // Default
    if (strpos($residenceRaw, 'asrama') !== false) {
        $status = 'asrama';
    } else if (strpos($residenceRaw, 'kontrakan') !== false) {
        $status = 'kontrakan';
    }

    // Default Password: "123456"
    $password = password_hash("123456", PASSWORD_DEFAULT);

    if (!empty($name) && !empty($phone)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, phone_number, password, residence_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $password, $status]);
            $success++;
        } catch (PDOException $e) {
            // Likely duplicate phone number
            // echo "Gagal import $name ($phone): " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    $count++;
}

fclose($file);

echo "Selesai.\n";
echo "Total Baris: $count\n";
echo "Berhasil Import: $success\n";
echo "Gagal (Duplikat/Error): $failed\n";

function cleanPhone($phone)
{
    // Remove non-numeric characters
    return preg_replace('/[^0-9]/', '', $phone);
}
?>
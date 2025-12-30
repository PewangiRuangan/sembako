<?php
require_once 'config.php';

try {
    $pdo->exec("ALTER TABLE claims ADD COLUMN collected_at TIMESTAMP NULL");
    echo "Migration successful: Added collected_at to claims table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column collected_at already exists. Skipping.\n";
    } else {
        echo "Migration failed: " . $e->getMessage() . "\n";
    }
}
?>
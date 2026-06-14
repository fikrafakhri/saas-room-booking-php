<?php
// helpers/log_helper.php

function writeActivityLog($pdo, $user_id, $action, $description) {
    // Mengambil IP Address asli pengguna
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    // IP fallback jika dijalankan di localhost ipv6
    if ($ip_address === '::1') {
        $ip_address = '127.0.0.1';
    }

    try {
        $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address) 
                VALUES (:user_id, :action, :description, :ip_address)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id'     => $user_id,
            ':action'      => $action,
            ':description' => $description,
            ':ip_address'  => $ip_address
        ]);
    } catch (\PDOException $e) {
        // Log error internal jika database gagal menulis log
        error_log("Gagal menulis audit log: " . $e->getMessage());
    }
}
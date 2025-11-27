<?php
require_once 'config.php';

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        // Update existing
        $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "Senha do usuário 'admin' atualizada para 'admin123'.";
    } else {
        // Create new
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo "Usuário 'admin' criado com a senha 'admin123'.";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
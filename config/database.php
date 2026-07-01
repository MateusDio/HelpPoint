<?php
// ============================================
// CONFIGURAÇÃO DO BANCO DE DADOS
// ============================================
// Valores sensiveis vem do arquivo .env (nao versionado).
// Veja .env.example para modelo.
// ============================================

require_once __DIR__ . '/env.php';

$__host_atual = $_SERVER['HTTP_HOST'] ?? '';
$__is_local = (strpos($__host_atual, 'localhost') !== false || strpos($__host_atual, '127.0.0.1') !== false);

if ($__is_local) {
    // === XAMPP LOCAL (defaults; sobrescriva no .env se necessario) ===
    $host = env('DB_HOST', 'localhost');
    $dbname = env('DB_NAME', 'teste');
    $username = env('DB_USER', 'root');
    $password = env('DB_PASS', '');
} else {
    // === PRODUÇÃO — obrigatorio ter .env preenchido ===
    $host = env('DB_HOST', '');
    $dbname = env('DB_NAME', '');
    $username = env('DB_USER', '');
    $password = env('DB_PASS', '');
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}

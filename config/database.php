<?php
// ============================================
// CONFIGURAÇÃO DO BANCO DE DADOS
// ============================================
// TODO: Definir nome do banco, usuário e senha
// com a equipe antes de rodar o projeto.
// ============================================

// Detecta ambiente: local (XAMPP) vs produção (hospedagem)
$__host_atual = $_SERVER['HTTP_HOST'] ?? '';
$__is_local = (strpos($__host_atual, 'localhost') !== false || strpos($__host_atual, '127.0.0.1') !== false);

if ($__is_local) {
    // === XAMPP LOCAL ===
    $host = 'localhost';
    $dbname = 'teste';
    $username = 'root';
    $password = '';
} else {
    // === PRODUÇÃO (InfinityFree) ===
    // TODO: preencher com os dados que o painel da hospedagem mostrar
    $host = 'sqlXXX.infinityfree.com';
    $dbname = 'if0_XXXXXXX_helppoint';
    $username = 'if0_XXXXXXX';
    $password = 'sua_senha_aqui';
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

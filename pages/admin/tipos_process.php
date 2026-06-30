<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    if ($nome === '') { header('Location: tipos.php?erro=campos'); exit(); }
    $stmt = $pdo->prepare("INSERT INTO tipo (nome) VALUES (:n)");
    $stmt->execute(['n' => $nome]);
    header('Location: tipos.php?sucesso=criado');
    exit();
}

if ($acao === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    if ($nome === '' || $id === 0) { header('Location: tipos.php?erro=campos'); exit(); }
    $stmt = $pdo->prepare("UPDATE tipo SET nome=:n WHERE id=:id");
    $stmt->execute(['n' => $nome, 'id' => $id]);
    header('Location: tipos.php?sucesso=editado');
    exit();
}

if ($acao === 'excluir' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tipo WHERE id=:id");
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        header('Location: tipos.php?erro=fk');
        exit();
    }
    header('Location: tipos.php?sucesso=excluido');
    exit();
}

header('Location: tipos.php');
exit();

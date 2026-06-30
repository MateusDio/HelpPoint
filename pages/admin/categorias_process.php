<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $desc = trim($_POST['descricao'] ?? '');
    if ($nome === '') { header('Location: categorias.php?erro=campos'); exit(); }
    $stmt = $pdo->prepare("INSERT INTO categoria (nome, descricao) VALUES (:n, :d)");
    $stmt->execute(['n' => $nome, 'd' => $desc]);
    header('Location: categorias.php?sucesso=criado');
    exit();
}

if ($acao === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $desc = trim($_POST['descricao'] ?? '');
    if ($nome === '' || $id === 0) { header('Location: categorias.php?erro=campos'); exit(); }
    $stmt = $pdo->prepare("UPDATE categoria SET nome=:n, descricao=:d WHERE id=:id");
    $stmt->execute(['n' => $nome, 'd' => $desc, 'id' => $id]);
    header('Location: categorias.php?sucesso=editado');
    exit();
}

if ($acao === 'excluir' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categoria WHERE id=:id");
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        header('Location: categorias.php?erro=fk');
        exit();
    }
    header('Location: categorias.php?sucesso=excluido');
    exit();
}

header('Location: categorias.php');
exit();

<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_id = (int)($_POST['tipo_id'] ?? 0);
    $n_serie = trim($_POST['n_serie'] ?? '');
    $patrimonio = trim($_POST['patrimonio'] ?? '');
    $status = $_POST['status'] ?? 'Disponivel';
    $desc = trim($_POST['descricao'] ?? '');
    if ($tipo_id === 0) { header('Location: equipamentos.php?erro=campos'); exit(); }
    $stmt = $pdo->prepare("INSERT INTO equipamentos (n_serie, patrimonio, status, descricao, tipo_id) VALUES (:s, :p, :st, :d, :t)");
    $stmt->execute([
        's' => $n_serie ?: null,
        'p' => $patrimonio ?: null,
        'st' => $status,
        'd' => $desc,
        't' => $tipo_id
    ]);
    header('Location: equipamentos.php?sucesso=criado');
    exit();
}

if ($acao === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $tipo_id = (int)($_POST['tipo_id'] ?? 0);
    $n_serie = trim($_POST['n_serie'] ?? '');
    $patrimonio = trim($_POST['patrimonio'] ?? '');
    $status = $_POST['status'] ?? 'Disponivel';
    $desc = trim($_POST['descricao'] ?? '');
    if ($id === 0 || $tipo_id === 0) { header('Location: equipamentos.php?erro=campos'); exit(); }
    $stmt = $pdo->prepare("UPDATE equipamentos SET n_serie=:s, patrimonio=:p, status=:st, descricao=:d, tipo_id=:t WHERE id=:id");
    $stmt->execute([
        's' => $n_serie ?: null,
        'p' => $patrimonio ?: null,
        'st' => $status,
        'd' => $desc,
        't' => $tipo_id,
        'id' => $id
    ]);
    header('Location: equipamentos.php?sucesso=editado');
    exit();
}

if ($acao === 'excluir' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM equipamentos WHERE id=:id");
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        header('Location: equipamentos.php?erro=fk');
        exit();
    }
    header('Location: equipamentos.php?sucesso=excluido');
    exit();
}

header('Location: equipamentos.php');
exit();

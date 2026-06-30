<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($acao === 'status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $validos = ['Aberto','Em Andamento','Concluido','Cancelado'];
    if ($id === 0 || !in_array($status, $validos, true)) {
        header('Location: chamados.php');
        exit();
    }
    $stmt = $pdo->prepare("UPDATE chamados SET status=:s WHERE id=:id");
    $stmt->execute(['s' => $status, 'id' => $id]);
    header('Location: chamados.php?sucesso=status');
    exit();
}

if ($acao === 'excluir' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM chamados WHERE id=:id");
    $stmt->execute(['id' => $id]);
    header('Location: chamados.php?sucesso=excluido');
    exit();
}

header('Location: chamados.php');
exit();

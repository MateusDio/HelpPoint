<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();

$acao = $_POST['acao'] ?? '';

if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)$_SESSION['user_id'];
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $equip_id_raw = $_POST['equipamento_id'] ?? '';
    $equipamento_id = $equip_id_raw === '' ? null : (int)$equip_id_raw;
    $nivel = $_POST['nivel'] ?? 'Medio';
    $local = trim($_POST['local'] ?? '');
    $obs = trim($_POST['obs'] ?? '');

    if ($categoria_id === 0 || $obs === '') {
        header('Location: index.php?erro=campos');
        exit();
    }

    $stmt = $pdo->prepare("
        INSERT INTO chamados (status, data, hora, nivel, obs, local, user_id, categoria_id, equipamento_id)
        VALUES ('Aberto', CURDATE(), CURTIME(), :nivel, :obs, :local, :uid, :cid, :eid)
    ");
    $stmt->execute([
        'nivel' => $nivel,
        'obs' => $obs,
        'local' => $local,
        'uid' => $userId,
        'cid' => $categoria_id,
        'eid' => $equipamento_id
    ]);

    header('Location: index.php?sucesso=criado');
    exit();
}

header('Location: index.php');
exit();

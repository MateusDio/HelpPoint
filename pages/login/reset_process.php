<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reset_password.php');
    exit();
}

$token = $_POST['token'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

if ($token === '' || $senha === '' || $confirmar_senha === '') {
    header('Location: reset_password.php?token=' . urlencode($token) . '&erro=campos');
    exit();
}

if ($senha !== $confirmar_senha) {
    header('Location: reset_password.php?token=' . urlencode($token) . '&erro=senhas');
    exit();
}

$stmt = $pdo->prepare("SELECT id, user_id, expira_em, usado_em FROM password_reset WHERE token = :token LIMIT 1");
$stmt->execute(['token' => $token]);
$registro = $stmt->fetch();

if (!$registro || $registro['usado_em'] !== null || strtotime($registro['expira_em']) < time()) {
    header('Location: reset_password.php?token=' . urlencode($token) . '&erro=token_invalido');
    exit();
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("UPDATE user SET senha = :senha WHERE id = :id");
    $stmt->execute(['senha' => $senhaHash, 'id' => $registro['user_id']]);

    $stmt = $pdo->prepare("UPDATE password_reset SET usado_em = NOW() WHERE id = :id");
    $stmt->execute(['id' => $registro['id']]);

    $pdo->commit();

    header('Location: index.php?sucesso=senha_redefinida');
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: reset_password.php?token=' . urlencode($token) . '&erro=erro_servidor');
    exit();
}

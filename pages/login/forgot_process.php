<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit();
}

$email = strtolower(trim($_POST['email'] ?? ''));
if ($email === '') {
    header('Location: forgot_password.php?erro=campos');
    exit();
}

$stmt = $pdo->prepare("SELECT id, nome FROM user WHERE LOWER(email) = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if ($user) {
    // Rate limit persistente: bloqueia se ja existe pedido nos ultimos 60s
    $stmt = $pdo->prepare("SELECT 1 FROM password_reset WHERE user_id = :uid AND criado_em > (NOW() - INTERVAL 60 SECOND) LIMIT 1");
    $stmt->execute(['uid' => $user['id']]);
    if ($stmt->fetchColumn()) {
        header('Location: forgot_password.php?sucesso=email_enviado');
        exit();
    }

    // Invalidar tokens de reset anteriores nao usados
    $stmt = $pdo->prepare("UPDATE password_reset SET usado_em = NOW() WHERE user_id = :uid AND usado_em IS NULL");
    $stmt->execute(['uid' => $user['id']]);

    $token = bin2hex(random_bytes(32));
    $expiracao = date('Y-m-d H:i:s', time() + 3600); // 1 hora

    $stmt = $pdo->prepare("INSERT INTO password_reset (user_id, token, expira_em) VALUES (:uid, :token, :expira)");
    $stmt->execute([
        'uid' => $user['id'],
        'token' => $token,
        'expira' => $expiracao
    ]);

    enviarEmailRedefinicaoSenha($email, $user['nome'], $token);
}

header('Location: forgot_password.php?sucesso=email_enviado');
exit();

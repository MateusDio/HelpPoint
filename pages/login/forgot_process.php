<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
if ($email === '') {
    header('Location: forgot_password.php?erro=campos');
    exit();
}

$stmt = $pdo->prepare("SELECT id, nome FROM user WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if ($user) {
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

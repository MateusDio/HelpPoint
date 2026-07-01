<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$email = strtolower(trim($_POST['email'] ?? ''));

if ($email === '') {
    header('Location: confirm_email.php?erro=email_vazio');
    exit();
}

// Buscar usuário e token
$stmt = $pdo->prepare("
    SELECT u.id, u.nome, u.email, ev.token, ev.expira_em, ev.verificado_em, ev.email_temporario
    FROM user u
    INNER JOIN email_verification ev ON ev.user_id = u.id
    WHERE u.email = :email1 OR ev.email_temporario = :email2
    LIMIT 1
");
$stmt->execute(['email1' => $email, 'email2' => $email]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: index.php?erro=email_nao_encontrado');
    exit();
}

if ($usuario['verificado_em'] !== null) {
    header('Location: index.php?sucesso=email_verificado');
    exit();
}

// Gerar novo token
$novoToken = bin2hex(random_bytes(32));
$novaExpiracao = date('Y-m-d H:i:s', time() + (24 * 3600));

$stmt = $pdo->prepare("UPDATE email_verification SET token = :token, expira_em = :expira WHERE user_id = :uid");
$stmt->execute([
    'token' => $novoToken,
    'expira' => $novaExpiracao,
    'uid' => $usuario['id']
]);

// Enviar email
$emailDestino = $usuario['email_temporario'] ?: $usuario['email'];
$enviado = enviarEmailVerificacao($emailDestino, $usuario['nome'], $novoToken);

if ($enviado) {
    header('Location: confirm_email.php?email=' . urlencode($emailDestino) . '&reenvio=enviado');
} else {
    header('Location: confirm_email.php?email=' . urlencode($emailDestino) . '&reenvio=erro');
}
exit();

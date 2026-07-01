<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$email = strtolower(trim($_POST['email'] ?? ''));

// Sempre redireciona para a mesma pagina com mesma mensagem generica —
// evita enumeracao de emails cadastrados.
$respostaGenerica = 'Location: confirm_email.php?email=' . urlencode($email) . '&reenvio=enviado';

if ($email === '') {
    header('Location: confirm_email.php?erro=email_vazio');
    exit();
}

// Buscar usuário e token (case-insensitive)
$stmt = $pdo->prepare("
    SELECT u.id, u.nome, u.email, ev.token, ev.expira_em, ev.verificado_em, ev.email_temporario, ev.criado_em
    FROM user u
    INNER JOIN email_verification ev ON ev.user_id = u.id
    WHERE LOWER(u.email) = :email1 OR LOWER(ev.email_temporario) = :email2
    LIMIT 1
");
$stmt->execute(['email1' => $email, 'email2' => $email]);
$usuario = $stmt->fetch();

if (!$usuario || $usuario['verificado_em'] !== null) {
    header($respostaGenerica);
    exit();
}

// Rate limit persistente: bloqueia reenvios em intervalo < 60s
if ($usuario['criado_em'] && (time() - strtotime($usuario['criado_em'])) < 60) {
    header($respostaGenerica);
    exit();
}

// Gerar novo token
$novoToken = bin2hex(random_bytes(32));
$novaExpiracao = date('Y-m-d H:i:s', time() + (24 * 3600));

$stmt = $pdo->prepare("UPDATE email_verification SET token = :token, expira_em = :expira, criado_em = NOW() WHERE user_id = :uid");
$stmt->execute([
    'token' => $novoToken,
    'expira' => $novaExpiracao,
    'uid' => $usuario['id']
]);

$emailDestino = $usuario['email_temporario'] ?: $usuario['email'];
enviarEmailVerificacao($emailDestino, $usuario['nome'], $novoToken);

header($respostaGenerica);
exit();

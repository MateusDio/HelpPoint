<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Validar campos
if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
    header('Location: register.php?erro=campos');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php?erro=email_invalido');
    exit();
}

// Validar senhas
if ($senha !== $confirmar_senha) {
    header('Location: register.php?erro=senhas');
    exit();
}

// Verificar se email ja existe
$stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    header('Location: register.php?erro=email_existe');
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Inserir usuario com email real. A confirmacao fica registrada em email_verification.
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO user (nome, email, senha, role) VALUES (:nome, :email, :senha, 'user')");
    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'senha' => $senhaHash
    ]);
    
    $userId = $pdo->lastInsertId();
    
    // Gerar token de verificacao (32 caracteres aleatorios)
    $token = bin2hex(random_bytes(32));
    $expiracao = date('Y-m-d H:i:s', time() + (24 * 3600)); // 24 horas
    
    // Registrar token no banco
    $stmt = $pdo->prepare("
        INSERT INTO email_verification (user_id, token, email_temporario, expira_em)
        VALUES (:uid, :token, :email, :expira)
    ");
    $stmt->execute([
        'uid' => $userId,
        'token' => $token,
        'email' => $email,
        'expira' => $expiracao
    ]);
    
    $pdo->commit();
    
    // Enviar email de verificacao
    $enviado = enviarEmailVerificacao($email, $nome, $token);
    
    // Redirecionar para página de confirmação (independente se email foi enviado ou não)
    header('Location: confirm_email.php?email=' . urlencode($email));
    exit();
    
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: register.php?erro=erro_servidor');
    exit();
}

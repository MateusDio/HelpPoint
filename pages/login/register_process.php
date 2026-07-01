<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Validar campos
if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
    header('Location: register.php?erro=campos');
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
    
    // Inserir usuario com email vazio (pendente verificacao)
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO user (nome, email, senha, role) VALUES (:nome, '', :senha, 'user')");
    $stmt->execute([
        'nome' => $nome,
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
    
    if ($enviado) {
        header('Location: register.php?sucesso=verificacao_enviada');
    } else {
        // Email nao foi enviado, mas o registro foi criado
        // Você pode tentar reenviar depois
        header('Location: register.php?aviso=email_nao_enviado');
    }
    exit();
    
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: register.php?erro=erro_servidor');
    exit();
}

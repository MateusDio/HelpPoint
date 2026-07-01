<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$email = strtolower(trim($_POST['email'] ?? ''));
$senha = $_POST['senha'] ?? '';

// Validar campos
if (empty($email) || empty($senha)) {
    header('Location: index.php?erro=campos');
    exit();
}

// Buscar usuario no banco
$stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

// Verificar senha
if (!$user || !password_verify($senha, $user['senha'])) {
    header('Location: index.php?erro=credenciais');
    exit();
}

// Verificar se o cadastro por email foi confirmado.
// Usuarios criados pelo admin nao possuem registro em email_verification e podem entrar normalmente.
$stmt = $pdo->prepare("
    SELECT verificado_em
    FROM email_verification
    WHERE user_id = :uid
    LIMIT 1
");
$stmt->execute(['uid' => $user['id']]);
$verificacao = $stmt->fetch();

if ($verificacao && $verificacao['verificado_em'] === null) {
    // Nao revelar que a senha estava correta. Usar mesmo erro generico do login;
    // o usuario legitimo tem o link "Reenviar verificacao" na propria tela de login.
    header('Location: index.php?erro=credenciais');
    exit();
}

// Criar sessao
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_nome'] = $user['nome'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];

// Redirecionar por role
if ($user['role'] === 'admin') {
    header('Location: ' . BASE_URL . '/pages/admin/dashboard.php');
} else {
    header('Location: ' . BASE_URL . '/pages/dashboard/index.php');
}
exit();

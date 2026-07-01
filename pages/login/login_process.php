<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
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

// Verificar se email foi confirmado
if (empty($user['email'])) {
    header('Location: index.php?erro=email_nao_verificado');
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

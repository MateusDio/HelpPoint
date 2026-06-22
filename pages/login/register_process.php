<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';

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

// Inserir usuario com role 'user' por padrao
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO user (nome, email, senha, role) VALUES (:nome, :email, :senha, 'user')");
$stmt->execute([
    'nome' => $nome,
    'email' => $email,
    'senha' => $senhaHash
]);

header('Location: index.php?sucesso=1');
exit();

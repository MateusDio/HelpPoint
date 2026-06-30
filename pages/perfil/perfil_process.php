<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$userId = (int)$_SESSION['user_id'];
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$funcao = trim($_POST['funcao'] ?? '');
$documento = trim($_POST['documento'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($nome === '' || $email === '') {
    header('Location: index.php?erro=campos');
    exit();
}

$stmt = $pdo->prepare("SELECT id FROM user WHERE email = :e AND id != :id LIMIT 1");
$stmt->execute(['e' => $email, 'id' => $userId]);
if ($stmt->fetch()) {
    header('Location: index.php?erro=email_existe');
    exit();
}

if (!empty($senha)) {
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE user SET nome=:n, email=:e, funcao=:f, documento=:d, senha=:s WHERE id=:id");
    $stmt->execute(['n' => $nome, 'e' => $email, 'f' => $funcao, 'd' => $documento, 's' => $hash, 'id' => $userId]);
} else {
    $stmt = $pdo->prepare("UPDATE user SET nome=:n, email=:e, funcao=:f, documento=:d WHERE id=:id");
    $stmt->execute(['n' => $nome, 'e' => $email, 'f' => $funcao, 'd' => $documento, 'id' => $userId]);
}

$_SESSION['user_nome'] = $nome;

header('Location: index.php?sucesso=1');
exit();

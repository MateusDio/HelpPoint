<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// ---- CRIAR usuario ----
if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $funcao = trim($_POST['funcao'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $role  = $_POST['role'] ?? 'user';

    if (empty($nome) || empty($email) || empty($senha)) {
        header('Location: usuarios.php?erro=campos');
        exit();
    }

    // Verificar email duplicado
    $stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        header('Location: usuarios.php?erro=email_existe');
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO user (nome, email, senha, funcao, documento, role) VALUES (:nome, :email, :senha, :funcao, :documento, :role)");
    $stmt->execute([
        'nome'      => $nome,
        'email'     => $email,
        'senha'     => $senhaHash,
        'funcao'    => $funcao,
        'documento' => $documento,
        'role'      => $role
    ]);

    header('Location: usuarios.php?sucesso=criado');
    exit();
}

// ---- EDITAR usuario ----
if ($acao === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int)($_POST['id'] ?? 0);
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $funcao = trim($_POST['funcao'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $role  = $_POST['role'] ?? 'user';

    if (empty($nome) || empty($email) || $id === 0) {
        header('Location: usuarios.php?erro=campos');
        exit();
    }

    // Verificar email duplicado (excluindo o proprio usuario)
    $stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email AND id != :id LIMIT 1");
    $stmt->execute(['email' => $email, 'id' => $id]);
    if ($stmt->fetch()) {
        header('Location: usuarios.php?erro=email_existe');
        exit();
    }

    // Se informou senha nova, atualiza; senao, mantem a atual
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE user SET nome = :nome, email = :email, senha = :senha, funcao = :funcao, documento = :documento, role = :role WHERE id = :id");
        $stmt->execute([
            'nome'      => $nome,
            'email'     => $email,
            'senha'     => $senhaHash,
            'funcao'    => $funcao,
            'documento' => $documento,
            'role'      => $role,
            'id'        => $id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE user SET nome = :nome, email = :email, funcao = :funcao, documento = :documento, role = :role WHERE id = :id");
        $stmt->execute([
            'nome'      => $nome,
            'email'     => $email,
            'funcao'    => $funcao,
            'documento' => $documento,
            'role'      => $role,
            'id'        => $id
        ]);
    }

    header('Location: usuarios.php?sucesso=editado');
    exit();
}

// ---- EXCLUIR usuario ----
if ($acao === 'excluir' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Nao permitir excluir a si mesmo
    if ($id === (int)$_SESSION['user_id']) {
        header('Location: usuarios.php?erro=excluir_proprio');
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header('Location: usuarios.php?sucesso=excluido');
    exit();
}

header('Location: usuarios.php');
exit();

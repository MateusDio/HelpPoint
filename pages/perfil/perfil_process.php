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
$bio = trim($_POST['bio'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($nome === '' || $email === '') {
    header('Location: index.php?erro=campos');
    exit();
}

$stmt = $pdo->prepare("SELECT id, avatar FROM user WHERE email = :e AND id != :id LIMIT 1");
$stmt->execute(['e' => $email, 'id' => $userId]);
if ($stmt->fetch()) {
    header('Location: index.php?erro=email_existe');
    exit();
}

$avatarName = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['avatar']['tmp_name'];
    $size = (int)$_FILES['avatar']['size'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    if ($size > 2 * 1024 * 1024 || !in_array($mime, $allowedTypes, true)) {
        header('Location: index.php?erro=avatar');
        exit();
    }

    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $avatarName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
    $uploadDir = __DIR__ . '/../../uploads/avatars';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $targetPath = $uploadDir . '/' . $avatarName;
    if (!move_uploaded_file($tmpName, $targetPath)) {
        header('Location: index.php?erro=avatar');
        exit();
    }
}

if (!empty($senha)) {
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    if ($avatarName !== null) {
        $stmt = $pdo->prepare("UPDATE user SET nome=:n, email=:e, funcao=:f, documento=:d, bio=:b, avatar=:a, senha=:s WHERE id=:id");
        $stmt->execute(['n' => $nome, 'e' => $email, 'f' => $funcao, 'd' => $documento, 'b' => $bio, 'a' => $avatarName, 's' => $hash, 'id' => $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE user SET nome=:n, email=:e, funcao=:f, documento=:d, bio=:b, senha=:s WHERE id=:id");
        $stmt->execute(['n' => $nome, 'e' => $email, 'f' => $funcao, 'd' => $documento, 'b' => $bio, 's' => $hash, 'id' => $userId]);
    }
} else {
    if ($avatarName !== null) {
        $stmt = $pdo->prepare("UPDATE user SET nome=:n, email=:e, funcao=:f, documento=:d, bio=:b, avatar=:a WHERE id=:id");
        $stmt->execute(['n' => $nome, 'e' => $email, 'f' => $funcao, 'd' => $documento, 'b' => $bio, 'a' => $avatarName, 'id' => $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE user SET nome=:n, email=:e, funcao=:f, documento=:d, bio=:b WHERE id=:id");
        $stmt->execute(['n' => $nome, 'e' => $email, 'f' => $funcao, 'd' => $documento, 'b' => $bio, 'id' => $userId]);
    }
}

$_SESSION['user_nome'] = $nome;

header('Location: index.php?sucesso=1');
exit();

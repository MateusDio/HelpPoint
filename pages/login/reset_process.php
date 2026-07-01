<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reset_password.php');
    exit();
}

$token = $_POST['token'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

if ($token === '' || $senha === '' || $confirmar_senha === '') {
    header('Location: reset_password.php?token=' . urlencode($token) . '&erro=campos');
    exit();
}

if ($senha !== $confirmar_senha) {
    header('Location: reset_password.php?token=' . urlencode($token) . '&erro=senhas');
    exit();
}

$stmt = $pdo->prepare("SELECT id, user_id, expira_em, usado_em FROM password_reset WHERE token = :token LIMIT 1");
$stmt->execute(['token' => $token]);
$registro = $stmt->fetch();

if (!$registro || $registro['usado_em'] !== null || strtotime($registro['expira_em']) < time()) {
    header('Location: reset_password.php?token=' . urlencode($token) . '&erro=token_invalido');
    exit();
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("UPDATE user SET senha = :senha WHERE id = :id");
    $stmt->execute(['senha' => $senhaHash, 'id' => $registro['user_id']]);

    // Invalidar TODOS tokens de reset pendentes do usuario
    $stmt = $pdo->prepare("UPDATE password_reset SET usado_em = NOW() WHERE user_id = :uid AND usado_em IS NULL");
    $stmt->execute(['uid' => $registro['user_id']]);

    $pdo->commit();

    // Se houver sessao ativa (do proprio usuario), destruir para forcar novo login
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    header('Location: index.php?sucesso=senha_redefinida');
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: reset_password.php?token=' . urlencode($token) . '&erro=erro_servidor');
    exit();
}

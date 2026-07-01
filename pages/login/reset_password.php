<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';
redirectIfLogged();

$token = $_GET['token'] ?? '';
$valid = false;
$erro = '';

if ($token) {
    $stmt = $pdo->prepare("SELECT pr.id, pr.user_id, pr.expira_em, pr.usado_em, u.nome FROM password_reset pr INNER JOIN user u ON u.id = pr.user_id WHERE pr.token = :token LIMIT 1");
    $stmt->execute(['token' => $token]);
    $registro = $stmt->fetch();

    if (!$registro || $registro['usado_em'] !== null || strtotime($registro['expira_em']) < time()) {
        $erro = 'token_invalido';
    } else {
        $valid = true;
    }
}

$pageTitle = 'Redefinir Senha';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - HelpPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../../assets/css/style.css') ?>">
</head>
<body>
<div class="auth-split modo-registro" id="authSplit">
    <aside class="auth-form-side">
        <div class="auth-form-stage">
            <div class="auth-form-box" data-form="reset">
                <a href="<?= BASE_URL ?>/" class="auth-brand">
                    <i class="bi bi-headset"></i> HelpPoint
                </a>
                <h2 class="auth-title">Redefinir Senha</h2>
                <p class="auth-subtitle">Defina uma nova senha para sua conta.</p>
                <?php if ($erro === 'token_invalido'): ?>
                    <div class="alert alert-danger py-2">Token inválido, expirado ou já utilizado.</div>
                    <p class="text-center mt-3"><a href="forgot_password.php">Solicitar novo link</a></p>
                <?php elseif ($erro === 'campos'): ?>
                    <div class="alert alert-danger py-2">Preencha todos os campos.</div>
                <?php elseif ($erro === 'senhas'): ?>
                    <div class="alert alert-danger py-2">As senhas não coincidem.</div>
                <?php elseif ($erro === 'erro_servidor'): ?>
                    <div class="alert alert-danger py-2">Erro interno. Tente novamente mais tarde.</div>
                <?php elseif (!$token): ?>
                    <div class="alert alert-danger py-2">Token não fornecido.</div>
                <?php endif; ?>
                <?php if ($valid): ?>
                    <form action="reset_process.php" method="POST" class="auth-form">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="mb-3">
                            <label class="form-label">Nova senha</label>
                            <div class="auth-input-group">
                                <i class="bi bi-lock"></i>
                                <input type="password" class="form-control" name="senha" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar senha</label>
                            <div class="auth-input-group">
                                <i class="bi bi-shield-lock"></i>
                                <input type="password" class="form-control" name="confirmar_senha" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 auth-btn">
                            <i class="bi bi-key"></i> Redefinir senha
                        </button>
                    </form>
                <?php endif; ?>
                <p class="auth-footer-link">
                    Lembrou? <a href="<?= BASE_URL ?>/pages/login/index.php">Fazer login</a>
                </p>
            </div>
        </div>
    </aside>
</div>
</body>
</html>

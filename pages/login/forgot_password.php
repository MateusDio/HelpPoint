<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfLogged();

$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';
$pageTitle = 'Recuperar Senha';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci a senha - HelpPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../../assets/css/style.css') ?>">
</head>
<body>
<div class="auth-split modo-registro" id="authSplit">
    <aside class="auth-form-side">
        <div class="auth-form-stage">
            <div class="auth-form-box" data-form="forgot">
                <a href="<?= BASE_URL ?>/" class="auth-brand">
                    <i class="bi bi-headset"></i> HelpPoint
                </a>
                <h2 class="auth-title">Recuperar Senha</h2>
                <p class="auth-subtitle">Digite seu email para receber um link de redefinição.</p>
                <?php if ($erro === 'campos'): ?>
                    <div class="alert alert-danger py-2">Informe um email válido.</div>
                <?php elseif ($erro === 'token_invalido'): ?>
                    <div class="alert alert-danger py-2">Token inválido ou expirado.</div>
                <?php elseif ($sucesso === 'email_enviado'): ?>
                    <div class="alert alert-success py-2">Se este email estiver cadastrado, você receberá instruções em instantes.</div>
                <?php endif; ?>
                <form action="forgot_process.php" method="POST" class="auth-form">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="auth-input-group">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 auth-btn">
                        <i class="bi bi-envelope"></i> Enviar link
                    </button>
                </form>
                <p class="auth-footer-link">
                    Lembrou a senha? <a href="<?= BASE_URL ?>/pages/login/index.php">Fazer login</a>
                </p>
            </div>
        </div>
    </aside>
</div>
</body>
</html>

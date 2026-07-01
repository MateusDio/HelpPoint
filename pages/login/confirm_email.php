<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';

// Se não veio da página de registro, redireciona
if (!isset($_GET['email'])) {
    header('Location: index.php');
    exit();
}

$email = $_GET['email'] ?? '';
$avisoReenvio = $_GET['reenvio'] ?? '';

$pageTitle = 'Confirme seu Email';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirme seu Email - HelpPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../../assets/css/style.css') ?>">
</head>
<body>
<div class="auth-split" id="authSplit">
    <aside class="auth-form-side">
        <div class="auth-form-stage">
            <div class="auth-form-box" data-form="confirm">
                <a href="<?= BASE_URL ?>/" class="auth-brand">
                    <i class="bi bi-headset"></i> HelpPoint
                </a>

                <h2 class="auth-title">
                    <i class="bi bi-envelope-check text-success"></i> Email Pendente
                </h2>
                <p class="auth-subtitle">Confirme seu email para começar.</p>

                <div class="alert alert-info">
                    <p><strong>Enviamos um link de confirmação para:</strong></p>
                    <p class="fw-bold text-break"><?= htmlspecialchars($email) ?></p>
                </div>

                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-info-circle"></i> Próximos passos:</h6>
                        <ol class="small mb-0">
                            <li>Verifique seu email (inclusive spam)</li>
                            <li>Clique no link de confirmação</li>
                            <li>Seu email será ativado e você poderá fazer login</li>
                        </ol>
                    </div>
                </div>

                <?php if ($avisoReenvio === 'enviado'): ?>
                    <div class="alert alert-success py-2">
                        <i class="bi bi-check-circle"></i> Email reenviado com sucesso! Verifique sua caixa de entrada.
                    </div>
                <?php elseif ($avisoReenvio === 'erro'): ?>
                    <div class="alert alert-warning py-2">
                        <i class="bi bi-exclamation-triangle"></i> Erro ao reenviar. Tente novamente em alguns minutos.
                    </div>
                <?php endif; ?>

                <form action="resend_verification.php" method="POST" class="mb-3">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-arrow-repeat"></i> Reenviar Email
                    </button>
                </form>

                <p class="text-center text-muted small mb-3">
                    Link expirou? <a href="resend_verification.php">Solicitar novo</a>
                </p>

                <hr>

                <p class="text-center">
                    <a href="<?= BASE_URL ?>/pages/login/index.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-box-arrow-in-right"></i> Voltar para Login
                    </a>
                </p>

                <p class="text-center text-muted small mt-4">
                    Não recebeu? Verifique a pasta de spam ou <a href="forgot_password.php">redefinir senha</a>.
                </p>
            </div>
        </div>
    </aside>

    <section class="auth-visual-side">
        <div class="auth-visual-main">
            <div class="auth-gradient">
                <div class="auth-blob blob-1"></div>
                <div class="auth-blob blob-2"></div>
                <div class="auth-blob blob-3"></div>
            </div>
        </div>
    </section>
</div>
</body>
</html>

<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfLogged();

$erro = isset($_GET['erro']) ? $_GET['erro'] : '';
$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HelpPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/HelpPoint/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2><i class="bi bi-headset"></i> HelpPoint</h2>

            <?php if ($erro === 'credenciais'): ?>
                <div class="alert alert-danger">Email ou senha incorretos.</div>
            <?php elseif ($erro === 'campos'): ?>
                <div class="alert alert-danger">Preencha todos os campos.</div>
            <?php endif; ?>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert alert-success">Cadastro feito! Faça login.</div>
            <?php endif; ?>

            <form action="login_process.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>

            <p class="text-center mt-3">
                Não tem conta? <a href="register.php">Cadastre-se</a>
            </p>
        </div>
    </div>
</body>
</html>

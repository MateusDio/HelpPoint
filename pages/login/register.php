<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfLogged();

$erro = isset($_GET['erro']) ? $_GET['erro'] : '';
$pageTitle = 'Cadastro';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - HelpPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/HelpPoint/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2><i class="bi bi-person-plus"></i> Cadastro</h2>

            <?php if ($erro === 'campos'): ?>
                <div class="alert alert-danger">Preencha todos os campos.</div>
            <?php elseif ($erro === 'email_existe'): ?>
                <div class="alert alert-danger">Este email já está cadastrado.</div>
            <?php elseif ($erro === 'senhas'): ?>
                <div class="alert alert-danger">As senhas não coincidem.</div>
            <?php endif; ?>

            <form action="register_process.php" method="POST">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <div class="mb-3">
                    <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
            </form>

            <p class="text-center mt-3">
                Já tem conta? <a href="index.php">Faça login</a>
            </p>
        </div>
    </div>
</body>
</html>

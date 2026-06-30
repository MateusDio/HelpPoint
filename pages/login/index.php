<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfLogged();

$erro = $_GET['erro'] ?? '';
$modo = $_GET['modo'] ?? 'login';
$pageTitle = 'Entrar';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - HelpPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../../assets/css/style.css') ?>">
</head>
<body>

<div class="auth-split <?= $modo === 'registro' ? 'modo-registro' : '' ?>" id="authSplit">

    <!-- Painel do FORM (move da esquerda <-> direita) -->
    <aside class="auth-form-side">
        <div class="auth-form-stage">

            <!-- FORM: LOGIN -->
            <div class="auth-form-box" data-form="login">
                <a href="<?= BASE_URL ?>/" class="auth-brand">
                    <i class="bi bi-headset"></i> HelpPoint
                </a>

                <h2 class="auth-title">Bem-vindo de volta</h2>
                <p class="auth-subtitle">Entre na sua conta para acompanhar chamados.</p>

                <?php if ($erro === 'credenciais'): ?>
                    <div class="alert alert-danger py-2">Email ou senha incorretos.</div>
                <?php elseif ($erro === 'campos' && $modo !== 'registro'): ?>
                    <div class="alert alert-danger py-2">Preencha todos os campos.</div>
                <?php endif; ?>

                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="alert alert-success py-2">Cadastro feito! Faca login.</div>
                <?php endif; ?>

                <form action="login_process.php" method="POST" class="auth-form">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="auth-input-group">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" name="email" placeholder="voce@email.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <div class="auth-input-group">
                            <i class="bi bi-lock"></i>
                            <input type="password" class="form-control" name="senha" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 auth-btn">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>
                </form>

                <p class="auth-footer-link">
                    Nao tem conta? <a href="#" onclick="toggleAuth('registro'); return false;">Cadastre-se</a>
                </p>
            </div>

            <!-- FORM: REGISTRO -->
            <div class="auth-form-box" data-form="registro">
                <a href="<?= BASE_URL ?>/" class="auth-brand">
                    <i class="bi bi-headset"></i> HelpPoint
                </a>

                <h2 class="auth-title">Crie sua conta</h2>
                <p class="auth-subtitle">Comece a abrir chamados em segundos. E gratis.</p>

                <?php if ($erro === 'campos' && $modo === 'registro'): ?>
                    <div class="alert alert-danger py-2">Preencha todos os campos.</div>
                <?php elseif ($erro === 'email_existe'): ?>
                    <div class="alert alert-danger py-2">Este email ja esta cadastrado.</div>
                <?php elseif ($erro === 'senhas'): ?>
                    <div class="alert alert-danger py-2">As senhas nao coincidem.</div>
                <?php endif; ?>

                <form action="register_process.php" method="POST" class="auth-form">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <div class="auth-input-group">
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="auth-input-group">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <div class="auth-input-group">
                            <i class="bi bi-lock"></i>
                            <input type="password" class="form-control" name="senha" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar Senha</label>
                        <div class="auth-input-group">
                            <i class="bi bi-shield-lock"></i>
                            <input type="password" class="form-control" name="confirmar_senha" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 auth-btn">
                        <i class="bi bi-person-plus"></i> Cadastrar
                    </button>
                </form>

                <p class="auth-footer-link">
                    Ja tem conta? <a href="#" onclick="toggleAuth('login'); return false;">Faca login</a>
                </p>
            </div>

        </div>
    </aside>

    <!-- Painel VISUAL (move da direita <-> esquerda) -->
    <section class="auth-visual-side">
        <div class="auth-visual-main">
            <div class="auth-gradient">
                <div class="auth-blob blob-1"></div>
                <div class="auth-blob blob-2"></div>
                <div class="auth-blob blob-3"></div>
            </div>

        </div>

        <div class="auth-marquee">
            <div class="auth-marquee-track">
                <?php
                $feedbacks = [
                    ['nome'=>'Ana M.',     'cargo'=>'Coordenadora', 'texto'=>'Reduziu nosso tempo de resposta em mais da metade.'],
                    ['nome'=>'Carlos R.',  'cargo'=>'Analista TI',   'texto'=>'Finalmente sei o status de cada chamado em tempo real.'],
                    ['nome'=>'Beatriz F.', 'cargo'=>'Diretora',       'texto'=>'A equipe inteira adotou em uma semana.'],
                    ['nome'=>'Diego S.',   'cargo'=>'Tecnico',        'texto'=>'O painel admin e muito intuitivo, amei os cards.'],
                    ['nome'=>'Eduarda P.', 'cargo'=>'Gerente',        'texto'=>'Visibilidade total dos equipamentos e historico.'],
                    ['nome'=>'Felipe T.',  'cargo'=>'Suporte N1',     'texto'=>'Acabou aquele caos de email pedindo manutencao.'],
                    ['nome'=>'Gabriela L.','cargo'=>'Administrativo', 'texto'=>'Interface limpa, sem firula. Funciona.'],
                    ['nome'=>'Hugo N.',    'cargo'=>'CEO',            'texto'=>'Melhor ferramenta de chamados que ja usamos.'],
                ];
                $loop = array_merge($feedbacks, $feedbacks);
                foreach ($loop as $f):
                    $i = strtoupper(mb_substr($f['nome'], 0, 1));
                ?>
                    <div class="auth-feedback">
                        <div class="auth-feedback-avatar"><?= $i ?></div>
                        <div class="auth-feedback-body">
                            <div class="auth-feedback-text">"<?= htmlspecialchars($f['texto']) ?>"</div>
                            <div class="auth-feedback-author"><?= htmlspecialchars($f['nome']) ?> · <span><?= htmlspecialchars($f['cargo']) ?></span></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<script>
function toggleAuth(modo) {
    const split = document.getElementById('authSplit');
    if (modo === 'registro') split.classList.add('modo-registro');
    else split.classList.remove('modo-registro');
}
</script>

</body>
</html>

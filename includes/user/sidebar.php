<?php
require_once __DIR__ . '/../../config/database.php';
$__uid = (int)$_SESSION['user_id'];
$__stmt = $pdo->prepare("SELECT nome, email FROM user WHERE id = :id LIMIT 1");
$__stmt->execute(['id' => $__uid]);
$__me = $__stmt->fetch() ?: ['nome' => $_SESSION['user_nome'] ?? '', 'email' => ''];
$__inicialCli = strtoupper(mb_substr($__me['nome'] ?? 'C', 0, 1));
?>
<header class="topbar topbar-cliente">
    <!-- Linha 1: logo + perfil -->
    <div class="topbar-row topbar-row-top">
        <div class="topbar-inner">
            <a href="<?= BASE_URL ?>/pages/dashboard/" class="topbar-logo">
                <i class="bi bi-headset"></i>
                <span>HelpPoint</span>
                <small class="topbar-tag-cli">Cliente</small>
            </a>

            <div class="topbar-actions">
                <button type="button" class="topbar-profile" data-bs-toggle="modal" data-bs-target="#modalConfigCliente">
                    <div class="topbar-avatar"><?= htmlspecialchars($__inicialCli) ?></div>
                    <div class="topbar-profile-info">
                        <span class="topbar-profile-name"><?= htmlspecialchars($__me['nome']) ?></span>
                        <span class="topbar-profile-email"><?= htmlspecialchars($__me['email']) ?></span>
                    </div>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Linha 2: menu -->
    <div class="topbar-row topbar-row-nav">
        <div class="topbar-inner">
            <nav class="topbar-nav">
                <a href="<?= BASE_URL ?>/pages/dashboard/" class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-grid"></i> <span>Inicio</span>
                </a>
                <a href="<?= BASE_URL ?>/pages/chamados/" class="<?= ($currentPage ?? '') === 'chamados' ? 'active' : '' ?>">
                    <i class="bi bi-ticket"></i> <span>Chamados</span>
                </a>
            </nav>
        </div>
    </div>
</header>

<main class="topbar-content topbar-content-cliente">
    <div class="topbar-container">

<!-- Modal Configuracoes Rapidas -->
<div class="modal fade" id="modalConfigCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm-config">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-gear"></i> Configuracoes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="config-user-card">
                    <div class="config-avatar"><?= htmlspecialchars($__inicialCli) ?></div>
                    <div>
                        <div class="config-name"><?= htmlspecialchars($__me['nome']) ?></div>
                        <div class="config-email"><?= htmlspecialchars($__me['email']) ?></div>
                    </div>
                </div>
                <form action="<?= BASE_URL ?>/pages/perfil/perfil_process.php" method="POST" class="mt-3">
                    <input type="hidden" name="funcao" value="">
                    <input type="hidden" name="documento" value="">
                    <div class="mb-2">
                        <label class="form-label small">Nome</label>
                        <input type="text" class="form-control form-control-sm" name="nome" value="<?= htmlspecialchars($__me['nome']) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Email</label>
                        <input type="email" class="form-control form-control-sm" name="email" value="<?= htmlspecialchars($__me['email']) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Nova Senha</label>
                        <input type="password" class="form-control form-control-sm" name="senha" placeholder="Deixe vazio para manter">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-check2"></i> Salvar</button>
                </form>
                <hr>
                <a href="<?= BASE_URL ?>/pages/perfil/" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                    <i class="bi bi-person-circle"></i> Perfil completo
                </a>
                <a href="<?= BASE_URL ?>/pages/login/logout.php" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </div>
</div>

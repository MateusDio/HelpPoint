<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="admin-wrapper">

    <!-- Sidebar -->
    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-headset"></i> HelpPoint</h4>
            <small>Painel Admin</small>
        </div>

        <ul class="sidebar-menu">
            <li class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/admin/dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="<?= ($currentPage ?? '') === 'chamados' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/admin/chamados.php">
                    <i class="bi bi-ticket"></i> Chamados
                </a>
            </li>

            <li class="<?= ($currentPage ?? '') === 'categorias' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/admin/categorias.php">
                    <i class="bi bi-tags"></i> Categorias
                </a>
            </li>

            <li class="<?= ($currentPage ?? '') === 'equipamentos' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/admin/equipamentos.php">
                    <i class="bi bi-pc-display"></i> Equipamentos
                </a>
            </li>

            <li class="<?= ($currentPage ?? '') === 'tipos' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/admin/tipos.php">
                    <i class="bi bi-list-check"></i> Tipos
                </a>
            </li>

            <li class="<?= ($currentPage ?? '') === 'usuarios' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/admin/usuarios.php">
                    <i class="bi bi-people"></i> Usuários
                </a>
            </li>

            <li class="<?= ($currentPage ?? '') === 'relatorios' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/admin/relatorios.php">
                    <i class="bi bi-bar-chart-line"></i> Relatórios
                </a>
            </li>

            <li class="<?= ($currentPage ?? '') === 'configuracoes' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/admin/configuracoes.php">
                    <i class="bi bi-gear"></i> Configurações
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <i class="bi bi-person-circle"></i>

                <span>
                    <?= htmlspecialchars($_SESSION['user_nome'] ?? 'Usuário'); ?>
                </span>
            </div>

            <a href="/HelpPoint/pages/login/logout.php" class="sidebar-logout">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
    </nav>

    <!-- Conteúdo -->
    <main class="admin-content">
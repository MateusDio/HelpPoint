<div class="admin-wrapper client-wrapper">
    <!-- Sidebar -->
    <nav class="admin-sidebar client-sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-headset"></i> HelpPoint</h4>
            <small>Painel Cliente</small>
        </div>
        <ul class="sidebar-menu">
            <li class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/dashboard/">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="<?php echo ($currentPage ?? '') === 'chamados' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/chamados/">
                    <i class="bi bi-ticket"></i> Meus Chamados
                </a>
            </li>
            <li class="<?php echo ($currentPage ?? '') === 'perfil' ? 'active' : ''; ?>">
                <a href="/HelpPoint/pages/perfil/">
                    <i class="bi bi-person-circle"></i> Meu Perfil
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <i class="bi bi-person-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['user_nome']); ?></span>
            </div>
            <a href="/HelpPoint/pages/login/logout.php" class="sidebar-logout">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
    </nav>

    <!-- Conteudo principal -->
    <main class="admin-content">

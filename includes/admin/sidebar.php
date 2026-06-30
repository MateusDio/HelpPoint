<?php
$__nomeAdmin = $_SESSION['user_nome'] ?? 'Admin';
$__inicial = strtoupper(mb_substr($__nomeAdmin, 0, 1));
?>
<header class="topbar">
    <!-- Linha 1: logo + ações -->
    <div class="topbar-row topbar-row-top">
        <div class="topbar-inner">
            <a href="<?= BASE_URL ?>/pages/admin/dashboard.php" class="topbar-logo">
                <i class="bi bi-headset"></i>
                <span>HelpPoint</span>
                <small>Admin</small>
            </a>

            <div class="topbar-actions">
                <div class="dropdown">
                    <button type="button" class="topbar-profile" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="topbar-avatar"><?= htmlspecialchars($__inicial) ?></div>
                        <span class="topbar-profile-name"><?= htmlspecialchars($__nomeAdmin) ?></span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="dropdown-header">
                            <strong><?= htmlspecialchars($__nomeAdmin) ?></strong><br>
                            <small class="text-muted">Administrador</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/pages/login/logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Linha 2: menu -->
    <div class="topbar-row topbar-row-nav">
        <div class="topbar-inner">
            <nav class="topbar-nav">
                <?php
                $items = [
                    ['key'=>'dashboard',    'url'=>BASE_URL.'/pages/admin/dashboard.php',     'icon'=>'bi-grid',          'label'=>'Dashboard'],
                    ['key'=>'chamados',     'url'=>BASE_URL.'/pages/admin/chamados.php',      'icon'=>'bi-ticket',        'label'=>'Chamados'],
                    ['key'=>'categorias',   'url'=>BASE_URL.'/pages/admin/categorias.php',    'icon'=>'bi-tags',          'label'=>'Categorias'],
                    ['key'=>'equipamentos', 'url'=>BASE_URL.'/pages/admin/equipamentos.php',  'icon'=>'bi-pc-display',    'label'=>'Equipamentos'],
                    ['key'=>'tipos',        'url'=>BASE_URL.'/pages/admin/tipos.php',         'icon'=>'bi-list-check',    'label'=>'Tipos'],
                    ['key'=>'usuarios',     'url'=>BASE_URL.'/pages/admin/usuarios.php',      'icon'=>'bi-people',        'label'=>'Usuarios'],
                ];
                foreach ($items as $it):
                    $active = ($currentPage ?? '') === $it['key'] ? 'active' : '';
                ?>
                <a href="<?= $it['url'] ?>" class="<?= $active ?>">
                    <i class="bi <?= $it['icon'] ?>"></i>
                    <span><?= $it['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </div>
</header>

<main class="topbar-content">
    <div class="topbar-container">

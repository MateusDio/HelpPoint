<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotAdmin();
$pageTitle = 'Configuracoes';
$currentPage = 'configuracoes';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-gear"></i> Configuracoes</h1>
</div>

<div class="placeholder-page">
    <i class="bi bi-gear"></i>
    <h3>Pagina em construcao</h3>
    <p>As configuracoes do sistema serao desenvolvidas aqui.</p>
</div>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

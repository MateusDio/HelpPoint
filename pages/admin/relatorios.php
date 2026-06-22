<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotAdmin();
$pageTitle = 'Relatorios';
$currentPage = 'relatorios';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-bar-chart-line"></i> Relatorios</h1>
</div>

<div class="placeholder-page">
    <i class="bi bi-bar-chart-line"></i>
    <h3>Pagina em construcao</h3>
    <p>Os relatorios serao desenvolvidos aqui.</p>
</div>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

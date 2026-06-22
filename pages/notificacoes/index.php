<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/dashboard.php');
    exit();
}
$pageTitle = 'Notificacoes';
$currentPage = 'notificacoes';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-bell"></i> Notificacoes</h1>
</div>

<div class="placeholder-page">
    <i class="bi bi-bell"></i>
    <h3>Pagina em construcao</h3>
    <p>As notificacoes serao exibidas aqui.</p>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

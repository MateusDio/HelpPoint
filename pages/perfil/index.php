<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/dashboard.php');
    exit();
}
$pageTitle = 'Meu Perfil';
$currentPage = 'perfil';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-person-circle"></i> Meu Perfil</h1>
</div>

<div class="placeholder-page">
    <i class="bi bi-person-circle"></i>
    <h3>Pagina em construcao</h3>
    <p>O gerenciamento de perfil sera desenvolvido aqui.</p>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

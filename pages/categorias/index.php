<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/categorias.php');
    exit();
}
$pageTitle = 'Categorias';
$currentPage = 'categorias';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-tags"></i> Categorias</h1>
</div>

<div class="crud-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Descricao</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" class="text-center text-muted py-4">Nenhuma categoria registrada</td>
            </tr>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

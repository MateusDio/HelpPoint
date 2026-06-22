<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotAdmin();
$pageTitle = 'Chamados';
$currentPage = 'chamados';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-ticket"></i> Chamados</h1>
</div>

<div class="crud-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Servico</th>
                <th>Categoria</th>
                <th>Usuario</th>
                <th>Nivel</th>
                <th>Status</th>
                <th>Data</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="8" class="text-center text-muted py-4">Nenhum chamado registrado</td>
            </tr>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

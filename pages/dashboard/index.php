<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/dashboard.php');
    exit();
}
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
</div>

<p>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['user_nome']); ?></strong>!</p>

<!-- Cards de estatisticas do cliente -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon text-primary"><i class="bi bi-ticket"></i></div>
            <div class="stats-number">0</div>
            <div class="stats-label">Meus Chamados</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon text-warning"><i class="bi bi-hourglass-split"></i></div>
            <div class="stats-number">0</div>
            <div class="stats-label">Em Andamento</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon text-success"><i class="bi bi-check-circle"></i></div>
            <div class="stats-number">0</div>
            <div class="stats-label">Resolvidos</div>
        </div>
    </div>
</div>

<div class="crud-table">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">Meus Ultimos Chamados</h5>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Servico</th>
                <th>Status</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" class="text-center text-muted py-4">Nenhum chamado registrado</td>
            </tr>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotAdmin();
$pageTitle = 'Dashboard Admin';
$currentPage = 'dashboard';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
</div>

<!-- Cards de estatisticas -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon text-primary"><i class="bi bi-ticket"></i></div>
            <div class="stats-number">0</div>
            <div class="stats-label">Chamados Abertos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon text-success"><i class="bi bi-check-circle"></i></div>
            <div class="stats-number">0</div>
            <div class="stats-label">Chamados Fechados</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon text-warning"><i class="bi bi-pc-display"></i></div>
            <div class="stats-number">0</div>
            <div class="stats-label">Equipamentos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon text-info"><i class="bi bi-people"></i></div>
            <div class="stats-number">0</div>
            <div class="stats-label">Usuarios</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="crud-table">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">Ultimos Chamados</h5>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Servico</th>
                        <th>Usuario</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Nenhum chamado registrado</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        <div class="crud-table">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">Atividade Recente</h5>
            </div>
            <div class="p-4 text-center text-muted">
                <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                <p class="mt-2">Sem atividades recentes</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

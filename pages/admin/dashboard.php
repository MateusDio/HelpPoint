<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();
$pageTitle = 'Dashboard Admin';
$currentPage = 'dashboard';

$abertos = (int)$pdo->query("SELECT COUNT(*) FROM chamados WHERE status IN ('Aberto','Em Andamento')")->fetchColumn();
$fechados = (int)$pdo->query("SELECT COUNT(*) FROM chamados WHERE status IN ('Concluido','Cancelado')")->fetchColumn();
$totalEquip = (int)$pdo->query("SELECT COUNT(*) FROM equipamentos")->fetchColumn();
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();

$ultimos = $pdo->query("
    SELECT c.id, c.status, c.data, c.hora,
           u.nome AS user_nome,
           cat.nome AS categoria_nome
    FROM chamados c
    INNER JOIN user u ON u.id = c.user_id
    INNER JOIN categoria cat ON cat.id = c.categoria_id
    ORDER BY c.id DESC
    LIMIT 8
")->fetchAll();

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon text-primary"><i class="bi bi-ticket"></i></div>
            <div class="stats-number"><?= $abertos ?></div>
            <div class="stats-label">Chamados Abertos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon text-success"><i class="bi bi-check-circle"></i></div>
            <div class="stats-number"><?= $fechados ?></div>
            <div class="stats-label">Chamados Fechados</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon text-warning"><i class="bi bi-pc-display"></i></div>
            <div class="stats-number"><?= $totalEquip ?></div>
            <div class="stats-label">Equipamentos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon text-info"><i class="bi bi-people"></i></div>
            <div class="stats-number"><?= $totalUsers ?></div>
            <div class="stats-label">Usuarios</div>
        </div>
    </div>
</div>

<div class="crud-table">
    <div class="p-3 border-bottom"><h5 class="mb-0">Ultimos Chamados</h5></div>
    <table class="table table-hover">
        <thead><tr><th>#</th><th>Categoria</th><th>Usuario</th><th>Status</th><th>Data</th></tr></thead>
        <tbody>
            <?php if (empty($ultimos)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhum chamado registrado</td></tr>
            <?php else: foreach ($ultimos as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['categoria_nome']) ?></td>
                    <td><?= htmlspecialchars($c['user_nome']) ?></td>
                    <td><?= htmlspecialchars($c['status']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['data'])) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

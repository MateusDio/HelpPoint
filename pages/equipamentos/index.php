<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/equipamentos.php');
    exit();
}
$pageTitle = 'Equipamentos';
$currentPage = 'equipamentos';

$equipamentos = $pdo->query("
    SELECT e.*, t.nome AS tipo_nome
    FROM equipamentos e
    INNER JOIN tipo t ON t.id = e.tipo_id
    ORDER BY t.nome
")->fetchAll();

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-pc-display"></i> Equipamentos</h1>
</div>

<div class="crud-table">
    <table class="table table-hover">
        <thead><tr><th>#</th><th>Tipo</th><th>N/Serie</th><th>Patrimonio</th><th>Status</th></tr></thead>
        <tbody>
            <?php if (empty($equipamentos)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhum equipamento registrado</td></tr>
            <?php else: foreach ($equipamentos as $e): ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['tipo_nome']) ?></td>
                    <td><?= htmlspecialchars($e['n_serie'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($e['patrimonio'] ?? '—') ?></td>
                    <td><span class="badge bg-info"><?= htmlspecialchars($e['status']) ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

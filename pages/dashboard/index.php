<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/dashboard.php');
    exit();
}
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM chamados WHERE user_id = :u");
$stmt->execute(['u' => $userId]);
$totalMeus = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM chamados WHERE user_id = :u AND status IN ('Aberto','Em Andamento')");
$stmt->execute(['u' => $userId]);
$emAndamento = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM chamados WHERE user_id = :u AND status = 'Concluido'");
$stmt->execute(['u' => $userId]);
$resolvidos = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT c.id, c.status, c.data, cat.nome AS categoria_nome
    FROM chamados c
    INNER JOIN categoria cat ON cat.id = c.categoria_id
    WHERE c.user_id = :u
    ORDER BY c.id DESC
    LIMIT 5
");
$stmt->execute(['u' => $userId]);
$ultimos = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
</div>

<p>Bem-vindo, <strong><?= htmlspecialchars($_SESSION['user_nome']) ?></strong>!</p>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon text-primary"><i class="bi bi-ticket"></i></div>
            <div class="stats-number"><?= $totalMeus ?></div>
            <div class="stats-label">Meus Chamados</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon text-warning"><i class="bi bi-hourglass-split"></i></div>
            <div class="stats-number"><?= $emAndamento ?></div>
            <div class="stats-label">Em Andamento</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon text-success"><i class="bi bi-check-circle"></i></div>
            <div class="stats-number"><?= $resolvidos ?></div>
            <div class="stats-label">Resolvidos</div>
        </div>
    </div>
</div>

<div class="crud-table">
    <div class="p-3 border-bottom"><h5 class="mb-0">Meus Ultimos Chamados</h5></div>
    <table class="table table-hover">
        <thead><tr><th>#</th><th>Categoria</th><th>Status</th><th>Data</th></tr></thead>
        <tbody>
            <?php if (empty($ultimos)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Voce ainda nao abriu chamados</td></tr>
            <?php else: foreach ($ultimos as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['categoria_nome']) ?></td>
                    <td><?= htmlspecialchars($c['status']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['data'])) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

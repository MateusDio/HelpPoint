<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: ' . BASE_URL . '/pages/admin/categorias.php');
    exit();
}
$pageTitle = 'Categorias';
$currentPage = 'categorias';

$categorias = $pdo->query("SELECT * FROM categoria ORDER BY nome")->fetchAll();

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-tags"></i> Categorias</h1>
</div>

<div class="crud-table">
    <table class="table table-hover">
        <thead><tr><th>#</th><th>Nome</th><th>Descricao</th></tr></thead>
        <tbody>
            <?php if (empty($categorias)): ?>
                <tr><td colspan="3" class="text-center text-muted py-4">Nenhuma categoria registrada</td></tr>
            <?php else: foreach ($categorias as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['nome']) ?></td>
                    <td><?= htmlspecialchars($c['descricao'] ?? '—') ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

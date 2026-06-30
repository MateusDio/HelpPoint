<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();
$pageTitle = 'Categorias';
$currentPage = 'categorias';

$categorias = $pdo->query("SELECT * FROM categoria ORDER BY id DESC")->fetchAll();
$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-tags"></i> Categorias</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria" onclick="limparModalCategoria()">
        <i class="bi bi-plus-lg"></i> Nova Categoria
    </button>
</div>

<?php if ($sucesso): ?>
    <div class="alert alert-success alert-dismissible fade show">Operacao realizada com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($erro === 'campos'): ?>
    <div class="alert alert-danger alert-dismissible fade show">Preencha o nome!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="crud-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Descricao</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categorias)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Nenhuma categoria cadastrada</td></tr>
            <?php else: foreach ($categorias as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['nome']) ?></td>
                    <td><?= htmlspecialchars($c['descricao'] ?? '—') ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='editarCategoria(<?= json_encode($c) ?>)'><i class="bi bi-pencil"></i></button>
                        <a href="categorias_process.php?acao=excluir&id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir esta categoria?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalCategoria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="categorias_process.php" method="POST">
                <input type="hidden" name="acao" id="catAcao" value="criar">
                <input type="hidden" name="id" id="catId" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="catTitulo">Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="catNome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descricao</label>
                        <textarea class="form-control" id="catDesc" name="descricao" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function limparModalCategoria() {
    document.getElementById('catAcao').value = 'criar';
    document.getElementById('catId').value = '';
    document.getElementById('catTitulo').textContent = 'Nova Categoria';
    document.getElementById('catNome').value = '';
    document.getElementById('catDesc').value = '';
}
function editarCategoria(c) {
    document.getElementById('catAcao').value = 'editar';
    document.getElementById('catId').value = c.id;
    document.getElementById('catTitulo').textContent = 'Editar Categoria';
    document.getElementById('catNome').value = c.nome;
    document.getElementById('catDesc').value = c.descricao || '';
    new bootstrap.Modal(document.getElementById('modalCategoria')).show();
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

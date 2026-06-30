<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();
$pageTitle = 'Tipos';
$currentPage = 'tipos';

$tipos = $pdo->query("SELECT * FROM tipo ORDER BY id DESC")->fetchAll();
$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-list-check"></i> Tipos</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTipo" onclick="limparModalTipo()">
        <i class="bi bi-plus-lg"></i> Novo Tipo
    </button>
</div>

<?php if ($sucesso): ?><div class="alert alert-success alert-dismissible fade show">Operacao realizada com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($erro === 'campos'): ?><div class="alert alert-danger alert-dismissible fade show">Preencha o nome!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($erro === 'fk'): ?><div class="alert alert-danger alert-dismissible fade show">Nao e possivel excluir: existem equipamentos vinculados.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="crud-table">
    <table class="table table-hover">
        <thead><tr><th>#</th><th>Nome</th><th>Acoes</th></tr></thead>
        <tbody>
            <?php if (empty($tipos)): ?>
                <tr><td colspan="3" class="text-center text-muted py-4">Nenhum tipo cadastrado</td></tr>
            <?php else: foreach ($tipos as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['nome']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='editarTipo(<?= json_encode($t) ?>)'><i class="bi bi-pencil"></i></button>
                        <a href="tipos_process.php?acao=excluir&id=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este tipo?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTipo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="tipos_process.php" method="POST">
                <input type="hidden" name="acao" id="tipoAcao" value="criar">
                <input type="hidden" name="id" id="tipoId" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="tipoTitulo">Novo Tipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tipoNome" name="nome" required>
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
function limparModalTipo() {
    document.getElementById('tipoAcao').value = 'criar';
    document.getElementById('tipoId').value = '';
    document.getElementById('tipoTitulo').textContent = 'Novo Tipo';
    document.getElementById('tipoNome').value = '';
}
function editarTipo(t) {
    document.getElementById('tipoAcao').value = 'editar';
    document.getElementById('tipoId').value = t.id;
    document.getElementById('tipoTitulo').textContent = 'Editar Tipo';
    document.getElementById('tipoNome').value = t.nome;
    new bootstrap.Modal(document.getElementById('modalTipo')).show();
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

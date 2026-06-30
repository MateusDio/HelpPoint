<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();
$pageTitle = 'Equipamentos';
$currentPage = 'equipamentos';

$tipos = $pdo->query("SELECT * FROM tipo ORDER BY nome")->fetchAll();
$equipamentos = $pdo->query("
    SELECT e.*, t.nome AS tipo_nome
    FROM equipamentos e
    INNER JOIN tipo t ON t.id = e.tipo_id
    ORDER BY e.id DESC
")->fetchAll();

$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-pc-display"></i> Equipamentos</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEquipamento" onclick="limparModalEquip()">
        <i class="bi bi-plus-lg"></i> Novo Equipamento
    </button>
</div>

<?php if ($sucesso): ?><div class="alert alert-success alert-dismissible fade show">Operacao realizada com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($erro === 'campos'): ?><div class="alert alert-danger alert-dismissible fade show">Preencha os campos obrigatorios!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($erro === 'sem_tipo'): ?><div class="alert alert-warning alert-dismissible fade show">Cadastre ao menos um Tipo antes.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($erro === 'fk'): ?><div class="alert alert-danger alert-dismissible fade show">Nao e possivel excluir: equipamento vinculado a chamados.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="crud-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th><th>N/Serie</th><th>Patrimonio</th><th>Tipo</th><th>Status</th><th>Descricao</th><th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($equipamentos)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhum equipamento cadastrado</td></tr>
            <?php else: foreach ($equipamentos as $e): ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['n_serie'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($e['patrimonio'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($e['tipo_nome']) ?></td>
                    <td><span class="badge bg-info"><?= htmlspecialchars($e['status']) ?></span></td>
                    <td><?= htmlspecialchars($e['descricao'] ?? '—') ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='editarEquip(<?= json_encode($e) ?>)'><i class="bi bi-pencil"></i></button>
                        <a href="equipamentos_process.php?acao=excluir&id=<?= $e['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este equipamento?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalEquipamento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="equipamentos_process.php" method="POST">
                <input type="hidden" name="acao" id="equipAcao" value="criar">
                <input type="hidden" name="id" id="equipId" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipTitulo">Novo Equipamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="equipTipo" name="tipo_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numero de Serie</label>
                        <input type="text" class="form-control" id="equipSerie" name="n_serie">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Patrimonio</label>
                        <input type="text" class="form-control" id="equipPat" name="patrimonio">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="equipStatus" name="status">
                            <option value="Disponivel">Disponivel</option>
                            <option value="Em Uso">Em Uso</option>
                            <option value="Manutencao">Manutencao</option>
                            <option value="Inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descricao</label>
                        <textarea class="form-control" id="equipDesc" name="descricao" rows="3"></textarea>
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
function limparModalEquip() {
    document.getElementById('equipAcao').value = 'criar';
    document.getElementById('equipId').value = '';
    document.getElementById('equipTitulo').textContent = 'Novo Equipamento';
    document.getElementById('equipTipo').value = '';
    document.getElementById('equipSerie').value = '';
    document.getElementById('equipPat').value = '';
    document.getElementById('equipStatus').value = 'Disponivel';
    document.getElementById('equipDesc').value = '';
}
function editarEquip(e) {
    document.getElementById('equipAcao').value = 'editar';
    document.getElementById('equipId').value = e.id;
    document.getElementById('equipTitulo').textContent = 'Editar Equipamento';
    document.getElementById('equipTipo').value = e.tipo_id;
    document.getElementById('equipSerie').value = e.n_serie || '';
    document.getElementById('equipPat').value = e.patrimonio || '';
    document.getElementById('equipStatus').value = e.status;
    document.getElementById('equipDesc').value = e.descricao || '';
    new bootstrap.Modal(document.getElementById('modalEquipamento')).show();
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

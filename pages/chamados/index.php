<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/chamados.php');
    exit();
}
$pageTitle = 'Meus Chamados';
$currentPage = 'chamados';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-ticket"></i> Meus Chamados</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalChamado">
        <i class="bi bi-plus-lg"></i> Novo Chamado
    </button>
</div>

<div class="crud-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Servico</th>
                <th>Categoria</th>
                <th>Status</th>
                <th>Data</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhum chamado registrado</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal Novo Chamado -->
<div class="modal fade" id="modalChamado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Chamado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="servicoChamado" class="form-label">Servico</label>
                        <input type="text" class="form-control" id="servicoChamado" name="servico" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoriaChamado" class="form-label">Categoria</label>
                        <select class="form-select" id="categoriaChamado" name="categoria">
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descricaoChamado" class="form-label">Descricao</label>
                        <textarea class="form-control" id="descricaoChamado" name="descricao" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

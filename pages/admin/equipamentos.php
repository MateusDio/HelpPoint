<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotAdmin();
$pageTitle = 'Equipamentos';
$currentPage = 'equipamentos';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-pc-display"></i> Equipamentos</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEquipamento">
        <i class="bi bi-plus-lg"></i> Novo Equipamento
    </button>
</div>

<div class="crud-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Tipo</th>
                <th>N/S</th>
                <th>Patrimonio</th>
                <th>Status</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhum equipamento cadastrado</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal Criar/Editar Equipamento -->
<div class="modal fade" id="modalEquipamento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="tipoEquip" class="form-label">Tipo</label>
                        <select class="form-select" id="tipoEquip" name="fk_tp">
                            <option value="">Selecione o tipo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nsEquip" class="form-label">Numero de Serie</label>
                        <input type="text" class="form-control" id="nsEquip" name="ns">
                    </div>
                    <div class="mb-3">
                        <label for="patrimonioEquip" class="form-label">Patrimonio</label>
                        <input type="text" class="form-control" id="patrimonioEquip" name="patrimonio">
                    </div>
                    <div class="mb-3">
                        <label for="statusEquip" class="form-label">Status</label>
                        <select class="form-select" id="statusEquip" name="status">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                            <option value="manutencao">Em Manutencao</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descEquip" class="form-label">Descricao</label>
                        <textarea class="form-control" id="descEquip" name="descricao" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

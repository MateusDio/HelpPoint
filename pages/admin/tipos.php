<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotAdmin();
$pageTitle = 'Tipos';
$currentPage = 'tipos';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-list-check"></i> Tipos</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTipo">
        <i class="bi bi-plus-lg"></i> Novo Tipo
    </button>
</div>

<div class="crud-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" class="text-center text-muted py-4">Nenhum tipo cadastrado</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal Criar/Editar Tipo -->
<div class="modal fade" id="modalTipo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Tipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="nomeTipo" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nomeTipo" name="nome" required>
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

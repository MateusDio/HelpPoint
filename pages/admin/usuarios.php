<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();
$pageTitle = 'Usuarios';
$currentPage = 'usuarios';

// Buscar todos os usuarios
$stmt = $pdo->query("SELECT * FROM user ORDER BY id DESC");
$usuarios = $stmt->fetchAll();

// Buscar usuario para edicao (se clicou em editar)
$editando = null;
if (isset($_GET['editar'])) {
    $stmtEdit = $pdo->prepare("SELECT * FROM user WHERE id = :id LIMIT 1");
    $stmtEdit->execute(['id' => (int)$_GET['editar']]);
    $editando = $stmtEdit->fetch();
}

$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-people"></i> Usuarios</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="limparModal()">
        <i class="bi bi-plus-lg"></i> Novo Usuario
    </button>
</div>

<?php if ($sucesso === 'criado'): ?>
    <div class="alert alert-success alert-dismissible fade show">Usuario criado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php elseif ($sucesso === 'editado'): ?>
    <div class="alert alert-success alert-dismissible fade show">Usuario atualizado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php elseif ($sucesso === 'excluido'): ?>
    <div class="alert alert-success alert-dismissible fade show">Usuario excluido com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($erro === 'campos'): ?>
    <div class="alert alert-danger alert-dismissible fade show">Preencha todos os campos obrigatorios!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php elseif ($erro === 'email_existe'): ?>
    <div class="alert alert-danger alert-dismissible fade show">Este email ja esta cadastrado!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php elseif ($erro === 'excluir_proprio'): ?>
    <div class="alert alert-danger alert-dismissible fade show">Voce nao pode excluir sua propria conta!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="crud-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Funcao</th>
                <th>Documento</th>
                <th>Role</th>
                <th>Criado em</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Nenhum usuario cadastrado</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nome']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['funcao'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($u['documento'] ?? '—'); ?></td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-primary">Usuario</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo isset($u['created_at']) ? date('d/m/Y H:i', strtotime($u['created_at'])) : '—'; ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editarUsuario(<?php echo htmlspecialchars(json_encode($u)); ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                        <a href="usuarios_process.php?acao=excluir&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuario?')">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Criar/Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="usuarios_process.php" method="POST">
                <input type="hidden" name="acao" id="modalAcao" value="criar">
                <input type="hidden" name="id" id="modalId" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Novo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomeUser" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nomeUser" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailUser" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="emailUser" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senhaUser" class="form-label" id="labelSenha">Senha <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="senhaUser" name="senha">
                        <small class="text-muted d-none" id="senhaHint">Deixe vazio para manter a senha atual.</small>
                    </div>
                    <div class="mb-3">
                        <label for="funUser" class="form-label">Funcao</label>
                        <input type="text" class="form-control" id="funUser" name="funcao">
                    </div>
                    <div class="mb-3">
                        <label for="docUser" class="form-label">Documento</label>
                        <input type="text" class="form-control" id="docUser" name="documento">
                    </div>
                    <div class="mb-3">
                        <label for="roleUser" class="form-label">Role</label>
                        <select class="form-select" id="roleUser" name="role">
                            <option value="user">Usuario (Cliente)</option>
                            <option value="admin">Administrador</option>
                        </select>
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
function limparModal() {
    document.getElementById('modalAcao').value = 'criar';
    document.getElementById('modalId').value = '';
    document.getElementById('modalTitulo').textContent = 'Novo Usuario';
    document.getElementById('nomeUser').value = '';
    document.getElementById('emailUser').value = '';
    document.getElementById('senhaUser').value = '';
    document.getElementById('senhaUser').required = true;
    document.getElementById('labelSenha').innerHTML = 'Senha <span class="text-danger">*</span>';
    document.getElementById('senhaHint').classList.add('d-none');
    document.getElementById('funUser').value = '';
    document.getElementById('docUser').value = '';
    document.getElementById('roleUser').value = 'user';
}

function editarUsuario(user) {
    document.getElementById('modalAcao').value = 'editar';
    document.getElementById('modalId').value = user.id;
    document.getElementById('modalTitulo').textContent = 'Editar Usuario';
    document.getElementById('nomeUser').value = user.nome;
    document.getElementById('emailUser').value = user.email;
    document.getElementById('senhaUser').value = '';
    document.getElementById('senhaUser').required = false;
    document.getElementById('labelSenha').innerHTML = 'Senha';
    document.getElementById('senhaHint').classList.remove('d-none');
    document.getElementById('funUser').value = user.funcao || '';
    document.getElementById('docUser').value = user.documento || '';
    document.getElementById('roleUser').value = user.role;

    var modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>

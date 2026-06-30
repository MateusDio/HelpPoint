<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/dashboard.php');
    exit();
}
$pageTitle = 'Meu Perfil';
$currentPage = 'perfil';

$userId = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM user WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

<div class="admin-page-header">
    <h1><i class="bi bi-person-circle"></i> Meu Perfil</h1>
</div>

<?php if ($sucesso): ?><div class="alert alert-success alert-dismissible fade show">Perfil atualizado!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($erro === 'campos'): ?><div class="alert alert-danger alert-dismissible fade show">Preencha nome e email!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($erro === 'email_existe'): ?><div class="alert alert-danger alert-dismissible fade show">Email ja em uso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="crud-table p-4" style="max-width: 600px;">
    <form action="perfil_process.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Nome <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Funcao</label>
            <input type="text" class="form-control" name="funcao" value="<?= htmlspecialchars($user['funcao'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Documento</label>
            <input type="text" class="form-control" name="documento" value="<?= htmlspecialchars($user['documento'] ?? '') ?>">
        </div>
        <hr>
        <div class="mb-3">
            <label class="form-label">Nova Senha</label>
            <input type="password" class="form-control" name="senha">
            <small class="text-muted">Deixe vazio para manter a senha atual.</small>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

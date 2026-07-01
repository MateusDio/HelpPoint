<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: ' . BASE_URL . '/pages/admin/dashboard.php');
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
<?php if ($erro === 'avatar'): ?><div class="alert alert-danger alert-dismissible fade show">A foto precisa ser uma imagem valida (PNG, JPG ou WEBP) e menor que 2MB.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="crud-table p-4" style="max-width: 700px;">
    <form action="perfil_process.php" method="POST" enctype="multipart/form-data">
        <div class="d-flex align-items-center gap-3 mb-4">
            <?php if (!empty($user['avatar'])): ?>
                <img id="avatarPreview" src="<?= BASE_URL ?>/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" alt="Foto de perfil" style="width: 84px; height: 84px; object-fit: cover; border-radius: 50%; border: 2px solid #e5e7eb;">
            <?php else: ?>
                <div id="avatarPreview" style="width: 84px; height: 84px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #64748b; border: 2px solid #e5e7eb;">
                    <?= strtoupper(mb_substr($user['nome'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div>
                <h5 class="mb-1">Foto de perfil</h5>
                <p class="text-muted mb-0">Envie uma imagem local do seu computador para personalizar sua conta.</p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto</label>
            <input type="file" class="form-control" name="avatar" id="avatarInput" accept="image/png,image/jpeg,image/webp,image/jpg">
            <small class="text-muted">Formatos aceitos: PNG, JPG e WEBP. Tamanho máximo: 2MB.</small>
        </div>
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
        <div class="mb-3">
            <label class="form-label">Bio</label>
            <textarea class="form-control" name="bio" rows="4" placeholder="Escreva um pouco sobre você..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
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

<script>
const avatarInput = document.getElementById('avatarInput');
const avatarPreview = document.getElementById('avatarPreview');
if (avatarInput && avatarPreview) {
    avatarInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            if (avatarPreview.tagName === 'IMG') {
                avatarPreview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id = 'avatarPreview';
                img.src = e.target.result;
                img.alt = 'Foto de perfil';
                img.style.width = '84px';
                img.style.height = '84px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '50%';
                img.style.border = '2px solid #e5e7eb';
                avatarPreview.replaceWith(img);
            }
        };
        reader.readAsDataURL(file);
    });
}
</script>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>

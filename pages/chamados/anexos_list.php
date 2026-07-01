<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();

$chamado_id = (int)($_GET['chamado_id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

if ($chamado_id === 0) {
    exit();
}

// Verificar que o usuário tem acesso a este chamado
$stmt = $pdo->prepare("SELECT user_id FROM chamados WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $chamado_id]);
$chamado = $stmt->fetch();

if (!$chamado) {
    exit();
}

if (!isAdmin() && (int)$chamado['user_id'] !== $userId) {
    exit();
}

// Buscar anexos
$stmt = $pdo->prepare("
    SELECT id, nome_arquivo, nome_original, tamanho, tipo_mime, data_upload, user_id
    FROM chamado_anexos
    WHERE chamado_id = :id
    ORDER BY data_upload DESC
");
$stmt->execute(['id' => $chamado_id]);
$anexos = $stmt->fetchAll();

if (empty($anexos)) {
    echo '<small class="text-muted">Sem anexos</small>';
    exit();
}

$uploadUrl = BASE_URL . '/uploads/chamados/';
foreach ($anexos as $a):
    $isImage = strpos($a['tipo_mime'], 'image/') === 0;
    $icon = $isImage ? 'bi-image' : 'bi-file-earmark';
    if ($a['tipo_mime'] === 'application/pdf') $icon = 'bi-file-pdf';
    if (strpos($a['tipo_mime'], 'word') !== false) $icon = 'bi-file-word';
?>
    <div class="attachment-item d-flex align-items-center justify-content-between p-2 bg-light rounded mb-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi <?= $icon ?>"></i>
            <div>
                <small class="d-block">
                    <a href="<?= htmlspecialchars($uploadUrl . $a['nome_arquivo']) ?>" target="_blank" class="text-decoration-none">
                        <?= htmlspecialchars($a['nome_original']) ?>
                    </a>
                </small>
                <small class="text-muted d-block"><?= number_format($a['tamanho'] / 1024, 1) ?> KB</small>
            </div>
        </div>
        <?php if (isAdmin() || (int)$a['user_id'] === $userId): ?>
            <a href="anexos_process.php?acao=excluir_anexo&id=<?= $a['id'] ?>" onclick="return confirm('Tem certeza?')" class="btn btn-sm btn-danger">
                <i class="bi bi-trash"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endforeach;

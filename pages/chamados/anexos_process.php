<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// ---- FAZER UPLOAD DE ANEXO ----
if ($acao === 'upload_anexo' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $chamado_id = (int)($_POST['chamado_id'] ?? 0);
    $userId = (int)$_SESSION['user_id'];

    if ($chamado_id === 0 || !isset($_FILES['anexo']) || $_FILES['anexo']['error'] === UPLOAD_ERR_NO_FILE) {
        header('Location: index.php?erro=campos');
        exit();
    }

    // Validar que o usuário pode fazer upload (é o dono do chamado ou é admin)
    $stmt = $pdo->prepare("SELECT user_id FROM chamados WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $chamado_id]);
    $chamado = $stmt->fetch();
    if (!$chamado) {
        header('Location: index.php?erro=chamado_nao_encontrado');
        exit();
    }
    if (!isAdmin() && (int)$chamado['user_id'] !== $userId) {
        header('Location: index.php?erro=acesso_negado');
        exit();
    }

    // Validar número de anexos existentes (máximo 5)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM chamado_anexos WHERE chamado_id = :id");
    $stmt->execute(['id' => $chamado_id]);
    $countAnexos = (int)$stmt->fetchColumn();
    if ($countAnexos >= 5) {
        header('Location: index.php?erro=max_anexos');
        exit();
    }

    $tmpName = $_FILES['anexo']['tmp_name'];
    $size = (int)$_FILES['anexo']['size'];
    $nomeOriginal = $_FILES['anexo']['name'];
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpName);
    finfo_close($finfo);
    $ext = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

    if ($size > 5 * 1024 * 1024 || !in_array($mime, $allowedMimes, true) || !in_array($ext, $allowedExts, true)) {
        header('Location: index.php?erro=arquivo_invalido');
        exit();
    }

    $nomeArquivo = 'anexo_' . $chamado_id . '_' . $userId . '_' . time() . '.' . $ext;
    $uploadDir = __DIR__ . '/../../uploads/chamados';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $targetPath = $uploadDir . '/' . $nomeArquivo;
    if (!move_uploaded_file($tmpName, $targetPath)) {
        header('Location: index.php?erro=upload_falhou');
        exit();
    }

    // Registrar no banco
    $stmt = $pdo->prepare("INSERT INTO chamado_anexos (chamado_id, nome_arquivo, nome_original, tamanho, tipo_mime, user_id) VALUES (:cid, :na, :no, :s, :mime, :uid)");
    $stmt->execute([
        'cid' => $chamado_id,
        'na' => $nomeArquivo,
        'no' => $nomeOriginal,
        's' => $size,
        'mime' => $mime,
        'uid' => $userId
    ]);

    header('Location: index.php?sucesso=anexo_adicionado');
    exit();
}

// ---- EXCLUIR ANEXO ----
if ($acao === 'excluir_anexo' && isset($_GET['id'])) {
    $anexoId = (int)$_GET['id'];
    $userId = (int)$_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT ca.user_id, ca.nome_arquivo, c.user_id as chamado_user FROM chamado_anexos ca INNER JOIN chamados c ON c.id = ca.chamado_id WHERE ca.id = :id LIMIT 1");
    $stmt->execute(['id' => $anexoId]);
    $anexo = $stmt->fetch();

    if (!$anexo) {
        header('Location: index.php?erro=anexo_nao_encontrado');
        exit();
    }

    // Validar permissão (é o dono do anexo ou é admin)
    if (!isAdmin() && (int)$anexo['user_id'] !== $userId && (int)$anexo['chamado_user'] !== $userId) {
        header('Location: index.php?erro=acesso_negado');
        exit();
    }

    // Deletar arquivo
    $filePath = __DIR__ . '/../../uploads/chamados/' . $anexo['nome_arquivo'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Deletar registro
    $stmt = $pdo->prepare("DELETE FROM chamado_anexos WHERE id = :id");
    $stmt->execute(['id' => $anexoId]);

    header('Location: index.php?sucesso=anexo_removido');
    exit();
}

header('Location: index.php');
exit();

<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();

$acao = $_POST['acao'] ?? '';

if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)$_SESSION['user_id'];
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $equip_id_raw = $_POST['equipamento_id'] ?? '';
    $equipamento_id = $equip_id_raw === '' ? null : (int)$equip_id_raw;
    $nivel = $_POST['nivel'] ?? 'Medio';
    $local = trim($_POST['local'] ?? '');
    $obs = trim($_POST['obs'] ?? '');

    if ($categoria_id === 0 || $obs === '') {
        header('Location: index.php?erro=campos');
        exit();
    }

    $stmt = $pdo->prepare("
        INSERT INTO chamados (status, data, hora, nivel, obs, local, user_id, categoria_id, equipamento_id)
        VALUES ('Aberto', CURDATE(), CURTIME(), :nivel, :obs, :local, :uid, :cid, :eid)
    ");
    $stmt->execute([
        'nivel' => $nivel,
        'obs' => $obs,
        'local' => $local,
        'uid' => $userId,
        'cid' => $categoria_id,
        'eid' => $equipamento_id
    ]);

    $chamadoId = $pdo->lastInsertId();

    // Processar anexos se houver
    if (isset($_FILES['anexos']) && is_array($_FILES['anexos']['name'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];
        $uploadDir = __DIR__ . '/../../uploads/chamados';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        for ($i = 0; $i < min(count($_FILES['anexos']['name']), 5); $i++) {
            if ($_FILES['anexos']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $tmpName = $_FILES['anexos']['tmp_name'][$i];
            $size = (int)$_FILES['anexos']['size'][$i];
            $nomeOriginal = $_FILES['anexos']['name'][$i];

            if ($size > 5 * 1024 * 1024) continue;

            $mime = finfo_file($finfo, $tmpName);
            $ext = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

            if (!in_array($mime, $allowedMimes, true) || !in_array($ext, $allowedExts, true)) continue;

            $nomeArquivo = 'anexo_' . $chamadoId . '_' . $userId . '_' . time() . '_' . $i . '.' . $ext;
            $targetPath = $uploadDir . '/' . $nomeArquivo;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $stmtAnexo = $pdo->prepare("INSERT INTO chamado_anexos (chamado_id, nome_arquivo, nome_original, tamanho, tipo_mime, user_id) VALUES (:cid, :na, :no, :s, :mime, :uid)");
                $stmtAnexo->execute([
                    'cid' => $chamadoId,
                    'na' => $nomeArquivo,
                    'no' => $nomeOriginal,
                    's' => $size,
                    'mime' => $mime,
                    'uid' => $userId
                ]);
            }
        }
        finfo_close($finfo);
    }

    header('Location: index.php?sucesso=criado');
    exit();
}

header('Location: index.php');
exit();

<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

$token = $_GET['token'] ?? '';
$mensagem = '';
$tipo_alerta = '';

if (!$token) {
    $tipo_alerta = 'danger';
    $mensagem = 'Token de verificação não fornecido.';
} else {
    // Validar token
    $validacao = validarTokenVerificacao($token, $pdo);
    
    if ($validacao === false) {
        $tipo_alerta = 'danger';
        $mensagem = 'Token inválido ou não encontrado.';
    } elseif (isset($validacao['error'])) {
        $tipo_alerta = 'warning';
        $mensagem = $validacao['error'];
    } else {
        // Token válido - verificar email
        try {
            $pdo->beginTransaction();
            
            $emailVerificado = strtolower(trim($validacao['email_temporario']));

            $stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email AND id != :id LIMIT 1");
            $stmt->execute([
                'email' => $emailVerificado,
                'id' => $validacao['user_id']
            ]);

            if ($stmt->fetch()) {
                throw new Exception('Este email ja esta vinculado a outra conta.');
            }

            // Corrige usuarios antigos que ficaram com email vazio no fluxo anterior.
            $stmt = $pdo->prepare("UPDATE user SET email = :email WHERE id = :id AND (email = '' OR email IS NULL)");
            $stmt->execute([
                'email' => $emailVerificado,
                'id' => $validacao['user_id']
            ]);
            
            // Marcar como verificado
            $stmt = $pdo->prepare("UPDATE email_verification SET verificado_em = NOW() WHERE id = :id");
            $stmt->execute(['id' => $validacao['id']]);
            
            $pdo->commit();
            
            $tipo_alerta = 'success';
            $mensagem = 'Email verificado com sucesso! Você pode fazer login agora.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $tipo_alerta = 'danger';
            $mensagem = 'Erro ao verificar email: ' . htmlspecialchars($e->getMessage());
        }
    }
}

$pageTitle = 'Verificar Email';
$currentPage = 'login';
require_once __DIR__ . '/../../includes/global/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <h2 class="text-center mb-4">
            <i class="bi bi-envelope-check"></i> Verificação de Email
        </h2>
        
        <div class="alert alert-<?= htmlspecialchars($tipo_alerta) ?>" role="alert">
            <?= htmlspecialchars($mensagem) ?>
        </div>
        
        <hr>
        
        <p class="text-center text-muted mb-3">
            <?php if ($tipo_alerta === 'success'): ?>
                <i class="bi bi-check-circle" style="font-size: 1.5rem; color: #28a745;"></i>
            <?php else: ?>
                <i class="bi bi-exclamation-circle" style="font-size: 1.5rem; color: #dc3545;"></i>
            <?php endif; ?>
        </p>
        
        <div class="text-center">
            <a href="index.php" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right"></i> Ir para Login
            </a>
        </div>
        
        <p class="text-center text-muted small mt-4">
            Não recebeu o email? Verifique sua pasta de spam.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/global/footer.php'; ?>

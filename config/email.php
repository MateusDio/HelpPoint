<?php
require_once __DIR__ . '/../phpmailer/src/Exception.php';
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';
// Configuração de email para verificação
define('MAIL_FROM_EMAIL', 'helppoint@helppoint.com');
define('MAIL_FROM_NAME', 'HelpPoint');

define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_SECURE', 'tls');

function gerarUrlAbsoluta($path) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $https ? 'https' : 'http';

    if ($host === '') {
        return BASE_URL . $path;
    }

    return $scheme . '://' . $host . BASE_URL . $path;
}

function enviarEmailHtml($destinatario, $assunto, $mensagem) {
    if (SMTP_HOST !== '' && SMTP_USERNAME !== '' && SMTP_PASSWORD !== '') {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addAddress($destinatario);
            $mail->addReplyTo(MAIL_FROM_EMAIL, MAIL_FROM_NAME);

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = $mensagem;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $mensagem));

            return $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('Erro PHPMailer: ' . $e->getMessage());
            return false;
        }
    }

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . MAIL_FROM_EMAIL . "\r\n";

    return @mail($destinatario, $assunto, $mensagem, $headers);
}

// Para usar com servidor SMTP ou serviço de email
// Você pode usar um serviço como SendGrid, Mailtrap, Gmail SMTP, etc.
// Por padrão, utilizaremos mail() do PHP

/**
 * Enviar email de verificação
 * @param string $destinatario Email do usuário
 * @param string $nome Nome do usuário
 * @param string $token Token de verificação
 * @return bool True se enviado com sucesso
 */
function enviarEmailVerificacao($destinatario, $nome, $token) {
    $link_verificacao = gerarUrlAbsoluta('/pages/login/verify_email.php?token=' . urlencode($token));
    
    $assunto = 'Confirme seu email - HelpPoint';
    
    $mensagem = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .footer { background: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
            .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .btn:hover { background: #0056b3; }
            .code { background: #e9ecef; padding: 10px; border-radius: 3px; font-family: monospace; word-break: break-all; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Bem-vindo ao HelpPoint!</h1>
            </div>
            <div class='content'>
                <p>Olá <strong>" . htmlspecialchars($nome) . "</strong>,</p>
                <p>Obrigado por se registrar no HelpPoint. Para ativar sua conta, por favor clique no link abaixo para confirmar seu endereço de email:</p>
                
                <center>
                    <a href='" . htmlspecialchars($link_verificacao) . "' class='btn'>Confirmar Email</a>
                </center>
                
                <p>Ou copie e cole este link no seu navegador:</p>
                <div class='code'>" . htmlspecialchars($link_verificacao) . "</div>
                
                <p style='color: #666; font-size: 12px;'>Este link expira em 24 horas.</p>
                
                <p>Se você não se registrou no HelpPoint, por favor ignore este email.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " HelpPoint. Todos os direitos reservados.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return enviarEmailHtml($destinatario, $assunto, $mensagem);
}

/**
 * Validar token de verificação
 * @param string $token Token a validar
 * @return array|false Array com dados se válido, false caso contrário
 */
function validarTokenVerificacao($token, $pdo) {
    $stmt = $pdo->prepare("
        SELECT ev.id, ev.user_id, ev.email_temporario, ev.expira_em, ev.verificado_em, u.email AS user_email
        FROM email_verification ev
        INNER JOIN user u ON u.id = ev.user_id
        WHERE ev.token = :token
        LIMIT 1
    ");
    $stmt->execute(['token' => $token]);
    $registro = $stmt->fetch();
    
    if (!$registro) {
        return false;
    }
    
    // Verificar se já foi verificado
    if ($registro['verificado_em'] !== null) {
        return ['error' => 'Email já foi verificado anteriormente'];
    }
    
    // Verificar se expirou
    if (strtotime($registro['expira_em']) < time()) {
        return ['error' => 'Link de verificação expirou'];
    }
    
    return $registro;
}

/**
 * Enviar email de redefinição de senha
 * @param string $destinatario Email do usuário
 * @param string $nome Nome do usuário
 * @param string $token Token de reset
 * @return bool True se enviado com sucesso
 */
function enviarEmailRedefinicaoSenha($destinatario, $nome, $token) {
    $link_reset = gerarUrlAbsoluta('/pages/login/reset_password.php?token=' . urlencode($token));
    $assunto = 'Redefinição de senha - HelpPoint';
    
    $mensagem = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .footer { background: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
            .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .btn:hover { background: #0056b3; }
            .code { background: #e9ecef; padding: 10px; border-radius: 3px; font-family: monospace; word-break: break-all; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Redefinição de senha HelpPoint</h1>
            </div>
            <div class='content'>
                <p>Olá <strong>" . htmlspecialchars($nome) . "</strong>,</p>
                <p>Recebemos uma solicitação para redefinir a senha da sua conta. Clique no botão abaixo para continuar:</p>
                <center>
                    <a href='" . htmlspecialchars($link_reset) . "' class='btn'>Redefinir senha</a>
                </center>
                <p>Ou copie e cole este link no seu navegador:</p>
                <div class='code'>" . htmlspecialchars($link_reset) . "</div>
                <p style='color: #666; font-size: 12px;'>Este link expira em 1 hora.</p>
                <p>Se você não solicitou a redefinição, ignore este email.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " HelpPoint. Todos os direitos reservados.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return enviarEmailHtml($destinatario, $assunto, $mensagem);
}

/**
 * Validar token de reset de senha
 * @param string $token Token a validar
 * @param PDO $pdo Conexão PDO
 * @return array|false Array de dados se válido, false caso contrário
 */
function validarTokenResetSenha($token, $pdo) {
    $stmt = $pdo->prepare("
        SELECT id, user_id, expira_em, usado_em
        FROM password_reset
        WHERE token = :token
        LIMIT 1
    ");
    $stmt->execute(['token' => $token]);
    $registro = $stmt->fetch();
    
    if (!$registro) {
        return false;
    }
    
    if ($registro['usado_em'] !== null) {
        return ['error' => 'Este link já foi usado. Solicite um novo.'];
    }
    
    if (strtotime($registro['expira_em']) < time()) {
        return ['error' => 'Este link expirou. Solicite um novo.'];
    }
    
    return $registro;
}
?>

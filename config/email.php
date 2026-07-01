<?php
// Configuração de email para verificação
define('MAIL_FROM_EMAIL', 'helppoint@helppoint.com');
define('MAIL_FROM_NAME', 'HelpPoint');

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
    $link_verificacao = BASE_URL . '/pages/login/verify_email.php?token=' . urlencode($token);
    
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
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . MAIL_FROM_EMAIL . "\r\n";
    
    // Se tiver configurado SMTP, use aqui. Por enquanto, usaremos mail()
    return @mail($destinatario, $assunto, $mensagem, $headers);
}

/**
 * Validar token de verificação
 * @param string $token Token a validar
 * @return array|false Array com dados se válido, false caso contrário
 */
function validarTokenVerificacao($token, $pdo) {
    $stmt = $pdo->prepare("
        SELECT id, user_id, email_temporario, expira_em, verificado_em
        FROM email_verification
        WHERE token = :token
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
?>

<?php
namespace MediBook\Services;

class EmailService
{
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $fromEmail;
    private $fromName;
    
    public function __construct()
    {
        // Configuración SMTP cargada desde variables de entorno (config/mail.php),
        // nunca hardcodeada en el código fuente.
        $config = require __DIR__ . '/../../config/mail.php';

        $this->smtpHost = $config['host'];
        $this->smtpPort = $config['port'];
        $this->smtpUsername = $config['username'];
        $this->smtpPassword = $config['password'];
        $this->fromEmail = $config['from']['address'];
        $this->fromName = $config['from']['name'];
    }
    
    public function sendPasswordResetEmail($email, $token)
    {
        $resetLink = "http://localhost/MediBook/src/Views/auth/reset-password.php?token=" . urlencode($token);
        
        $subject = "MediBook - Recuperación de Contraseña";
        
        $htmlMessage = $this->getPasswordResetTemplate($resetLink);
        
        return $this->sendEmail($email, $subject, $htmlMessage);
    }
    
    public function sendWelcomeEmail($email, $name)
    {
        $subject = "¡Bienvenido a MediBook!";
        
        $htmlMessage = $this->getWelcomeTemplate($name);
        
        return $this->sendEmail($email, $subject, $htmlMessage);
    }
    
    private function sendEmail($to, $subject, $htmlMessage)
    {
        // Por ahora solo usamos mail() nativo de PHP
        // En producción se puede integrar PHPMailer o un servicio como SendGrid
        return $this->sendWithNativeMail($to, $subject, $htmlMessage);
    }
    
    private function sendWithPHPMailer($to, $subject, $htmlMessage)
    {
        // PHPMailer no está instalado por defecto, usar mail() nativo
        return $this->sendWithNativeMail($to, $subject, $htmlMessage);
    }
    
    private function sendWithNativeMail($to, $subject, $htmlMessage)
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromEmail}>" . "\r\n";
        $headers .= "Reply-To: soporte@medibook.com" . "\r\n";
        
        return mail($to, $subject, $htmlMessage, $headers);
    }
    
    private function getPasswordResetTemplate($resetLink)
    {
        return "
        <html>
        <head>
            <title>Recuperación de Contraseña - MediBook</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 10px; border-left: 4px solid #667eea; }
                .button { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
                .url-box { word-break: break-all; background: #e9ecef; padding: 15px; border-radius: 8px; font-size: 12px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin: 0; font-size: 2.5rem;'>🏥 MediBook</h1>
                    <p style='margin: 5px 0 0 0; opacity: 0.9;'>Sistema de Gestión Médica</p>
                </div>
                
                <div class='content'>
                    <h2 style='color: #2c3e50; margin-top: 0;'>Recuperación de Contraseña</h2>
                    
                    <p>Hola,</p>
                    
                    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en MediBook.</p>
                    
                    <p>Si solicitaste este cambio, haz clic en el siguiente enlace para crear una nueva contraseña:</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetLink}' class='button'>
                            🔐 Restablecer Contraseña
                        </a>
                    </div>
                    
                    <p><strong>⏰ Este enlace expirará en 1 hora por seguridad.</strong></p>
                    
                    <p>Si no puedes hacer clic en el enlace, copia y pega la siguiente URL en tu navegador:</p>
                    <div class='url-box'>{$resetLink}</div>
                    
                    <hr style='border: none; border-top: 1px solid #dee2e6; margin: 30px 0;'>
                    
                    <h3 style='color: #dc3545;'>🛡️ ¿No solicitaste este cambio?</h3>
                    <p>Si no solicitaste restablecer tu contraseña, puedes ignorar este email. Tu contraseña permanecerá sin cambios.</p>
                    
                    <h4>💡 Consejos de Seguridad:</h4>
                    <ul>
                        <li>Usa contraseñas únicas y seguras</li>
                        <li>No compartas tus credenciales con nadie</li>
                        <li>Cierra sesión al usar computadoras públicas</li>
                        <li>Activa la autenticación de dos factores cuando esté disponible</li>
                    </ul>
                </div>
                
                <div class='footer'>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                    <p><strong>© 2025 MediBook - Sistema de Gestión Médica</strong></p>
                    <p>Si necesitas ayuda, contacta a: <a href='mailto:soporte@medibook.com'>soporte@medibook.com</a></p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getWelcomeTemplate($name)
    {
        return "
        <html>
        <head>
            <title>¡Bienvenido a MediBook!</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; padding: 20px; background: linear-gradient(135deg, #51cf66 0%, #69db7c 100%); color: white; border-radius: 10px; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 10px; border-left: 4px solid #51cf66; }
                .button { background: linear-gradient(45deg, #51cf66, #69db7c); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin: 0; font-size: 2.5rem;'>🏥 MediBook</h1>
                    <p style='margin: 5px 0 0 0; opacity: 0.9;'>¡Bienvenido a bordo!</p>
                </div>
                
                <div class='content'>
                    <h2 style='color: #2c3e50; margin-top: 0;'>¡Hola {$name}!</h2>
                    
                    <p>¡Bienvenido a <strong>MediBook</strong>! Tu cuenta ha sido creada exitosamente.</p>
                    
                    <p>Ahora puedes acceder a todas las funcionalidades de nuestro sistema de gestión médica:</p>
                    
                    <ul>
                        <li>📅 Programar y gestionar citas médicas</li>
                        <li>👥 Administrar pacientes y historiales</li>
                        <li>👨‍⚕️ Portal médico completo</li>
                        <li>📊 Reportes y analytics en tiempo real</li>
                    </ul>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='http://localhost/MediBook/' class='button'>
                            🚀 Comenzar Ahora
                        </a>
                    </div>
                    
                    <h4>💡 Próximos Pasos:</h4>
                    <ol>
                        <li>Completa tu perfil de usuario</li>
                        <li>Explora las diferentes secciones del sistema</li>
                        <li>Contacta a soporte si tienes alguna pregunta</li>
                    </ol>
                </div>
                
                <div class='footer'>
                    <p>Gracias por elegir MediBook</p>
                    <p><strong>© 2025 MediBook - Sistema de Gestión Médica</strong></p>
                    <p>¿Necesitas ayuda? Escríbenos: <a href='mailto:soporte@medibook.com'>soporte@medibook.com</a></p>
                </div>
            </div>
        </body>
        </html>";
    }
}
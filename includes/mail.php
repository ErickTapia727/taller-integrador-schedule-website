<?php
/**
 * Funciones para envío de correos electrónicos con PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/email.php';

/**
 * Configurar PHPMailer con SMTP
 * @return PHPMailer
 */
function getMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';
        
        // Configuración del remitente
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Desactivar verificación SSL en desarrollo (quitar en producción)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
    } catch (Exception $e) {
        error_log("Error configurando PHPMailer: " . $e->getMessage());
    }
    
    return $mail;
}

/**
 * Enviar correo genérico
 * @param string $toEmail Email del destinatario
 * @param string $toName Nombre del destinatario
 * @param string $subject Asunto del correo
 * @param string $message Mensaje en HTML
 * @return bool
 */
function sendEmail($toEmail, $toName, $subject, $message) {
    // Validar email
    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Email inválido: " . $toEmail);
        return false;
    }
    
    try {
        $mail = getMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $message;
        
        $sent = $mail->send();
        
        if (!$sent) {
            error_log("Error al enviar email a: " . $toEmail);
        }
        
        return $sent;
        
    } catch (Exception $e) {
        error_log("Error PHPMailer: " . $e->getMessage());
        return false;
    }
}

/**
 * Enviar correo de notificación de cancelación de cita
 * @param string $toEmail Email del destinatario
 * @param string $clientName Nombre del cliente
 * @param array $appointmentData Datos de la cita
 * @param string $adminNotes Notas del administrador
 * @return bool
 */
function sendCancellationEmail($toEmail, $clientName, $appointmentData, $adminNotes = '') {
    // Validar email
    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Email inválido: " . $toEmail);
        return false;
    }
    
    // Preparar asunto
    $subject = "Cita Cancelada - Dog Cute Spa";
    
    // Preparar mensaje
    $date = date('d/m/Y', strtotime($appointmentData['appointment_date']));
    $startTime = date('H:i', strtotime($appointmentData['start_time']));
    $endTime = date('H:i', strtotime($appointmentData['end_time']));
    $petName = htmlspecialchars($appointmentData['pet_name'] ?? 'su mascota');
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background-color: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; }
            .info-box { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #dc3545; }
            .notes-box { background-color: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; }
            .footer { background-color: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
            strong { color: #dc3545; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🐾 Cita Cancelada</h1>
            </div>
            <div class='content'>
                <p>Estimado/a <strong>" . htmlspecialchars($clientName) . "</strong>,</p>
                
                <p>Le informamos que su cita ha sido <strong>cancelada</strong> por el administrador.</p>
                
                <div class='info-box'>
                    <h3>Detalles de la cita cancelada:</h3>
                    <p><strong>📅 Fecha:</strong> {$date}</p>
                    <p><strong>🕐 Horario:</strong> {$startTime} - {$endTime}</p>
                    <p><strong>🐶 Mascota:</strong> {$petName}</p>
                </div>";
    
    if (!empty($adminNotes)) {
        $message .= "
                <div class='notes-box'>
                    <h3>Motivo de la cancelación:</h3>
                    <p>" . nl2br(htmlspecialchars($adminNotes)) . "</p>
                </div>";
    }
    
    $message .= "
                <p>Si tiene alguna duda o desea reagendar, puede contactarnos o crear una nueva cita desde su cuenta.</p>
                
                <p>Lamentamos los inconvenientes que esto pueda causar.</p>
                
                <p>Atentamente,<br><strong>Equipo Dog Cute Spa</strong></p>
            </div>
            <div class='footer'>
                <p>Dog Cute Spa - Cuidado profesional para tu mascota</p>
                <p>contacto@dogcutespa.cl</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    try {
        $mail = getMailer();
        $mail->addAddress($toEmail, $clientName);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $message;
        
        $sent = $mail->send();
        
        if (!$sent) {
            error_log("Error al enviar email a: " . $toEmail);
        }
        
        return $sent;
        
    } catch (Exception $e) {
        error_log("Error PHPMailer: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Enviar correo de notificación de cita completada
 * @param string $toEmail Email del destinatario
 * @param string $clientName Nombre del cliente
 * @param array $appointmentData Datos de la cita
 * @param string $adminNotes Notas del administrador
 * @return bool
 */
function sendCompletionEmail($toEmail, $clientName, $appointmentData, $adminNotes = '') {
    // Validar email
    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Email inválido: " . $toEmail);
        return false;
    }
    
    // Preparar asunto
    $subject = "Cita Completada - Dog Cute Spa";
    
    // Preparar mensaje
    $date = date('d/m/Y', strtotime($appointmentData['appointment_date']));
    $startTime = date('H:i', strtotime($appointmentData['start_time']));
    $endTime = date('H:i', strtotime($appointmentData['end_time']));
    $petName = htmlspecialchars($appointmentData['pet_name'] ?? 'su mascota');
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background-color: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; }
            .info-box { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; }
            .notes-box { background-color: #d1ecf1; padding: 15px; margin: 15px 0; border-left: 4px solid #17a2b8; }
            .footer { background-color: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
            strong { color: #28a745; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✅ Cita Completada</h1>
            </div>
            <div class='content'>
                <p>Estimado/a <strong>" . htmlspecialchars($clientName) . "</strong>,</p>
                
                <p>¡Gracias por confiar en nosotros! Su cita ha sido <strong>completada exitosamente</strong>.</p>
                
                <div class='info-box'>
                    <h3>Detalles de la cita:</h3>
                    <p><strong>📅 Fecha:</strong> {$date}</p>
                    <p><strong>🕐 Horario:</strong> {$startTime} - {$endTime}</p>
                    <p><strong>🐶 Mascota:</strong> {$petName}</p>
                </div>";
    
    if (!empty($adminNotes)) {
        $message .= "
                <div class='notes-box'>
                    <h3>Observaciones del servicio:</h3>
                    <p>" . nl2br(htmlspecialchars($adminNotes)) . "</p>
                </div>";
    }
    
    $message .= "
                <p>Esperamos que {$petName} haya disfrutado de nuestro servicio. ¡Nos encantaría verlos nuevamente pronto!</p>
                
                <p>Puede agendar una nueva cita desde su cuenta cuando lo desee.</p>
                
                <p>Saludos cordiales,<br><strong>Equipo Dog Cute Spa</strong></p>
            </div>
            <div class='footer'>
                <p>Dog Cute Spa - Cuidado profesional para tu mascota</p>
                <p>contacto@dogcutespa.cl</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    try {
        $mail = getMailer();
        $mail->addAddress($toEmail, $clientName);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $message;
        
        $sent = $mail->send();
        
        if (!$sent) {
            error_log("Error al enviar email a: " . $toEmail);
        }
        
        return $sent;
        
    } catch (Exception $e) {
        error_log("Error PHPMailer: " . $mail->ErrorInfo);
        return false;
    }
}

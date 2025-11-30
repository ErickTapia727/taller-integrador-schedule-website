<?php
/**
 * Script para enviar recordatorios automáticos de citas
 * Ejecutar mediante cron job: php /ruta/cron_recordatorios.php
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Appointment.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/includes/mail.php';

// Configuración: cuántos meses antes enviar el recordatorio
$meses_anticipacion = 2; // 2 meses antes

$appointmentModel = new Appointment();
$userModel = new User();

// Fecha de hoy
$fecha_hoy = date('Y-m-d');

echo "[" . date('Y-m-d H:i:s') . "] Buscando citas con recordatorio para hoy: $fecha_hoy\n";

// Obtener todas las citas que tienen recordatorio programado para hoy y aún no se han enviado
$query = "SELECT a.*, u.name, u.email, u.phone, p.name as pet_name, p.breed 
          FROM appointments a
          INNER JOIN users u ON a.user_id = u.id
          INNER JOIN pets p ON a.pet_id = p.id
          WHERE a.reminder_date = :fecha_hoy
          AND a.reminder_sent = FALSE
          AND a.status IN ('Pendiente', 'Confirmado')
          ORDER BY a.start_time";

$db = (new Database())->getConnection();
$stmt = $db->prepare($query);
$stmt->bindParam(':fecha_hoy', $fecha_hoy);
$stmt->execute();
$citas = $stmt->fetchAll();

echo "Citas con recordatorio para hoy: " . count($citas) . "\n\n";

// Enviar recordatorio por cada cita
foreach ($citas as $cita) {
    $nombre_cliente = $cita['name'];
    $email_cliente = $cita['email'];
    $nombre_mascota = $cita['pet_name'];
    $raza = $cita['breed'];
    $fecha = date('d/m/Y', strtotime($cita['appointment_date']));
    $hora = date('H:i', strtotime($cita['start_time']));
    $servicio = $cita['service'];
    
    // Calcular cuántos días faltan para la cita
    $dias_faltantes = floor((strtotime($cita['appointment_date']) - time()) / 86400);
    $texto_tiempo = $dias_faltantes > 60 ? "en aproximadamente 2 meses" : "en $dias_faltantes días";
    
    // Asunto del email
    $subject = "🐶 Recordatorio: Cita en Dog Cute Spa {$texto_tiempo}";
    
    // Cuerpo del email
    $message = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;'>
            <h1 style='color: white; margin: 0;'>🐶 Dog Cute Spa</h1>
            <p style='color: white; margin: 10px 0 0 0;'>Recordatorio de Cita</p>
        </div>
        
        <div style='padding: 30px; background: #f8f9fa;'>
            <h2 style='color: #333;'>¡Hola {$nombre_cliente}! 👋</h2>
            
            <p style='font-size: 16px; color: #555;'>
                Te recordamos que <strong>{$nombre_mascota}</strong> tiene una cita programada <strong>{$texto_tiempo}</strong>:
            </p>
            
            <div style='background: white; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #667eea;'>
                <p style='margin: 5px 0;'><strong>📅 Fecha:</strong> {$fecha}</p>
                <p style='margin: 5px 0;'><strong>⏰ Hora:</strong> {$hora}</p>
                <p style='margin: 5px 0;'><strong>🐕 Mascota:</strong> {$nombre_mascota} ({$raza})</p>
                <p style='margin: 5px 0;'><strong>✂️ Servicio:</strong> {$servicio}</p>
            </div>
            
            <div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                <p style='margin: 0; color: #856404;'>
                    <strong>⚠️ Importante:</strong> Si no puedes asistir, por favor avísanos con anticipación.
                </p>
            </div>
            
            <div style='text-align: center; margin: 30px 0;'>
                <p style='margin-bottom: 15px;'>📍 <strong>Dirección:</strong> Av. Apóstol Santiago 1437</p>
                <a href='https://wa.me/56953979347' style='display: inline-block; background: #25D366; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold;'>
                    💬 Contactar por WhatsApp
                </a>
            </div>
            
            <p style='color: #666; font-size: 14px; text-align: center; margin-top: 30px;'>
                ¡Esperamos ver a {$nombre_mascota} pronto! 🐾
            </p>
        </div>
        
        <div style='background: #333; padding: 20px; text-align: center; color: white; font-size: 12px;'>
            <p style='margin: 0;'>Dog Cute Spa - Atención personalizada y libre de estrés</p>
            <p style='margin: 5px 0;'>📞 +56 9 5397 9347 | 📍 Av. Apóstol Santiago 1437</p>
        </div>
    </div>
    ";
    
    try {
        $resultado = sendEmail($email_cliente, $nombre_cliente, $subject, $message);
        
        if ($resultado) {
            echo "✓ Recordatorio enviado a: {$nombre_cliente} ({$email_cliente}) - {$nombre_mascota}\n";
            
            // Marcar el recordatorio como enviado
            $update_query = "UPDATE appointments SET reminder_sent = TRUE WHERE id = :id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':id', $cita['id'], PDO::PARAM_INT);
            $update_stmt->execute();
        } else {
            echo "✗ Error al enviar a: {$email_cliente}\n";
        }
    } catch (Exception $e) {
        echo "✗ Excepción al enviar a {$email_cliente}: " . $e->getMessage() . "\n";
    }
    
    // Pequeña pausa entre emails para no saturar el servidor
    sleep(2);
}

echo "\n[" . date('Y-m-d H:i:s') . "] Proceso completado.\n";
?>

<?php
/**
 * Script de prueba para el sistema de envío de emails
 */

require_once __DIR__ . '/includes/mail.php';

echo "<h1>Test de Sistema de Emails</h1>";
echo "<hr>";

// Datos de prueba
$testEmail = "ericktapia693@gmail.com"; // ⚠️ CAMBIA ESTO POR TU EMAIL REAL
$clientName = "Juan Pérez";

$appointmentData = [
    'appointment_date' => '2025-12-01',
    'start_time' => '10:00:00',
    'end_time' => '11:00:00',
    'pet_name' => 'Firulais'
];

$adminNotes = "La mascota mostró ansiedad. Recomendamos venir con el juguete favorito la próxima vez.";

echo "<h2>1. Test Email de Cancelación</h2>";
echo "<p><strong>Enviando a:</strong> $testEmail</p>";

$cancelResult = sendCancellationEmail(
    $testEmail,
    $clientName,
    $appointmentData,
    $adminNotes
);

if ($cancelResult) {
    echo "<p style='color: green;'>✅ Email de cancelación enviado correctamente</p>";
} else {
    echo "<p style='color: red;'>❌ Error al enviar email de cancelación</p>";
    echo "<p>Posibles razones:</p>";
    echo "<ul>";
    echo "<li>El servidor no tiene configurado un servidor SMTP</li>";
    echo "<li>La función mail() está deshabilitada</li>";
    echo "<li>El email es inválido</li>";
    echo "</ul>";
}

echo "<hr>";

echo "<h2>2. Test Email de Completado</h2>";
echo "<p><strong>Enviando a:</strong> $testEmail</p>";

$completionResult = sendCompletionEmail(
    $testEmail,
    $clientName,
    $appointmentData,
    $adminNotes
);

if ($completionResult) {
    echo "<p style='color: green;'>✅ Email de completado enviado correctamente</p>";
} else {
    echo "<p style='color: red;'>❌ Error al enviar email de completado</p>";
}

echo "<hr>";

// Verificar configuración del servidor
echo "<h2>3. Información del Sistema</h2>";

echo "<h3>Configuración de PHP mail():</h3>";
echo "<ul>";
echo "<li><strong>sendmail_path:</strong> " . ini_get('sendmail_path') . "</li>";
echo "<li><strong>SMTP:</strong> " . ini_get('SMTP') . "</li>";
echo "<li><strong>smtp_port:</strong> " . ini_get('smtp_port') . "</li>";
echo "</ul>";

echo "<h3>Extensiones disponibles:</h3>";
echo "<ul>";
$extensions = get_loaded_extensions();
$mailExtensions = array_filter($extensions, function($ext) {
    return stripos($ext, 'mail') !== false || stripos($ext, 'imap') !== false;
});
if (count($mailExtensions) > 0) {
    foreach ($mailExtensions as $ext) {
        echo "<li>$ext</li>";
    }
} else {
    echo "<li>No se encontraron extensiones de mail</li>";
}
echo "</ul>";

echo "<hr>";
echo "<h2>4. Configuración</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8;'>";
echo "<h3>✅ PHPMailer instalado correctamente</h3>";
echo "<p><strong>Siguiente paso:</strong> Edita <code>config/email.php</code> con tus credenciales SMTP</p>";
echo "<h4>Para Gmail:</h4>";
echo "<ol>";
echo "<li>Activa verificación en 2 pasos: <a href='https://myaccount.google.com/security'>https://myaccount.google.com/security</a></li>";
echo "<li>Genera contraseña de aplicación: <a href='https://myaccount.google.com/apppasswords'>https://myaccount.google.com/apppasswords</a></li>";
echo "<li>Selecciona 'Correo' y 'Otro' como dispositivo</li>";
echo "<li>Copia la contraseña de 16 caracteres (sin espacios)</li>";
echo "<li>Pégala en <code>SMTP_PASSWORD</code> en config/email.php</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><strong>📧 Revisa la carpeta de SPAM si usaste un email real</strong></p>";
?>

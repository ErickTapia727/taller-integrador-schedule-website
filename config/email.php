<?php
/**
 * Configuración de Email con PHPMailer
 */

// Configuración SMTP - CAMBIA ESTOS VALORES
define('SMTP_HOST', 'smtp.gmail.com');  // Servidor SMTP
define('SMTP_PORT', 587);                 // Puerto (587 para TLS, 465 para SSL)
define('SMTP_USERNAME', 'ericktapia693@gmail.com'); // ⚠️ CAMBIA ESTO
define('SMTP_PASSWORD', 'qlhr hlmm sxzf oxef');     // ⚠️ CAMBIA ESTO (contraseña de aplicación de Gmail)
define('SMTP_SECURE', 'tls');            // tls o ssl
define('SMTP_FROM_EMAIL', 'noreply@dogcutespa.cl');
define('SMTP_FROM_NAME', 'Dog Cute Spa');

// Para Gmail:
// 1. Activa verificación en 2 pasos en tu cuenta
// 2. Ve a https://myaccount.google.com/apppasswords
// 3. Genera una "contraseña de aplicación"
// 4. Usa esa contraseña aquí (sin espacios)

// Alternativas gratuitas:
// - Mailgun: https://www.mailgun.com/ (12,000 emails/mes gratis)
// - SendGrid: https://sendgrid.com/ (100 emails/día gratis)
// - Mailtrap: https://mailtrap.io/ (solo para testing)

<?php
/**
 * Archivo de configuración global
 */

// Modo de desarrollo
define('DEBUG_MODE', true);

// Zona horaria
date_default_timezone_set('America/Santiago');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rutas del proyecto
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('MODELS_PATH', BASE_PATH . '/models');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');

// Validaciones
define('MIN_PASSWORD_LENGTH', 8);
define('MAX_PASSWORD_LENGTH', 50);

// Formato de teléfono chileno
define('PHONE_PATTERN', '/^\+56[0-9]{9}$/');

// Horario de atención
define('BUSINESS_START_HOUR', 8);
define('BUSINESS_END_HOUR', 17);
define('APPOINTMENT_DURATION_HOURS', 2);

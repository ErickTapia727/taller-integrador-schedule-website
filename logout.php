<?php
/**
 * Cerrar Sesión - Usando Backend
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

// Cerrar sesión usando la función del backend
logoutUser();

// Redirigir al usuario a la página de inicio de sesión
header('Location: login.php?mensaje=sesion_cerrada');
exit();
?>

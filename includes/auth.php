<?php
/**
 * Funciones de autenticación y sesión
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Verificar si el usuario está autenticado
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verificar si el usuario es administrador
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Administrador';
}

/**
 * Verificar si el usuario es cliente
 * @return bool
 */
function isClient() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Cliente';
}

/**
 * Obtener ID del usuario actual
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtener información del usuario actual
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'role' => $_SESSION['role'] ?? null,
    ];
}

/**
 * Iniciar sesión de usuario
 * @param array $user Datos del usuario
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['login_time'] = time();
    
    // Regenerar ID de sesión por seguridad
    session_regenerate_id(true);
}

/**
 * Cerrar sesión del usuario
 */
function logoutUser() {
    // Limpiar todas las variables de sesión
    $_SESSION = [];
    
    // Destruir la cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destruir la sesión
    session_destroy();
}

/**
 * Requerir autenticación (redirigir si no está autenticado)
 * @param string $redirectUrl URL de redirección
 */
function requireAuth($redirectUrl = '/login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Requerir rol de administrador
 * @param string $redirectUrl URL de redirección
 */
function requireAdmin($redirectUrl = '/inicio.php') {
    requireAuth();
    
    if (!isAdmin()) {
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Verificar tiempo de sesión (timeout)
 * @param int $maxLifetime Tiempo máximo en segundos (default: 2 horas)
 * @return bool
 */
function checkSessionTimeout($maxLifetime = 7200) {
    if (isset($_SESSION['login_time'])) {
        $sessionAge = time() - $_SESSION['login_time'];
        
        if ($sessionAge > $maxLifetime) {
            logoutUser();
            return false;
        }
    }
    
    return true;
}

/**
 * Generar token CSRF
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

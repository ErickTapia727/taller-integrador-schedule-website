<?php
/**
 * Procesar Login - Usando Backend con MySQL
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validators.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Obtener datos del formulario
$correo = isset($_POST['inputCorreo']) ? sanitizeInput($_POST['inputCorreo']) : '';
$contrasena = isset($_POST['inputContraseña']) ? $_POST['inputContraseña'] : '';

// Validaciones básicas
if (empty($correo) || empty($contrasena)) {
    $params = http_build_query([
        'error' => 'datos_incompletos',
        'correo' => $correo
    ]);
    header("Location: login.php?$params");
    exit();
}

if (!validateEmail($correo)) {
    $params = http_build_query([
        'error' => 'email_invalido',
        'correo' => $correo
    ]);
    header("Location: login.php?$params");
    exit();
}

try {
    // Autenticar usuario con la base de datos
    $userModel = new User();
    $user = $userModel->verifyCredentials($correo, $contrasena);
    
    if ($user) {
        // Autenticación exitosa
        loginUser($user);
        $userModel->updateLastLogin($user['id']);
        
        // Redirigir al dashboard
        header('Location: agenda.php');
        exit();
    } else {
        // Credenciales incorrectas
        $params = http_build_query([
            'error' => 'credenciales_incorrectas',
            'correo' => $correo
        ]);
        header("Location: login.php?$params");
        exit();
    }
    
} catch (Exception $e) {
    // Error del servidor
    error_log("Error en login: " . $e->getMessage());
    $params = http_build_query([
        'error' => 'error_servidor',
        'correo' => $correo
    ]);
    header("Location: login.php?$params");
    exit();
}

?>
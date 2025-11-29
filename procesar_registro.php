<?php
/**
 * Procesar Registro - Usando Backend con MySQL
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/includes/validators.php';

// Obtener los datos del formulario
$nombre = isset($_POST['inputNombre']) ? sanitizeInput($_POST['inputNombre']) : '';
$correo = isset($_POST['inputCorreo']) ? sanitizeInput($_POST['inputCorreo']) : '';
$contrasena = isset($_POST['inputContraseña']) ? $_POST['inputContraseña'] : '';
$confirmar_contrasena = isset($_POST['inputConfirmarContraseña']) ? $_POST['inputConfirmarContraseña'] : '';
$rut = isset($_POST['inputRut']) ? sanitizeInput($_POST['inputRut']) : '';
$telefono = isset($_POST['inputTelefono']) ? sanitizeInput($_POST['inputTelefono']) : '';

// Función auxiliar para redirigir con errores
function redirectWithError($error, $nombre, $correo, $rut, $telefono) {
    $params = http_build_query([
        'error' => $error,
        'nombre' => $nombre,
        'correo' => $correo,
        'rut' => $rut,
        'telefono' => $telefono
    ]);
    header("Location: signin.php?$params");
    exit();
}

// Validaciones básicas
if (empty($nombre) || empty($correo) || empty($contrasena) || empty($confirmar_contrasena) || empty($rut)) {
    redirectWithError('datos_incompletos', $nombre, $correo, $rut, $telefono);
}

if (!validateEmail($correo)) {
    redirectWithError('email_invalido', $nombre, $correo, $rut, $telefono);
}

// Validar que las contraseñas coincidan
if ($contrasena !== $confirmar_contrasena) {
    redirectWithError('contrasenas_no_coinciden', $nombre, $correo, $rut, $telefono);
}

// Validar criterios de contraseña
$passwordValidation = validatePassword($contrasena);
if (!$passwordValidation['valid']) {
    redirectWithError('contrasena_debil', $nombre, $correo, $rut, $telefono);
}

// Validar RUT
if (!validateRut($rut)) {
    redirectWithError('rut_invalido', $nombre, $correo, $rut, $telefono);
}

// Validar teléfono si se proporcionó
if (!empty($telefono)) {
    $telefonoFormateado = formatPhone($telefono);
    if (!validatePhone($telefonoFormateado)) {
        redirectWithError('telefono_invalido', $nombre, $correo, $rut, $telefono);
    }
    $telefono = $telefonoFormateado;
}

try {
    $userModel = new User();
    
    // Verificar si el email ya existe
    if ($userModel->findByEmail($correo)) {
        redirectWithError('email_existe', $nombre, $correo, $rut, $telefono);
    }
    
    // Verificar si el RUT ya existe
    if ($userModel->findByRut($rut)) {
        redirectWithError('rut_existe', $nombre, $correo, $rut, $telefono);
    }
    
    // Verificar si el teléfono ya existe (solo si se proporcionó)
    if (!empty($telefono) && $userModel->findByPhone($telefono)) {
        redirectWithError('telefono_existe', $nombre, $correo, $rut, $telefono);
    }
    
    // Crear el nuevo usuario
    $userData = [
        'name' => $nombre,
        'email' => $correo,
        'rut' => formatRut($rut),
        'phone' => $telefono,
        'password' => $contrasena, // Se hasheará automáticamente en createUser()
        'role' => 'Cliente'
    ];
    
    $userId = $userModel->createUser($userData);
    
    if ($userId) {
        // Registro exitoso
        header('Location: login.php?registro=exitoso');
        exit();
    } else {
        redirectWithError('error_servidor', $nombre, $correo, $rut, $telefono);
    }
    
} catch (Exception $e) {
    error_log("Error en registro: " . $e->getMessage());
    redirectWithError('error_servidor', $nombre, $correo, $rut, $telefono);
}

?>
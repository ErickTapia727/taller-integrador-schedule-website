<?php
// 1. Incluir nuestro archivo de funciones de la nueva carpeta
include 'includes/utils.php';

// 2. Obtener los datos del formulario de signin.php (defensivo)
$nombre = isset($_POST['inputNombre']) ? trim($_POST['inputNombre']) : '';
$correo = isset($_POST['inputCorreo']) ? trim($_POST['inputCorreo']) : '';
$contrasena = isset($_POST['inputContraseña']) ? $_POST['inputContraseña'] : '';
$confirmar_contrasena = isset($_POST['inputConfirmarContraseña']) ? $_POST['inputConfirmarContraseña'] : '';
$rut = isset($_POST['inputRut']) ? trim($_POST['inputRut']) : '';
$telefono = isset($_POST['inputTelefono']) ? trim($_POST['inputTelefono']) : '';

// Validaciones básicas
if ($nombre === '' || $correo === '' || $contrasena === '' || $confirmar_contrasena === '' || $rut === '') {
    $params = http_build_query([
        'error' => 'datos_incompletos',
        'nombre' => $nombre,
        'correo' => $correo,
        'rut' => $rut,
        'telefono' => $telefono
    ]);
    header("Location: signin.php?$params");
    exit();
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $params = http_build_query([
        'error' => 'email_invalido',
        'nombre' => $nombre,
        'correo' => $correo,
        'rut' => $rut,
        'telefono' => $telefono
    ]);
    header("Location: signin.php?$params");
    exit();
}

// Validar que las contraseñas coincidan
if ($contrasena !== $confirmar_contrasena) {
    $params = http_build_query([
        'error' => 'contrasenas_no_coinciden',
        'nombre' => $nombre,
        'correo' => $correo,
        'rut' => $rut,
        'telefono' => $telefono
    ]);
    header("Location: signin.php?$params");
    exit();
}

// 3. Validar criterios de contraseña
function validarCriteriosContrasena($contrasena) {
    // Mínimo 8 caracteres
    if (strlen($contrasena) < 8) {
        return false;
    }
    
    // Al menos una mayúscula y una minúscula
    if (!preg_match('/[A-Z]/', $contrasena) || !preg_match('/[a-z]/', $contrasena)) {
        return false;
    }
    
    // Al menos un número o símbolo especial
    if (!preg_match('/[0-9!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $contrasena)) {
        return false;
    }
    
    return true;
}

// Validar formato de teléfono chileno
function validarTelefonoChileno($telefono) {
    // Formato: +56 9 XXXX XXXX
    return preg_match('/^\+56 9 \d{4} \d{4}$/', $telefono);
}

// Validar teléfono
if (!empty($telefono) && !validarTelefonoChileno($telefono)) {
    $params = http_build_query([
        'error' => 'telefono_invalido',
        'nombre' => $nombre,
        'correo' => $correo,
        'rut' => $rut,
        'telefono' => $telefono
    ]);
    header("Location: signin.php?$params");
    exit();
}

if (!validarCriteriosContrasena($contrasena)) {
    $params = http_build_query([
        'error' => 'contrasena_debil',
        'nombre' => $nombre,
        'correo' => $correo,
        'rut' => $rut,
        'telefono' => $telefono
    ]);
    header("Location: signin.php?$params");
    exit();
}

// 4. ¡Validar el RUT!
if (!validarRut($rut)) {
    // El RUT es inválido.
    // Devolvemos al usuario al formulario de registro con un mensaje de error.
    $params = http_build_query([
        'error' => 'rut_invalido',
        'nombre' => $nombre,
        'correo' => $correo,
        'rut' => $rut,
        'telefono' => $telefono
    ]);
    header("Location: signin.php?$params");
    exit();
}

// 4. (Lógica de Base de Datos - SIMULADA)
// Aquí deberías:
// a) Verificar si el $correo o el $rut YA EXISTEN en tu base de datos.
//    Si existen -> header('Location: signin.php?error=usuario_existe'); exit();
//
// b) Hashear la contraseña (¡MUY IMPORTANTE!)
//    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
//
// c) Guardar el nuevo usuario en la base de datos
//    INSERT INTO usuarios (nombre, correo, rut, telefono, contrasena_hash, rol) 
//    VALUES (?, ?, ?, ?, ?, 'client');

// SIMULACIÓN: Verificar usuarios existentes en archivo persistente
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cargar usuarios existentes desde archivo
$usuarios_file = __DIR__ . '/temp_users.json';
$usuarios_existentes = [];

if (file_exists($usuarios_file)) {
    $usuarios_existentes = json_decode(file_get_contents($usuarios_file), true) ?: [];
}

// Incluir usuarios demo para verificación de duplicados
$usuarios_demo = [
    [
        'correo' => 'admin@example.com', 
        'rut' => '11111111-1',
        'nombre' => 'Administrador del Sistema'
    ],
    [
        'correo' => 'cliente@example.com', 
        'rut' => '22222222-2',
        'nombre' => 'Cliente Demo'
    ]
];

$todos_usuarios = array_merge($usuarios_demo, $usuarios_existentes);

// Comprobar si el correo, RUT o nombre ya existen
foreach ($todos_usuarios as $usuario) {
    if ($usuario['correo'] === $correo) {
        $params = http_build_query([
            'error' => 'email_existe',
            'nombre' => $nombre,
            'correo' => $correo,
            'rut' => $rut,
            'telefono' => $telefono
        ]);
        header("Location: signin.php?$params");
        exit();
    }
    if ($usuario['rut'] === $rut) {
        $params = http_build_query([
            'error' => 'rut_existe',
            'nombre' => $nombre,
            'correo' => $correo,
            'rut' => $rut,
            'telefono' => $telefono
        ]);
        header("Location: signin.php?$params");
        exit();
    }
    // Nueva validación para nombres duplicados
    if (isset($usuario['nombre']) && strtolower(trim($usuario['nombre'])) === strtolower(trim($nombre))) {
        $params = http_build_query([
            'error' => 'nombre_existe',
            'nombre' => $nombre,
            'correo' => $correo,
            'rut' => $rut,
            'telefono' => $telefono
        ]);
        header("Location: signin.php?$params");
        exit();
    }
}

// Registrar nuevo usuario
$nuevo_usuario = [
    'id' => count($usuarios_existentes) + 100, // ID único
    'nombre' => $nombre,
    'correo' => $correo,
    'rut' => $rut,
    'telefono' => $telefono,
    'password' => $contrasena, // En producción: password_hash($contrasena, PASSWORD_DEFAULT)
    'role' => 'client',
    'fecha_registro' => date('Y-m-d H:i:s')
];

// Añadir a la lista y guardar en archivo
$usuarios_existentes[] = $nuevo_usuario;
file_put_contents($usuarios_file, json_encode($usuarios_existentes, JSON_PRETTY_PRINT));

// 5. Redirigir al login con mensaje de éxito
// El usuario ahora puede iniciar sesión con su nueva cuenta.
header('Location: login.php?registro=exitoso');
exit();

?>
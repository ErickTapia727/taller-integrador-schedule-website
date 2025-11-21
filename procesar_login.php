<?php
// 1. INICIAR LA SESIÓN
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// 3. Obtener datos del formulario de login.php
$correo = isset($_POST['inputCorreo']) ? trim($_POST['inputCorreo']) : '';
$contrasena = isset($_POST['inputContraseña']) ? $_POST['inputContraseña'] : '';

// Validaciones básicas
if ($correo === '' || $contrasena === '') {
    $params = http_build_query([
        'error' => 'datos_incompletos',
        'correo' => $correo
    ]);
    header("Location: login.php?$params");
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $params = http_build_query([
        'error' => 'email_invalido',
        'correo' => $correo
    ]);
    header("Location: login.php?$params");
    exit();
}

// 4. SIMULACIÓN DE AUTENTICACIÓN
// En producción: consultar base de datos con password_verify()
$usuarios_demo = [
    [
        'id' => 1,
        'nombre' => 'Administrador del Sistema',
        'correo' => 'admin@example.com',
        'role' => 'admin',
        'password' => 'Admin123!' // Cumple criterios: 8+ chars, mayús/minús, número y símbolo
    ],
    [
        'id' => 2, 
        'nombre' => 'Cliente Demo',
        'correo' => 'cliente@example.com',
        'role' => 'client',
        'password' => 'Cliente123!' // Cumple criterios: 8+ chars, mayús/minús, número y símbolo
    ]
];

// Cargar usuarios registrados desde archivo persistente
$usuarios_file = __DIR__ . '/temp_users.json';
if (file_exists($usuarios_file)) {
    $usuarios_guardados = json_decode(file_get_contents($usuarios_file), true);
    if ($usuarios_guardados) {
        $usuarios_demo = array_merge($usuarios_demo, $usuarios_guardados);
    }
}

// Buscar usuario por correo
$usuario_encontrado = null;
foreach ($usuarios_demo as $usuario) {
    if ($usuario['correo'] === $correo) {
        $usuario_encontrado = $usuario;
        break;
    }
}

// Verificar credenciales
if (!$usuario_encontrado || $usuario_encontrado['password'] !== $contrasena) {
    // Credenciales incorrectas
    $params = http_build_query([
        'error' => 'credenciales_incorrectas',
        'correo' => $correo
    ]);
    header("Location: login.php?$params");
    exit();
}

// 5. AUTENTICACIÓN EXITOSA - Crear sesión
$_SESSION['user_id'] = $usuario_encontrado['id'];
$_SESSION['user_name'] = $usuario_encontrado['nombre'];
$_SESSION['user_role'] = $usuario_encontrado['role'];

// 6. Redirigir al dashboard principal
header('Location: agenda.php');
exit();

?>
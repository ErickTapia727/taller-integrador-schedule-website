<?php
// 1. Iniciar la sesión para poder acceder a ella.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Destruir todas las variables de sesión.
$_SESSION = array();

// 3. Si se desea destruir la sesión completamente, borre también la cookie de sesión.
// Nota: ¡Esto destruirá la sesión, y no solo los datos de la sesión!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finalmente, destruir la sesión.
session_destroy();

// 5. Redirigir al usuario a la página de inicio de sesión.
header('Location: login.php?mensaje=sesion_cerrada');
exit();
?>
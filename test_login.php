<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/User.php';

$userModel = new User();

$email = 'admin@dogcutespa.cl';
$password = 'Admin123!';

echo "Probando login con:\n";
echo "Email: $email\n";
echo "Password: $password\n\n";

$user = $userModel->findByEmail($email);

if ($user) {
    echo "Usuario encontrado:\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Nombre: " . $user['name'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Password Hash: " . $user['password'] . "\n\n";
    
    // Verificar password
    if (password_verify($password, $user['password'])) {
        echo "✓ Contraseña CORRECTA\n";
    } else {
        echo "✗ Contraseña INCORRECTA\n";
        echo "\nGenerando nuevo hash...\n";
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        echo "Nuevo hash: $newHash\n";
    }
} else {
    echo "✗ Usuario NO encontrado\n";
}
?>

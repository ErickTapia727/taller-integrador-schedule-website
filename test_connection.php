<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    echo "✓ Conexión exitosa a la base de datos\n";
    echo "✓ Base de datos: taller_integrador_db\n";
    
    // Verificar tablas
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\n✓ Tablas creadas:\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    // Verificar usuarios de prueba
    $stmt = $conn->query("SELECT name, email, role FROM users");
    $users = $stmt->fetchAll();
    
    echo "\n✓ Usuarios de prueba:\n";
    foreach ($users as $user) {
        echo "  - {$user['name']} ({$user['email']}) - {$user['role']}\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>

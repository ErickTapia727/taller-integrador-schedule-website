<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

echo "<h2>Debug de búsqueda - erick</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // 1. Ver todos los usuarios
    echo "<h3>Usuarios en el sistema:</h3>";
    $stmt = $db->query("SELECT id, name, email, rut FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Email</th><th>RUT</th></tr>";
    foreach ($users as $user) {
        echo "<tr><td>{$user['id']}</td><td>{$user['name']}</td><td>{$user['email']}</td><td>{$user['rut']}</td></tr>";
    }
    echo "</table>";
    
    // 2. Ver todas las citas con joins
    echo "<h3>Todas las citas (últimas 20):</h3>";
    $query = "SELECT a.id, u.name as cliente, p.name as mascota, a.appointment_date, a.status 
              FROM appointments a 
              JOIN users u ON a.user_id = u.id 
              JOIN pets p ON a.pet_id = p.id 
              ORDER BY a.id DESC LIMIT 20";
    $stmt = $db->query($query);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($appointments) > 0) {
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Cliente</th><th>Mascota</th><th>Fecha</th><th>Estado</th></tr>";
        foreach ($appointments as $apt) {
            echo "<tr><td>{$apt['id']}</td><td>{$apt['cliente']}</td><td>{$apt['mascota']}</td><td>{$apt['appointment_date']}</td><td>{$apt['status']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay citas en la base de datos</p>";
    }
    
    // 3. Buscar específicamente "erick"
    echo "<h3>Búsqueda con LIKE '%erick%':</h3>";
    $searchTerm = '%erick%';
    $query = "SELECT a.id, u.name as cliente, p.name as mascota, a.appointment_date, a.status 
              FROM appointments a 
              JOIN users u ON a.user_id = u.id 
              JOIN pets p ON a.pet_id = p.id 
              WHERE LOWER(u.name) LIKE LOWER(?) OR LOWER(p.name) LIKE LOWER(?) OR LOWER(u.email) LIKE LOWER(?) OR u.rut LIKE ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($results) > 0) {
        echo "<p style='color: green;'>Encontrados: " . count($results) . " resultados</p>";
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Cliente</th><th>Mascota</th><th>Fecha</th><th>Estado</th></tr>";
        foreach ($results as $apt) {
            echo "<tr><td>{$apt['id']}</td><td>{$apt['cliente']}</td><td>{$apt['mascota']}</td><td>{$apt['appointment_date']}</td><td>{$apt['status']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No se encontraron resultados para 'erick'</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

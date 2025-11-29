<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Appointment.php';

$model = new Appointment();

echo "<h2>Test Simple de Búsqueda</h2>";

// Test 1: Sin filtros
echo "<h3>1. Sin filtros (todas las citas):</h3>";
try {
    $all = $model->getAllAppointmentsHistory();
    echo "Total: " . count($all) . "<br>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test 2: Con búsqueda "erick"
echo "<h3>2. Búsqueda con 'erick':</h3>";
try {
    $results = $model->searchAppointments('erick', null, null);
    echo "Resultados: " . count($results) . "<br>";
    if (count($results) > 0) {
        echo "<pre>" . print_r($results, true) . "</pre>";
    } else {
        echo "<p style='color:red'>No se encontraron resultados</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 3: Con búsqueda "e"
echo "<h3>3. Búsqueda con 'e':</h3>";
try {
    $results2 = $model->searchAppointments('e', null, null);
    echo "Resultados: " . count($results2) . "<br>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test 4: Consulta SQL directa
echo "<h3>4. Consulta SQL directa:</h3>";
try {
    require_once __DIR__ . '/config/database.php';
    $db = new Database();
    $conn = $db->getConnection();

    $searchTerm = '%erick%';
    $sql = "SELECT a.id, u.name as cliente, p.name as mascota 
            FROM appointments a 
            JOIN users u ON a.user_id = u.id 
            JOIN pets p ON a.pet_id = p.id 
            WHERE u.name LIKE ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$searchTerm]);
    $directResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Resultados consulta directa: " . count($directResults) . "<br>";
    if (count($directResults) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Cliente</th><th>Mascota</th></tr>";
        foreach ($directResults as $row) {
            echo "<tr><td>{$row['id']}</td><td>{$row['cliente']}</td><td>{$row['mascota']}</td></tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "<h3>FIN DEL TEST</h3>";
?>

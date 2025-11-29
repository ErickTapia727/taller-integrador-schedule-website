<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Appointment.php';

$model = new Appointment();

echo "<h2>Test de Prioridad - búsqueda 'c'</h2>";

$results = $model->searchAppointments('c', null, null);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Cliente</th><th>¿Nombre tiene c?</th><th>Mascota</th><th>¿Mascota tiene c?</th><th>Email</th><th>¿Email tiene c?</th><th>Priority</th></tr>";
foreach ($results as $r) {
    $nombreTieneC = stripos($r['owner_name'], 'c') !== false ? 'SÍ' : 'NO';
    $mascotaTieneC = stripos($r['pet_name'], 'c') !== false ? 'SÍ' : 'NO';
    $emailTieneC = stripos($r['owner_email'], 'c') !== false ? 'SÍ' : 'NO';
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($r['owner_name']) . "</td>";
    echo "<td><strong>" . $nombreTieneC . "</strong></td>";
    echo "<td>" . htmlspecialchars($r['pet_name']) . "</td>";
    echo "<td><strong>" . $mascotaTieneC . "</strong></td>";
    echo "<td>" . htmlspecialchars($r['owner_email']) . "</td>";
    echo "<td><strong>" . $emailTieneC . "</strong></td>";
    echo "<td><strong>" . (isset($r['priority']) ? $r['priority'] : 'N/A') . "</strong></td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Lógica esperada:</h3>";
echo "<p>- Si el <strong>nombre</strong> tiene 'c' → Priority = 1</p>";
echo "<p>- Si solo la <strong>mascota</strong> tiene 'c' → Priority = 2</p>";
echo "<p>- Si solo el <strong>email</strong> tiene 'c' → Priority = 3</p>";
echo "<p>- Si solo el <strong>RUT</strong> tiene 'c' → Priority = 4</p>";
?>


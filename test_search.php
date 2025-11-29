<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Appointment.php';

$appointmentModel = new Appointment();

echo "<h2>Test de búsqueda: Claudio</h2>";

// Probar búsqueda
$searchTerm = "Claudio";
$results = $appointmentModel->searchAppointments($searchTerm, null, null);

echo "<p>Término de búsqueda: <strong>" . htmlspecialchars($searchTerm) . "</strong></p>";
echo "<p>Resultados encontrados: <strong>" . count($results) . "</strong></p>";

if (!empty($results)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Cliente</th><th>Mascota</th><th>Fecha</th><th>Estado</th></tr>";
    foreach ($results as $apt) {
        echo "<tr>";
        echo "<td>" . $apt['id'] . "</td>";
        echo "<td>" . htmlspecialchars($apt['owner_name']) . "</td>";
        echo "<td>" . htmlspecialchars($apt['pet_name']) . "</td>";
        echo "<td>" . $apt['appointment_date'] . "</td>";
        echo "<td>" . $apt['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No se encontraron resultados</p>";
}

// Probar sin filtros
echo "<hr><h2>Todas las citas</h2>";
$allResults = $appointmentModel->getAllAppointmentsHistory();
echo "<p>Total de citas: <strong>" . count($allResults) . "</strong></p>";

if (!empty($allResults)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Cliente</th><th>Mascota</th><th>Fecha</th><th>Estado</th></tr>";
    foreach ($allResults as $apt) {
        echo "<tr>";
        echo "<td>" . $apt['id'] . "</td>";
        echo "<td>" . htmlspecialchars($apt['owner_name']) . "</td>";
        echo "<td>" . htmlspecialchars($apt['pet_name']) . "</td>";
        echo "<td>" . $apt['appointment_date'] . "</td>";
        echo "<td>" . $apt['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>

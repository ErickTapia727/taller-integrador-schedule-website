<?php
/**
 * Descargar historial de citas en formato CSV
 */

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Appointment.php';

$appointmentModel = new Appointment();
$appointments = [];
$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

try {
    if ($isAdmin) {
        // Admin: obtener todas las citas con filtros si existen
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
        $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : null;
        $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : null;
        
        if (!empty($searchTerm) || $dateFrom || $dateTo) {
            $appointments = $appointmentModel->searchAppointments($searchTerm, $dateFrom, $dateTo);
        } else {
            $appointments = $appointmentModel->getAllAppointmentsHistory();
        }
    } else {
        // Cliente: solo sus citas
        $userId = intval($_SESSION['user_id']);
        $appointments = $appointmentModel->getAppointmentsByUserId($userId);
    }
} catch (Exception $e) {
    error_log("Error al cargar historial para descarga: " . $e->getMessage());
    $appointments = [];
}

// Configurar headers para descarga CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="historial_citas_' . date('Y-m-d_His') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Crear el output
$output = fopen('php://output', 'w');

// BOM para UTF-8 (para que Excel abra correctamente los acentos)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Escribir encabezados según el rol
if ($isAdmin) {
    fputcsv($output, [
        'ID Cita',
        'Cliente',
        'RUT Cliente',
        'Email Cliente',
        'Teléfono Cliente',
        'Mascota',
        'Especie',
        'Raza',
        'Edad Mascota',
        'Peso Mascota (kg)',
        'Servicio',
        'Fecha Cita',
        'Hora Inicio',
        'Hora Fin',
        'Estado',
        'Notas Cliente',
        'Notas Admin',
        'Fecha Creación'
    ], ';');
} else {
    fputcsv($output, [
        'ID Cita',
        'Mascota',
        'Especie',
        'Raza',
        'Servicio',
        'Fecha Cita',
        'Hora Inicio',
        'Hora Fin',
        'Estado',
        'Notas'
    ], ';');
}

// Escribir datos
foreach ($appointments as $apt) {
    if ($isAdmin) {
        // Vista de administrador con todos los datos
        fputcsv($output, [
            $apt['id'],
            $apt['owner_name'] ?? 'N/A',
            $apt['owner_rut'] ?? 'N/A',
            $apt['owner_email'] ?? 'N/A',
            $apt['owner_phone'] ?? 'N/A',
            $apt['pet_name'] ?? 'N/A',
            $apt['pet_species'] ?? 'N/A',
            $apt['pet_breed'] ?? 'N/A',
            $apt['pet_age'] ? $apt['pet_age'] . ' años' : 'N/A',
            $apt['pet_weight'] ?? 'N/A',
            $apt['service'],
            date('d/m/Y', strtotime($apt['appointment_date'])),
            date('H:i', strtotime($apt['start_time'])),
            date('H:i', strtotime($apt['end_time'])),
            $apt['status'],
            $apt['notes'] ?? '',
            $apt['admin_notes'] ?? '',
            date('d/m/Y H:i', strtotime($apt['created_at']))
        ], ';');
    } else {
        // Vista de cliente
        fputcsv($output, [
            $apt['id'],
            $apt['pet_name'] ?? 'N/A',
            $apt['pet_species'] ?? 'N/A',
            $apt['pet_breed'] ?? 'N/A',
            $apt['service'],
            date('d/m/Y', strtotime($apt['appointment_date'])),
            date('H:i', strtotime($apt['start_time'])),
            date('H:i', strtotime($apt['end_time'])),
            $apt['status'],
            $apt['notes'] ?? ''
        ], ';');
    }
}

fclose($output);
exit();

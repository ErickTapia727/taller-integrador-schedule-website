<?php
// 1. Set the page context variables
$active_link = 'history';

// 2. Check role from session instead of file parsing
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_admin_check = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

if ($is_admin_check) {
    $page_title = "Historial de Citas";
} else {
    $page_title = "Mi Historial";
}

// 3. Include required models
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Appointment.php';

// 4. Get appointments from database
$appointmentModel = new Appointment();
$appointments = [];

// Handle search filters (for admin)
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : null;

try {
    if ($is_admin_check) {
        // Admin: Get all appointments or filtered results
        if (!empty($searchTerm) || $dateFrom || $dateTo) {
            error_log("Historial - Búsqueda con filtros: search='$searchTerm', dateFrom='$dateFrom', dateTo='$dateTo'");
            $appointments = $appointmentModel->searchAppointments($searchTerm, $dateFrom, $dateTo);
            error_log("Historial - Resultados encontrados: " . count($appointments));
        } else {
            $appointments = $appointmentModel->getAllAppointmentsHistory();
            error_log("Historial - Total de citas sin filtros: " . count($appointments));
        }
    } else {
        // Client: Get only their appointments
        $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $appointments = $appointmentModel->getAppointmentsByUserId($userId);
    }
} catch (Exception $e) {
    error_log("Error al cargar historial: " . $e->getMessage());
    $appointments = [];
}

// 3. Include the header
// The header will read $page_title, $active_link, and define the REAL $is_admin
include 'layout/header.php';
?>

<!-- 4. Page Content -->
<div class="container-fluid">

    <?php if ($is_admin): ?>
    <!-- =================================================================== -->
    <!-- =================== ADMINISTRATOR VIEW ============================ -->
    <!-- =================================================================== -->
    
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0 fw-bold">Historial de Citas</h2>
                <?php
                // Build download URL with current filters
                $downloadParams = [];
                if (!empty($searchTerm)) $downloadParams['search'] = urlencode($searchTerm);
                if (!empty($dateFrom)) $downloadParams['date_from'] = $dateFrom;
                if (!empty($dateTo)) $downloadParams['date_to'] = $dateTo;
                $downloadUrl = 'download_history.php' . (!empty($downloadParams) ? '?' . http_build_query($downloadParams) : '');
                ?>
                <a href="<?php echo htmlspecialchars($downloadUrl); ?>" class="btn btn-outline-danger">
                    <i class="bi bi-download me-2"></i>
                    Descargar Historial
                </a>
            </div>
            <!-- Admin Filters -->
            <form method="GET" action="history.php" id="filterForm">
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por cliente, mascota, email o RUT..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" id="dateFrom" value="<?php echo htmlspecialchars($dateFrom ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" id="dateTo" value="<?php echo htmlspecialchars($dateTo ?? ''); ?>">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn flex-fill" type="submit" style="background-color: var(--active-link-color); color: white;">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <a href="history.php" class="btn btn-outline-secondary flex-fill">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Cliente</th>
                            <th scope="col">Mascota</th>
                            <th scope="col">Servicio</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Hora</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <?php if (!empty($searchTerm) || $dateFrom || $dateTo): ?>
                                        <i class="bi bi-search"></i><br>
                                        No se encontraron citas con los filtros aplicados.<br>
                                        <a href="history.php" class="btn btn-sm btn-outline-secondary mt-2">Limpiar filtros</a>
                                    <?php else: ?>
                                        No hay citas registradas en el historial.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $appointment): 
                                // Format date
                                $dateFormatted = date('d/m/Y', strtotime($appointment['appointment_date']));
                                $timeFormatted = date('H:i', strtotime($appointment['start_time']));
                                
                                // Determine badge class based on status
                                $badgeClass = 'bg-secondary';
                                switch($appointment['status']) {
                                    case 'Completado':
                                        $badgeClass = 'bg-success';
                                        break;
                                    case 'Cancelado':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    case 'Confirmado':
                                        $badgeClass = 'bg-info';
                                        break;
                                    case 'Pendiente':
                                        $badgeClass = 'bg-warning';
                                        break;
                                }
                            ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($appointment['owner_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['pet_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['service']); ?></td>
                                    <td><?php echo $dateFormatted; ?></td>
                                    <td><?php echo $timeFormatted; ?></td>
                                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($appointment['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- =================================================================== -->
    <!-- ======================= CLIENT VIEW =============================== -->
    <!-- =================================================================== -->
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold">Mi Historial de Citas</h2>
        <!-- Optional: A download button -->
        <?php
        // Build download URL with current filters
        $downloadParams = [];
        if (!empty($searchTerm)) $downloadParams['search'] = urlencode($searchTerm);
        if (!empty($dateFrom)) $downloadParams['date_from'] = $dateFrom;
        if (!empty($dateTo)) $downloadParams['date_to'] = $dateTo;
        $downloadUrl = 'download_history.php' . (!empty($downloadParams) ? '?' . http_build_query($downloadParams) : '');
        ?>
        <a href="<?php echo htmlspecialchars($downloadUrl); ?>" class="btn btn-outline-danger">
            <i class="bi bi-download me-2"></i>
            Descargar Historial
        </a>
    </div>

    <!-- Client History Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Mascota</th>
                            <th scope="col">Servicio</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Hora</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No tienes citas registradas en el historial.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $appointment): 
                                // Format date
                                $dateFormatted = date('d/m/Y', strtotime($appointment['appointment_date']));
                                $timeFormatted = date('H:i', strtotime($appointment['start_time']));
                                
                                // Determine badge class based on status
                                $badgeClass = 'bg-secondary';
                                switch($appointment['status']) {
                                    case 'Completado':
                                        $badgeClass = 'bg-success';
                                        break;
                                    case 'Cancelado':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    case 'Confirmado':
                                        $badgeClass = 'bg-info';
                                        break;
                                    case 'Pendiente':
                                        $badgeClass = 'bg-warning';
                                        break;
                                }
                            ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($appointment['pet_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['service']); ?></td>
                                    <td><?php echo $dateFormatted; ?></td>
                                    <td><?php echo $timeFormatted; ?></td>
                                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($appointment['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php endif; ?>

</div>
<!-- === END PAGE CONTENT === -->


<!-- 5. Page-Specific JavaScript (if any) -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // We can add filtering logic here later
        const isAdmin = document.body.dataset.isAdmin === 'true';

        if (isAdmin) {
            console.log("Modo Administrador de Historial activo.");
        } else {
            console.log("Modo Cliente de Historial activo.");
        }
    });
</script>


<?php
// 6. Include the standard footer
include 'layout/footer.php';
?>
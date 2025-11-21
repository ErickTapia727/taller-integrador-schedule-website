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
            <h2 class="h5 mb-0 fw-bold">Filtrar Historial</h2>
            <!-- Admin Filters -->
            <div class="row g-3 align-items-center mt-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" placeholder="Buscar por cliente, mascota o RUT...">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="dateFrom">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="dateTo">
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn" type="button" style="background-color: var(--active-link-color); color: white;">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
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
                            <th scope="col">Costo</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Initialize appointment history storage if not exists
                        if (!isset($_SESSION['appointment_history'])) {
                            $_SESSION['appointment_history'] = [];
                        }
                        
                        if (empty($_SESSION['appointment_history'])): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No hay citas registradas en el historial.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($_SESSION['appointment_history'] as $appointment): 
                                // Format date from YYYY-MM-DD to dd/mm/yyyy
                                $dateFormatted = '';
                                if (!empty($appointment['date'])) {
                                    $dateParts = explode('-', $appointment['date']);
                                    if (count($dateParts) === 3) {
                                        $dateFormatted = $dateParts[2] . '/' . $dateParts[1] . '/' . $dateParts[0];
                                    }
                                }
                                
                                // Determine badge class based on status
                                $badgeClass = 'bg-secondary';
                                switch(strtolower($appointment['status'] ?? 'pendiente')) {
                                    case 'completado':
                                        $badgeClass = 'bg-success';
                                        break;
                                    case 'cancelado':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    case 'en progreso':
                                        $badgeClass = 'bg-warning';
                                        break;
                                }
                            ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($appointment['client_name'] ?? 'Cliente no especificado'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['pet_name'] ?? 'Mascota sin nombre'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['service'] ?? 'Servicio no especificado'); ?></td>
                                    <td><?php echo htmlspecialchars($dateFormatted); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['time'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['cost'] ?? '$0'); ?></td>
                                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst(htmlspecialchars($appointment['status'] ?? 'Pendiente')); ?></span></td>
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
        <button class="btn btn-outline-danger">
            <i class="bi bi-download me-2"></i>
            Descargar Historial
        </button>
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
                            <th scope="col">Costo</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Initialize appointment history storage if not exists
                        if (!isset($_SESSION['appointment_history'])) {
                            $_SESSION['appointment_history'] = [];
                        }
                        
                        // Get current user's appointments from session
                        $uid = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
                        $userAppointments = [];
                        
                        foreach ($_SESSION['appointment_history'] as $appointment) {
                            if (isset($appointment['client_id']) && $appointment['client_id'] === $uid) {
                                $userAppointments[] = $appointment;
                            }
                        }
                        
                        if (empty($userAppointments)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No tienes citas registradas en el historial.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($userAppointments as $appointment): 
                                // Format date from YYYY-MM-DD to dd/mm/yyyy
                                $dateFormatted = '';
                                if (!empty($appointment['date'])) {
                                    $dateParts = explode('-', $appointment['date']);
                                    if (count($dateParts) === 3) {
                                        $dateFormatted = $dateParts[2] . '/' . $dateParts[1] . '/' . $dateParts[0];
                                    }
                                }
                                
                                // Determine badge class based on status
                                $badgeClass = 'bg-secondary';
                                switch(strtolower($appointment['status'] ?? 'pendiente')) {
                                    case 'completado':
                                        $badgeClass = 'bg-success';
                                        break;
                                    case 'cancelado':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    case 'en progreso':
                                        $badgeClass = 'bg-warning';
                                        break;
                                }
                            ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($appointment['pet_name'] ?? 'Mascota sin nombre'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['service'] ?? 'Servicio no especificado'); ?></td>
                                    <td><?php echo htmlspecialchars($dateFormatted); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['time'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['cost'] ?? '$0'); ?></td>
                                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst(htmlspecialchars($appointment['status'] ?? 'Pendiente')); ?></span></td>
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
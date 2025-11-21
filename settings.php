<?php
// 1. Set page context variables
$active_link = 'settings';
$page_title = "Ajustes";

// 2. Include the header (which defines $is_admin)
include 'layout/header.php';
?>

<!-- 3. Page Content -->
<div class="container-fluid">
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Page Header -->
            <div class="mb-5 border-bottom pb-3">
                <h2 class="h3 fw-bold">Configuración</h2>
                <p class="text-muted">Gestiona tu acceso y datos personales.</p>
            </div>

            <!-- SECTION 1: SESSION (Visible to Everyone) -->
            <section class="mb-5">
                <h4 class="h5 fw-bold mb-3 text-dark">Sesión Actual</h4>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h6 class="fw-bold mb-1">Cerrar Sesión</h6>
                                <p class="text-muted small mb-0">Salir de tu cuenta en este dispositivo.</p>
                            </div>
                            <a href="logout.php" class="btn btn-outline-dark px-4">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: ACCOUNT DATA (Visible ONLY to Clients) -->
            <?php if (!$is_admin): ?>
            <section class="mb-5">
                <h4 class="h5 fw-bold mb-3 text-dark">Datos de la Cuenta</h4>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h6 class="fw-bold mb-1">Eliminar Cuenta</h6>
                                <p class="text-muted small mb-0">Borrar permanentemente tu perfil y todos los datos asociados.</p>
                            </div>
                            <!-- Button triggers the modal -->
                            <button type="button" class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="bi bi-trash-fill me-2"></i>Eliminar Cuenta
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

        </div>
    </div>

</div>
<!-- === END PAGE CONTENT === -->


<!-- =================================================================== -->
<!-- =================== DELETE CONFIRMATION MODAL ===================== -->
<!-- =================================================================== -->
<!-- Only render the modal if the user is NOT an admin -->
<?php if (!$is_admin): ?>
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="deleteAccountModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">¿Estás seguro de que quieres eliminar tu cuenta? Se borrará todo tu historial y las mascotas registradas.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <!-- In a real app, this form would submit to a delete_account.php script -->
                <form action="logout.php" method="GET"> <!-- Using logout for simulation -->
                    <button type="submit" class="btn btn-danger">
                        Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- 4. Page-Specific JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log("Página de Ajustes cargada.");
    });
</script>


<?php
// 5. Include the standard footer
include 'layout/footer.php';
?>
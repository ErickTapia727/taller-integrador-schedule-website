<?php
// Set context variables for the header template
$page_title = "Prueba de Bootstrap";
$active_link = 'settings';

// Include the standard header
include 'layout/header.php';
?>

<div class="container-fluid">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>¡Prueba de Bootstrap!</strong> Esta página prueba todos los componentes animados.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h3>1. Modal (Animación de fade)</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testModal">
                Abrir Modal
            </button>
            
            <!-- Modal -->
            <div class="modal fade" id="testModal" tabindex="-1" aria-labelledby="testModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="testModalLabel">Modal de Prueba</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Si ves este modal con animación suave de fade-in, Bootstrap está funcionando correctamente.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h3>2. Collapse (Animación de slide)</h3>
            <button class="btn btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Toggle Collapse
            </button>
            <div class="collapse mt-2" id="collapseExample">
                <div class="card card-body">
                    Este contenido se expande y colapsa con animación. Si ves un efecto de slide suave, funciona perfectamente.
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <h3>3. Accordion (Múltiples collapses)</h3>
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Item #1
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Contenido del primer item del accordion.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Item #2
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Contenido del segundo item del accordion.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h3>4. Dropdown (Animación fade)</h3>
            <div class="dropdown">
                <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Acción 1</a></li>
                    <li><a class="dropdown-item" href="#">Acción 2</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Otra acción</a></li>
                </ul>
            </div>

            <h3 class="mt-4">5. Tooltip (Hover)</h3>
            <button type="button" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="¡Soy un tooltip!">
                Pasa el mouse aquí
            </button>

            <h3 class="mt-4">6. Popover (Click)</h3>
            <button type="button" class="btn btn-danger" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="Este es el contenido del popover." title="Popover Title">
                Click para Popover
            </button>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3>7. Tabs (Animación de fade entre tabs)</h3>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Home</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Contact</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <div class="p-3">Contenido de Home - Con animación fade</div>
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div class="p-3">Contenido de Profile - Con animación fade</div>
                </div>
                <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                    <div class="p-3">Contenido de Contact - Con animación fade</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3>8. Offcanvas (Slide desde el lado)</h3>
            <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                Abrir Offcanvas
            </button>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div>
                        Este panel se desliza desde el lateral. Si ves una animación suave, todo funciona bien.
                    </div>
                    <div class="dropdown mt-3">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Dropdown en Offcanvas
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Acción</a></li>
                            <li><a class="dropdown-item" href="#">Otra acción</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mb-5">
        <div class="col-12">
            <h3>9. Toast (Notificación animada)</h3>
            <button type="button" class="btn btn-primary" id="liveToastBtn">Mostrar Toast</button>

            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Bootstrap</strong>
                        <small>Justo ahora</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ¡Toast con animación! Si aparece suavemente, todo funciona.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-success mt-4" role="alert">
        <h4 class="alert-heading">¿Qué verificar?</h4>
        <ul>
            <li>Los modales deben aparecer con efecto fade (oscurecimiento del fondo)</li>
            <li>Los collapse/accordion deben expandirse con animación slide suave</li>
            <li>Los dropdowns deben aparecer con fade</li>
            <li>Los tooltips y popovers deben aparecer suavemente</li>
            <li>Las tabs deben cambiar con fade entre contenidos</li>
            <li>El offcanvas debe deslizarse desde el lateral</li>
            <li>Los toasts deben aparecer con animación</li>
        </ul>
        <hr>
        <p class="mb-0">Si todas estas animaciones funcionan correctamente, Bootstrap está completamente configurado.</p>
    </div>
</div>

<script>
    // Script para el Toast
    document.addEventListener('DOMContentLoaded', function () {
        const toastTrigger = document.getElementById('liveToastBtn');
        const toastLiveExample = document.getElementById('liveToast');
        
        if (toastTrigger) {
            toastTrigger.addEventListener('click', function () {
                const toast = new bootstrap.Toast(toastLiveExample);
                toast.show();
            });
        }
    });
</script>

<?php
// Include the standard footer
include 'layout/footer.php';
?>

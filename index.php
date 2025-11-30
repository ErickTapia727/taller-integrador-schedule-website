<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peluquería canina DogCuiteSpa - Inicio</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="libs/bootstrap/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="src/main.css">
</head>

<body>

    <!--/////////////////////////////-->
    <!--TODO: Re-estructurar todo el archivo para que sea más atráctivo y profesional con información de la clienta y reviews de google-->
    <!--/////////////////////////////-->

    <!-- Variables de configuración del negocio -->
    <?php
    // === VARIABLES DE INFORMACIÓN DEL NEGOCIO ===
    // Modifica estas variables para personalizar la información mostrada
    
    $negocio_nombre = "DogCuteSpa";
    $negocio_slogan = "Peluquería canina profesional";
    $negocio_descripcion = "⭐️ Peluquería y estética canina especializada
                        ❤️ Atención cálida y libre de estrés  
                        🏠 Home Studio personalizado
                        🐶 Tu mascota se sentirá como en casa";

    // Horarios de atención
    $horario_dias = "Lunes a Viernes";
    $horario_horas = "08:00 - 17:00";
    $horario_sabado = "Sábados: 09:00 - 13:00";
    $horario_domingo = "Domingos: Cerrado";

    // Información de contacto
    $telefono = "+56 9 5397 9347";
    $telefono_link = "56953979347";
    //TODO: preguntar correo de contacto a clienta
    $email = "contacto@dogcutespa.cl";
    $direccion = "Av. Apóstol Santiago 1437";

    // Servicio principal de peluquería canina
    $servicio_principal = [
        'titulo' => 'Corte Profesional',
        'descripcion' => 'Servicio completo de peluquería canina que incluye baño con productos especializados, secado, corte de pelo según la raza y preferencias, corte de uñas y limpieza de oídos.',
        'caracteristicas' => [
            'Baño con shampoo especializado según tipo de pelo',
            'Secado profesional sin estrés',
            'Corte según raza y preferencias del cliente', 
            'Corte de uñas incluido',
            'Limpieza de oídos básica',
            'Ambiente relajado y cómodo'
        ]
    ];

    // Servicios adicionales
    $servicios_extras = [
        [
            'icono' => 'bi-droplet-fill',
            'titulo' => 'Solo Baño',
            'descripcion' => 'Servicio de baño completo con shampoo especializado, secado profesional, corte de uñas y limpieza de oídos.',
            'precio' => 'Consultar precio'
        ]
    ];

    // Equipo de peluquería canina
    $equipo = [
        [
            'nombre' => 'Nycole Inostroza',
            'especialidad' => 'Propietaria y Peluquera Canina',
            'experiencia' => 'Especialista en cuidado y estética canina'
        ]
    ];

    // Testimonios de clientes - Comentado para futuras reseñas reales
    $testimonios = [];
    ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container-fluid vh-100 d-flex align-items-center">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold text-dark mb-3"><?php echo $negocio_nombre; ?></h1>
                        <p class="lead text-primary fw-semibold mb-2" style="font-size: 1.5rem;">✨ Atención personalizada y libre de estrés</p>
                        <p class="text-muted mb-4"><?php echo $negocio_slogan; ?></p>
                        <p class="mb-4 fs-5 lh-base">En Dog Cute Spa cuidamos a cada perrito con amor, paciencia y un trato suave. Usamos productos seguros y un manejo respetuoso para que disfruten su experiencia. Ofrecemos baños, cortes y limpieza con dedicación, para que tu peludo salga feliz, cómodo y oliendo delicioso. 🐶💕</p>
                        
                        <div class="d-flex gap-3 mb-4">
                            <a href="signin.php" class="btn btn-primary btn-lg custom-button">
                                <i class="bi bi-person-plus me-2"></i>Registrarse
                            </a>
                            <a href="login.php" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                            </a>
                        </div>
                        
                        <!-- Información de contacto rápido -->
                        <div class="contact-quick bg-light p-3 rounded">
                            <div class="row text-center">
                                <div class="col-md-6 mb-2">
                                    <i class="bi bi-whatsapp text-success fs-4"></i>
                                    <small class="d-block mt-1"><strong><?php echo $telefono; ?></strong></small>
                                    <a href="https://wa.me/<?php echo $telefono_link; ?>" target="_blank" class="btn btn-sm btn-outline-success mt-1">
                                        <i class="bi bi-whatsapp me-1"></i>Contactar
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                    <small class="d-block mt-1"><strong><?php echo $direccion; ?></strong></small>
                                    <a href="https://maps.google.com/?q=<?php echo urlencode($direccion); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="bi bi-map me-1"></i>Ver Mapa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 text-center">
                        <img src="images/dogcutespa-resized.png" class="img-fluid rounded shadow" alt="<?php echo $negocio_nombre; ?>" style="max-height: 500px; width: auto; border: 4px solid #000000;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Nuestro Servicio Principal</h2>
            
            <!-- Servicio Principal Destacado -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-primary">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-scissors text-primary" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="card-title text-primary mb-3"><?php echo $servicio_principal['titulo']; ?></h3>
                            <p class="lead mb-4"><?php echo $servicio_principal['descripcion']; ?></p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">✨ Incluye:</h5>
                                    <ul class="list-unstyled text-start">
                                        <?php foreach ($servicio_principal['caracteristicas'] as $caracteristica): ?>
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <?php echo $caracteristica; ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-primary text-white p-4 rounded">
                                        <h5 class="text-center mb-3">🐕 Proceso de Atención</h5>
                                        <div class="small">
                                            <div class="mb-2"><strong>1.</strong> Recepción y evaluación</div>
                                            <div class="mb-2"><strong>2.</strong> Baño con productos especiales</div>
                                            <div class="mb-2"><strong>3.</strong> Secado profesional</div>
                                            <div class="mb-2"><strong>4.</strong> Corte personalizado</div>
                                            <div><strong>5.</strong> Detalles finales</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Servicios Adicionales -->
            <h3 class="text-center mb-4">Servicios Adicionales</h3>
            <div class="row justify-content-center">
                <?php foreach ($servicios_extras as $servicio): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="<?php echo $servicio['icono']; ?> text-primary mb-3" style="font-size: 2.5rem;"></i>
                            <h5 class="card-title"><?php echo $servicio['titulo']; ?></h5>
                            <p class="card-text"><?php echo $servicio['descripcion']; ?></p>
                            <span class="badge bg-primary"><?php echo $servicio['precio']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Equipo Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nuestro Equipo</h2>
            <div class="row justify-content-center">
                <?php foreach ($equipo as $peluquero): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="card-title"><?php echo $peluquero['nombre']; ?></h5>
                            <p class="text-muted mb-2"><?php echo $peluquero['especialidad']; ?></p>
                            <small class="text-secondary"><?php echo $peluquero['experiencia']; ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Horarios y Contacto Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="mb-4"><i class="bi bi-clock me-2"></i>Horarios de Atención</h3>
                    <div class="mb-3">
                        <strong><?php echo $horario_dias; ?>:</strong> <?php echo $horario_horas; ?>
                    </div>
                    <div class="mb-3">
                        <strong><?php echo $horario_sabado; ?></strong>
                    </div>
                    <div>
                        <strong><?php echo $horario_domingo; ?></strong>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h3 class="mb-4"><i class="bi bi-envelope me-2"></i>Información de Contacto</h3>
                    <div class="mb-3">
                        <i class="bi bi-whatsapp me-2"></i>
                        <strong>WhatsApp:</strong> 
                        <a href="https://wa.me/<?php echo $telefono_link; ?>" target="_blank" class="text-white text-decoration-none">
                            <?php echo $telefono; ?> <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                    <div class="mb-3">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        <strong>Dirección:</strong> 
                        <a href="https://maps.google.com/?q=<?php echo urlencode($direccion); ?>" target="_blank" class="text-white text-decoration-none">
                            <?php echo $direccion; ?>
                        </a>
                    </div>
                    <div>
                        <i class="bi bi-envelope-fill me-2"></i>
                        <strong>Email:</strong> 
                        <a href="mailto:<?php echo $email; ?>" class="text-white text-decoration-none">
                            <?php echo $email; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Final -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h2 class="mb-4">¿Tu perrito necesita un nuevo look? ✨</h2>
            <p class="lead mb-4">Agenda una cita y dale a tu mascota el cuidado que se merece</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="signin.php" class="btn btn-light btn-lg">
                    <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                </a>
                <a href="login.php" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Ya tengo cuenta
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-2">&copy; 2025 <?php echo $negocio_nombre; ?>. Todos los derechos reservados.</p>
            <p class="mb-0">
                <i class="bi bi-scissors text-warning"></i> 
                Haciendo felices a las mascotas con estilo
            </p>
        </div>
    </footer>

    <!-- Estilos adicionales para la página de inicio -->
    <style>
        .hero-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .custom-button {
            background: linear-gradient(45deg, var(--bs-primary), #8b5cf6);
            border: none;
            transition: all 0.3s ease;
        }
        
        .custom-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .contact-quick {
            border-left: 4px solid var(--bs-primary);
        }
        
        .card {
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .bi-quote {
            opacity: 0.3;
        }
    </style>

    <!-- Bootstrap JS -->
    <script src="libs/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>

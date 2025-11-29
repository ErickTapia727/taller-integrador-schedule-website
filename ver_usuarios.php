<?php
/**
 * Página para ver usuarios registrados en la base de datos
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/User.php';

// Protección básica: solo accesible en modo desarrollo
if (!DEBUG_MODE) {
    die('Acceso denegado');
}

try {
    $userModel = new User();
    $users = $userModel->getAll();
} catch (Exception $e) {
    die('Error al obtener usuarios: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Registrados - Dog Cute Spa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .user-card {
            transition: transform 0.2s;
        }
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .badge-admin {
            background-color: #dc3545;
        }
        .badge-client {
            background-color: #0d6efd;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
        }
        .refresh-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-4">
                    <i class="bi bi-people-fill text-primary"></i>
                    Usuarios Registrados
                </h1>
                <p class="text-muted">Monitoreo en tiempo real de la base de datos MySQL</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
                <a href="login.php" class="btn btn-success">
                    <i class="bi bi-box-arrow-in-right"></i> Ir al Login
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h3 class="mb-0"><?= count($users) ?></h3>
                    <p class="mb-0"><i class="bi bi-people"></i> Total Usuarios</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h3 class="mb-0">
                        <?= count(array_filter($users, fn($u) => $u['role'] === 'Administrador')) ?>
                    </h3>
                    <p class="mb-0"><i class="bi bi-shield-fill-check"></i> Administradores</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h3 class="mb-0">
                        <?= count(array_filter($users, fn($u) => $u['role'] === 'Cliente')) ?>
                    </h3>
                    <p class="mb-0"><i class="bi bi-person-fill"></i> Clientes</p>
                </div>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Lista de Usuarios</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>RUT</th>
                                <th>Teléfono</th>
                                <th>Rol</th>
                                <th>Registrado</th>
                                <th>Último Login</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox display-4 text-muted"></i>
                                        <p class="text-muted">No hay usuarios registrados</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><strong>#<?= htmlspecialchars($user['id']) ?></strong></td>
                                        <td>
                                            <i class="bi bi-person-circle text-primary"></i>
                                            <?= htmlspecialchars($user['name']) ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-envelope"></i>
                                            <?= htmlspecialchars($user['email']) ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-card-text"></i>
                                            <?= htmlspecialchars($user['rut']) ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-telephone"></i>
                                            <?= htmlspecialchars($user['phone']) ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $user['role'] === 'Administrador' ? 'badge-admin' : 'badge-client' ?>">
                                                <?= htmlspecialchars($user['role']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-check"></i>
                                                <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <small class="text-success">
                                                    <i class="bi bi-clock-history"></i>
                                                    <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted">
                                                    <i class="bi bi-dash-circle"></i> Nunca
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Información</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><i class="bi bi-check-circle text-success"></i> Base de datos: <strong>taller_integrador_db</strong></li>
                            <li><i class="bi bi-check-circle text-success"></i> Contraseñas: <strong>Hasheadas con bcrypt</strong></li>
                            <li><i class="bi bi-check-circle text-success"></i> Usuario MySQL: <strong>taller_user</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-key-fill"></i> Usuarios de Prueba</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Administrador:</strong></p>
                        <ul class="list-unstyled small mb-3">
                            <li>Email: <code>admin@dogcutespa.cl</code></li>
                            <li>Contraseña: <code>Admin123!</code></li>
                        </ul>
                        <p class="mb-2"><strong>Cliente:</strong></p>
                        <ul class="list-unstyled small mb-0">
                            <li>Email: <code>cliente@example.cl</code></li>
                            <li>Contraseña: <code>Cliente123!</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante de actualizar -->
    <button class="btn btn-primary btn-lg rounded-circle refresh-btn" onclick="location.reload()" title="Actualizar">
        <i class="bi bi-arrow-clockwise"></i>
    </button>

    <script>
        // Auto-refresh cada 10 segundos
        setTimeout(() => {
            location.reload();
        }, 10000);

        // Mostrar notificación de auto-refresh
        console.log('La página se actualizará automáticamente cada 10 segundos');
    </script>
</body>
</html>

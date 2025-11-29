# Guía de Instalación del Backend

## Estructura Creada

```
/var/www/html/taller-integrador-schedule-website-main/
├── config/
│   ├── database.php      # Conexión PDO a MySQL
│   └── config.php        # Configuración global
├── models/
│   ├── BaseModel.php     # Clase base con CRUD
│   ├── User.php          # Modelo de usuarios
│   ├── Pet.php           # Modelo de mascotas
│   └── Appointment.php   # Modelo de citas
├── includes/
│   ├── auth.php          # Funciones de autenticación
│   └── validators.php    # Funciones de validación
└── database.sql          # Script SQL de la base de datos
```

## Paso 1: Configurar la Base de Datos

### Opción A: Desde terminal
```bash
mysql -u root -p < database.sql
```

### Opción B: Desde phpMyAdmin
1. Abrir phpMyAdmin
2. Crear nueva base de datos: `taller_integrador_db`
3. Importar el archivo `database.sql`

## Paso 2: Configurar la Conexión

Editar el archivo `config/database.php` con tus credenciales:

```php
private $host = 'localhost';
private $db_name = 'taller_integrador_db';
private $username = 'root';        // Tu usuario MySQL
private $password = '';            // Tu contraseña MySQL
```

## Paso 3: Probar la Conexión

Crear un archivo `test_connection.php` en la raíz:

```php
<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    echo "✓ Conexión exitosa a la base de datos";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
```

## Paso 4: Usar los Modelos

### Ejemplo - Autenticación de Usuario
```php
<?php
require_once 'config/config.php';
require_once 'models/User.php';
require_once 'includes/auth.php';

$userModel = new User();

// Verificar credenciales
$user = $userModel->verifyCredentials('admin@dogcutespa.cl', 'Admin123!');

if ($user) {
    loginUser($user);
    header('Location: inicio.php');
} else {
    echo "Credenciales incorrectas";
}
?>
```

### Ejemplo - Crear una Mascota
```php
<?php
require_once 'models/Pet.php';

$petModel = new Pet();

$newPet = [
    'user_id' => 1,
    'name' => 'Firulais',
    'species' => 'Perro',
    'breed' => 'Labrador',
    'age' => 3,
    'weight' => 25.5
];

$petId = $petModel->createPet($newPet);
echo "Mascota creada con ID: $petId";
?>
```

### Ejemplo - Crear una Cita
```php
<?php
require_once 'models/Appointment.php';

$appointmentModel = new Appointment();

// Verificar disponibilidad
$available = $appointmentModel->isTimeSlotAvailable(
    '2025-11-26', 
    '10:00:00', 
    '12:00:00'
);

if ($available) {
    $newAppointment = [
        'user_id' => 1,
        'pet_id' => 1,
        'appointment_date' => '2025-11-26',
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'service' => 'Baño y corte'
    ];
    
    $appointmentId = $appointmentModel->createAppointment($newAppointment);
    echo "Cita creada con ID: $appointmentId";
} else {
    echo "Horario no disponible";
}
?>
```

## Paso 5: Migrar Código Existente

### Actualizar `procesar_login.php`
Reemplazar la lógica actual con:

```php
<?php
require_once 'config/config.php';
require_once 'models/User.php';
require_once 'includes/auth.php';
require_once 'includes/validators.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!validateEmail($email)) {
        $_SESSION['error'] = 'Email inválido';
        header('Location: login.php');
        exit;
    }
    
    $userModel = new User();
    $user = $userModel->verifyCredentials($email, $password);
    
    if ($user) {
        loginUser($user);
        $userModel->updateLastLogin($user['id']);
        header('Location: inicio.php');
    } else {
        $_SESSION['error'] = 'Credenciales incorrectas';
        header('Location: login.php');
    }
    exit;
}
?>
```

## Usuarios de Prueba

### Administrador
- **Email:** admin@dogcutespa.cl
- **Contraseña:** Admin123!

### Cliente
- **Email:** cliente@example.cl
- **Contraseña:** Cliente123!

## Funciones Disponibles

### Autenticación (`includes/auth.php`)
- `isLoggedIn()` - Verificar si hay sesión activa
- `isAdmin()` - Verificar si es administrador
- `isClient()` - Verificar si es cliente
- `getCurrentUserId()` - Obtener ID del usuario actual
- `loginUser($user)` - Iniciar sesión
- `logoutUser()` - Cerrar sesión
- `requireAuth()` - Requerir autenticación
- `requireAdmin()` - Requerir rol admin

### Validaciones (`includes/validators.php`)
- `validateEmail($email)` - Validar email
- `validateRut($rut)` - Validar RUT chileno
- `validatePhone($phone)` - Validar teléfono
- `validatePassword($password)` - Validar contraseña
- `sanitizeInput($input)` - Limpiar entrada
- `validateDate($date)` - Validar fecha
- `isBusinessHours($time)` - Verificar horario de atención

## Próximos Pasos

1. ✓ Crear estructura de backend
2. ✓ Configurar base de datos
3. ⏳ Migrar `procesar_login.php` y `procesar_registro.php`
4. ⏳ Actualizar `agenda.php` para usar el modelo Appointment
5. ⏳ Actualizar `clients.php` para usar los modelos
6. ⏳ Crear API REST en la carpeta `api/`

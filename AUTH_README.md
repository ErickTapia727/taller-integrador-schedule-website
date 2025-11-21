# Sistema de Autenticación - Documentación para Backend

## 📋 Estado Actual: Frontend listo para integración con MySQL

### ✅ Archivos implementados y funcionales:

1. **login.php** - Formulario de inicio de sesión
2. **signin.php** - Formulario de registro (incluye teléfono y validación RUT)
3. **procesar_login.php** - Procesador de login con validación
4. **procesar_registro.php** - Procesador de registro con validación RUT
5. **logout.php** - Cierre de sesión completo
6. **layout/header.php** - Manejo de sesiones y roles (DEBUG_MODE)
7. **includes/utils.php** - Validación de RUT chileno

## 🔑 Credenciales de prueba (simulación actual):

### Admin:
- **Email:** admin@example.com
- **Password:** Admin123!

### Cliente:
- **Email:** cliente@example.com  
- **Password:** Cliente123!

## 🚀 Modo DEBUG vs PRODUCCIÓN:

En `layout/header.php` línea 10-11:
```php
$DEBUG_MODE = false;  // true = simulación, false = login real
$DEBUG_FORCE_ROLE = 'client';  // solo aplica si DEBUG_MODE = true
```

## 📊 Estructura de Base de Datos sugerida:

### Tabla `users`:
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    rut VARCHAR(12) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') DEFAULT 'client',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔧 Tareas para el desarrollador de Backend:

### 🔥 **IMPORTANTE: Persistencia de usuarios resuelta**
**Problema:** Usuarios registrados se perdían al hacer logout.
**Solución aplicada:** Sistema de archivos temporal (`temp_users.json`) para persistencia entre sesiones.
**Para producción:** Reemplazar archivo temporal por base de datos MySQL.

### 1. Actualizar `procesar_login.php` (líneas 25-47):
```php
// Reemplazar carga de archivo temporal por consulta real:
$stmt = $pdo->prepare("SELECT id, nombre, correo, password_hash, role FROM users WHERE correo = ?");
$stmt->execute([$correo]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($contrasena, $usuario['password_hash'])) {
    // Crear sesión...
} else {
    header('Location: login.php?error=credenciales_incorrectas');
}
```

### 2. Actualizar `procesar_registro.php` (líneas 27-49):
```php
// Reemplazar archivo temporal por consultas reales:
// Verificar si email/RUT existen:
$stmt = $pdo->prepare("SELECT id FROM users WHERE correo = ? OR rut = ?");
$stmt->execute([$correo, $rut]);
if ($stmt->fetch()) {
    header('Location: signin.php?error=usuario_existe');
    exit();
}

// Hashear contraseña y guardar:
$hash = password_hash($contrasena, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (nombre, correo, rut, telefono, password_hash) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$nombre, $correo, $rut, $telefono, $hash]);
```

### 3. Eliminar archivo temporal:
- **Función ya implementada:** `validarRut()` en `includes/utils.php`
- **Uso:** `if (!validarRut($rut)) { /* error */ }`
- **Soporta:** Formato con/sin puntos y guion (12.345.678-9 o 12345678-9)

## 🎯 Flujo completo funcionando:

1. ✅ Usuario accede sin sesión → redirige a `login.php`
2. ✅ Login exitoso → redirige a `agenda.php` con sesión activa
3. ✅ Registro nuevo → valida RUT → guarda → redirige a login
4. ✅ Logout → destruye sesión → redirige a login
5. ✅ Header detecta role automáticamente (admin/client)

## 📱 Campos del formulario de registro:
- ✅ Nombre completo (`inputNombre`)
- ✅ Correo electrónico (`inputCorreo`) 
- ✅ Contraseña (`inputContraseña`)
- ✅ RUT (`inputRut`) - con validación
- ✅ Teléfono (`inputTelefono`) - nuevo campo

## 🔐 Seguridad implementada:
- ✅ Validación de inputs (trim, filter_var)
- ✅ Validación RUT con algoritmo módulo 11
- ✅ Preparado para password_hash/password_verify  
- ✅ Control de sesiones seguro
- ✅ Redirecciones con mensajes de error específicos

## 🎨 Frontend completado:
- ✅ Estilos CSS responsive (main.scss → main.css)
- ✅ Formularios con animaciones de labels flotantes
- ✅ Mensajes de error/éxito categorizados
- ✅ Integración con sistema de roles existente

### Next steps: Solo reemplazar simulación por MySQL y todo funcionará. 🚀
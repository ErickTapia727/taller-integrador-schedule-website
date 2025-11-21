# 🐕 Dog Cute Spa - Sistema de Gestión de Citas

> **Sistema web para spa canino con gestión completa de citas, clientes y mascotas**

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.8-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![SCSS](https://img.shields.io/badge/SCSS-CC6699?style=for-the-badge&logo=sass&logoColor=white)](https://sass-lang.com)

## 📋 Descripción

Dog Cute Spa es un sistema web completo para la gestión de un spa canino que incluye:

- **🗓️ Sistema de agenda** con vista semanal para administradores y clientes
- **👥 Gestión de clientes** con registro, edición y eliminación
- **🐕 Gestión de mascotas** con información detallada de cada animal
- **🔐 Sistema de autenticación** completo con roles (admin/cliente)
- **📱 Diseño responsivo** compatible con dispositivos móviles
- **✅ Validaciones robustas** para todos los formularios

## 🚀 Características Principales

### 🔑 **Sistema de Autenticación**
- **Registro de usuarios** con validación de RUT chileno
- **Login seguro** con criterios de contraseña robustos
- **Roles diferenciados**: Administrador y Cliente
- **Sesiones persistentes** con logout seguro
- **Validación de duplicados** (email, RUT, nombre)

### 📅 **Sistema de Agenda**
- **Vista semanal** con navegación por meses
- **Gestión de horarios** (08:00 - 17:00) en bloques de 2 horas
- **Agendamiento de citas** por parte de clientes
- **Bloqueo de horarios** por parte de administradores
- **Reportes de citas** con estado (completado/cancelado)

### 👤 **Gestión de Usuarios**
- **Administradores**: Ver todos los clientes y sus mascotas
- **Clientes**: Gestionar solo sus propias mascotas
- **CRUD completo** para clientes y mascotas
- **Validación de datos** en tiempo real

### 📱 **Diseño Responsivo**
- **Bootstrap 5.3.8** para UI consistente
- **SCSS personalizado** con tema rosa/blanco
- **Iconos Bootstrap** para mejor UX
- **Adaptable** a móviles, tablets y desktop

## 🛠️ Tecnologías Utilizadas

### **Frontend**
- **HTML5** + **CSS3** + **JavaScript ES6**
- **Bootstrap 5.3.8** (vía NPM)
- **SCSS** para estilos personalizados
- **Bootstrap Icons** para iconografía

### **Backend**
- **PHP 8.0+** para lógica del servidor
- **Sesiones PHP** para gestión de estado
- **Validación server-side** robusta
- **Arquitectura modular** con includes

### **Herramientas de Desarrollo**
- **NPM** para gestión de dependencias
- **Live Sass Compiler** (VS Code) o **npm scripts**
- **Git** para control de versiones

## 📦 Instalación

### **Prerequisitos**
```bash
# Servidor web (Apache/Nginx)
# PHP 8.0+
# Node.js y NPM
```

### **Pasos de Instalación**

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd dog-cute-spa
```

2. **Instalar dependencias NPM**
```bash
npm install
```

3. **Compilar SCSS**
```bash
# Opción 1: NPM script
npm run build-css

# Opción 2: Live Sass Compiler (VS Code)
# Abrir src/main.scss y usar la extensión
```

4. **Configurar servidor web**
```bash
# Apache: Apuntar DocumentRoot a la carpeta del proyecto
# Nginx: Configurar root a la carpeta del proyecto
```

5. **Configurar permisos (si es necesario)**
```bash
chmod 755 includes/
chmod 644 *.php
```

## 🎮 Uso del Sistema

### **Credenciales Demo**
```bash
# Administrador
Email: admin@example.com
Password: Admin123!

# Cliente Demo  
Email: cliente@example.com
Password: Cliente123!
```

### **Flujo de Trabajo**

#### **👨‍💼 Como Administrador:**
1. **Login** → Acceso completo al sistema
2. **Agenda** → Ver/bloquear horarios, gestionar reportes
3. **Clientes** → Ver todos los clientes y sus mascotas
4. **Configuración** → Gestión de perfil

#### **👤 Como Cliente:**
1. **Registro** → Crear cuenta con validaciones
2. **Login** → Acceso a funciones de cliente
3. **Agenda** → Agendar citas para mis mascotas
4. **Mascotas** → Gestionar mis mascotas registradas
5. **Historial** → Ver mis citas pasadas

## 📁 Estructura del Proyecto

```
dog-cute-spa/
├── 📄 *.php                 # Páginas principales
├── 📁 includes/            # Funciones reutilizables
│   └── utils.php           # Validaciones (ej: RUT)
├── 📁 layout/              # Plantillas compartidas  
│   ├── header.php          # Header con autenticación
│   └── footer.php          # Footer estándar
├── 📁 src/                 # Assets del frontend
│   ├── main.scss           # Estilos personalizados
│   └── main.css            # CSS compilado
├── 📁 images/              # Imágenes del proyecto
├── 📁 node_modules/        # Dependencias NPM
├── 📄 package.json         # Configuración NPM
├── 📄 temp_users.json      # Usuarios demo (temporal)
└── 📄 *.md                 # Documentación
```

## 📚 Documentación

### **Archivos de Documentación Incluidos:**
- **📄 AUTH_README.md** - Sistema de autenticación completo
- **📄 TESTING_AUTH.md** - Guía de testing del sistema
- **📄 PROBLEMA_RESUELTO.md** - Resolución de bugs críticos
- **📄 CREDENCIALES_ACTUALIZADAS.md** - Nuevas credenciales seguras
- **📄 VALIDACION_DUPLICADOS.md** - Sistema anti-duplicados

### **Componentes Principales:**
- **agenda.php** - Sistema de citas con calendario
- **clients.php** - Gestión dual admin/cliente
- **login.php** / **signin.php** - Autenticación
- **procesar_*.php** - Lógica de backend
- **layout/header.php** - Gestión de sesiones centralized

## 🔧 Configuración

### **Modo Debug vs Producción**
```php
// En layout/header.php
$DEBUG_MODE = true;   // Desarrollo: usar usuarios demo
$DEBUG_MODE = false;  // Producción: requerir login real
```

### **Criterios de Contraseña**
- ✅ Mínimo 8 caracteres
- ✅ Al menos una mayúscula y minúscula  
- ✅ Al menos un número o símbolo especial

### **Formato de Teléfono**
- ✅ Formato chileno: `+56 9 XXXX XXXX`
- ✅ Autoformato en tiempo real

## 🧪 Testing

### **Casos de Prueba Principales:**
1. **Registro de usuario** con validaciones
2. **Login/Logout** completo
3. **Agendamiento de citas** por clientes
4. **Gestión de mascotas** CRUD
5. **Bloqueo de horarios** por admin
6. **Validación de duplicados** en registro

Ver **TESTING_AUTH.md** para guía completa de testing.

## 🔄 Próximos Pasos

### **Migración a Base de Datos:**
- [ ] Reemplazar archivo JSON con MySQL
- [ ] Implementar password hashing real
- [ ] Añadir índices para optimización

### **Funcionalidades Adicionales:**
- [ ] Sistema de notificaciones
- [ ] Reportes de ingresos  
- [ ] Gestión de servicios y precios
- [ ] API REST para app móvil

## 📞 Soporte

Para soporte técnico o preguntas sobre el sistema:
- **Documentación**: Revisar archivos *.md incluidos
- **Testing**: Seguir TESTING_AUTH.md
- **Configuración**: Ver AUTH_README.md

## 📄 Licencia

Este proyecto es para uso educativo y comercial del Dog Cute Spa.

---

**🐕 ¡Desarrollado con amor para nuestros amigos peludos! 🐕**
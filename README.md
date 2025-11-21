## Sistema de Gestión de Citas

- Utiliza LAMP stack para desarrollo de sitios web,
se desarrolló en una VM con Ubuntu 24.04,
las funciones .html se desarrollaron utilizando bootstrap 5.3.8 a través de NMP para mayor personalización con main.scss,
para convertir main.scss en main.css se utilizó la extensión de visual studio code "Live Sass Compiler" de Glenn Marks

## Funcionalidades Principales
- Autenticación y Seguridad
- Control de Acceso: Sistema de registro y validación de usuarios (incluyendo validación de RUT y número teléfonico).

Roles: Perfiles diferenciados para Administradores y Clientes.
Seguridad: Gestión de sesiones persistentes, validación de duplicados (email, RUT) y criterios de contraseña implementados en el registro

## Sistema de Agenda
Visualización: Calendario con vista semanal y navegación mensual.
Horarios: Gestión de bloques de 2 horas entre las 08:00 y las 17:00.
Operaciones: Agendamiento por parte de clientes y bloqueo de horarios/gestión de reportes por parte de administradores.

## Gestión de Usuarios y Mascotas
- Administradores: Acceso global a la lista de clientes y mascotas.
- Clientes: Gestión exclusiva de sus propias mascotas e historial.

## Datos: Funcionalidad CRUD (Crear, Leer, Actualizar, Eliminar) completa.

## LAMP Stack
Frontend
- Core: HTML5, CSS3, JavaScript ES6.
- Framework: Bootstrap 5.3.8 (vía NPM).
- Estilos: SCSS personalizado y Bootstrap Icons.
- Servidor Local: Apache2
  
Backend
- Lenguaje: PHP 8.0+.
- Base de datos: MySQL
- Arquitectura: Modular basada en includes, uso de sesiones PHP y validaciones del lado del servidor.

Herramientas de Desarrollo
Gestión de Paquetes: NPM.

Compilación: Scripts NPM y Live Sass Compiler para conversión de .scss a .css

Control de Versiones: Git.

## Instalación y Configuración - Requisitos Previos
- Servidor web (Apache o Nginx).
- PHP 8.0 o superior.
- Node.js y NPM.

## Pasos de Despliegue
Clonar el repositorio.

- Instalar dependencias mediante npm install.
- Compilar los archivos SCSS (npm run build-css).
- Configurar el directorio raíz del servidor web.
- Ajustar permisos de lectura/escritura en carpetas del sistema.

## Configuración del Sistema
- Entorno: Variable $DEBUG_MODE para alternar entre modo desarrollo (usuarios demo) y producción.
- Formatos: Estandarización de teléfonos (formato chileno) y validación de contraseñas seguras.

## Estructura del Proyecto
El sistema se organiza en:
Core: Scripts PHP principales para la lógica de negocio.
Includes/Layout: Componentes reutilizables y plantillas de interfaz.
Src/Assets: Código fuente frontend y recursos gráficos.
Data: Almacenamiento temporal basado en JSON (temp_users.json).

## Próximos Pasos (Roadmap)
Base de Datos: Migración del sistema de archivos JSON a MySQL.
Seguridad: Implementación de hashing para contraseñas.
Funcionalidades: Desarrollo de sistema de notificaciones, reportes financieros y API REST.
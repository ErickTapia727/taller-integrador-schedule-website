Sistema de Gestión de Citas
Descripción General
Sistema web integral diseñado para la administración de un spa canino. La plataforma permite la gestión centralizada de citas, bases de datos de clientes y registros de mascotas, implementando un diseño responsivo y validaciones de seguridad robustas.

Funcionalidades Principales
Autenticación y Seguridad
Control de Acceso: Sistema de registro y validación de usuarios (incluyendo validación de RUT chileno).

Roles: Perfiles diferenciados para Administradores y Clientes.

Seguridad: Gestión de sesiones persistentes, validación de duplicados (email, RUT) y criterios estrictos de contraseña.

Sistema de Agenda
Visualización: Calendario con vista semanal y navegación mensual.

Horarios: Gestión de bloques de 2 horas entre las 08:00 y las 17:00.

Operaciones: Agendamiento por parte de clientes y bloqueo de horarios/gestión de reportes por parte de administradores.

Gestión de Usuarios y Mascotas
Administradores: Acceso global a la lista de clientes y mascotas.

Clientes: Gestión exclusiva de sus propias mascotas e historial.

Datos: Funcionalidad CRUD (Crear, Leer, Actualizar, Eliminar) completa.

Stack Tecnológico
Frontend
Core: HTML5, CSS3, JavaScript ES6.

Framework: Bootstrap 5.3.8 (vía NPM).

Estilos: SCSS personalizado y Bootstrap Icons.

Backend
Lenguaje: PHP 8.0+.

Arquitectura: Modular basada en includes, uso de sesiones PHP y validaciones del lado del servidor.

Herramientas de Desarrollo
Gestión de Paquetes: NPM.

Compilación: Scripts NPM o Live Sass Compiler.

Control de Versiones: Git.

Instalación y Configuración
Requisitos Previos
Servidor web (Apache o Nginx).

PHP 8.0 o superior.

Node.js y NPM.

Pasos de Despliegue
Clonar el repositorio.

Instalar dependencias mediante npm install.

Compilar los archivos SCSS (npm run build-css).

Configurar el directorio raíz del servidor web.

Ajustar permisos de lectura/escritura en carpetas del sistema.

Configuración del Sistema
Entorno: Variable $DEBUG_MODE para alternar entre modo desarrollo (usuarios demo) y producción.

Formatos: Estandarización de teléfonos (formato chileno) y validación de contraseñas seguras.

Estructura del Proyecto
El sistema se organiza en:

Core: Scripts PHP principales para la lógica de negocio.

Includes/Layout: Componentes reutilizables y plantillas de interfaz.

Src/Assets: Código fuente frontend y recursos gráficos.

Data: Almacenamiento temporal basado en JSON (temp_users.json).

Próximos Pasos (Roadmap)
Base de Datos: Migración del sistema de archivos JSON a MySQL.

Seguridad: Implementación de hashing para contraseñas.

Funcionalidades: Desarrollo de sistema de notificaciones, reportes financieros y API REST.

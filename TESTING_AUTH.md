# 🧪 Testing del Sistema de Autenticación

## 🔧 **SOLUCIÓN APLICADA: Persistencia de usuarios entre sesiones**

**Problema resuelto:** Los usuarios registrados durante una sesión se perdían al hacer logout.
**Solución:** Usuarios registrados se guardan en `temp_users.json` (persistente entre logouts).

## Pasos de prueba para verificar funcionalidad completa:

### 📝 **PASO 1: Configurar modo DEBUG**
1. Abrir `layout/header.php`
2. Verificar líneas 10-11:
   ```php
   $DEBUG_MODE = false;  // DEBE estar en false para probar login real
   $DEBUG_FORCE_ROLE = 'client';
   ```

### 🔐 **PASO 2: Probar LOGIN con usuarios demo**
1. Ir a `http://localhost/login.php`
2. **Probar Admin:**
   - Email: `admin@example.com`
   - Password: `Admin123!`
   - ✅ Debe redirigir a agenda.php como administrador
3. **Probar Cliente:**
   - Email: `cliente@example.com`  
   - Password: `Cliente123!`
   - ✅ Debe redirigir a agenda.php como cliente

### ✍️ **PASO 3: Probar REGISTRO y PERSISTENCIA (CRÍTICO)**
1. Desde login.php, hacer clic en "¿No tienes cuenta? Regístrate"
2. Llenar formulario completo:
   ```
   Nombre: Juan Pérez Ejemplo
   Email: nuevo@test.com
   Contraseña: test123
   RUT: 12345678-9
   Teléfono: +56912345678
   ```
3. ✅ Debe validar RUT y redirigir a login con mensaje "¡Cuenta creada!"
4. ✅ Login con `nuevo@test.com` / `test123` debe funcionar
5. ✅ **CRITICAL:** Hacer logout y volver a hacer login con los mismos datos
6. ✅ **DEBE FUNCIONAR** - usuario guardado en `temp_users.json`

### ❌ **PASO 4: Probar validaciones de errores**

#### Login con datos incorrectos:
- ✅ Email incorrecto → "Correo o contraseña incorrectos"
- ✅ Campos vacíos → "Todos los campos son obligatorios"
- ✅ Email inválido → "Formato de correo inválido"

#### Registro con errores:
- ✅ RUT inválido (ej: `11111111-1`) → "RUT ingresado no es válido"
- ✅ Email duplicado → "El correo ya está registrado"
- ✅ RUT duplicado → "El RUT ya está registrado"
- ✅ Campos vacíos → "Todos los campos son obligatorios"

### 🚪 **PASO 5: Probar LOGOUT**
1. Con sesión activa, ir a settings.php
2. Hacer clic en "Cerrar Sesión" 
3. ✅ Debe volver a login.php con mensaje "Sesión cerrada correctamente"
4. ✅ Intentar acceder a agenda.php debe redirigir a login

### 🔄 **PASO 6: Probar flujo completo de PERSISTENCIA**
1. ✅ Registrar nuevo usuario → debe crear entrada en `temp_users.json`
2. ✅ Login exitoso → acceso a sistema con role correcto
3. ✅ Logout → sesión destruida PERO usuario permanece en archivo
4. ✅ Re-login con mismo usuario → debe funcionar perfectamente
5. ✅ Navegación entre páginas → mantiene sesión activa
6. ✅ Múltiples ciclos login/logout → usuario siempre disponible

**🔍 VERIFICACIÓN:** Revisar contenido de `temp_users.json`:
```bash
cat temp_users.json
# Debe mostrar usuarios registrados en formato JSON
```

### 🛠️ **PASO 7: Verificar validación RUT**
Probar estos RUTs en el registro:

#### ✅ RUTs VÁLIDOS:
- `12.345.678-5`
- `11.111.111-1`  
- `9999999-9`
- `77777777-7`

#### ❌ RUTs INVÁLIDOS:
- `12345678-0` (dígito incorrecto)
- `11111111-2` (dígito incorrecto)
- `abc123456-9` (formato inválido)
- `123` (muy corto)

### 📱 **PASO 8: Verificar responsividad**
1. ✅ Formularios se adaptan en mobile/desktop
2. ✅ Labels flotantes funcionan correctamente
3. ✅ Iconos se posicionan correctamente
4. ✅ Mensajes de error se muestran claramente

---

## ✅ Checklist final para el desarrollador:

- [ ] DEBUG_MODE configurado correctamente
- [ ] Login admin/cliente funciona
- [ ] Registro de nuevo usuario funciona  
- [ ] Validación RUT opera correctamente
- [ ] Mensajes de error son específicos y claros
- [ ] Logout destruye sesión completamente
- [ ] Redirecciones funcionan sin loops
- [ ] Formularios son responsive
- [ ] No hay errores de sintaxis PHP
- [ ] Integración con header/roles funciona

**🎯 Resultado esperado:** Sistema de auth completo listo para migrar a MySQL.

---

### 📞 Troubleshooting común:

#### Si login no funciona:
1. Verificar que `DEBUG_MODE = false`
2. Verificar credenciales exactas
3. Comprobar errores en logs PHP

#### Si registro/login falla después de logout:
1. ✅ **FIXED:** Verificar que `temp_users.json` existe y contiene usuarios
2. Comprobar permisos de escritura en directorio
3. Revisar logs PHP para errores de archivo
4. Verificar que `__DIR__ . '/temp_users.json'` es accesible

#### Si sesión no persiste:
1. Verificar configuración de PHP sessions
2. Comprobar headers no enviados antes de session_start()
3. Revisar permisos de directorio de sesiones
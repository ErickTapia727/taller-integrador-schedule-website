# GUÍA: Configurar Recordatorios Automáticos por Email

## ¿Qué hace el sistema?
Envía un email automático a los clientes **2 MESES ANTES** de su cita programada, recordándoles:
- Fecha y hora de la cita
- Nombre de la mascota
- Servicio contratado
- Dirección del local
- Botón directo a WhatsApp

## ¿Cómo funciona?
1. **Al crear una cita**: Automáticamente se calcula la fecha del recordatorio (2 meses antes)
2. **Cron job diario**: Revisa qué citas tienen recordatorio programado para hoy
3. **Envío de email**: Envía el recordatorio y marca como enviado para no duplicar

---

## PASO 1: Configurar Cron Job en Linux

### Opción A: Cron diario a las 9:00 AM

```bash
# Abrir el editor de cron
crontab -e

# Agregar esta línea (envía recordatorios todos los días a las 9:00 AM)
0 9 * * * /usr/bin/php /var/www/html/taller-integrador-schedule-website-main/cron_recordatorios.php >> /var/www/html/taller-integrador-schedule-website-main/logs/recordatorios.log 2>&1
```

### Opción B: Cron dos veces al día (9 AM y 6 PM)

```bash
0 9 * * * /usr/bin/php /var/www/html/taller-integrador-schedule-website-main/cron_recordatorios.php >> /var/www/html/taller-integrador-schedule-website-main/logs/recordatorios.log 2>&1
0 18 * * * /usr/bin/php /var/www/html/taller-integrador-schedule-website-main/cron_recordatorios.php >> /var/www/html/taller-integrador-schedule-website-main/logs/recordatorios.log 2>&1
```

---

## PASO 2: Crear carpeta para logs

```bash
mkdir -p /var/www/html/taller-integrador-schedule-website-main/logs
chmod 755 /var/www/html/taller-integrador-schedule-website-main/logs
```

---

## PASO 3: Probar manualmente

Ejecuta el script para verificar que funciona:

```bash
cd /var/www/html/taller-integrador-schedule-website-main
php cron_recordatorios.php
```

Deberías ver algo como:
```
[2025-11-29 09:00:00] Buscando citas para: 2025-11-30
Citas encontradas: 3

✓ Recordatorio enviado a: Juan Pérez (juan@example.com) - Max
✓ Recordatorio enviado a: María López (maria@example.com) - Luna
✓ Recordatorio enviado a: Carlos Torres (carlos@example.com) - Rocky

[2025-11-29 09:00:15] Proceso completado.
```

---

## PASO 4: Personalizar configuración

### Cambiar el tiempo de anticipación del recordatorio

Si deseas cambiar de 2 meses a otro periodo, edita `models/Appointment.php` líneas 15 y 33:

```php
// Cambiar de '-2 months' a:
$reminderDate->modify('-3 months');  // 3 meses antes
$reminderDate->modify('-1 month');   // 1 mes antes
$reminderDate->modify('-1 week');    // 1 semana antes
$reminderDate->modify('-3 days');    // 3 días antes
```

**Importante:** Después de cambiar, actualiza las citas existentes:
```bash
mysql -u taller_user -p'taller_pass_2025' taller_integrador_db -e "UPDATE appointments SET reminder_date = DATE_SUB(appointment_date, INTERVAL 1 MONTH), reminder_sent = FALSE;"
```

---

## PASO 5: Verificar logs

Ver los últimos recordatorios enviados:

```bash
tail -f /var/www/html/taller-integrador-schedule-website-main/logs/recordatorios.log
```

---

## ALTERNATIVA: Si no tienes acceso a Cron

### Usando servicio de hosting con panel (cPanel/Plesk):

1. Ir a "Cron Jobs" en el panel
2. Crear nuevo cron job:
   - Comando: `/usr/bin/php /ruta/completa/cron_recordatorios.php`
   - Frecuencia: Diario a las 9:00 AM

### Usando un servicio externo (EasyCron, Cron-Job.org):

1. Crear un archivo wrapper público:
   ```php
   // recordatorios_trigger.php
   <?php
   // Verificar token de seguridad
   if (!isset($_GET['token']) || $_GET['token'] !== 'TU_TOKEN_SECRETO_AQUI') {
       die('Acceso denegado');
   }
   include 'cron_recordatorios.php';
   ?>
   ```

2. Configurar el servicio externo para llamar:
   ```
   https://tudominio.com/recordatorios_trigger.php?token=TU_TOKEN_SECRETO_AQUI
   ```

---

## Personalización del Email

Puedes modificar el diseño del email editando la variable `$message` en `cron_recordatorios.php`:

- **Colores**: Cambiar `#667eea` y `#764ba2`
- **Logo**: Agregar `<img src="https://tudominio.com/images/logo.png">`
- **Contenido**: Modificar textos y estructura HTML

---

## Monitoreo

Para recibir notificaciones si hay errores:

```bash
# En crontab, agregar tu email:
MAILTO=tu_email@gmail.com

0 9 * * * /usr/bin/php /var/www/html/taller-integrador-schedule-website-main/cron_recordatorios.php
```

---

## Desactivar Recordatorios

Para pausar temporalmente:

```bash
# Comentar la línea en crontab
crontab -e
# Agregar # al inicio:
# 0 9 * * * /usr/bin/php ...
```

---

## Soporte para múltiples recordatorios

Si quieres enviar recordatorios en diferentes momentos:

1. Duplicar el archivo: `cron_recordatorios_2dias.php`
2. Cambiar `$dias_anticipacion = 2`
3. Agregar otra línea en cron:
   ```bash
   0 9 * * * /usr/bin/php /ruta/cron_recordatorios_2dias.php >> /ruta/logs/recordatorios_2dias.log 2>&1
   ```

---

## Estadísticas de envío

Ver cuántos recordatorios se han enviado hoy:

```bash
grep "Recordatorio enviado" /var/www/html/taller-integrador-schedule-website-main/logs/recordatorios.log | grep "$(date +%Y-%m-%d)" | wc -l
```

<?php
/**
 * Script para actualizar la base de datos - Agregar estado "Bloqueado"
 */

require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "=== Actualizando Base de Datos ===\n\n";
    
    // Verificar estructura actual
    echo "1. Verificando columna 'status' actual...\n";
    $stmt = $db->query("SHOW COLUMNS FROM appointments LIKE 'status'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Tipo actual: " . $column['Type'] . "\n\n";
    
    // Actualizar columna para incluir "Bloqueado"
    echo "2. Agregando valor 'Bloqueado' a la columna status...\n";
    $sql = "ALTER TABLE appointments 
            MODIFY COLUMN status ENUM('Pendiente', 'Confirmado', 'Completado', 'Cancelado', 'Bloqueado') 
            DEFAULT 'Pendiente'";
    
    $db->exec($sql);
    echo "   ✅ Columna actualizada exitosamente\n\n";
    
    // Verificar el cambio
    echo "3. Verificando cambio...\n";
    $stmt = $db->query("SHOW COLUMNS FROM appointments LIKE 'status'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Tipo nuevo: " . $column['Type'] . "\n\n";
    
    echo "=== ✅ Actualización completada ===\n";
    echo "Ahora puedes bloquear horarios desde la agenda.\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

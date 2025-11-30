<?php
/**
 * Configuración de conexión a la base de datos MySQL
 * Utiliza PDO para una conexión segura y moderna
 */

class Database {
    // Configuración de la base de datos taller_user
    private $host = 'localhost';
    private $db_name = 'taller_integrador_db';
    private $username = 'taller_user';
    private $password = 'taller_pass_2025';
    private $charset = 'utf8mb4';
    
    private $conn = null;
    
    /**
     * Obtiene la conexión a la base de datos
     * @return PDO|null
     */
    public function getConnection() {
        if ($this->conn !== null) {
            return $this->conn;
        }
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            throw new Exception("No se pudo conectar a la base de datos");
        }
        
        return $this->conn;
    }
    
    /**
     * Cierra la conexión a la base de datos
     */
    public function closeConnection() {
        $this->conn = null;
    }
}

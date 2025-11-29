<?php
/**
 * Modelo de Usuario
 */

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    
    /**
     * Buscar usuario por email
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Buscar usuario por RUT
     * @param string $rut
     * @return array|false
     */
    public function findByRut($rut) {
        $query = "SELECT * FROM {$this->table} WHERE rut = :rut LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':rut', $rut);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Buscar usuario por teléfono
     * @param string $phone
     * @return array|false
     */
    public function findByPhone($phone) {
        $query = "SELECT * FROM {$this->table} WHERE phone = :phone LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Crear un nuevo usuario
     * @param array $data
     * @return int|false
     */
    public function createUser($data) {
        // Hashear la contraseña antes de guardar
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        // Establecer valores por defecto
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }
    
    /**
     * Verificar credenciales de usuario
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function verifyCredentials($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Actualizar último login
     * @param int $userId
     * @return bool
     */
    public function updateLastLogin($userId) {
        $query = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Obtener usuarios por rol
     * @param string $role
     * @return array
     */
    public function getUsersByRole($role) {
        return $this->findWhere(['role' => $role]);
    }
}

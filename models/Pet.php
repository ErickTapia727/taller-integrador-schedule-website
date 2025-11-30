<?php
/**
 * Modelo de Mascota
 */

require_once __DIR__ . '/BaseModel.php';

class Pet extends BaseModel {
    protected $table = 'pets';
    
    /**
     * Alias para getById (para compatibilidad)
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        return $this->getById($id);
    }
    
    /**
     * Obtener mascotas de un usuario específico
     * @param int $userId
     * @return array
     */
    public function getPetsByUserId($userId) {
        return $this->findWhere(['user_id' => $userId]);
    }
    
    /**
     * Obtener todas las mascotas con información del dueño
     * @return array
     */
    public function getAllWithOwner() {
        $query = "SELECT p.*, u.name as owner_name, u.email as owner_email 
                  FROM {$this->table} p 
                  INNER JOIN users u ON p.user_id = u.id 
                  ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar mascota por nombre
     * @param string $name
     * @param int|null $userId
     * @return array
     */
    public function findByName($name, $userId = null) {
        $query = "SELECT * FROM {$this->table} WHERE name LIKE :name";
        
        if ($userId !== null) {
            $query .= " AND user_id = :user_id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', "%{$name}%");
        
        if ($userId !== null) {
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Crear una nueva mascota
     * @param array $data
     * @return int|false
     */
    public function createPet($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
    
    /**
     * Verificar si el usuario es dueño de la mascota
     * @param int $petId
     * @param int $userId
     * @return bool
     */
    public function isPetOwner($petId, $userId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE id = :pet_id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':pet_id', $petId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}

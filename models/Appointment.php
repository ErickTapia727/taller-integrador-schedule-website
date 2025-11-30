<?php
/**
 * Modelo de Cita/Agendamiento
 */

require_once __DIR__ . '/BaseModel.php';

class Appointment extends BaseModel {
    protected $table = 'appointments';
    
    /**
     * Crear una nueva cita
     * Sobrescribe el método padre para calcular automáticamente reminder_date
     * @param array $data
     * @return int|false
     */
    public function create(array $data) {
        // Calcular la fecha del recordatorio (2 meses antes)
        if (isset($data['appointment_date'])) {
            $appointmentDate = new DateTime($data['appointment_date']);
            $reminderDate = clone $appointmentDate;
            $reminderDate->modify('-2 months');
            $data['reminder_date'] = $reminderDate->format('Y-m-d');
            $data['reminder_sent'] = false;
        }
        
        return parent::create($data);
    }
    
    /**
     * Actualizar una cita
     * Sobrescribe el método padre para recalcular reminder_date si cambia la fecha
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data) {
        // Si se actualiza la fecha de la cita, recalcular reminder_date
        if (isset($data['appointment_date'])) {
            $appointmentDate = new DateTime($data['appointment_date']);
            $reminderDate = clone $appointmentDate;
            $reminderDate->modify('-2 months');
            $data['reminder_date'] = $reminderDate->format('Y-m-d');
            $data['reminder_sent'] = false;
        }
        
        return parent::update($id, $data);
    }
    
    /**
     * Obtener citas de un usuario específico
     * @param int $userId
     * @return array
     */
    public function getAppointmentsByUserId($userId) {
        $query = "SELECT a.*, 
                  p.name as pet_name, p.species as pet_species, p.breed as pet_breed, 
                  p.age as pet_age, p.weight as pet_weight,
                  u.name as owner_name, u.email as owner_email, u.phone as owner_phone, u.rut as owner_rut
                  FROM {$this->table} a 
                  INNER JOIN pets p ON a.pet_id = p.id 
                  INNER JOIN users u ON a.user_id = u.id 
                  WHERE a.user_id = :user_id 
                  ORDER BY a.appointment_date DESC, a.start_time DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener citas por fecha
     * @param string $date Formato: Y-m-d
     * @return array
     */
    public function getAppointmentsByDate($date) {
        $query = "SELECT a.*, 
                  p.name as pet_name, p.species as pet_species, p.breed as pet_breed, 
                  p.age as pet_age, p.weight as pet_weight,
                  u.name as owner_name, u.email as owner_email, u.phone as owner_phone, u.rut as owner_rut
                  FROM {$this->table} a 
                  INNER JOIN pets p ON a.pet_id = p.id 
                  INNER JOIN users u ON a.user_id = u.id 
                  WHERE a.appointment_date = :date 
                  ORDER BY a.start_time ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener citas por rango de fechas
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getAppointmentsByDateRange($startDate, $endDate) {
        $query = "SELECT a.*, 
                  p.name as pet_name, p.species as pet_species, p.breed as pet_breed, 
                  p.age as pet_age, p.weight as pet_weight,
                  u.name as owner_name, u.email as owner_email, u.phone as owner_phone, u.rut as owner_rut
                  FROM {$this->table} a 
                  INNER JOIN pets p ON a.pet_id = p.id 
                  INNER JOIN users u ON a.user_id = u.id 
                  WHERE a.appointment_date BETWEEN :start_date AND :end_date 
                  ORDER BY a.appointment_date ASC, a.start_time ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Verificar disponibilidad de horario
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    public function isTimeSlotAvailable($date, $startTime, $endTime) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE appointment_date = :date 
                  AND status != 'Cancelado'
                  AND (
                      (start_time < :end_time AND end_time > :start_time)
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] == 0;
    }
    
    /**
     * Crear una nueva cita
     * @param array $data
     * @return int|false
     */
    public function createAppointment($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'Pendiente';
        return $this->create($data);
    }
    
    /**
     * Actualizar estado de la cita
     * @param int $appointmentId
     * @param string $status
     * @param string|null $adminNotes Notas opcionales del administrador
     * @return bool
     */
    public function updateStatus($appointmentId, $status, $adminNotes = null) {
        $data = ['status' => $status];
        if ($adminNotes !== null) {
            $data['admin_notes'] = $adminNotes;
        }
        return $this->update($appointmentId, $data);
    }
    
    /**
     * Obtener citas pendientes
     * @return array
     */
    public function getPendingAppointments() {
        $query = "SELECT a.*, p.name as pet_name, u.name as owner_name 
                  FROM {$this->table} a 
                  INNER JOIN pets p ON a.pet_id = p.id 
                  INNER JOIN users u ON a.user_id = u.id 
                  WHERE a.status = 'Pendiente' 
                  AND a.appointment_date >= CURDATE()
                  ORDER BY a.appointment_date ASC, a.start_time ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener historial completo de citas (para administrador)
     * @return array
     */
    public function getAllAppointmentsHistory() {
        $query = "SELECT a.*, 
                  p.name as pet_name, p.species as pet_species, p.breed as pet_breed, 
                  p.age as pet_age, p.weight as pet_weight,
                  u.name as owner_name, u.email as owner_email, u.phone as owner_phone, u.rut as owner_rut
                  FROM {$this->table} a 
                  INNER JOIN pets p ON a.pet_id = p.id 
                  INNER JOIN users u ON a.user_id = u.id 
                  ORDER BY a.appointment_date DESC, a.start_time DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar citas por término de búsqueda (nombre cliente, mascota, RUT)
     * @param string $searchTerm
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function searchAppointments($searchTerm = '', $dateFrom = null, $dateTo = null) {
        $searchValue = !empty($searchTerm) ? '%' . $searchTerm . '%' : '';
        
        $query = "SELECT a.*, 
                  p.name as pet_name, p.species as pet_species, p.breed as pet_breed, 
                  p.age as pet_age, p.weight as pet_weight,
                  u.name as owner_name, u.email as owner_email, u.phone as owner_phone, u.rut as owner_rut";
        
        // Agregar columnas de prioridad si hay búsqueda
        if (!empty($searchTerm)) {
            $query .= ",
                  LEAST(
                      IF(u.name LIKE " . $this->db->quote($searchValue) . ", 
                          IF(u.name LIKE " . $this->db->quote(strtolower($searchTerm) . '%') . " OR u.name LIKE " . $this->db->quote(ucfirst(strtolower($searchTerm)) . '%') . ", 0.5, 1), 
                          999),
                      IF(p.name LIKE " . $this->db->quote($searchValue) . ", 
                          IF(p.name LIKE " . $this->db->quote(strtolower($searchTerm) . '%') . " OR p.name LIKE " . $this->db->quote(ucfirst(strtolower($searchTerm)) . '%') . ", 1.5, 2), 
                          999),
                      IF(u.email LIKE " . $this->db->quote($searchValue) . ", 3, 999),
                      IF(u.rut LIKE " . $this->db->quote($searchValue) . ", 4, 999)
                  ) as priority";
        }
        
        $query .= " FROM {$this->table} a 
                  INNER JOIN pets p ON a.pet_id = p.id 
                  INNER JOIN users u ON a.user_id = u.id";
        
        $conditions = [];
        $params = [];
        
        if (!empty($searchTerm)) {
            $conditions[] = "(u.name LIKE ? OR p.name LIKE ? OR u.rut LIKE ? OR u.email LIKE ?)";
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
        }
        
        if (!empty($dateFrom)) {
            $conditions[] = "a.appointment_date >= ?";
            $params[] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $conditions[] = "a.appointment_date <= ?";
            $params[] = $dateTo;
        }
        
        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Ordenar por prioridad si hay búsqueda
        if (!empty($searchTerm)) {
            $query .= " ORDER BY priority ASC, a.appointment_date DESC, a.start_time DESC";
        } else {
            $query .= " ORDER BY a.appointment_date DESC, a.start_time DESC";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        
        return $results;
    }
}

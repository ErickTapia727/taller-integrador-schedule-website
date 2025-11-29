<?php
/**
 * API para gestionar citas
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Pet.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/validators.php';
require_once __DIR__ . '/../includes/mail.php';

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit();
}

$userId = intval($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    $appointmentModel = new Appointment();
    $petModel = new Pet();
    $userModel = new User();
    
    switch ($method) {
        case 'GET':
            // Obtener citas
            if ($action === 'get_appointments') {
                $date = isset($_GET['date']) ? $_GET['date'] : null;
                $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
                $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
                
                if ($date) {
                    $appointments = $appointmentModel->getAppointmentsByDate($date);
                } elseif ($startDate && $endDate) {
                    $appointments = $appointmentModel->getAppointmentsByDateRange($startDate, $endDate);
                } elseif ($isAdmin) {
                    $appointments = $appointmentModel->getAllAppointmentsHistory();
                } else {
                    $appointments = $appointmentModel->getAppointmentsByUserId($userId);
                }
                
                echo json_encode(['success' => true, 'appointments' => $appointments]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($action === 'create') {
                // Log received data for debugging
                error_log("Datos recibidos en API: " . json_encode($data));
                
                // Crear nueva cita
                if (!isset($data['pet_id']) || !isset($data['service']) || 
                    !isset($data['appointment_date']) || !isset($data['start_time']) || 
                    !isset($data['end_time'])) {
                    
                    // Identificar qué datos faltan
                    $missing = [];
                    if (!isset($data['pet_id'])) $missing[] = 'pet_id';
                    if (!isset($data['service'])) $missing[] = 'service';
                    if (!isset($data['appointment_date'])) $missing[] = 'appointment_date';
                    if (!isset($data['start_time'])) $missing[] = 'start_time';
                    if (!isset($data['end_time'])) $missing[] = 'end_time';
                    
                    http_response_code(400);
                    echo json_encode([
                        'success' => false, 
                        'error' => 'Datos incompletos', 
                        'missing_fields' => $missing,
                        'received_data' => $data
                    ]);
                    exit();
                }
                
                // Validar que la mascota pertenece al usuario (si no es admin)
                if (!$isAdmin) {
                    $pet = $petModel->getById($data['pet_id']);
                    if (!$pet || $pet['user_id'] != $userId) {
                        http_response_code(403);
                        echo json_encode(['success' => false, 'error' => 'No tienes permiso para agendar para esta mascota']);
                        exit();
                    }
                }
                
                // Verificar disponibilidad
                $available = $appointmentModel->isTimeSlotAvailable(
                    $data['appointment_date'],
                    $data['start_time'],
                    $data['end_time']
                );
                
                if (!$available) {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'error' => 'El horario no está disponible']);
                    exit();
                }
                
                // Crear cita
                $appointmentData = [
                    'user_id' => $isAdmin && isset($data['user_id']) ? $data['user_id'] : $userId,
                    'pet_id' => $data['pet_id'],
                    'appointment_date' => $data['appointment_date'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'service' => $data['service'],
                    'status' => isset($data['status']) ? $data['status'] : 'Pendiente',
                    'notes' => isset($data['notes']) ? $data['notes'] : null,
                    'admin_notes' => isset($data['admin_notes']) ? $data['admin_notes'] : null
                ];
                
                $appointmentId = $appointmentModel->createAppointment($appointmentData);
                
                if ($appointmentId) {
                    echo json_encode(['success' => true, 'appointment_id' => $appointmentId]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Error al crear la cita']);
                }
                
            } elseif ($action === 'block') {
                // Bloquear horario (solo admin)
                if (!$isAdmin) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'No autorizado']);
                    exit();
                }
                
                // Crear una cita de tipo "bloqueado"
                $appointmentData = [
                    'user_id' => $userId,
                    'pet_id' => 1, // ID ficticio para bloqueos
                    'appointment_date' => $data['appointment_date'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'service' => 'Bloqueo administrativo',
                    'status' => 'Bloqueado', // Estado específico para bloqueos
                    'admin_notes' => isset($data['reason']) ? $data['reason'] : 'Bloqueado por administrador'
                ];
                
                $appointmentId = $appointmentModel->createAppointment($appointmentData);
                
                if ($appointmentId) {
                    echo json_encode(['success' => true, 'appointment_id' => $appointmentId]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Error al bloquear horario']);
                }
                
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($action === 'update_status') {
                // Actualizar estado de cita
                if (!isset($data['appointment_id']) || !isset($data['status'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                    exit();
                }
                
                // Opcional: notas del admin
                $adminNotes = isset($data['admin_notes']) ? $data['admin_notes'] : null;
                
                // Obtener datos de la cita antes de actualizar
                $appointment = $appointmentModel->getById($data['appointment_id']);
                if (!$appointment) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Cita no encontrada']);
                    exit();
                }
                
                // Actualizar estado
                $result = $appointmentModel->updateStatus(
                    $data['appointment_id'], 
                    $data['status'],
                    $adminNotes
                );
                
                if ($result) {
                    // Obtener información completa para el email
                    $appointmentDetails = $appointmentModel->getById($data['appointment_id']);
                    
                    // Obtener datos del usuario
                    $user = $userModel->getById($appointment['user_id']);
                    
                    // Obtener datos de la mascota
                    $pet = $petModel->getById($appointment['pet_id']);
                    
                    if ($user && $user['email']) {
                        $appointmentData = [
                            'appointment_date' => $appointment['appointment_date'],
                            'start_time' => $appointment['start_time'],
                            'end_time' => $appointment['end_time'],
                            'pet_name' => $pet ? $pet['name'] : 'Mascota'
                        ];
                        
                        // Enviar email según el nuevo estado
                        if ($data['status'] === 'Cancelado') {
                            sendCancellationEmail(
                                $user['email'],
                                $user['name'],
                                $appointmentData,
                                $adminNotes ?? ''
                            );
                        } elseif ($data['status'] === 'Completado') {
                            sendCompletionEmail(
                                $user['email'],
                                $user['name'],
                                $appointmentData,
                                $adminNotes ?? ''
                            );
                        }
                    }
                    
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            }
            break;
            
        case 'DELETE':
            if ($action === 'cancel') {
                $appointmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
                
                if (!$appointmentId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'ID inválido']);
                    exit();
                }
                
                // Verificar si es un bloqueo o una cita real
                $appointment = $appointmentModel->getById($appointmentId);
                
                if ($appointment && $appointment['status'] === 'Bloqueado') {
                    // Es un bloqueo, eliminar completamente
                    $result = $appointmentModel->delete($appointmentId);
                } else {
                    // Es una cita real, solo cambiar estado
                    $result = $appointmentModel->updateStatus($appointmentId, 'Cancelado');
                }
                
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Error al cancelar']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    }
    
} catch (Exception $e) {
    error_log("Error en API appointments: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error del servidor']);
}
?>

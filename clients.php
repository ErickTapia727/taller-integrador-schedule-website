<?php
// 1. Set the page context variables
$active_link = 'clients'; // This is for the sidebar highlight

// Si hay sesión disponible podemos determinar el role antes de incluir header.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_admin_check = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

if ($is_admin_check) {
    $page_title = "Administrar Clientes";
} else {
    $page_title = "Mis Mascotas";
}

// Load models for database operations
require_once __DIR__ . '/models/Pet.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Appointment.php';

$petModel = new Pet();

// Load clients with appointments if admin
$clientsWithAppointments = [];
if ($is_admin_check) {
    try {
        $appointmentModel = new Appointment();
        $userModel = new User();
        
        // Get all appointments with user info
        $appointments = $appointmentModel->getAllAppointmentsHistory();
        
        // Extract unique users
        $userIds = [];
        foreach ($appointments as $apt) {
            if (!in_array($apt['user_id'], $userIds)) {
                $userIds[] = $apt['user_id'];
                $clientsWithAppointments[] = [
                    'id' => $apt['user_id'],
                    'name' => $apt['owner_name'],
                    'rut' => $apt['owner_rut'],
                    'phone' => $apt['owner_phone'],
                    'email' => $apt['owner_email']
                ];
            }
        }
    } catch (Exception $e) {
        error_log('Error loading clients: ' . $e->getMessage());
    }
}

// -----------------------
// Initialize demo storage in session (for backwards compatibility)
// -----------------------
if (!isset($_SESSION['clients'])) {
    $_SESSION['clients'] = [];
    $_SESSION['next_client_id'] = 1;
}
if (!isset($_SESSION['pets'])) {
    $_SESSION['pets'] = [];
    $_SESSION['next_pet_id'] = 1;
}

// -----------------------
// AJAX / server-side handlers
// -----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['ajax_action'];

    $respond = function($ok, $data = []) {
        echo json_encode(array_merge(['success' => $ok], $data));
        exit();
    };

    // Add or edit pet (for clients)
    if ($action === 'add_or_edit_pet') {
        $id = isset($_POST['petId']) && $_POST['petId'] !== '' ? intval($_POST['petId']) : null;
        $name = isset($_POST['petName']) ? trim($_POST['petName']) : '';
        $breed = isset($_POST['petBreed']) ? trim($_POST['petBreed']) : '';
        $weight = isset($_POST['petWeight']) ? floatval($_POST['petWeight']) : null;
        $birth = isset($_POST['petBirthdate']) ? trim($_POST['petBirthdate']) : '';
        $notes = isset($_POST['petNotes']) ? trim($_POST['petNotes']) : '';
        $species = isset($_POST['petSpecies']) ? trim($_POST['petSpecies']) : 'Perro';
        
        // Get current user id
        $ownerId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
        
        if (!$ownerId) {
            $respond(false, ['error' => 'Usuario no identificado']);
        }

        // Basic validation
        if (empty($name)) {
            $respond(false, ['error' => 'El nombre es requerido']);
        }
        
        // Calculate age from birthdate
        $age = null;
        if (!empty($birth)) {
            $birthDate = new DateTime($birth);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
        }
        
        // Prepare data for database
        $petData = [
            'user_id' => $ownerId,
            'name' => $name,
            'species' => $species,
            'breed' => $breed,
            'age' => $age,
            'weight' => $weight,
            'notes' => $notes
        ];
        
        try {
            if ($id === null) {
                // Create new pet
                $newId = $petModel->create($petData);
                if ($newId) {
                    $petData['id'] = $newId;
                    $respond(true, ['pet' => $petData, 'message' => 'Mascota registrada exitosamente']);
                } else {
                    $respond(false, ['error' => 'Error al registrar la mascota']);
                }
            } else {
                // Update existing pet
                $success = $petModel->update($id, $petData);
                if ($success) {
                    $petData['id'] = $id;
                    $respond(true, ['pet' => $petData, 'message' => 'Mascota actualizada exitosamente']);
                } else {
                    $respond(false, ['error' => 'Error al actualizar la mascota']);
                }
            }
        } catch (Exception $e) {
            $respond(false, ['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    }

    // Delete pet (for clients)
    if ($action === 'delete_pet') {
        $id = isset($_POST['petId']) ? intval($_POST['petId']) : 0;
        $ownerId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
        
        if (!$ownerId) {
            $respond(false, ['error' => 'Usuario no identificado']);
        }
        
        if ($id) {
            try {
                // Verify ownership
                $pet = $petModel->findById($id);
                if ($pet && $pet['user_id'] == $ownerId) {
                    $success = $petModel->delete($id);
                    if ($success) {
                        $respond(true, ['message' => 'Mascota eliminada exitosamente']);
                    } else {
                        $respond(false, ['error' => 'Error al eliminar la mascota']);
                    }
                } else {
                    $respond(false, ['error' => 'No tienes permisos para eliminar esta mascota']);
                }
            } catch (Exception $e) {
                $respond(false, ['error' => 'Error en la base de datos: ' . $e->getMessage()]);
            }
        } else {
            $respond(false, ['error' => 'ID de mascota inválido']);
        }
    }

    // Return pets for a given client (admin function)
    if ($action === 'get_client_pets') {
        $clientId = isset($_POST['clientId']) ? intval($_POST['clientId']) : 0;
        
        if (!$clientId) {
            $respond(false, ['error' => 'ID de cliente inválido']);
        }
        
        try {
            // Get pets from database
            $pets = $petModel->getPetsByUserId($clientId);
            
            // Format pets data
            $formattedPets = [];
            foreach ($pets as $pet) {
                $formattedPets[] = [
                    'id' => $pet['id'],
                    'name' => $pet['name'],
                    'species' => $pet['species'],
                    'breed' => $pet['breed'],
                    'age' => $pet['age'],
                    'weight' => $pet['weight'],
                    'notes' => $pet['notes']
                ];
            }
            
            $respond(true, ['pets' => $formattedPets]);
        } catch (Exception $e) {
            $respond(false, ['error' => 'Error al cargar mascotas: ' . $e->getMessage()]);
        }
    }

    // Delete client (and associated pets) - admin function
    if ($action === 'delete_client') {
        $clientId = isset($_POST['clientId']) ? intval($_POST['clientId']) : 0;
        if ($clientId && isset($_SESSION['clients'][$clientId])) {
            unset($_SESSION['clients'][$clientId]);
            // remove pets belonging to this client
            foreach ($_SESSION['pets'] as $pid => $pet) {
                if ($pet['owner_id'] === $clientId) unset($_SESSION['pets'][$pid]);
            }
            $respond(true);
        } else {
            $respond(false, ['error' => 'Cliente no encontrado']);
        }
    }

    // Populate demo clients + pets
    if ($action === 'populate_demo') {
        // create 3 demo clients with pets
        $examples = [
            ['name' => 'María Pérez', 'rut' => '12.345.678-5', 'phone' => '+56 9 1234 5678', 'email' => 'maria@example.com',
                'pets' => [
                    ['name'=>'Fido','breed'=>'Labrador','weight'=>10,'birth'=>'2018-06-12','notes'=>'Muy juguetón'],
                    ['name'=>'Coco','breed'=>'Caniche','weight'=>4,'birth'=>'2021-03-01','notes'=>'Sociable']
                ]
            ],
            ['name' => 'Juan Gómez', 'rut' => '98.765.432-1', 'phone' => '+56 9 8765 4321', 'email' => 'juan@example.com',
                'pets' => [
                    ['name'=>'Rex','breed'=>'Beagle','weight'=>8,'birth'=>'2019-01-05','notes'=>'Activo']
                ]
            ],
            ['name' => 'Ana Ruiz', 'rut' => '11.222.333-k', 'phone' => '+56 9 5555 1111', 'email' => 'ana@example.com',
                'pets' => [] // no pets
            ],
        ];
        foreach ($examples as $c) {
            $cid = $_SESSION['next_client_id']++;
            $_SESSION['clients'][$cid] = ['id'=>$cid,'name'=>$c['name'],'rut'=>$c['rut'],'phone'=>$c['phone'],'email'=>$c['email']];
            foreach ($c['pets'] as $pet) {
                $pid = $_SESSION['next_pet_id']++;
                $_SESSION['pets'][$pid] = [
                    'id' => $pid,
                    'owner_id' => $cid,
                    'name' => $pet['name'],
                    'breed' => $pet['breed'],
                    'weight' => $pet['weight'],
                    'birth' => $pet['birth'],
                    'notes' => $pet['notes'],
                ];
            }
        }
        $respond(true, ['clients' => array_values($_SESSION['clients'])]);
    }

    $respond(false, ['error' => 'Acción no reconocida']);
}
// -----------------------
// End AJAX handlers
// -----------------------

// 3. Include the header
include 'layout/header.php';
?>

<!-- 4. Page Content -->
<div class="container-fluid">

    <?php if ($is_admin): ?>
    <!-- ADMIN VIEW: list clients -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold">Clientes registrados</h5>
            <div>
                <a href="clients.php" class="btn btn-secondary btn-sm">Refrescar</a>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($clientsWithAppointments)): ?>
                <div class="p-4 text-center text-muted">
                    No hay clientes registrados. Los clientes aparecerán aquí cuando tengan al menos una cita agendada.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>RUT</th>
                                <th>Teléfono</th>
                                <th>Correo electrónico</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientsWithAppointments as $client): ?>
                                <tr data-client-id="<?php echo htmlspecialchars($client['id']); ?>">
                                    <td><?php echo htmlspecialchars($client['id']); ?></td>
                                    <td><?php echo htmlspecialchars($client['name']); ?></td>
                                    <td><?php echo htmlspecialchars($client['rut'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($client['phone'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button class="btn btn-sm btn-outline-primary view-pets-btn" data-client-id="<?php echo htmlspecialchars($client['id']); ?>" data-client-name="<?php echo htmlspecialchars($client['name']); ?>">
                                                <i class="bi bi-card-list"></i> Ver mascotas
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal: show client's pets -->
    <div class="modal fade" id="clientPetsModal" tabindex="-1" aria-labelledby="clientPetsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="clientPetsModalLabel">Mascotas del cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div id="clientPetsContainer">Cargando...</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- CLIENT VIEW: show only the logged-in client's pets -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold">Mis Mascotas</h2>
        <button class="btn" id="openRegisterBtn" style="background-color: var(--active-link-color); color: white;" data-bs-toggle="modal" data-bs-target="#registerPetModal">
            <i class="bi bi-plus-circle-fill me-2"></i> Agregar Mascota
        </button>
    </div>

    <div class="row g-3" id="petsRow">
        <?php
        // For clients, load their pets from database
        $uid = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $myPets = [];
        if ($uid > 0) {
            $myPets = $petModel->getPetsByUserId($uid);
        }
        
        if (empty($myPets)) {
            echo '<div class="col-12"><div class="alert alert-info">No tienes mascotas registradas. Haz clic en "Agregar Mascota" para registrar una.</div></div>';
        } else {
            foreach ($myPets as $pet) {
                $birthDisplay = $pet['age'] ? $pet['age'] . ' año' . ($pet['age'] != 1 ? 's' : '') : '—';
                $weightDisplay = $pet['weight'] ? number_format($pet['weight'], 1) . ' kg' : '—';
                $speciesDisplay = $pet['species'] ?? 'Perro';
                
                echo '<div class="col-md-6 col-lg-4" data-pet-id="'.htmlspecialchars($pet['id']).'">';
                echo '  <div class="card shadow-sm h-100">';
                echo '    <div class="card-body d-flex flex-column">';
                echo '      <h5 class="card-title h4 fw-bold">'.htmlspecialchars($pet['name'] ?: 'Sin nombre').'</h5>';
                echo '      <h6 class="card-subtitle mb-2 text-muted">'.htmlspecialchars($pet['breed'] ?: 'Raza no especificada').' - '.$weightDisplay.'</h6>';
                echo '      <p class="card-text"><ul class="list-unstyled mb-0">';
                echo '        <li><strong>Especie:</strong> '.htmlspecialchars($speciesDisplay).'</li>';
                echo '        <li><strong>Edad:</strong> '.htmlspecialchars($birthDisplay).'</li>';
                echo '        <li><strong>Notas:</strong> '.htmlspecialchars($pet['notes'] ?: '—').'</li>';
                echo '      </ul></p>';
                echo '      <div class="mt-auto pt-3">';
                echo '        <button class="btn btn-sm btn-outline-secondary edit-pet-btn" data-pet-id="'.htmlspecialchars($pet['id']).'"><i class="bi bi-pencil-fill"></i> Editar</button> ';
                echo '        <button class="btn btn-sm btn-outline-danger delete-pet-btn" data-pet-id="'.htmlspecialchars($pet['id']).'"><i class="bi bi-trash-fill"></i> Eliminar</button>';
                echo '      </div>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
        }
        ?>
    </div>

    <?php endif; ?>

</div>
<!-- === END PAGE CONTENT === -->

<!-- REGISTER / EDIT PET MODAL (for clients) -->
<?php if (!$is_admin): ?>
<div class="modal fade" id="registerPetModal" tabindex="-1" aria-labelledby="registerPetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="register-pet-form" class="needs-validation" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="registerPetModalLabel">Registrar / Editar Mascota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="petId" name="petId" value="">

                    <div class="mb-3">
                        <label for="petName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="petName" name="petName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="petSpecies" class="form-label">Especie <span class="text-danger">*</span></label>
                        <select class="form-select" id="petSpecies" name="petSpecies" required>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="petBreed" class="form-label">Raza <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="petBreed" name="petBreed" required>
                    </div>

                    <div class="mb-3">
                        <label for="petWeight" class="form-label">Peso (kg) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="petWeight" name="petWeight" required>
                    </div>

                    <div class="mb-3">
                        <label for="petBirthdate" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="petBirthdate" name="petBirthdate">
                    </div>

                    <div class="mb-3">
                        <label for="petNotes" class="form-label">Notas</label>
                        <textarea class="form-control" id="petNotes" name="petNotes" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button id="savePetBtn" type="submit" class="btn btn-black">Guardar Mascota</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 5. Page-Specific JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const petsRow = document.getElementById('petsRow');
    const petForm = document.getElementById('register-pet-form');
    const registerModalEl = document.getElementById('registerPetModal');
    const registerModal = registerModalEl ? new bootstrap.Modal(registerModalEl) : null;
    const clientPetsModalEl = document.getElementById('clientPetsModal');
    const clientPetsModal = clientPetsModalEl ? new bootstrap.Modal(clientPetsModalEl) : null;
    const clientPetsContainer = document.getElementById('clientPetsContainer');

    // Helper: format YYYY-MM-DD -> dd/mm/yyyy
    function formatDateDMY(dateStr) {
        if (!dateStr) return '';
        const p = dateStr.split('-');
        if (p.length !== 3) return dateStr;
        return `${p[2]}/${p[1]}/${p[0]}`;
    }

    // Build pet card DOM from pet object (for client view)
    function buildPetCard(pet) {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        col.setAttribute('data-pet-id', pet.id);
        col.innerHTML = `
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title h4 fw-bold">${pet.name || 'Sin nombre'}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">${pet.breed || 'Raza no especificada'} - ${isNaN(pet.weight) ? '-' : (pet.weight + 'kg')}</h6>
                    <p class="card-text"><ul class="list-unstyled mb-0">
                        <li><strong>Nacimiento:</strong> ${formatDateDMY(pet.birth)}</li>
                        <li><strong>Notas:</strong> ${pet.notes || '—'}</li>
                    </ul></p>
                    <div class="mt-auto pt-3">
                        <button class="btn btn-sm btn-outline-secondary edit-pet-btn" data-pet-id="${pet.id}"><i class="bi bi-pencil-fill"></i> Editar</button>
                        <button class="btn btn-sm btn-outline-danger delete-pet-btn" data-pet-id="${pet.id}"><i class="bi bi-trash-fill"></i> Eliminar</button>
                    </div>
                </div>
            </div>
        `;
        attachCardListeners(col);
        return col;
    }

    // Attach listeners for edit/delete buttons inside a pet card column element
    function attachCardListeners(col) {
        const editBtn = col.querySelector('.edit-pet-btn');
        const delBtn = col.querySelector('.delete-pet-btn');

        if (editBtn) {
            editBtn.addEventListener('click', (e) => {
                const id = editBtn.getAttribute('data-pet-id');
                const cardCol = document.querySelector(`[data-pet-id="${id}"]`);
                if (!cardCol) return;
                
                // Parse data from card DOM
                const title = cardCol.querySelector('.card-title')?.textContent || '';
                const subtitle = cardCol.querySelector('.card-subtitle')?.textContent || '';
                const breedWeight = subtitle.split(' - ');
                const breed = breedWeight[0] ? breedWeight[0].trim() : '';
                const weight = breedWeight[1] ? breedWeight[1].replace('kg','').trim() : '';
                
                // Parse birth date from card
                const birthLi = cardCol.querySelector('.card-text li');
                let birth = '';
                if (birthLi) {
                    const txt = birthLi.textContent || '';
                    const m = txt.match(/Nacimiento:\s*([\d\/]+)/);
                    if (m && m[1]) {
                        const parts = m[1].split('/');
                        if (parts.length === 3) birth = `${parts[2]}-${parts[1]}-${parts[0]}`; // back to YYYY-MM-DD
                    }
                }
                
                // Parse notes
                const notesLi = cardCol.querySelectorAll('.card-text li')[1];
                const notes = notesLi ? notesLi.textContent.replace('Notas:', '').trim() || '' : '';
                
                // Populate modal fields
                document.getElementById('petId').value = id;
                document.getElementById('petName').value = title;
                document.getElementById('petBreed').value = breed;
                document.getElementById('petWeight').value = weight;
                document.getElementById('petBirthdate').value = birth;
                document.getElementById('petNotes').value = notes;
                
                if (registerModal) registerModal.show();
            });
        }

        if (delBtn) {
            delBtn.addEventListener('click', async (e) => {
                const id = delBtn.getAttribute('data-pet-id');
                if (!confirm('¿Eliminar esta mascota? Esta acción no puede deshacerse.')) return;
                const form = new URLSearchParams();
                form.append('ajax_action','delete_pet');
                form.append('petId', id);
                try {
                    const res = await fetch(window.location.pathname, { method: 'POST', body: form });
                    const json = await res.json();
                    if (json.success) {
                        const el = document.querySelector(`[data-pet-id="${id}"]`);
                        if (el) el.remove();
                        // If no pets left, show empty message
                        if (petsRow && petsRow.children.length === 0) {
                            petsRow.innerHTML = '<div class="col-12 text-muted">No tienes mascotas registradas.</div>';
                        }
                    } else {
                        alert(json.error || 'Error al eliminar');
                    }
                } catch (err) {
                    alert('Error de red al eliminar');
                }
            });
        }
    }

    // Attach listeners to existing cards (for client view)
    document.querySelectorAll('#petsRow > [data-pet-id]').forEach(col => attachCardListeners(col));

    // Open empty form when clicking the "add" button (client view)
    const openRegisterBtn = document.getElementById('openRegisterBtn');
    if (openRegisterBtn) {
        openRegisterBtn.addEventListener('click', () => {
            // clear form for new pet
            document.getElementById('petId').value = '';
            if (petForm) petForm.reset();
        });
    }

    // Submit form (create or edit) via AJAX - CLIENT VIEW
    if (petForm) {
        petForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('petId').value;
            const name = document.getElementById('petName').value.trim();
            const breed = document.getElementById('petBreed').value.trim();
            const weightEl = document.getElementById('petWeight');
            const weight = parseFloat(weightEl.value);
            const birth = document.getElementById('petBirthdate').value;
            const notes = document.getElementById('petNotes').value.trim();

            if (!breed) {
                alert('Raza es obligatoria');
                return;
            }
            if (isNaN(weight) || weight < 0) {
                alert('Ingrese un peso válido');
                return;
            }
            if (weight > 12) {
                alert('El peso no puede superar 12 kg.');
                return;
            }

            const form = new URLSearchParams();
            form.append('ajax_action','add_or_edit_pet');
            form.append('petId', id);
            form.append('petName', name);
            form.append('petBreed', breed);
            form.append('petWeight', weight);
            form.append('petBirthdate', birth);
            form.append('petNotes', notes);

            try {
                const res = await fetch(window.location.pathname, { method: 'POST', body: form });
                const json = await res.json();
                if (json.success && json.pet) {
                    const pet = json.pet;
                    // If editing, replace existing card; if creating, prepend
                    const existing = document.querySelector(`[data-pet-id="${pet.id}"]`);
                    const newCard = buildPetCard(pet);
                    if (existing) {
                        existing.replaceWith(newCard);
                    } else {
                        // Remove empty message if exists
                        const emptyMsg = petsRow.querySelector('.col-12.text-muted');
                        if (emptyMsg) emptyMsg.remove();
                        petsRow.prepend(newCard);
                    }
                    if (registerModal) registerModal.hide();
                    petForm.reset();
                } else {
                    alert(json.error || 'Error al guardar mascota');
                }
            } catch (err) {
                alert('Error de red al guardar mascota');
            }
        });
    }

    // ADMIN FUNCTIONS
    // Admin: view pets button
    document.querySelectorAll('.view-pets-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const clientId = btn.getAttribute('data-client-id');
            const clientName = btn.getAttribute('data-client-name');
            if (!clientId) return;
            
            // Update modal title
            const modalTitle = document.getElementById('clientPetsModalLabel');
            if (modalTitle) {
                modalTitle.textContent = `Mascotas de ${clientName}`;
            }
            
            if (clientPetsContainer) clientPetsContainer.innerHTML = 'Cargando...';
            const form = new URLSearchParams();
            form.append('ajax_action','get_client_pets');
            form.append('clientId', clientId);
            try {
                const res = await fetch(window.location.pathname, { method: 'POST', body: form });
                const json = await res.json();
                if (json.success) {
                    const pets = json.pets || [];
                    if (pets.length === 0) {
                        clientPetsContainer.innerHTML = '<div class="text-muted">Este cliente no tiene mascotas registradas.</div>';
                    } else {
                        const row = document.createElement('div');
                        row.className = 'row g-3';
                        pets.forEach(p => {
                            const col = document.createElement('div');
                            col.className = 'col-md-6';
                            col.innerHTML = `
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-1"><i class="bi bi-heart-fill me-2" style="color: var(--active-link-color);"></i>${p.name || 'Sin nombre'}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">${p.species || '—'}</h6>
                                        <p class="mb-1"><strong>Raza:</strong> ${p.breed || '—'}</p>
                                        <p class="mb-1"><strong>Edad:</strong> ${p.age ? p.age + ' años' : '—'}</p>
                                        <p class="mb-1"><strong>Peso:</strong> ${p.weight ? p.weight + ' kg' : '—'}</p>
                                        <p class="mb-0"><strong>Notas:</strong> ${p.notes || '—'}</p>
                                    </div>
                                </div>
                            `;
                            row.appendChild(col);
                        });
                        clientPetsContainer.innerHTML = '';
                        clientPetsContainer.appendChild(row);
                    }
                    if (clientPetsModal) clientPetsModal.show();
                } else {
                    if (clientPetsContainer) clientPetsContainer.innerHTML = '<div class="text-danger">Error al obtener mascotas: ' + (json.error || 'Desconocido') + '</div>';
                }
            } catch (err) {
                if (clientPetsContainer) clientPetsContainer.innerHTML = '<div class="text-danger">Error de red: ' + err.message + '</div>';
            }
        });
    });

    // Admin: delete client button
    document.querySelectorAll('.delete-client-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const clientId = btn.getAttribute('data-client-id');
            if (!clientId) return;
            if (!confirm('¿Eliminar/banear este cliente? Esta acción eliminará también sus mascotas.')) return;
            const form = new URLSearchParams();
            form.append('ajax_action','delete_client');
            form.append('clientId', clientId);
            try {
                const res = await fetch(window.location.pathname, { method: 'POST', body: form });
                const json = await res.json();
                if (json.success) {
                    // remove row from table
                    const tr = document.querySelector(`tr[data-client-id="${clientId}"]`);
                    if (tr) tr.remove();
                } else {
                    alert(json.error || 'Error al eliminar cliente');
                }
            } catch (err) {
                alert('Error de red al eliminar cliente');
            }
        });
    });

    // Populate demo data (admin)
    const adminPopulateBtn = document.getElementById('adminPopulateBtn');
    if (adminPopulateBtn) {
        adminPopulateBtn.addEventListener('click', async () => {
            if (!confirm('Poblar con clientes y mascotas de ejemplo?')) return;
            const form = new URLSearchParams();
            form.append('ajax_action','populate_demo');
            try {
                const res = await fetch(window.location.pathname, { method: 'POST', body: form });
                const json = await res.json();
                if (json.success) {
                    location.reload();
                } else {
                    alert(json.error || 'Error al poblar datos de ejemplo');
                }
            } catch (err) {
                alert('Error de red al poblar');
            }
        });
    }
});
</script>

<?php
// 6. Include the standard footer
include 'layout/footer.php';
?>
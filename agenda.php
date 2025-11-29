<?php
// Set context variables for the header template
$page_title = "Agenda";
$active_link = 'agenda';

// Include the standard header
include 'layout/header.php';

// Load Pet model to get current user's pets from database
require_once __DIR__ . '/models/Pet.php';

// Get current user's pets for JavaScript
$userPets = [];
if (!$is_admin && isset($_SESSION['user_id'])) {
    $currentUserId = intval($_SESSION['user_id']);
    $petModel = new Pet();
    $pets = $petModel->getPetsByUserId($currentUserId);
    
    foreach ($pets as $pet) {
        $userPets[] = [
            'id' => $pet['id'],
            'name' => $pet['name'],
            'breed' => $pet['breed'] ?? 'Raza no especificada'
        ];
    }
}
$userPetsJson = json_encode($userPets);
?>

<style>
    /* Estilos para las celdas del calendario */
    .hourly-cell {
        cursor: pointer;
        transition: background-color 0.2s;
        min-height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .hourly-cell:hover:not(.disabled):not(.bg-secondary):not(.bg-info):not(.bg-success) {
        background-color: rgba(255, 171, 196, 0.2) !important;
    }
    
    .hourly-cell.disabled {
        cursor: not-allowed !important;
        opacity: 0.8;
    }
    
    .hourly-cell.bg-secondary {
        background-color: #6c757d !important;
        cursor: not-allowed !important;
    }
    
    /* Casillas bloqueadas - clickeables para admin */
    .hourly-cell.bg-dark {
        background-color: #343a40 !important;
        cursor: pointer !important;
        opacity: 0.85;
    }
    
    .hourly-cell.bg-dark:hover {
        background-color: #495057 !important;
        opacity: 1;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }
    
    .hourly-cell.bg-info {
        background-color: #fff3cd !important;
        color: #856404 !important;
        cursor: pointer;
        min-height: 110px;
        border: 1px solid #ffc107 !important;
    }
    
    .hourly-cell.bg-info:hover {
        background-color: #ffe69c !important;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.4);
    }
    
    /* Estilo para citas completadas */
    .hourly-cell.bg-success {
        background-color: #d4edda !important;
        color: #155724 !important;
        cursor: pointer;
        min-height: 110px;
        border: 2px solid #28a745 !important;
    }
    
    .hourly-cell.bg-success:hover {
        background-color: #c3e6cb !important;
        box-shadow: 0 0 15px rgba(40, 167, 69, 0.4);
    }
</style>

<!-- === CALENDAR CONTROLS === -->
<div class="card p-4 mb-4 shadow-sm">
    
    <?php if ($is_admin): ?>
    <!-- ADMINISTRATOR VIEW -->
    <div class="d-flex justify-content-center justify-content-md-start align-items-center mb-3 admin-nav-control">
        <button class="btn btn-outline-secondary me-3" id="prevMonthYearBtn"><i class="bi bi-chevron-left"></i></button>
        <select class="form-select me-2" id="adminYearSelect"></select>
        <select class="form-select me-3" id="adminMonthSelect"></select>
        <button class="btn btn-outline-secondary" id="nextMonthYearBtn"><i class="bi bi-chevron-right"></i></button>
    </div>

    <?php else: ?>
    <!-- CLIENT VIEW -->
    <ul class="nav nav-tabs border-bottom-0 month-nav-tabs mb-3" id="monthTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="month1-tab" type="button">Current Month</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="month2-tab" type="button">Next Month</button>
      </li>
    </ul>
    <?php endif; ?>

    <div class="btn-toolbar justify-content-center week-selector" role="toolbar" aria-label="Selector de semana" id="weekSelectionBar"></div>
</div>

<!-- === CALENDAR GRID === -->
<div class="card shadow-sm p-3">
    <div class="hourly-grid-container bg-white">
        <div class="row g-0" id="weekDayHeader"></div>
        <div id="hourlyGridBody"></div>
    </div>
</div>


<!-- =================================================================== -->
<!-- ========================= MODALS ================================== -->
<!-- =================================================================== -->

<!-- 0. OPTION SELECTOR MODAL (NEW) -->
<!-- This pops up first when clicking a slot -->
<div class="modal fade" id="optionSelectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title fw-bold">Opciones</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-grid gap-2">
         <!-- This content is dynamic based on role -->
         <button id="optActionBtn" class="btn btn-primary">Acción</button>
      </div>
    </div>
  </div>
</div>

<!-- 1. CLIENT: Booking Modal -->
<div class="modal fade" id="clientBookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Agendar Hora</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Estás agendando para el: <span id="bookingDateDisplay" class="fw-bold"></span></p>
        <form id="bookingForm">
            <div class="mb-3">
                <label class="form-label">Servicio</label>
                <select class="form-select" id="bookingService" required>
                    <option value="Baño y corte de pelo" selected>Baño y corte de pelo</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Mascota</label>
                <select class="form-select" id="bookingMascot" required>
                    <!-- Populated by JS -->
                </select>
            </div>
            <input type="hidden" id="bookingSlotId">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn text-white" style="background-color: var(--active-link-color);" id="confirmBookingBtn">Agendar Hora</button>
      </div>
    </div>
  </div>
</div>

<!-- 2. CLIENT: No Mascot Warning Modal -->
<div class="modal fade" id="noMascotModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Atención</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Para agendar una hora, primero debes registrar al menos una mascota en tu perfil.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <a href="clients.php" class="btn btn-danger">Ir a Registrar Mascota</a>
      </div>
    </div>
  </div>
</div>

<!-- 3. ADMIN: Block/Unblock Modal (Refined) -->
<div class="modal fade" id="adminBlockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="blockModalTitle">Gestionar Horario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="blockModalMessage">¿Confirmas que deseas bloquear este horario?</p>
        <input type="hidden" id="blockSlotId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-dark" id="toggleBlockBtn">Confirmar Bloqueo</button>
      </div>
    </div>
  </div>
</div>

<!-- 4. ADMIN: Report Modal (For Booked Slots) -->
<div class="modal fade" id="adminReportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: var(--active-link-color); color: white;">
        <h5 class="modal-title fw-bold"><i class="bi bi-clipboard-data me-2"></i>Detalles de la Cita</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person-circle me-2"></i>Información del Cliente</h6>
            <p class="mb-2"><strong>Nombre:</strong> <span id="reportClientName"></span></p>
            <p class="mb-2"><strong>Email:</strong> <span id="reportClientEmail"></span></p>
            <p class="mb-2"><strong>Teléfono:</strong> <span id="reportClientPhone"></span></p>
            <p class="mb-2"><strong>RUT:</strong> <span id="reportClientRut"></span></p>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-heart-fill me-2"></i>Información de la Mascota</h6>
            <p class="mb-2"><strong>Nombre:</strong> <span id="reportMascotName"></span></p>
            <p class="mb-2"><strong>Especie:</strong> <span id="reportMascotSpecies"></span></p>
            <p class="mb-2"><strong>Raza:</strong> <span id="reportMascotBreed"></span></p>
            <p class="mb-2"><strong>Edad:</strong> <span id="reportMascotAge"></span></p>
            <p class="mb-2"><strong>Peso:</strong> <span id="reportMascotWeight"></span></p>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-12">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-calendar-check me-2"></i>Información de la Cita</h6>
            <p class="mb-2"><strong>Servicio:</strong> <span id="reportServiceName"></span></p>
            <p class="mb-2"><strong>Fecha:</strong> <span id="reportAppointmentDate"></span></p>
            <p class="mb-2"><strong>Horario:</strong> <span id="reportAppointmentTime"></span></p>
            <p class="mb-2"><strong>Estado:</strong> <span id="reportAppointmentStatus" class="badge"></span></p>
          </div>
        </div>
        <hr>
        <div class="mb-3">
            <label class="form-label fw-bold"><i class="bi bi-pencil-square me-2"></i>Notas/Observaciones del Admin</label>
            <textarea class="form-control" rows="4" id="reportNotes" placeholder="Agrega observaciones del servicio o motivo de cancelación. Esta nota se enviará por correo al cliente."></textarea>
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Al marcar como Completado o Cancelar, se enviará un email al cliente con estas notas.
            </small>
        </div>
        <input type="hidden" id="reportSlotId">
        <input type="hidden" id="reportAppointmentId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-success" id="markCompletedBtn"><i class="bi bi-check-circle me-2"></i>Marcar Completado</button>
        <button type="button" class="btn btn-danger" id="markCanceledBtn"><i class="bi bi-x-circle me-2"></i>Cancelar Cita</button>
      </div>
    </div>
  </div>
</div>


<!-- =================================================================== -->
<!-- ========================= JAVASCRIPT ============================== -->
<!-- =================================================================== -->
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- CONFIGURATION ---
        const isAdmin = document.body.dataset.isAdmin === 'true'; 
        const today = new Date(); 
        const month1 = new Date(today.getFullYear(), today.getMonth(), 1); 
        const month2 = new Date(today.getFullYear(), today.getMonth() + 1, 1); 
        let displayedMonth = new Date(month1); 
        let displayedWeekIndex = 0; 

        const timeSlots = ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 14:00', '14:00 - 16:00', '16:00 - 17:00'];
        const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        // --- MOCK DATA (Simulating Database) ---
        const slotState = {};
        
        // --- LOAD APPOINTMENTS FROM DATABASE ---
        const loadAppointments = async () => {
            try {
                // Get current month range
                const startDate = new Date(displayedMonth.getFullYear(), displayedMonth.getMonth(), 1);
                const endDate = new Date(displayedMonth.getFullYear(), displayedMonth.getMonth() + 1, 0);
                
                const response = await fetch(`api/appointments.php?action=get_appointments&start_date=${formatDateKey(startDate)}&end_date=${formatDateKey(endDate)}`);
                const result = await response.json();
                
                console.log('Citas cargadas desde DB:', result);
                
                if (result.success) {
                    // Limpiar estado anterior
                    Object.keys(slotState).forEach(key => delete slotState[key]);
                    
                    result.appointments.forEach(apt => {
                        // Ignorar citas canceladas para que la casilla quede disponible
                        if (apt.status === 'Cancelado') {
                            return;
                        }
                        
                        // Convertir start_time y end_time al formato del slot: "HH:MM - HH:MM"
                        const startTime = apt.start_time.substring(0, 5); // "10:00:00" -> "10:00"
                        const endTime = apt.end_time.substring(0, 5);     // "12:00:00" -> "12:00"
                        const timeSlot = `${startTime} - ${endTime}`;     // "10:00 - 12:00"
                        const slotId = `${apt.appointment_date}_${timeSlot}`;
                        
                        console.log('Generando slotId:', slotId);
                        
                        // Verificar si es un bloqueo
                        if (apt.status === 'Bloqueado') {
                            slotState[slotId] = {
                                status: 'blocked',
                                id: apt.id
                            };
                        } else {
                            // Es una cita normal
                            slotState[slotId] = {
                                status: 'booked',
                                client: apt.owner_name,
                                clientEmail: apt.owner_email,
                                clientPhone: apt.owner_phone,
                                clientRut: apt.owner_rut,
                                mascot: apt.pet_name,
                                mascotSpecies: apt.pet_species,
                                mascotBreed: apt.pet_breed,
                                mascotAge: apt.pet_age ? `${apt.pet_age} años` : 'No especificada',
                                mascotWeight: apt.pet_weight ? `${apt.pet_weight} kg` : 'No especificado',
                                service: apt.service,
                                appointmentDate: apt.appointment_date,
                                appointmentTime: timeSlot,
                                id: apt.id,
                                appointment_status: apt.status
                            };
                        }
                    });
                    
                    console.log('Estado de slots después de cargar:', slotState);
                    renderWeeklyGrid();
                }
            } catch (error) {
                console.error('Error loading appointments:', error);
            }
        };

        // --- CLIENT MASCOTS (Real user data) ---
        const clientMascots = <?php echo $userPetsJson; ?>;
        console.log('Mascotas cargadas:', clientMascots);

        // --- STATE FOR SELECTION ---
        let selectedSlotId = null;
        let selectedDateDisplay = "";
        let selectedTimeDisplay = "";


        // --- DOM ELEMENTS ---
        const weekSelectionBar = document.getElementById('weekSelectionBar');
        const weekDayHeader = document.getElementById('weekDayHeader');
        const hourlyGridBody = document.getElementById('hourlyGridBody');
        
        // Modals
        const optionModal = new bootstrap.Modal(document.getElementById('optionSelectionModal'));
        const bookingModal = new bootstrap.Modal(document.getElementById('clientBookingModal'));
        const noMascotModal = new bootstrap.Modal(document.getElementById('noMascotModal'));
        const adminBlockModal = new bootstrap.Modal(document.getElementById('adminBlockModal'));
        const adminReportModal = new bootstrap.Modal(document.getElementById('adminReportModal'));


        // --- HELPER FUNCTIONS ---
        const getMonthYearString = (date) => {
            let str = date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
            return str.charAt(0).toUpperCase() + str.slice(1);
        };

        const getWeeksInMonth = (date) => {
            const year = date.getFullYear();
            const month = date.getMonth();
            
            // Primer día del mes
            const firstDay = new Date(year, month, 1);
            const firstDayOfWeek = firstDay.getDay(); // 0=Domingo, 1=Lunes, etc
            
            // Último día del mes
            const lastDay = new Date(year, month + 1, 0);
            const totalDaysInMonth = lastDay.getDate();
            
            // Calcular semanas necesarias para mostrar todo el mes
            // Días desde el lunes anterior al primer día del mes hasta el último día
            const daysFromMonday = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1; // Ajuste para que lunes sea el inicio
            const totalDaysToShow = daysFromMonday + totalDaysInMonth;
            const weeksNeeded = Math.ceil(totalDaysToShow / 7);
            
            return weeksNeeded;
        };

        const formatDateKey = (date) => {
            return date.toISOString().split('T')[0]; // Returns YYYY-MM-DD
        };

        const isDayInteractive = (day) => {
            if (isAdmin) {
                const minDate = new Date('2025-01-01T00:00:00');
                return day >= minDate;
            } else {
                return day.getMonth() === displayedMonth.getMonth() && day.getFullYear() === displayedMonth.getFullYear();
            }
        };

        // --- RENDER LOGIC ---
        function renderAll() {
            renderWeekSelectionBar();
            renderWeeklyGrid();
        }

        function renderWeekSelectionBar() {
            weekSelectionBar.innerHTML = ''; 
            const totalWeeks = getWeeksInMonth(displayedMonth);
            const btnGroup = document.createElement('div');
            btnGroup.className = 'btn-group flex-wrap';
            btnGroup.setAttribute('role', 'group');

            for (let i = 0; i < totalWeeks; i++) {
                const button = document.createElement('button');
                button.type = 'button';
                const btnColor = i === displayedWeekIndex ? 'btn-danger active' : 'btn-outline-danger';
                button.className = `btn m-1 ${btnColor}`;
                button.textContent = `Semana ${i + 1}`;
                button.addEventListener('click', () => { displayedWeekIndex = i; renderAll(); });
                btnGroup.appendChild(button);
            }
            weekSelectionBar.appendChild(btnGroup);
        }

        function renderWeeklyGrid() {
            const firstDayOfMonth = new Date(displayedMonth.getFullYear(), displayedMonth.getMonth(), 1);
            
            // Encontrar el lunes anterior o igual al primer día del mes
            let weekStartDate = new Date(firstDayOfMonth);
            const firstDayOfWeek = firstDayOfMonth.getDay(); // 0=Domingo, 1=Lunes, etc
            const daysToSubtract = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1; // Si es domingo, retroceder 6 días
            weekStartDate.setDate(weekStartDate.getDate() - daysToSubtract);
            
            // Avanzar a la semana seleccionada
            weekStartDate.setDate(weekStartDate.getDate() + (displayedWeekIndex * 7));
            
            const daysInWeek = [];
            for (let i = 0; i < 7; i++) {
                daysInWeek.push(new Date(weekStartDate));
                weekStartDate.setDate(weekStartDate.getDate() + 1);
            }

            // Header
            weekDayHeader.innerHTML = '<div class="col" style="flex: 0 0 100px; max-width: 100px;"></div>'; 
            daysInWeek.forEach(day => {
                const isOutOfBounds = day.getMonth() !== displayedMonth.getMonth();
                const headerClass = isOutOfBounds ? 'disabled text-muted' : '';
                weekDayHeader.innerHTML += `
                    <div class="col day-column-header ${headerClass}">
                        ${dayNames[day.getDay()]} <span class="fs-5">${day.getDate()}</span>
                    </div>`;
            });

            // Body
            hourlyGridBody.innerHTML = ''; 
            timeSlots.forEach(slot => {
                const row = document.createElement('div');
                row.className = 'row g-0';
                row.innerHTML = `<div class="col time-slot-label" style="flex: 0 0 100px; max-width: 100px;"><small class="text-nowrap">${slot}</small></div>`;

                daysInWeek.forEach(day => {
                    const dateKey = formatDateKey(day);
                    const slotId = `${dateKey}_${slot}`;
                    const data = slotState[slotId];
                    
                    let cellContent = '';
                    let cellClass = 'hourly-cell';
                    
                    if (data) {
                        if (data.status === 'booked') {
                            if (isAdmin) {
                                // Determinar color según estado de la cita
                                let bgClass = 'bg-info';
                                let statusIcon = '';
                                if (data.appointment_status === 'Completado') {
                                    bgClass = 'bg-success';
                                    statusIcon = '<i class="bi bi-check-circle-fill" style="font-size: 1.2rem;"></i> ';
                                }
                                
                                cellClass += ` ${bgClass} text-white border-white`;
                                cellContent = `<div class="p-2">
                                    <div class="fw-bold" style="font-size: 0.85rem;">${statusIcon}<i class="bi bi-person-fill me-1"></i>${data.client}</div>
                                    <div style="font-size: 0.8rem;"><i class="bi bi-heart-fill me-1"></i>${data.mascot}</div>
                                    <div style="font-size: 0.75rem; opacity: 0.7;">${data.service}</div>
                                </div>`;
                            } else {
                                cellClass += ' bg-secondary text-white disabled';
                                cellContent = '<small>Ocupado</small>';
                            }
                        } else if (data.status === 'blocked') {
                             // Bloqueado - solo para admin, NO agregar disabled
                             cellClass += ' bg-dark text-white';
                             cellContent = '<small><i class="bi bi-lock me-1"></i>Bloqueado</small>';
                        }
                    }

                    // Determine if clickable (Admin can click everything, Client restricted)
                    const isDayDisabled = !isDayInteractive(day) || day.getMonth() !== displayedMonth.getMonth();
                    if (isDayDisabled) {
                        cellClass += ' disabled';
                    }

                    row.innerHTML += `
                        <div class="col ${cellClass}" 
                             data-slot-id="${slotId}"
                             data-date="${dateKey}"
                             data-time="${slot}">
                             ${cellContent}
                        </div>`;
                });
                hourlyGridBody.appendChild(row);
            });
        }

        // --- CLICK HANDLING (STEP 1: OPTION SELECTOR) ---
        hourlyGridBody.addEventListener('click', (e) => {
            // Buscar el contenedor de celda por el atributo data-slot-id para hacerlo robusto
            const cell = e.target.closest('[data-slot-id]');
            if (!cell) return;

            // Evitar clic en celdas deshabilitadas
            if (cell.classList.contains('disabled')) {
                return; // No hacer nada si la celda está deshabilitada
            }
            
            // Para clientes: evitar clic en horarios ocupados o bloqueados
            if (!isAdmin && (cell.classList.contains('bg-secondary') || cell.classList.contains('bg-dark') || cell.classList.contains('bg-info') || cell.classList.contains('bg-success'))) {
                return; // No hacer nada si el horario ya está ocupado
            }
            
            // Para admins: evitar clic solo en bg-secondary (pasado)
            if (isAdmin && cell.classList.contains('bg-secondary')) {
                return; // No permitir interacción con días pasados
            }

            // Store selection state
             selectedSlotId = cell.getAttribute('data-slot-id');
             const rawDate = cell.getAttribute('data-date'); // YYYY-MM-DD
             selectedTimeDisplay = cell.getAttribute('data-time');
             const currentData = slotState[selectedSlotId];
             
             console.log('Click en celda:', {
                 slotId: selectedSlotId,
                 currentData: currentData,
                 isAdmin: isAdmin,
                 classList: cell.className
             });

            // Format date for display (Fixing the "undefined" bug)
            // Create date object and adjust for timezone offset to avoid day-off errors
            const dateObj = new Date(rawDate);
            const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
            const adjustedDate = new Date(dateObj.getTime() + userTimezoneOffset);
            selectedDateDisplay = adjustedDate.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' });


            // Setup Option Modal Button
            const optBtn = document.getElementById('optActionBtn');
            
            if (isAdmin) {
                // Admin Logic
                if (!currentData) {
                    // Empty -> Show "Bloquear"
                    optBtn.textContent = "Bloquear Horario";
                    optBtn.className = "btn btn-dark w-100";
                    optBtn.onclick = () => {
                         optionModal.hide();
                         openAdminBlockModal(selectedSlotId);
                    };
                    optionModal.show();
                } else if (currentData.status === 'blocked') {
                    // Blocked -> Abrir directamente el modal de desbloqueo
                    console.log('Abriendo modal de desbloqueo para:', selectedSlotId);
                    openAdminBlockModal(selectedSlotId);
                } else if (currentData.status === 'booked') {
                     // Booked -> Go straight to report (skip option selector for booked items usually, or show "Ver Reporte")
                     openAdminReportModal(selectedSlotId, currentData);
                }
            } else {
                // Client Logic
                // Empty -> Show "Agendar"
                optBtn.textContent = "Agendar Horario";
                optBtn.className = "btn btn-dark w-100";
                optBtn.onclick = () => {
                    optionModal.hide();
                    openClientBookingModal(selectedSlotId, selectedDateDisplay, selectedTimeDisplay);
                };
                optionModal.show();
            }
        });

        // --- SPECIFIC MODAL OPENERS ---

        function openClientBookingModal(slotId, dateText, timeText) {
             const displayEl = document.getElementById('bookingDateDisplay');
             if (displayEl) {
                 displayEl.textContent = `${dateText} a las ${timeText}`;
             }
             document.getElementById('bookingSlotId').value = slotId;
             
             const mascotSelect = document.getElementById('bookingMascot');
             mascotSelect.innerHTML = '';
             if (clientMascots.length > 0) {
                clientMascots.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;  // FIX: usar ID en lugar de name
                    opt.textContent = `${m.name} (${m.breed})`;
                    mascotSelect.appendChild(opt);
                });
             } else {
                const opt = document.createElement('option');
                opt.value = ''; opt.textContent = 'No hay mascotas registradas';
                mascotSelect.appendChild(opt);
             }
             bookingModal.show();
        }

        function openAdminBlockModal(slotId) {
            document.getElementById('blockSlotId').value = slotId;
            const current = slotState[slotId];
            const btn = document.getElementById('toggleBlockBtn');
            const modalTitle = document.getElementById('blockModalTitle');
            const modalMessage = document.getElementById('blockModalMessage');
            
            if (current && current.status === 'blocked') {
                modalTitle.textContent = "Desbloquear Horario";
                modalMessage.textContent = "¿Confirmas que deseas desbloquear este horario? Quedará disponible para nuevas citas.";
                btn.innerHTML = '<i class="bi bi-unlock me-2"></i>Desbloquear';
                btn.className = "btn btn-warning";
            } else {
                modalTitle.textContent = "Bloquear Horario";
                modalMessage.textContent = "¿Confirmas que deseas bloquear este horario? No estará disponible para citas.";
                btn.innerHTML = '<i class="bi bi-lock me-2"></i>Bloquear';
                btn.className = "btn btn-dark";
            }
            adminBlockModal.show();
        }

        function openAdminReportModal(slotId, data) {
            document.getElementById('reportSlotId').value = slotId;
            document.getElementById('reportAppointmentId').value = data.id;
            
            // Información del cliente
            document.getElementById('reportClientName').textContent = data.client || 'N/A';
            document.getElementById('reportClientEmail').textContent = data.clientEmail || 'N/A';
            document.getElementById('reportClientPhone').textContent = data.clientPhone || 'N/A';
            document.getElementById('reportClientRut').textContent = data.clientRut || 'N/A';
            
            // Información de la mascota
            document.getElementById('reportMascotName').textContent = data.mascot || 'N/A';
            document.getElementById('reportMascotSpecies').textContent = data.mascotSpecies || 'N/A';
            document.getElementById('reportMascotBreed').textContent = data.mascotBreed || 'N/A';
            document.getElementById('reportMascotAge').textContent = data.mascotAge || 'N/A';
            document.getElementById('reportMascotWeight').textContent = data.mascotWeight || 'N/A';
            
            // Información de la cita
            document.getElementById('reportServiceName').textContent = data.service || 'N/A';
            
            // Formatear fecha
            const dateObj = new Date(data.appointmentDate);
            const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
            const adjustedDate = new Date(dateObj.getTime() + userTimezoneOffset);
            const formattedDate = adjustedDate.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('reportAppointmentDate').textContent = formattedDate;
            document.getElementById('reportAppointmentTime').textContent = data.appointmentTime || 'N/A';
            
            // Estado con badge
            const statusElement = document.getElementById('reportAppointmentStatus');
            statusElement.textContent = data.appointment_status;
            statusElement.className = 'badge ';
            switch(data.appointment_status) {
                case 'Completado':
                    statusElement.classList.add('bg-success');
                    break;
                case 'Cancelado':
                    statusElement.classList.add('bg-danger');
                    break;
                case 'Confirmado':
                    statusElement.classList.add('bg-info');
                    break;
                case 'Pendiente':
                    statusElement.classList.add('bg-warning', 'text-dark');
                    break;
                default:
                    statusElement.classList.add('bg-secondary');
            }
            
            document.getElementById('reportNotes').value = ""; 
            adminReportModal.show();
        }

        // --- CONFIRMATION ACTIONS ---

        // 1. CLIENT: Confirm Booking
        const confirmBookingBtn = document.getElementById('confirmBookingBtn');
        if (confirmBookingBtn) {
            confirmBookingBtn.addEventListener('click', async () => {
                 console.log('=== INICIO DEL PROCESO DE AGENDAMIENTO ===');
                 
                 if (clientMascots.length === 0) {
                     bookingModal.hide();
                     noMascotModal.show();
                     return;
                 }
                 
                 const slotId = document.getElementById('bookingSlotId').value;
                 const service = document.getElementById('bookingService').value;
                 const mascotId = document.getElementById('bookingMascot').value;
                 
                 console.log('SlotId:', slotId);
                 console.log('Service:', service);
                 console.log('MascotId:', mascotId);
                 
                 // Validación de campos
                 if (!mascotId || mascotId === '') {
                     alert('Por favor selecciona una mascota');
                     return;
                 }
                 
                 if (!service || service === '') {
                     alert('Por favor selecciona un servicio');
                     return;
                 }
                 
                 if (!slotId || slotId === '') {
                     alert('Error: No se detectó el horario seleccionado');
                     return;
                 }
                 
                 // Parse slotId: YYYY-MM-DD_HH:MM:SS - HH:MM:SS
                 const [date, timeRange] = slotId.split('_');
                 
                 if (!timeRange) {
                     console.error('Error al parsear slotId:', slotId);
                     alert('Error en el formato del horario');
                     return;
                 }
                 
                 const [startTime, endTime] = timeRange.split(' - ');
                 
                 // Extract just HH:MM from the time strings
                 const cleanStartTime = startTime.split(':').slice(0, 2).join(':');
                 const cleanEndTime = endTime.split(':').slice(0, 2).join(':');
                 
                 console.log('Datos a enviar:', {
                     pet_id: parseInt(mascotId),
                     service: service,
                     appointment_date: date,
                     start_time: cleanStartTime,
                     end_time: cleanEndTime
                 });
                 
                 // Save to database
                 try {
                     const response = await fetch('api/appointments.php?action=create', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json' },
                         body: JSON.stringify({
                             pet_id: parseInt(mascotId),
                             service: service,
                             appointment_date: date,
                             start_time: cleanStartTime,
                             end_time: cleanEndTime
                         })
                     });
                     
                     console.log('Response status:', response.status);
                     const result = await response.json();
                     console.log('Respuesta del servidor:', result);
                     
                     if (result.success) {
                         // Update local state
                         const mascotName = clientMascots.find(m => m.id == mascotId)?.name || 'Mascota';
                         slotState[slotId] = {
                             status: 'booked',
                             client: 'Cliente (Yo)', 
                             mascot: mascotName,
                             service: service,
                             id: result.appointment_id
                         };
                         bookingModal.hide();
                         renderWeeklyGrid();
                         alert('¡Cita agendada exitosamente!');
                     } else {
                         // Show detailed error message
                         let errorMsg = result.error || 'Error desconocido';
                         if (result.missing_fields) {
                             errorMsg += '\nCampos faltantes: ' + result.missing_fields.join(', ');
                         }
                         if (result.received_data) {
                             console.error('Datos recibidos por el servidor:', result.received_data);
                         }
                         alert('Error al agendar: ' + errorMsg);
                     }
                 } catch (error) {
                     console.error('Error:', error);
                     alert('Error de conexión. Por favor intenta nuevamente.');
                 }
            });
        }
 
         // 2. ADMIN: Confirm Block/Unblock
        const toggleBlockBtn = document.getElementById('toggleBlockBtn');
        if (toggleBlockBtn) {
            toggleBlockBtn.addEventListener('click', async () => {
                 const slotId = document.getElementById('blockSlotId').value;
                 const current = slotState[slotId];
                 const isBlocked = current && current.status === 'blocked';
                 
                 // Parse slotId: YYYY-MM-DD_HH:MM - HH:MM
                 const [date, timeRange] = slotId.split('_');
                 const [startTime, endTime] = timeRange.split(' - ');
                 
                 if (isBlocked) {
                     // Desbloquear - Eliminar de la BD
                     if (!current.id) {
                         console.error('No se encontró ID de bloqueo');
                         delete slotState[slotId];
                         adminBlockModal.hide();
                         renderWeeklyGrid();
                         return;
                     }
                     
                     try {
                         const response = await fetch(`api/appointments.php?action=cancel&id=${current.id}`, {
                             method: 'DELETE'
                         });
                         
                         const result = await response.json();
                         
                         if (result.success) {
                             delete slotState[slotId];
                             alert('Horario desbloqueado');
                         } else {
                             alert('Error al desbloquear: ' + (result.error || 'Error desconocido'));
                         }
                     } catch (error) {
                         console.error('Error:', error);
                         alert('Error de conexión');
                     }
                 } else {
                     // Bloquear - Crear en la BD
                     try {
                         const response = await fetch('api/appointments.php?action=block', {
                             method: 'POST',
                             headers: { 'Content-Type': 'application/json' },
                             body: JSON.stringify({
                                 appointment_date: date,
                                 start_time: startTime,
                                 end_time: endTime,
                                 reason: 'Bloqueado por administrador'
                             })
                         });
                         
                         const result = await response.json();
                         
                         if (result.success) {
                             slotState[slotId] = { 
                                 status: 'blocked',
                                 id: result.appointment_id 
                             };
                             alert('Horario bloqueado');
                         } else {
                             alert('Error al bloquear: ' + (result.error || 'Error desconocido'));
                         }
                     } catch (error) {
                         console.error('Error:', error);
                         alert('Error de conexión');
                     }
                 }
                 
                 adminBlockModal.hide();
                 await loadAppointments(); // Recargar citas
                 renderWeeklyGrid();
            });
        }
 
         // 3. ADMIN: Report Actions
         const handleReport = async (action) => {
             const appointmentId = document.getElementById('reportAppointmentId').value;
             const adminNotes = document.getElementById('reportNotes').value.trim();
             
             if (!appointmentId) {
                 alert('Error: ID de cita no encontrado');
                 return;
             }
             
             // Validar que haya notas al cancelar (recomendado)
             if (action === 'Cancelado' && !adminNotes) {
                 if (!confirm('No has agregado un motivo de cancelación. ¿Deseas continuar sin notas?')) {
                     return;
                 }
             }
             
             const confirmMsg = action === 'Completado' 
                 ? '¿Confirmar que esta cita fue completada?\n\nSe enviará un email al cliente con las observaciones.' 
                 : '¿Confirmar la cancelación de esta cita?\n\nSe enviará un email al cliente con el motivo.';
             
             if (!confirm(confirmMsg)) {
                 return;
             }
             
             try {
                 const response = await fetch('api/appointments.php?action=update_status', {
                     method: 'PUT',
                     headers: { 'Content-Type': 'application/json' },
                     body: JSON.stringify({
                         appointment_id: parseInt(appointmentId),
                         status: action,
                         admin_notes: adminNotes
                     })
                 });
                 
                 const result = await response.json();
                 
                 if (result.success) {
                     alert(`Cita marcada como ${action} exitosamente.\n\n📧 Se ha enviado un email de notificación al cliente.`);
                     adminReportModal.hide();
                     await loadAppointments(); // Recargar citas
                     renderWeeklyGrid();
                 } else {
                     alert('Error al actualizar: ' + (result.error || 'Error desconocido'));
                 }
             } catch (error) {
                 console.error('Error:', error);
                 alert('Error de conexión. Por favor intenta nuevamente.');
             }
         };
        const markCompletedBtn = document.getElementById('markCompletedBtn');
        const markCanceledBtn = document.getElementById('markCanceledBtn');
        if (markCompletedBtn) markCompletedBtn.addEventListener('click', () => handleReport('Completado'));
        if (markCanceledBtn) markCanceledBtn.addEventListener('click', () => handleReport('Cancelado'));


        // --- INITIALIZATION (NAV LOGIC) ---
        if (isAdmin) {
            const adminYearSelect = document.getElementById('adminYearSelect');
            const adminMonthSelect = document.getElementById('adminMonthSelect');
            const initAdminSelectors = () => {
                const currentYear = today.getFullYear();
                for (let y = 2025; y <= currentYear + 5; y++) {
                    const option = document.createElement('option');
                    option.value = y; option.textContent = y;
                    if (y === displayedMonth.getFullYear()) option.selected = true;
                    adminYearSelect.appendChild(option);
                }
                monthNames.forEach((name, m) => {
                    const option = document.createElement('option');
                    option.value = m; option.textContent = name;
                    if (m === displayedMonth.getMonth()) option.selected = true;
                    adminMonthSelect.appendChild(option);
                });
            };
            adminYearSelect.addEventListener('change', () => { displayedMonth.setFullYear(adminYearSelect.value); displayedWeekIndex = 0; renderAll(); });
            adminMonthSelect.addEventListener('change', () => { displayedMonth.setMonth(adminMonthSelect.value); displayedWeekIndex = 0; renderAll(); });
            document.getElementById('prevMonthYearBtn').addEventListener('click', () => { displayedMonth.setMonth(displayedMonth.getMonth() - 1); adminYearSelect.value = displayedMonth.getFullYear(); adminMonthSelect.value = displayedMonth.getMonth(); displayedWeekIndex = 0; renderAll(); });
            document.getElementById('nextMonthYearBtn').addEventListener('click', () => { displayedMonth.setMonth(displayedMonth.getMonth() + 1); adminYearSelect.value = displayedMonth.getFullYear(); adminMonthSelect.value = displayedMonth.getMonth(); displayedWeekIndex = 0; renderAll(); });
            initAdminSelectors();
        } else {
             const m1Btn = document.getElementById('month1-tab');
             const m2Btn = document.getElementById('month2-tab');
             const m1 = new Date(today.getFullYear(), today.getMonth(), 1);
             const m2 = new Date(today.getFullYear(), today.getMonth() + 1, 1);
             m1Btn.textContent = getMonthYearString(m1);
             m2Btn.textContent = getMonthYearString(m2);
             m1Btn.addEventListener('click', () => { 
                 displayedMonth = m1; 
                 displayedWeekIndex = 0; 
                 loadAppointments(); 
                 renderAll();
                 m1Btn.classList.add('active'); 
                 m2Btn.classList.remove('active');
             });
             m2Btn.addEventListener('click', () => { 
                 displayedMonth = m2; 
                 displayedWeekIndex = 0; 
                 loadAppointments(); 
                 renderAll();
                 m2Btn.classList.add('active'); 
                 m1Btn.classList.remove('active');
             });
        }

        // Load appointments from database and render
        loadAppointments();
        renderAll(); // Initial render of week buttons
    });
</script>

<?php
// Include the standard footer
include 'layout/footer.php';

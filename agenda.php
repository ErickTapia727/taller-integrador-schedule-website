<?php
// Set context variables for the header template
$page_title = "Agenda";
$active_link = 'agenda';

// Include the standard header
include 'layout/header.php';

// Get current user's pets for JavaScript
$userPets = [];
if (!$is_admin && isset($_SESSION['user_id']) && isset($_SESSION['pets'])) {
    $currentUserId = intval($_SESSION['user_id']);
    foreach ($_SESSION['pets'] as $pet) {
        if ($pet['owner_id'] === $currentUserId) {
            $userPets[] = [
                'id' => $pet['id'],
                'name' => $pet['name'],
                'breed' => $pet['breed'] ?? 'Raza no especificada'
            ];
        }
    }
}
$userPetsJson = json_encode($userPets);
?>

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
        <h5 class="modal-title fw-bold">Gestionar Horario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>¿Confirmas que deseas bloquear este horario?</p>
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
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Reporte de Servicio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1"><strong>Cliente:</strong> <span id="reportClientName"></span></p>
        <p class="mb-3"><strong>Mascota:</strong> <span id="reportMascotName"></span></p>
        <p class="mb-3"><strong>Servicio:</strong> <span id="reportServiceName"></span></p>
        
        <div class="mb-3">
            <label class="form-label">Notas del Admin</label>
            <textarea class="form-control" rows="3" id="reportNotes" placeholder="Detalles del servicio..."></textarea>
        </div>
        <input type="hidden" id="reportSlotId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="markCompletedBtn"><i class="bi bi-check-circle me-2"></i>Completado</button>
        <button type="button" class="btn btn-danger" id="markCanceledBtn"><i class="bi bi-x-circle me-2"></i>Cancelado</button>
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

        // --- CLIENT MASCOTS (Real user data) ---
        const clientMascots = <?php echo $userPetsJson; ?>;

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
            const lastDay = new Date(year, month + 1, 0);
            return Math.ceil(lastDay.getDate() / 7);
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
            let weekStartDate = new Date(firstDayOfMonth);
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
                                cellClass += ' bg-info text-white border-white';
                                cellContent = `<small>${data.client}<br>${data.mascot}<br>${data.service}</small>`;
                            } else {
                                cellClass += ' bg-secondary text-white disabled';
                                cellContent = '<small>Horario agendado</small>';
                            }
                        } else if (data.status === 'blocked') {
                             cellClass += ' bg-dark text-white disabled';
                             cellContent = '<small>Horario bloqueado</small>';
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

            // Validation for disabled cells
            if (cell.classList.contains('disabled') && !cell.classList.contains('bg-secondary') && !cell.classList.contains('bg-dark')) {
                if (!isAdmin) return;
            }
            if (!isAdmin && (cell.classList.contains('bg-secondary') || cell.classList.contains('bg-dark'))) {
                return;
            }

            // Store selection state
             selectedSlotId = cell.getAttribute('data-slot-id');
             const rawDate = cell.getAttribute('data-date'); // YYYY-MM-DD
             selectedTimeDisplay = cell.getAttribute('data-time');
             const currentData = slotState[selectedSlotId];

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
                    // Blocked -> Show "Desbloquear"
                    optBtn.textContent = "Desbloquear Horario";
                    optBtn.className = "btn btn-warning w-100";
                    optBtn.onclick = () => {
                         optionModal.hide();
                         openAdminBlockModal(selectedSlotId); // Reuse block modal logic
                    };
                    optionModal.show();
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
                    opt.value = m.name;
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
            
            if (current && current.status === 'blocked') {
                btn.textContent = "Desbloquear";
                btn.className = "btn btn-warning";
            } else {
                btn.textContent = "Bloquear";
                btn.className = "btn btn-dark";
            }
            adminBlockModal.show();
        }

        function openAdminReportModal(slotId, data) {
            document.getElementById('reportSlotId').value = slotId;
            document.getElementById('reportClientName').textContent = data.client;
            document.getElementById('reportMascotName').textContent = data.mascot;
            document.getElementById('reportServiceName').textContent = data.service;
            document.getElementById('reportNotes').value = ""; 
            adminReportModal.show();
        }

        // --- CONFIRMATION ACTIONS ---

        // 1. CLIENT: Confirm Booking
        const confirmBookingBtn = document.getElementById('confirmBookingBtn');
        if (confirmBookingBtn) {
            confirmBookingBtn.addEventListener('click', () => {
                 if (clientMascots.length === 0) {
                     bookingModal.hide();
                     noMascotModal.show();
                     return;
                 }
                 const slotId = document.getElementById('bookingSlotId').value;
                 const service = document.getElementById('bookingService').value;
                 const mascot = document.getElementById('bookingMascot').value;
 
                 slotState[slotId] = {
                     status: 'booked',
                     client: 'Cliente (Yo)', 
                     mascot: mascot,
                     service: service
                 };
                 bookingModal.hide();
                 renderWeeklyGrid();
            });
        }
 
         // 2. ADMIN: Confirm Block/Unblock
        const toggleBlockBtn = document.getElementById('toggleBlockBtn');
        if (toggleBlockBtn) {
            toggleBlockBtn.addEventListener('click', () => {
                 const slotId = document.getElementById('blockSlotId').value;
                 const current = slotState[slotId];
                 if (current && current.status === 'blocked') {
                     delete slotState[slotId]; 
                 } else {
                     slotState[slotId] = { status: 'blocked' }; 
                 }
                 adminBlockModal.hide();
                 renderWeeklyGrid();
            });
        }
 
         // 3. ADMIN: Report Actions
         const handleReport = (action) => {
             // Log action...
             adminReportModal.hide();
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
             m1Btn.addEventListener('click', () => { displayedMonth = m1; displayedWeekIndex = 0; renderAll(); m1Btn.classList.add('active'); m2Btn.classList.remove('active');});
             m2Btn.addEventListener('click', () => { displayedMonth = m2; displayedWeekIndex = 0; renderAll(); m2Btn.classList.add('active'); m1Btn.classList.remove('active');});
        }

        renderAll();
    });
</script>

<?php
// Include the standard footer
include 'layout/footer.php';
?>

<?php
$_SESSION['appointment_history'][] = [
    'client_id' => $clientId,
    'client_name' => $clientName,
    'pet_name' => $petName,      // ← Nombre real de la mascota
    'service' => $service,
    'date' => $date,            // YYYY-MM-DD
    'time' => $time,
    'cost' => $cost,
    'status' => 'completado'    // o 'cancelado'
];
?>
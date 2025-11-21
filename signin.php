<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller integrador - Registro</title>

    <link rel="stylesheet" href="/src/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

  </head>
<body>
  <div class="container-fluid vh-100">
    <div class="row h-100 justify-content-center">
      <div class="p-4 bg-primary my-5">

        <div class="text-center mb-3 mt-3">
          <h1 class="bg-light fw-bold py-3 px-5 d-inline-block shadow">¡Bienvenido!</h1>
        </div>

        <div class="row align-items-center">

          <div class="col-md-6 text-center">

            <!-- 
                ACTUALIZADO: El 'action' ahora apunta a 'procesar_registro.php'.
                Y los inputs tienen el atributo 'name' para que PHP pueda recibirlos.
            -->
            <form action="procesar_registro.php" method="POST" class="registro-form">

              <h2 class="mb-4 fs-1 fw-bold">Registrarse</h2>

              <!-- Mensajes de Error -->
              <?php 
              // Mantener valores del formulario
              $nombre_value = isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '';
              $correo_value = isset($_GET['correo']) ? htmlspecialchars($_GET['correo']) : '';
              $rut_value = isset($_GET['rut']) ? htmlspecialchars($_GET['rut']) : '';
              $telefono_value = isset($_GET['telefono']) ? htmlspecialchars($_GET['telefono']) : '';
              ?>
              
              <?php if (isset($_GET['error'])): ?>
                <?php if ($_GET['error'] === 'rut_invalido'): ?>
                    <div class="alert alert-danger">Rut inválido.</div>
                <?php elseif ($_GET['error'] === 'email_existe'): ?>
                    <div class="alert alert-danger">Ya existe una cuenta con ese correo</div>
                <?php elseif ($_GET['error'] === 'rut_existe'): ?>
                    <div class="alert alert-danger">Ya existe una cuenta con ese rut.</div>
                <?php elseif ($_GET['error'] === 'nombre_existe'): ?>
                    <div class="alert alert-danger">Ya existe una cuenta con ese nombre.</div>
                <?php elseif ($_GET['error'] === 'contrasenas_no_coinciden'): ?>
                    <div class="alert alert-danger">Las contraseñas no coinciden.</div>
                <?php elseif ($_GET['error'] === 'datos_incompletos'): ?>
                    <div class="alert alert-danger">Rellenar todos los campos.</div>
                <?php elseif ($_GET['error'] === 'email_invalido'): ?>
                    <div class="alert alert-danger">Correo inválido.</div>
                <?php elseif ($_GET['error'] === 'contrasena_debil'): ?>
                    <div class="alert alert-danger">Contraseña no cumple con los criterios.</div>
                <?php elseif ($_GET['error'] === 'telefono_invalido'): ?>
                    <div class="alert alert-danger">Teléfono inválido.</div>
                <?php else: ?>
                    <div class="alert alert-danger">Por favor, revisa el campo resaltado e intenta nuevamente.</div>
                <?php endif; ?>
              <?php endif; ?>

              <div class="group mb-5 mt-5">
                <input required="" type="text" class="input <?php echo (isset($_GET['error']) && in_array($_GET['error'], ['datos_incompletos', 'nombre_existe'])) && (($_GET['error'] === 'datos_incompletos' && empty($nombre_value)) || $_GET['error'] === 'nombre_existe') ? 'error-field' : ''; ?>" id="inputNombre" name="inputNombre" maxlength="50" value="<?php echo $nombre_value; ?>">
                <i class="bi bi-person custom-icono"></i>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label for="inputNombre">Nombre completo</label>
              </div>

              <div class="group mb-5">
                <input required="" type="text" class="input <?php echo (isset($_GET['error']) && in_array($_GET['error'], ['email_invalido', 'email_existe'])) ? 'error-field' : ''; ?>" id="inputCorreo" name="inputCorreo" maxlength="50" value="<?php echo $correo_value; ?>">
                <i class="bi bi-envelope custom-icono"></i>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label for="inputCorreo">Correo electrónico</label>
              </div>

              <div class="group mb-5 password-container">
                <!-- Cambiado a type="password" por seguridad -->
                <input required="" type="password" class="input <?php echo (isset($_GET['error']) && in_array($_GET['error'], ['contrasena_debil', 'contrasenas_no_coinciden'])) ? 'error-field' : ''; ?>" id="inputContraseña" name="inputContraseña" maxlength="50" minlength="8">
                <i class="bi bi-lock custom-icono"></i>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label for="inputContraseña">Contraseña</label>
                
                <!-- Tooltip de criterios de contraseña -->
                <div class="password-tooltip">
                  <h6 class="mb-2">Criterios de contraseña:</h6>
                  <ul class="mb-0">
                    <li id="length-check">✗ Mínimo 8 caracteres</li>
                    <li id="case-check">✗ Al menos una mayúscula y una minúscula</li>
                    <li id="number-special-check">✗ Al menos un número o símbolo especial</li>
                  </ul>
                </div>
              </div>

              <div class="group mb-5">
                <input required="" type="password" class="input <?php echo (isset($_GET['error']) && $_GET['error'] === 'contrasenas_no_coinciden') ? 'error-field' : ''; ?>" id="inputConfirmarContraseña" name="inputConfirmarContraseña" maxlength="50" minlength="8">
                <i class="bi bi-lock-fill custom-icono"></i>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label for="inputConfirmarContraseña">Confirmar Contraseña</label>
              </div>

              <div class="group mb-5">
                <input required="" type="text" class="input <?php echo (isset($_GET['error']) && in_array($_GET['error'], ['rut_invalido', 'rut_existe'])) ? 'error-field' : ''; ?>" id="inputRut" name="inputRut" maxlength="12" value="<?php echo $rut_value; ?>">
                <i class="bi bi-person-vcard custom-icono"></i>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label for="inputRut">Rut (ej: 12345678-9)</label>
              </div>

              <div class="group mb-5">
                <input required="" type="tel" class="input <?php echo (isset($_GET['error']) && $_GET['error'] === 'telefono_invalido') ? 'error-field' : ''; ?>" id="inputTelefono" name="inputTelefono" maxlength="15" value="<?php echo $telefono_value; ?>" placeholder="+56 9 XXXX XXXX">
                <i class="bi bi-telephone custom-icono"></i>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label for="inputTelefono">Teléfono (ej: +56 9 1234 5678)</label>
              </div>


              <button type="submit" class="custom-button mb-3">
                Registrarse
                <div class="arrow-wrapper">
                  <div class="arrow"></div>
                </div>
              </button>

              <a href="login.php" class="py-3 fs-4 bg-transparent text-dark fw-bold ">¡Ya tienes una cuenta? Iniciar Sesión</a>

            </form>

          </div>
            <div class="col-md-6 text-center ">
              <!-- TODO: Ask the client for the original image since its quality is too low -->
              <img src="images/dogcutespa-resized.png" class="img-fluid rounded shadow" alt="logo" style="max-height: 600px; width: auto; border: 6px solid #000000; box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);"> 
            </div>
        </div>
      </div>
    </div>
  </div>

<script>
// Validación de criterios de contraseña
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('inputContraseña');
    const confirmPasswordInput = document.getElementById('inputConfirmarContraseña');
    const lengthCheck = document.getElementById('length-check');
    const caseCheck = document.getElementById('case-check');
    const numberSpecialCheck = document.getElementById('number-special-check');
    const phoneInput = document.getElementById('inputTelefono');

    // Validación de contraseña en tiempo real
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Verificar longitud mínima
            if (password.length >= 8) {
                lengthCheck.innerHTML = '✓ Mínimo 8 caracteres';
                lengthCheck.className = 'valid';
            } else {
                lengthCheck.innerHTML = '✗ Mínimo 8 caracteres';
                lengthCheck.className = 'invalid';
            }
            
            // Verificar mayúscula y minúscula
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            if (hasUpper && hasLower) {
                caseCheck.innerHTML = '✓ Al menos una mayúscula y una minúscula';
                caseCheck.className = 'valid';
            } else {
                caseCheck.innerHTML = '✗ Al menos una mayúscula y una minúscula';
                caseCheck.className = 'invalid';
            }
            
            // Verificar número o símbolo especial
            const hasNumberOrSpecial = /[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
            if (hasNumberOrSpecial) {
                numberSpecialCheck.innerHTML = '✓ Al menos un número o símbolo especial';
                numberSpecialCheck.className = 'valid';
            } else {
                numberSpecialCheck.innerHTML = '✗ Al menos un número o símbolo especial';
                numberSpecialCheck.className = 'invalid';
            }
            
            // Verificar coincidencia con confirmación
            checkPasswordMatch();
        });
    }

    // Validación de confirmación de contraseña
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    }

    // Función para verificar coincidencia de contraseñas
    function checkPasswordMatch() {
        if (passwordInput && confirmPasswordInput) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    confirmPasswordInput.classList.remove('error-field');
                    confirmPasswordInput.classList.add('valid-field');
                } else {
                    confirmPasswordInput.classList.remove('valid-field');
                    confirmPasswordInput.classList.add('error-field');
                }
            } else {
                confirmPasswordInput.classList.remove('error-field', 'valid-field');
            }
        }
    }

    // Validación y formato de teléfono chileno
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // Remover todos los caracteres no numéricos
            
            // Si empieza con 569, mantenerlo
            if (value.startsWith('569')) {
                if (value.length <= 11) {
                    // Formato: +56 9 XXXX XXXX
                    if (value.length > 3) {
                        if (value.length > 7) {
                            this.value = `+56 9 ${value.substring(3, 7)} ${value.substring(7, 11)}`;
                        } else {
                            this.value = `+56 9 ${value.substring(3)}`;
                        }
                    } else {
                        this.value = `+56 9`;
                    }
                }
            } 
            // Si empieza con 56 pero no 569
            else if (value.startsWith('56') && !value.startsWith('569')) {
                this.value = '+56 9 ';
            }
            // Si empieza con 9
            else if (value.startsWith('9')) {
                if (value.length <= 9) {
                    if (value.length > 5) {
                        this.value = `+56 9 ${value.substring(1, 5)} ${value.substring(5, 9)}`;
                    } else if (value.length > 1) {
                        this.value = `+56 9 ${value.substring(1)}`;
                    } else {
                        this.value = '+56 9 ';
                    }
                }
            }
            // Si no empieza con ninguno de los anteriores, agregar prefijo
            else if (value.length > 0) {
                this.value = '+56 9 ';
            }
        });

        // Validar en envío del formulario - Solo validaciones de formato, no errores
        const form = phoneInput.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : '';
                const phone = phoneInput.value;
                
                // Solo verificar formato básico de teléfono si no está vacío
                if (phone && phone !== '+56 9 ') {
                    const phoneRegex = /^\+56 9 \d{4} \d{4}$/;
                    if (!phoneRegex.test(phone)) {
                        e.preventDefault();
                        alert('El formato del teléfono debe ser: +56 9 XXXX XXXX');
                        return;
                    }
                }
                
                // El resto de validaciones las manejará el backend
                // Los errores se mostrarán en la sección "Mensajes de Error"
            });
        }
    }
});
</script>

</body>
</html>
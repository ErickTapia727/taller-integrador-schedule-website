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
      <div class="p-5 bg-primary my-5">

        <div class="text-center mb-5 mt-3">
          <h1 class="bg-light fw-bold py-3 px-5 d-inline-block shadow">¡Bienvenido!</h1>
        </div>

        <div class="row align-items-center">

          <div class="col-md-6 text-center">

            <!-- 
                ACTUALIZADO: El 'action' ahora apunta a 'procesar_registro.php'.
                Y los inputs tienen el atributo 'name' para que PHP pueda recibirlos.
            -->
            <form action="procesar_registro.php" method="POST" class="registro-form">

              <h2 class="mb-5 fs-1 fw-bold">Registrarse</h2>

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
                    <div class="alert alert-danger">Ya existe una cuente con ese nombre.</div>
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

              <div class="group mb-5">
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
                <input required="" type="password" class="input <?php echo (isset($_GET['error']) && $_GET['error'] === 'contrasena_debil') ? 'error-field' : ''; ?>" id="inputContraseña" name="inputContraseña" maxlength="50" minlength="8">
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
              <img src="images/dogcutespa.png" class="img-fluid rounded shadow mt-5" alt="logo" style="max-height: 400px; width: auto; border: 4px solid #000000; box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);"> 
            </div>
        </div>
      </div>
    </div>
  </div>

<style>
/* Estilos para el tooltip de criterios de contraseña */
.password-container {
  position: relative;
}

.password-tooltip {
  position: absolute;
  top: -10px;
  left: 520px;
  background: #333;
  color: white;
  padding: 15px;
  border-radius: 8px;
  font-size: 14px;
  width: 280px;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s, visibility 0.3s;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.password-tooltip::before {
  content: '';
  position: absolute;
  top: 20px;
  left: -8px;
  width: 0;
  height: 0;
  border-top: 8px solid transparent;
  border-bottom: 8px solid transparent;
  border-right: 8px solid #333;
}

.password-container:hover .password-tooltip,
.password-container .input:focus ~ .password-tooltip {
  opacity: 1;
  visibility: visible;
}

.password-tooltip ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.password-tooltip li {
  padding: 2px 0;
  font-size: 13px;
}

.password-tooltip li.valid {
  color: #4CAF50;
}

.password-tooltip li.invalid {
  color: #ff6b6b;
}

/* Estilos para campos con error */
.error-field {
  border-bottom: 2px solid #dc3545 !important;
  background: rgba(220, 53, 69, 0.1) !important;
}

.error-field:focus {
  border-bottom: 2px solid #dc3545 !important;
}

.error-field:focus ~ .bar:before,
.error-field:focus ~ .bar:after {
  background: #dc3545 !important;
}
</style>

<script>
// Validación de criterios de contraseña
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('inputContraseña');
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
        });
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

        // Validar en envío del formulario
        const form = phoneInput.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const phone = phoneInput.value;
                
                // Validación de contraseña
                const hasMinLength = password.length >= 8;
                const hasUpper = /[A-Z]/.test(password);
                const hasLower = /[a-z]/.test(password);
                const hasNumberOrSpecial = /[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
                
                // Validación de teléfono chileno
                const phoneRegex = /^\+56 9 \d{4} \d{4}$/;
                const isValidPhone = phoneRegex.test(phone);
                
                if (!hasMinLength || !hasUpper || !hasLower || !hasNumberOrSpecial) {
                    e.preventDefault();
                    alert('La contraseña no cumple con todos los criterios requeridos.');
                    return;
                }
                
                if (!isValidPhone) {
                    e.preventDefault();
                    alert('El formato del teléfono debe ser: +56 9 XXXX XXXX');
                    return;
                }
            });
        }
    }
});
</script>

</body>
</html>
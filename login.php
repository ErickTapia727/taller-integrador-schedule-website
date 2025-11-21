<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller integrador - Iniciar Sesión</title>

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
                ACTUALIZADO: El 'action' ahora apunta a 'procesar_login.php'.
                Y los inputs tienen el atributo 'name' para que PHP pueda recibirlos.
            -->
            <form action="procesar_login.php" method="POST" class="inicio_sesion-form">

              <h2 class="mb-5 fs-1 fw-bold">Iniciar Sesión</h2>

              <!-- Mensajes de Error/Éxito -->
              <?php 
              // Mantener valores del formulario
              $correo_value = isset($_GET['correo']) ? htmlspecialchars($_GET['correo']) : '';
              ?>
              
              <?php if (isset($_GET['error'])): ?>
                <?php if ($_GET['error'] === 'credenciales_incorrectas'): ?>
                    <div class="alert alert-danger">Correo o contraseña inválidos.</div>
                <?php elseif ($_GET['error'] === 'datos_incompletos'): ?>
                    <div class="alert alert-danger">Correo o contraseña inválidos.</div>
                <?php elseif ($_GET['error'] === 'email_invalido'): ?>
                    <div class="alert alert-danger">Correo o contraseña inválidos.</div>
                <?php elseif ($_GET['error'] === 'no_sesion'): ?>
                    <div class="alert alert-warning">Debes iniciar sesión para acceder.</div>
                <?php else: ?>
                    <div class="alert alert-danger">Correo o contraseña inválidos.</div>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
                <div class="alert alert-success">¡Cuenta creada! Por favor, inicia sesión.</div>
              <?php endif; ?>
              <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'sesion_cerrada'): ?>
                <div class="alert alert-info">Sesión cerrada correctamente.</div>
              <?php endif; ?>

              <div class="group mb-5">
                <input required="" type="text" class="input <?php echo (isset($_GET['error']) && in_array($_GET['error'], ['email_invalido', 'credenciales_incorrectas', 'datos_incompletos'])) ? 'error-field' : ''; ?>" id="inputCorreo" name="inputCorreo" maxlength="50" value="<?php echo $correo_value; ?>">
                <i class="bi bi-envelope custom-icono"></i>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label for="inputCorreo">Correo electrónico</label>
              </div>

              <div class="group mb-5 password-container">
                <input required="" type="password" class="input <?php echo (isset($_GET['error']) && in_array($_GET['error'], ['credenciales_incorrectas', 'datos_incompletos'])) ? 'error-field' : ''; ?>" id="inputContraseña" name="inputContraseña" maxlength="50" minlength="8">
                <i class="bi bi-lock custom-icono"></i>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label for="inputContraseña">Contraseña</label>
                
              </div>
              <button type="submit" class="custom-button" style="padding:15px 190px">
                Iniciar sesión
                <div class="arrow-wrapper">
                  <div class="arrow"></div>
                </div>
              </button>
              
              <a href="signin.php" class="py-3 fs-4 d-block mt-3 bg-transparent text-dark fw-bold">¿No tienes cuenta? Regístrate</a>

            </form>
          </div>
            <div class="col-md-6 text-center ">
              //TODO: Ask the client for the original image since its quality is too low
              <img src="images/dogcutespa-resized.png" class="img-fluid rounded shadow mt-5" alt="logo" style="max-height: 600px; width: auto; border: 6px solid #000000; box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);"> 
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
});
</script>

</body>
</html>
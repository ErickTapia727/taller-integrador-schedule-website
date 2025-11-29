<?php
/**
 * Funciones de validación
 */

/**
 * Validar email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar RUT chileno
 * @param string $rut
 * @return bool
 */
function validateRut($rut) {
    // Eliminar puntos y guión
    $rut = preg_replace('/[^0-9kK]/', '', $rut);
    
    if (strlen($rut) < 2) {
        return false;
    }
    
    $dv = strtoupper(substr($rut, -1));
    $numero = substr($rut, 0, -1);
    
    // Calcular dígito verificador
    $suma = 0;
    $multiplo = 2;
    
    for ($i = strlen($numero) - 1; $i >= 0; $i--) {
        $suma += $numero[$i] * $multiplo;
        $multiplo = ($multiplo < 7) ? $multiplo + 1 : 2;
    }
    
    $dvEsperado = 11 - ($suma % 11);
    
    if ($dvEsperado == 11) {
        $dvEsperado = '0';
    } elseif ($dvEsperado == 10) {
        $dvEsperado = 'K';
    } else {
        $dvEsperado = (string)$dvEsperado;
    }
    
    return $dv === $dvEsperado;
}

/**
 * Formatear RUT (agregar puntos y guión)
 * @param string $rut
 * @return string
 */
function formatRut($rut) {
    $rut = preg_replace('/[^0-9kK]/', '', $rut);
    $dv = substr($rut, -1);
    $numero = substr($rut, 0, -1);
    $numero = number_format($numero, 0, '', '.');
    return $numero . '-' . $dv;
}

/**
 * Validar teléfono chileno
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    // Formato: +56912345678 (código país + 9 dígitos)
    return preg_match('/^\+56[0-9]{9}$/', $phone) === 1;
}

/**
 * Formatear teléfono chileno
 * @param string $phone
 * @return string
 */
function formatPhone($phone) {
    // Eliminar todo excepto números
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Si empieza con 569, agregar +
    if (substr($phone, 0, 3) === '569') {
        return '+' . $phone;
    }
    
    // Si empieza con 56, agregar +
    if (substr($phone, 0, 2) === '56') {
        return '+' . $phone;
    }
    
    // Si empieza con 9 y tiene 9 dígitos, agregar +56
    if (substr($phone, 0, 1) === '9' && strlen($phone) === 9) {
        return '+56' . $phone;
    }
    
    return $phone;
}

/**
 * Validar contraseña
 * @param string $password
 * @return array ['valid' => bool, 'errors' => array]
 */
function validatePassword($password) {
    $errors = [];
    $minLength = defined('MIN_PASSWORD_LENGTH') ? MIN_PASSWORD_LENGTH : 8;
    $maxLength = defined('MAX_PASSWORD_LENGTH') ? MAX_PASSWORD_LENGTH : 50;
    
    // Verificar longitud mínima
    if (strlen($password) < $minLength) {
        $errors[] = "La contraseña debe tener al menos {$minLength} caracteres";
    }
    
    // Verificar longitud máxima
    if (strlen($password) > $maxLength) {
        $errors[] = "La contraseña no debe exceder {$maxLength} caracteres";
    }
    
    // Verificar al menos una letra mayúscula
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "La contraseña debe contener al menos una letra mayúscula";
    }
    
    // Verificar al menos una letra minúscula
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "La contraseña debe contener al menos una letra minúscula";
    }
    
    // Verificar al menos un número O un carácter especial (no ambos requeridos)
    $hasNumber = preg_match('/[0-9]/', $password);
    $hasSpecial = preg_match('/[^A-Za-z0-9]/', $password);
    
    if (!$hasNumber && !$hasSpecial) {
        $errors[] = "La contraseña debe contener al menos un número o un carácter especial";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Sanitizar entrada de texto
 * @param string $input
 * @return string
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Validar fecha
 * @param string $date Formato: Y-m-d
 * @return bool
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Validar hora
 * @param string $time Formato: H:i
 * @return bool
 */
function validateTime($time) {
    return preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $time) === 1;
}

/**
 * Validar que la fecha no sea pasada
 * @param string $date Formato: Y-m-d
 * @return bool
 */
function isDateFuture($date) {
    $inputDate = DateTime::createFromFormat('Y-m-d', $date);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    return $inputDate >= $today;
}

/**
 * Validar horario de atención
 * @param string $time Formato: H:i
 * @return bool
 */
function isBusinessHours($time) {
    $hour = (int)substr($time, 0, 2);
    $startHour = defined('BUSINESS_START_HOUR') ? BUSINESS_START_HOUR : 8;
    $endHour = defined('BUSINESS_END_HOUR') ? BUSINESS_END_HOUR : 17;
    
    return $hour >= $startHour && $hour < $endHour;
}

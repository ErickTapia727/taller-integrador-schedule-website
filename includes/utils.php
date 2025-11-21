<?php

/**
 * Valida un RUT chileno.
 * * @param string $rut El RUT a validar. Puede tener puntos y guion.
 * @return bool True si el RUT es válido, false en caso contrario.
 */
function validarRut($rut) {
    // 1. Limpiar el RUT (quitar puntos y guion)
    $rutLimpio = str_replace(array('.', '-'), '', $rut);
    
    // 2. Separar el cuerpo del dígito verificador
    $cuerpo = substr($rutLimpio, 0, -1);
    $dv = strtoupper(substr($rutLimpio, -1)); // Dígito verificador
    
    // 3. Validar formato básico
    if (!ctype_digit($cuerpo) || strlen($cuerpo) < 7) {
        return false; // El cuerpo debe ser solo números y tener al menos 7 dígitos
    }
    
    // 4. Calcular el dígito verificador esperado (Algoritmo Módulo 11)
    $suma = 0;
    $multiplo = 2;
    
    // Recorrer el cuerpo de derecha a izquierda
    for ($i = 1; $i <= strlen($cuerpo); $i++) {
        $suma += $multiplo * ($cuerpo[strlen($cuerpo) - $i]);
        $multiplo++;
        if ($multiplo == 8) {
            $multiplo = 2;
        }
    }
    
    $resto = $suma % 11;
    $dvEsperado = 11 - $resto;
    
    // 5. Manejar los casos especiales (K y 0)
    if ($dvEsperado == 11) {
        $dvEsperado = '0';
    } elseif ($dvEsperado == 10) {
        $dvEsperado = 'K';
    } else {
        $dvEsperado = (string)$dvEsperado;
    }
    
    // 6. Comparar el DV calculado con el DV ingresado
    if ($dvEsperado == $dv) {
        return true;
    } else {
        return false;
    }
}

?>
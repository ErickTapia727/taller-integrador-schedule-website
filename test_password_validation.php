<?php
require_once 'includes/validators.php';

echo "<h2>Test de Validación de Contraseñas</h2>";
echo "<style>
    .pass { color: green; font-weight: bold; }
    .fail { color: red; font-weight: bold; }
    body { font-family: Arial; padding: 20px; }
    .test { margin: 15px 0; padding: 10px; border: 1px solid #ddd; }
</style>";

$testCases = [
    // Contraseñas que DEBEN ser válidas
    ['password' => 'Abc12345', 'expected' => true, 'description' => 'Con mayúscula, minúscula y número'],
    ['password' => 'Password1', 'expected' => true, 'description' => 'Con mayúscula, minúscula y número'],
    ['password' => 'Abcd@efg', 'expected' => true, 'description' => 'Con mayúscula, minúscula y carácter especial'],
    ['password' => 'TestPass!', 'expected' => true, 'description' => 'Con mayúscula, minúscula y símbolo'],
    ['password' => 'MyPass123', 'expected' => true, 'description' => 'Con mayúscula, minúscula y números'],
    
    // Contraseñas que DEBEN ser inválidas
    ['password' => 'abc123', 'expected' => false, 'description' => 'Sin mayúscula (DEBE FALLAR)'],
    ['password' => 'ABC123', 'expected' => false, 'description' => 'Sin minúscula (DEBE FALLAR)'],
    ['password' => 'Abcdefgh', 'expected' => false, 'description' => 'Sin número ni especial (DEBE FALLAR)'],
    ['password' => 'Short1', 'expected' => false, 'description' => 'Menos de 8 caracteres (DEBE FALLAR)'],
    ['password' => 'ABCDEFGH', 'expected' => false, 'description' => 'Solo mayúsculas (DEBE FALLAR)'],
];

foreach ($testCases as $index => $test) {
    $result = validatePassword($test['password']);
    $passed = $result['valid'] === $test['expected'];
    
    echo "<div class='test'>";
    echo "<strong>Test " . ($index + 1) . ":</strong> " . htmlspecialchars($test['description']) . "<br>";
    echo "<strong>Contraseña:</strong> <code>" . htmlspecialchars($test['password']) . "</code><br>";
    echo "<strong>Esperado:</strong> " . ($test['expected'] ? 'Válida' : 'Inválida') . "<br>";
    echo "<strong>Resultado:</strong> " . ($result['valid'] ? 'Válida' : 'Inválida') . "<br>";
    
    if (!empty($result['errors'])) {
        echo "<strong>Errores:</strong><ul>";
        foreach ($result['errors'] as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<strong>Estado:</strong> <span class='" . ($passed ? 'pass' : 'fail') . "'>";
    echo $passed ? "✓ PASÓ" : "✗ FALLÓ";
    echo "</span>";
    echo "</div>";
}

echo "<hr><h3>Resumen de criterios actuales:</h3>";
echo "<ul>";
echo "<li>✓ Mínimo 8 caracteres</li>";
echo "<li>✓ Al menos una letra mayúscula</li>";
echo "<li>✓ Al menos una letra minúscula</li>";
echo "<li>✓ Al menos un número <strong>O</strong> un carácter especial (no ambos obligatorios)</li>";
echo "</ul>";
?>

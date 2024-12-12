<?php
// Función para validar el RUT chileno
function validarRUT($rut) {
    // Eliminar puntos y guión (si se ingresan)
    $rut = preg_replace('/[\.\-]/', '', $rut);

    // Verificar que el RUT tenga un formato válido
    if (!preg_match('/^[0-9]{7,8}-[0-9kK]{1}$/', $rut)) {
        return false;
    }

    // Separar el número y el dígito verificador
    $rut_numeros = substr($rut, 0, -2);
    $rut_digito = strtoupper(substr($rut, -1));

    // Calcular el dígito verificador esperado
    $factor = 2;
    $suma = 0;

    for ($i = strlen($rut_numeros) - 1; $i >= 0; $i--) {
        $suma += $rut_numeros[$i] * $factor;
        $factor = $factor == 7 ? 2 : $factor + 1;
    }

    $digito_esperado = 11 - ($suma % 11);
    $digito_esperado = $digito_esperado == 11 ? 0 : ($digito_esperado == 10 ? 'K' : $digito_esperado);

    // Comparar el dígito verificador ingresado con el esperado
    return $rut_digito == $digito_esperado;
}

// Ejemplo de uso
$rut_ingresado = '12345678-9'; // Aquí debes usar el valor ingresado desde el formulario

if (validarRUT($rut_ingresado)) {
    echo "El RUT es válido";
} else {
    echo "El RUT es inválido";
}
?>

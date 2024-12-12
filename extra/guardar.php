<?php
if (isset($_POST['medicamentos'])) {
    $medicamentosSeleccionados = $_POST['medicamentos'];

    echo "<h2>Receta guardada correctamente con los siguientes medicamentos:</h2>";
    echo "<ul>";
    foreach ($medicamentosSeleccionados as $medicamento) {
        echo "<li>Medicamento ID: " . $medicamento . "</li>";
    }
    echo "</ul>";
} else {
    echo "<h2>No se han seleccionado medicamentos para la receta.</h2>";
}

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: prueba_log/login.php");
    exit();
}

if ($_SESSION['rol'] !== 'paciente') {
    header("Location: acceso_denegado.php");
    exit();
}
?>

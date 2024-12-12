<?php
require_once('../setup.php');

$medicamento_id = $_POST['medicamento_id'];
$nombre = $_POST['nombre'];
$uso = $_POST['uso'];
$similar_a = $_POST['similar_a'];

$sql = "UPDATE lista_medicamentos SET nombre='$nombre', uso='$uso', similar_a='$similar_a' WHERE id=$medicamento_id";

if ($conn->query($sql) === TRUE) {
    header('Location: ../Administrador/gestionar_med.php');
    exit();
} else {
    echo "Error al actualizar el medicamento: " . $conn->error;
}
$conn->close();
?>

<?php
require_once('../setup.php');

$nombre = $_POST['nombre'];
$uso = $_POST['uso'];
$similar_a = $_POST['similar_a'];

$sql = "INSERT INTO lista_medicamentos (nombre, uso, similar_a) VALUES ('$nombre', '$uso', '$similar_a')";

if ($conn->query($sql) === TRUE) {
    header('Location: ../Administrador/gestionar_med.php');
    exit();
} else {
    echo "Error al agregar el medicamento: " . $conn->error;
}
$conn->close();
?>

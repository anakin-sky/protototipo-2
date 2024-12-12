<?php
require_once('../setup.php');

if (isset($_GET['id'])) {
    $medicamento_id = $_GET['id'];

    $sql = "UPDATE lista_medicamentos SET estado = 'eliminado' WHERE id = $medicamento_id";

    if ($conn->query($sql) === TRUE) {
        header('Location: ../Administrador/gestionar_med.php');
        exit();
    } else {
        echo "Error al eliminar el medicamento: " . $conn->error;
    }
} else {
    echo "ID del medicamento no especificado";
}
$conn->close();
?>

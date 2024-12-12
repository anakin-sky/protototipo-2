<?php
require_once('../setup.php');

$sql = "SELECT * FROM lista_medicamentos WHERE estado = 'activo'";
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta SQL: " . $conn->error);
}

session_start();

$medicamentos = [];

while ($row = $result->fetch_assoc()) {
    $medicamentos[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lista de Medicamentos</title>
</head>
<body>
<h1>Seleccionar Medicamentos</h1>

<form action="crear.php" method="post" target="_blank">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Uso</th>
                <th>Similar a</th>
                <th>Acciones</th>
                <th>Seleccionar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($medicamentos as $medicamento) {
                echo "<tr>";
                echo "<td>" . $medicamento['id'] . "</td>";
                echo "<td>" . $medicamento['nombre'] . "</td>";
                echo "<td>" . $medicamento['uso'] . "</td>";
                echo "<td>" . $medicamento['similar_a'] . "</td>";
                echo "<td><a href='editar_medicamento.php?id=" . $medicamento['id'] . "'>Editar</a> | ";
                echo "<td><a href='proceso_eliminar_medicamento.php?id=" . $medicamento['id'] . "'>Eliminar</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <a  href="agregar_medicamento.php">Agregar Nuevo Medicamento</a>
    <input type="submit" value="Agregar a receta">
</form>
</body>
</html>

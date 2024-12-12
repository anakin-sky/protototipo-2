<!DOCTYPE html>
<html>
<head>
    <title>Detalles de la receta</title>
</head>
<body>
    <?php
    require_once('setup.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['medicamentos_seleccionados']) && !empty($_POST['medicamentos_seleccionados'])) {
            echo "<h2>Detalles de la receta:</h2>";
            echo "<ul>";

            foreach ($_POST['medicamentos_seleccionados'] as $id_medicamento) {
                $sql = "SELECT * FROM lista_medicamentos WHERE ID = $id_medicamento";
                $result = $conn->query($sql);
            
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if (array_key_exists('id', $row) && array_key_exists('nombre', $row) && array_key_exists('uso', $row) && array_key_exists('similar_a', $row)) {
                            echo "<li>ID: " . $row['id'] . ", Nombre: " . $row['nombre'] . ", Uso: " . $row['uso'] . ", Similar a: " . $row['similar_a'] . "</li>";
                        } else {
                            echo "Algunas claves no están definidas en los datos del medicamento.";
                        }
                    }
                } else {
                    echo "No se encontraron detalles para el medicamento con ID $id_medicamento";
                }
            }
            
        }
    }     

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['medicamentos_seleccionados']) && !empty($_POST['medicamentos_seleccionados'])) {
        $nombre_usuario = "nombre_de_usuario";
        $sql_id_usuario = "SELECT id FROM usuarios WHERE usuario = '$nombre_usuario'";
        $result_id_usuario = $conn->query($sql_id_usuario);
        
        if ($result_id_usuario->num_rows > 0) {
            $row_id_usuario = $result_id_usuario->fetch_assoc();
            $id_usuario = $row_id_usuario['id'];
            $detalle_receta = "Detalles de la receta";
            $sql_insert = "INSERT INTO recetas (id_usuario, detalle) VALUES ('$id_usuario', '$detalle_receta')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "La receta se ha guardado correctamente en la base de datos.";
            } else {
                echo "Error al guardar la receta: " . $conn->error;
            }
        } else {
            echo "Usuario no encontrado.";
        }
    }
}
    ?>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Medicamento</title>
    <link rel="stylesheet" href="style_edit.css">
</head>
<body>
    <div class="container">
        <h1 class="nuevo_med">Editar Medicamento</h1>
        <?php
        require_once('../setup.php');
        
        $medicamento_id = $_GET['id'];

        $sql = "SELECT * FROM lista_medicamentos WHERE id = $medicamento_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $medicamento = $result->fetch_assoc();
            echo "<div class='formulario-container'>";
            echo "<form action='proceso_editar_medicamento.php' method='post'>";
            echo "<label for='nombre'>Nombre:</label>";
            echo "<input type='text' id='nombre' name='nombre' value='" . $medicamento['nombre'] . "' class='input-field'><br>";

            echo "<label for='uso'>Uso:</label>";
            echo "<input type='text' id='uso' name='uso' value='" . $medicamento['uso'] . "' class='input-field'><br>";

            echo "<label for='similar_a'>Similar a:</label>";
            echo "<input type='text' id='similar_a' name='similar_a' value='" . $medicamento['similar_a'] . "' class='input-field'><br>";

            echo "<input type='hidden' name='medicamento_id' value='" . $medicamento['id'] . "'>";
            echo "<input type='submit' value='Actualizar' class='btn-submit'>";
            echo "<a href='../Administrador/gestionar_med.php'' class='boton-volver' style='margin-left: 10px;'>Volver</a>";
            echo "</form>";
            echo "</div>";
        } else {
            echo "Medicamento no encontrado";
        }
        
        ?>
    </div>
</body>
</html>

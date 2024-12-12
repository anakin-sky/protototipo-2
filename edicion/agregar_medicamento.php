<!DOCTYPE html>
<html>
<head>
    <title>Agregar Medicamento</title>
    <link rel="stylesheet" href="style_edit.css">
    <script>
        function validarFormulario() {
            var nombre = document.getElementById('nombre').value;
            var uso = document.getElementById('uso').value;
            var similar_a = document.getElementById('similar_a').value;

            if (nombre.trim() === '' || uso.trim() === '' || similar_a.trim() === '') {
                alert('Por favor, complete todos los campos');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Agregar Nuevo Medicamento</h1>

        <div class="formulario-container">
            <form action="proceso_agregar_medicamento.php" method="post" onsubmit="return validarFormulario()">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="input-field"><br>
                <label for="uso">Uso:</label>
                <input type="text" id="uso" name="uso" class="input-field"><br>
                <label for="similar_a">Similar a:</label>
                <input type="text" id="similar_a" name="similar_a" class="input-field"><br>
                <br>
                <input type="submit" value="Agregar Medicamento" class="btn-submit">
                <a href="../Administrador/gestionar_med.php" class="boton-volver" style='margin-left: 10px;'>Volver</a>
                <br>
            </form>
        </div>
    </div>
</body>
</html>

<?php
require_once('../setup.php');
require_once('../phpqrcode/qrlib.php'); // Incluir la librería phpqrcode

$sql_usuarios = "SELECT * FROM usuarios WHERE rol = 'paciente'";
$result_usuarios = $conn->query($sql_usuarios);

if ($result_usuarios === false) {
    die("Error en la consulta SQL de usuarios: " . $conn->error);
}

$usuarios = [];
while ($row_usuario = $result_usuarios->fetch_assoc()) {
    $usuarios[] = $row_usuario;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['usuario'], $_POST['medicamentos_seleccionados'])) {
        $id_usuario = $_POST['usuario'];
        $medicamentos_seleccionados = $_POST['medicamentos_seleccionados'];
        $fecha_creacion = date('Y-m-d'); // Fecha actual para la receta

        foreach ($medicamentos_seleccionados as $id_medicamento) {
            $cantidad = $_POST['cantidad_' . $id_medicamento];
            $dosis = $_POST['dosis_' . $id_medicamento];
            $frecuencia = $_POST['frecuencia_' . $id_medicamento];
        
            $sql_insert = "INSERT INTO guardar_receta (id_medicamento, nombre_medicamento, uso_medicamento, id_usuario, usuario, nombre, apellido, cantidad, dosis, frecuencia, fecha_creacion)
                           SELECT lm.id, lm.nombre, lm.uso, $id_usuario, u.usuario, u.nombre, u.apellido, $cantidad, '$dosis', '$frecuencia', '$fecha_creacion'
                           FROM lista_medicamentos AS lm
                           JOIN usuarios AS u ON u.id = $id_usuario
                           WHERE lm.id = $id_medicamento";
        
            $result_insert = $conn->query($sql_insert);
        
            if ($result_insert === false) {
                echo "<script>alert('Error al guardar la receta: " . $conn->error . "');</script>";
                exit();
            }
        }

        // Obtener la información del usuario para generar el código QR
        $sql_info_usuario = "SELECT usuario, nombre, apellido FROM usuarios WHERE id = $id_usuario";
        $result_info_usuario = $conn->query($sql_info_usuario);

        if ($result_info_usuario === false) {
            echo "<script>alert('Error al obtener la información del usuario: " . $conn->error . "');</script>";
            exit();
        }

        $info_usuario = $result_info_usuario->fetch_assoc();
        $nombre_completo = $info_usuario['nombre'] . ' ' . $info_usuario['apellido'];
        $rut_usuario = $info_usuario['usuario'];

        // Generar el código QR
        $qr_text = "Receta creada para: " . $nombre_completo . " (Usuario: $rut_usuario) \nFecha: " . $fecha_creacion;
        $qr_filename = "../img/QR/receta_usuario_" . $id_usuario . "_" . date('Ymd') . ".png";
        QRcode::png($qr_text, $qr_filename, QR_ECLEVEL_L, 10);

        // Redirigir automáticamente a la página de ver receta con un breve retraso
        header("Refresh: 0.5; url=../recetasMedico/ver_receta_paciente.php?id=$id_usuario&fecha=$fecha_creacion");
        echo "<p>Guardando receta, por favor espera...</p>";
        exit();
    }
}

// Consultar medicamentos activos
$sql_medicamentos = "SELECT * FROM lista_medicamentos WHERE estado = 'activo'";
$result_medicamentos = $conn->query($sql_medicamentos);

if ($result_medicamentos === false) {
    die("Error en la consulta SQL de medicamentos: " . $conn->error);
}

$medicamentos = [];
while ($row_medicamento = $result_medicamentos->fetch_assoc()) {
    $medicamentos[] = $row_medicamento;
}
?>




<!DOCTYPE html>
<html>
<head>
    <title>Crear Receta</title>
    <link rel="stylesheet" href="style_Preceta.css">
    <style>
        body {
            background-image: url('../img/agregar_img.jpg');
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .encabezado {
            text-align: center;
            padding: 20px;
            color: white;
        }
        .volver_mi {
            display: block;
            margin: 20px auto;
            text-align: center;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            width: 120px;
        }
        .search-wrapper {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }
        .autocomplete-container, .search-container {
            width: 45%;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #f2f2f2;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .autocomplete-list {
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
            z-index: 1000;
            left: 0;
        }
        .autocomplete-list div {
            padding: 8px;
            cursor: pointer;
        }
        .autocomplete-list div:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <header class="encabezado">
        <h1>Crear Receta</h1>
    </header>
    <main>
        <form action="" method="post" onsubmit="return validarFormulario()">
            <div class="search-wrapper">
                <div class="autocomplete-container">
                    <input type="text" id="buscarPaciente" placeholder="Buscar paciente..." autocomplete="off">
                    <input type="hidden" id="idPaciente" name="usuario">
                    <div id="autocomplete-list" class="autocomplete-list"></div>
                </div>

                <div class="search-container">
                    <input type="text" id="buscarMedicamento" placeholder="Buscar medicamento..." onkeyup="filtrarMedicamentos()">
                </div>
            </div>

            <div>
                <input type="submit" value="Guardar Receta"> <a href="../index.php" class="volver_mi">Volver</a>
            </div>

            <table id="tablaMedicamentos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Uso</th>
                        <th>Similar a</th>
                        <th>Cantidad</th>
                        <th>Concentración (Gramos)</th>
                        <th>Frecuencia (Horas)</th>
                        <th>Seleccionar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicamentos as $medicamento) : ?>
                        <tr>
                            <td><?php echo $medicamento['id']; ?></td>
                            <td><?php echo $medicamento['nombre']; ?></td>
                            <td><?php echo $medicamento['uso']; ?></td>
                            <td><?php echo $medicamento['similar_a']; ?></td>
                            <td><input type="number" name="cantidad_<?php echo $medicamento['id']; ?>" value="1"></td>
                            <td>
                                <select name="dosis_<?php echo $medicamento['id']; ?>">
                                    <option value="1mg">1 mg</option>
                                    <option value="100mg">100 mg</option>
                                    <option value="500mg">500 mg</option>
                                    <option value="1g">1 g</option>
                                    <option value="5g">5 g</option>
                                </select>
                            </td>
                            <td>
                                <select name="frecuencia_<?php echo $medicamento['id']; ?>">
                                    <option value="1">Cada 1 hora</option>
                                    <option value="2">Cada 2 horas</option>
                                    <option value="4">Cada 4 horas</option>
                                    <option value="8">Cada 8 horas</option>
                                    <option value="12">Cada 12 horas</option>
                                    <option value="24">Cada 24 horas</option>
                                </select>
                            </td>
                            <td><input type="checkbox" name="medicamentos_seleccionados[]" value="<?php echo $medicamento['id']; ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <br>
        </form>
    </main>

    <script>
        function validarFormulario() {
            const idPaciente = document.getElementById('idPaciente').value;
            const checkboxes = document.querySelectorAll('input[name="medicamentos_seleccionados[]"]:checked');
            const nombrePaciente = document.getElementById('buscarPaciente').value; // Asumimos que el input tiene el nombre del paciente
            const rutPaciente = document.getElementById('rutPaciente') ? document.getElementById('rutPaciente').value : ''; // Si hay un campo oculto o accesible para el RUT

            if (!idPaciente) {
                alert("Por favor, seleccione un paciente.");
                return false;
            }

            if (checkboxes.length === 0) {
                alert("Por favor, seleccione al menos un medicamento.");
                return false;
            }

            // Mensaje de confirmación para la creación exitosa, mostrando nombre y RUT
            alert(`Receta creada correctamente para el paciente:\n${nombrePaciente}`);
            return true;
        }

        document.getElementById('buscarPaciente').addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) {
                document.getElementById('autocomplete-list').innerHTML = '';
                return;
            }

            fetch('buscar_paciente.php?query=' + query)
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('autocomplete-list');
                    list.innerHTML = '';

                    data.forEach(item => {
                        const option = document.createElement('div');
                        option.textContent = item.text;
                        option.onclick = function() {
                            document.getElementById('buscarPaciente').value = item.text;
                            document.getElementById('idPaciente').value = item.id;
                            list.innerHTML = '';
                        };
                        list.appendChild(option);
                    });
                });
        });

        function filtrarMedicamentos() {
            var input = document.getElementById('buscarMedicamento');
            var filter = input.value.toUpperCase();
            var table = document.getElementById('tablaMedicamentos');
            var tr = table.getElementsByTagName('tr');
                
            for (var i = 1; i < tr.length; i++) {
                var tdNombre = tr[i].getElementsByTagName('td')[1];
                var tdUso = tr[i].getElementsByTagName('td')[2];
                var tdSimilar = tr[i].getElementsByTagName('td')[3];
                
                if (tdNombre || tdUso || tdSimilar) {
                    var textValueNombre = tdNombre ? tdNombre.textContent || tdNombre.innerText : "";
                    var textValueUso = tdUso ? tdUso.textContent || tdUso.innerText : "";
                    var textValueSimilar = tdSimilar ? tdSimilar.textContent || tdSimilar.innerText : "";
                    
                    if (
                        textValueNombre.toUpperCase().indexOf(filter) > -1 ||
                        textValueUso.toUpperCase().indexOf(filter) > -1 ||
                        textValueSimilar.toUpperCase().indexOf(filter) > -1
                    ) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>

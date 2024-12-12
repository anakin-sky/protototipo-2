<?php
require_once('../setup.php');

session_start();

$paciente_id = $_GET['id'] ?? null;
$fecha_original = $_GET['fecha'] ?? null;

if (!$paciente_id || !$fecha_original) {
    die("ID de paciente o fecha no proporcionados.");
}

// Obtener los datos del paciente
$sql_paciente = "SELECT nombre, apellido, usuario FROM usuarios WHERE id = ?";
$stmt_paciente = $conn->prepare($sql_paciente);
$stmt_paciente->bind_param('i', $paciente_id);
$stmt_paciente->execute();
$result_paciente = $stmt_paciente->get_result();

if (!$result_paciente->num_rows) {
    die("Paciente no encontrado.");
}

$paciente = $result_paciente->fetch_assoc();
$nombre_paciente = $paciente['nombre'];
$apellido_paciente = $paciente['apellido'];
$rut_paciente = $paciente['usuario'];

$stmt_paciente->close();

// Obtener recetas originales de la fecha proporcionada
$sql_recetas = "SELECT * FROM guardar_receta WHERE id_usuario = ? AND fecha_creacion = ?";
$stmt_recetas = $conn->prepare($sql_recetas);
$stmt_recetas->bind_param('is', $paciente_id, $fecha_original);
$stmt_recetas->execute();
$result_recetas = $stmt_recetas->get_result();

$recetas = [];
while ($row_receta = $result_recetas->fetch_assoc()) {
    $recetas[] = $row_receta;
}
$stmt_recetas->close();

// Obtener todos los medicamentos activos de la base de datos para el desplegable
$sql_medicamentos = "SELECT id, nombre, uso FROM lista_medicamentos WHERE estado = 'activo'";
$result_medicamentos = $conn->query($sql_medicamentos);

$medicamentos = [];
if ($result_medicamentos) {
    while ($row_medicamento = $result_medicamentos->fetch_assoc()) {
        $medicamentos[] = $row_medicamento;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actualizar = $_POST['actualizar'] ?? []; // Índices seleccionados
    $id_medicamento = $_POST['id_medicamento'] ?? [];
    $nombre_medicamento_ids = $_POST['nombre_medicamento'] ?? [];
    $uso_medicamento = $_POST['uso_medicamento'] ?? [];
    $cantidad = $_POST['cantidad'] ?? [];
    $dosis = $_POST['dosis'] ?? [];
    $frecuencia = $_POST['frecuencia'] ?? [];

    // Obtener el nombre del emisor desde la sesión
    $nombre_emisor = $_SESSION['nombre'] ?? 'Desconocido';
    $apellido_emisor = $_SESSION['apellido'] ?? 'Desconocido';
    $emisor = $nombre_emisor . ' ' . $apellido_emisor;

    foreach ($actualizar as $index) {
        // Validar que el índice del checkbox esté en el rango correcto
        if (!isset($id_medicamento[$index], $nombre_medicamento_ids[$index])) {
            continue;
        }

        // Buscar el nombre del medicamento seleccionado
        $nombre_medicamento_seleccionado = '';
        foreach ($medicamentos as $medicamento) {
            if ($medicamento['id'] == $nombre_medicamento_ids[$index]) {
                $nombre_medicamento_seleccionado = $medicamento['nombre'];
                break;
            }
        }

        $uso = $uso_medicamento[$index];
        $cant = $cantidad[$index];
        $dos = $dosis[$index];
        $freq = $frecuencia[$index];

        // Insertar nueva receta con datos completos, incluyendo el emisor
        $sql_insert = "INSERT INTO guardar_receta (id_medicamento, nombre_medicamento, uso_medicamento, id_usuario, nombre, apellido, usuario, cantidad, dosis, frecuencia, fecha_creacion, emisor)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param('issississss', $id_medicamento[$index], $nombre_medicamento_seleccionado, $uso, $paciente_id, $nombre_paciente, $apellido_paciente, $rut_paciente, $cant, $dos, $freq, $emisor);
        if (!$stmt_insert->execute()) {
            echo "<script>alert('Error al crear la receta: " . $stmt_insert->error . "');</script>";
        }
        $stmt_insert->close();
    }

    echo "<script>alert('Receta modificada correctamente.'); window.location.href = 'buscar_paciente.php?id=$paciente_id';</script>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Receta</title>
    <link rel="stylesheet" href="style_receta.css">
    <style>
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
        select, input[type="number"], input[type="text"], input[type="checkbox"] {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            margin: 0;
        }
        select {
            height: 40px;
            font-size: 14px;
        }
        .custom-select {
            position: relative;
            width: 20%;
        }
        .select-all-checkbox {
            margin: 0;
        }
        .volver_mi:last-of-type {
            background-color: #fd7b7b; /*rojo*/
            color: white;
        }
        .volver_mi:last-of-type:hover {
            background-color: #ee4242; /*rojo oscuro*/
        }
    </style>
    <script>
        function seleccionarTodos(checkbox) {
            const checkboxes = document.querySelectorAll('.select-medicamento');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
        }
    </script>
</head>
<body style="background-image: url('../img/medico_img.jpg')">
    <h1 style="text-align: center;">Editar Receta</h1>
    <form method="post">
        <table>
        <div style="text-align: center; margin-top: 20px;">
            <button type="submit">Editar receta</button>
            <a href="buscar_paciente.php" class="volver_mi" style="margin-left: 20px;">Volver</a>
        </div>
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="select-all-checkbox" onclick="seleccionarTodos(this)">
                    </th>
                    <th>ID Medicamento</th>
                    <th>Nombre Medicamento</th>
                    <th>Cantidad</th>
                    <th>Concentración (Gramos)</th>
                    <th>Frecuencia (Horas)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recetas as $index => $receta) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="select-medicamento" name="actualizar[]" value="<?php echo $index; ?>">
                        </td>
                        <td>
                            <input type="hidden" name="id_medicamento[]" id="id_<?php echo $index; ?>" value="<?php echo htmlspecialchars($receta['id_medicamento']); ?>">
                            <input type="text" value="<?php echo htmlspecialchars($receta['id_medicamento']); ?>" readonly>
                        </td>
                        <td class="custom-select">
                            <select name="nombre_medicamento[]" id="dropdown_<?php echo $index; ?>">
                                <option value="">Seleccione un medicamento</option>
                                <?php foreach ($medicamentos as $medicamento) : ?>
                                    <option value="<?php echo htmlspecialchars($medicamento['id']); ?>" <?php echo $receta['id_medicamento'] == $medicamento['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($medicamento['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" name="cantidad[]" value="<?php echo htmlspecialchars($receta['cantidad']); ?>"></td>
                        <td>
                            <select name="dosis[]">
                                <option value="1mg" <?php echo $receta['dosis'] == '1mg' ? 'selected' : ''; ?>>1 mg</option>
                                <option value="100mg" <?php echo $receta['dosis'] == '100mg' ? 'selected' : ''; ?>>100 mg</option>
                                <option value="500mg" <?php echo $receta['dosis'] == '500mg' ? 'selected' : ''; ?>>500 mg</option>
                                <option value="1g" <?php echo $receta['dosis'] == '1g' ? 'selected' : ''; ?>>1 g</option>
                                <option value="5g" <?php echo $receta['dosis'] == '5g' ? 'selected' : ''; ?>>5 g</option>
                            </select>
                        </td>
                        <td>
                            <select name="frecuencia[]">
                                <option value="1" <?php echo $receta['frecuencia'] == '1' ? 'selected' : ''; ?>>Cada 1 hora</option>
                                <option value="2" <?php echo $receta['frecuencia'] == '2' ? 'selected' : ''; ?>>Cada 2 horas</option>
                                <option value="4" <?php echo $receta['frecuencia'] == '4' ? 'selected' : ''; ?>>Cada 4 horas</option>
                                <option value="8" <?php echo $receta['frecuencia'] == '8' ? 'selected' : ''; ?>>Cada 8 horas</option>
                                <option value="12" <?php echo $receta['frecuencia'] == '12' ? 'selected' : ''; ?>>Cada 12 horas</option>
                                <option value="24" <?php echo $receta['frecuencia'] == '24' ? 'selected' : ''; ?>>Cada 24 horas</option>
                            </select>
                        </td>
                    </tr>
                    <!-- Campo oculto para uso del medicamento -->
                    <input type="hidden" name="uso_medicamento[]" id="uso_<?php echo $index; ?>" value="<?php echo htmlspecialchars($receta['uso_medicamento']); ?>">
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</body>
</html>

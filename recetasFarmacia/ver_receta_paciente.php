<?php
require_once('../setup.php');

session_start();

$paciente_id = $_GET['id'] ?? null;
$fecha_seleccionada = $_GET['fecha'] ?? null;

if (!$paciente_id || !$fecha_seleccionada) {
    die("ID de paciente o fecha no proporcionados.");
}

// Consulta para obtener las recetas de la fecha seleccionada
$sql_recetas = "SELECT * FROM guardar_receta WHERE id_usuario = ? AND fecha_creacion = ?";
$stmt_recetas = $conn->prepare($sql_recetas);
$stmt_recetas->bind_param('is', $paciente_id, $fecha_seleccionada);
$stmt_recetas->execute();
$result_recetas = $stmt_recetas->get_result();

$recetas = [];
while ($row_receta = $result_recetas->fetch_assoc()) {
    $recetas[] = $row_receta;
}

$stmt_recetas->close();

// Obtener información del paciente
$sql_paciente = "SELECT nombre, apellido, usuario AS rut FROM usuarios WHERE id = ?";
$stmt_paciente = $conn->prepare($sql_paciente);
$stmt_paciente->bind_param('i', $paciente_id);
$stmt_paciente->execute();
$result_paciente = $stmt_paciente->get_result();
$paciente = $result_paciente->fetch_assoc();
$stmt_paciente->close();

if (!$paciente) {
    die("No se encontró información del paciente.");
}

$nombre_paciente = $paciente['nombre'] . ' ' . $paciente['apellido'];
$rut_paciente = $paciente['rut'];

// Construir la ruta del archivo QR
$qr_filename = "../img/QR/receta_usuario_" . $paciente_id . "_" . date('Ymd', strtotime($fecha_seleccionada)) . ".png";

if (!file_exists($qr_filename)) {
    // Generar el código QR si no existe
    $qr_text = "Receta para: " . $nombre_paciente . "\n";
    $qr_text .= "RUT: " . $rut_paciente . "\n\n";
    foreach ($recetas as $receta) {
        $qr_text .= "Medicamento: " . $receta['nombre_medicamento'] . "\n";
        $qr_text .= "Uso: " . $receta['uso_medicamento'] . "\n";
        $qr_text .= "Dosis: " . ($receta['dosis'] ?: 'No especificado') . "\n";
        $qr_text .= "Cantidad: " . $receta['cantidad'] . "\n";
        $qr_text .= "Frecuencia: " . $receta['frecuencia'] . " horas\n";
        $qr_text .= "Estado: " . $receta['estado'] . "\n\n";
    }
    $qr_text .= "Emisor: " . $recetas[0]['emisor'] . "\n";
    $qr_text .= "Fecha: " . $fecha_seleccionada;

    QRcode::png($qr_text, $qr_filename, QR_ECLEVEL_L, 10);
}

// Manejar la solicitud para marcar un medicamento como recibido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_recibido'])) {
    $medicamento_id = $_POST['medicamento_id'];
    $sql_marcar = "UPDATE guardar_receta SET estado = 'inactivo' WHERE id_medicamento = ? AND id_usuario = ? AND fecha_creacion = ?";
    $stmt_marcar = $conn->prepare($sql_marcar);
    $stmt_marcar->bind_param('iis', $medicamento_id, $paciente_id, $fecha_seleccionada);

    if ($stmt_marcar->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=$paciente_id&fecha=$fecha_seleccionada");
        exit();
    } else {
        echo "<script>alert('Error al marcar el medicamento como recibido: " . $stmt_marcar->error . "');</script>";
    }

    $stmt_marcar->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Recetas del <?php echo htmlspecialchars($fecha_seleccionada); ?></title>
    <link rel="stylesheet" href="style_receta.css">
    <style>
        body {
            background-image: url('../img/medico_img.jpg');
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #f2f2f2;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }

        td {
            text-align: justify;
        }

        td:nth-child(1), /* Centrar números en las columnas específicas */
        td:nth-child(5), 
        td:nth-child(6) {
            text-align: center;
        }

        .boton-wrapper {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 20px;
        }

        .boton {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            color: white;
            transition: background-color 0.3s ease;
        }

        .boton-seleccionar {
            background-color: #6CB4EE; /* Azul suave */
        }

        .boton-seleccionar:hover {
            background-color: #5B9BD5; /* Azul ligeramente más oscuro */
        }

        .boton-inicio {
            background-color: #90EE90; /* Verde suave */
        }

        .boton-inicio:hover {
            background-color: #7AC87A; /* Verde ligeramente más oscuro */
        }

        .boton-recibido {
            background-color: #FF7F50; /* Coral suave */
            border: none;
            padding: 8px 12px;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .boton-recibido:hover {
            background-color: #FF6347; /* Coral más oscuro */
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Recetas del <?php echo htmlspecialchars($fecha_seleccionada); ?></h1>

    <div class="boton-wrapper">
        <a href="buscar_recetas.php?id=<?php echo $paciente_id; ?>" class="boton boton-seleccionar">Volver a Selección de Fecha</a>
        <a href="../index.php" class="boton boton-inicio">Volver al inicio</a>
    </div>

    <?php if (count($recetas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Medicamento</th>
                    <th>Nombre Medicamento</th>
                    <th>Uso</th>
                    <th>Concentración Recomendada</th>
                    <th>Comprimidos</th>
                    <th>Frecuencia (Horas)</th>
                    <th>Fecha Creación</th>
                    <th>Médico Emisor</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recetas as $receta) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($receta['id_medicamento']); ?></td>
                        <td><?php echo htmlspecialchars($receta['nombre_medicamento']); ?></td>
                        <td><?php echo htmlspecialchars($receta['uso_medicamento']); ?></td>
                        <td><?php echo htmlspecialchars($receta['dosis']) ?: 'No especificado'; ?></td>
                        <td><?php echo htmlspecialchars($receta['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($receta['frecuencia']); ?></td>
                        <td><?php echo htmlspecialchars($receta['fecha_creacion']); ?></td>
                        <td><?php echo htmlspecialchars($receta['emisor']); ?></td>
                        <td>
                            <?php if ($receta['estado'] === 'activo'): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="medicamento_id" value="<?php echo $receta['id_medicamento']; ?>">
                                    <button type="submit" name="marcar_recibido" class="boton-recibido">Marcar Recibido</button>
                                </form>
                            <?php else: ?>
                                <span>Recibido</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 20px;">
            <h3 style="color: red">Código QR Generado:</h3>
            <img src="<?php echo htmlspecialchars($qr_filename); ?>" alt="Código QR de la Receta">
        </div>
    <?php else: ?>
        <p style="text-align: center;">No se encontraron recetas para esta fecha.</p>
    <?php endif; ?>
</body>
</html>

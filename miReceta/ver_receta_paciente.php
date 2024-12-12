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

// Manejar el cambio de estado a 'inactivo' si se envía la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_medicamento'])) {
    $id_medicamento = $_POST['id_medicamento'];
    $sql_update = "UPDATE guardar_receta SET estado = 'inactivo' WHERE id_usuario = ? AND fecha_creacion = ? AND id_medicamento = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('isi', $paciente_id, $fecha_seleccionada, $id_medicamento);
    if ($stmt_update->execute()) {
        // Redirigir de nuevo a la página después de actualizar
        header("Location: ver_receta_paciente.php?id=$paciente_id&fecha=$fecha_seleccionada");
        exit();
    } else {
        echo "<script>alert('Error al actualizar el medicamento: " . $stmt_update->error . "');</script>";
    }
    $stmt_update->close();
}

// Construir la ruta del archivo QR
$qr_filename = "../img/QR/receta_usuario_" . $paciente_id . "_" . date('Ymd', strtotime($fecha_seleccionada)) . ".png";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recetas del <?php echo htmlspecialchars($fecha_seleccionada); ?></title>
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
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            text-align: center; /* Centrar el texto en los encabezados */
        }
        td {
            text-align: justify; /* Justificar el texto */
        }
        td:nth-child(3), 
        td:nth-child(4), 
        td:nth-child(5),
        td:nth-child(6) {
            text-align: center;
        }
    </style>

</head>
<body style="background-image: url('../img/medico_img.jpg')">
    <h1>Recetas del <?php echo htmlspecialchars($fecha_seleccionada); ?></h1>
    <div>
        <a href="buscar_recetas.php?id=<?php echo $paciente_id; ?>" class="volver_mi" style="margin-left: 20px">Volver a Selección de Fecha</a>
        <a href="../index.php" class="volver_mi" style="margin-left: 20px">Volver al inicio</a>
    </div>

    <?php if (count($recetas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre Medicamento</th>
                    <th>Uso</th>
                    <th>Concentración Recomendada</th>
                    <th>Comprimidos</th>
                    <th>Frecuencia (Horas)</th>
                    <th>Fecha Creación</th>
                    <th>Médico Emisor</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recetas as $receta) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($receta['nombre_medicamento']); ?></td>
                        <td><?php echo htmlspecialchars($receta['uso_medicamento']); ?></td>
                        <td><?php echo htmlspecialchars($receta['dosis']) ?: 'No especificado'; ?></td>
                        <td><?php echo htmlspecialchars($receta['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($receta['frecuencia']); ?></td>
                        <td><?php echo htmlspecialchars($receta['fecha_creacion']); ?></td>
                        <td><?php echo htmlspecialchars($receta['emisor']); ?></td>
                        <td><?php echo htmlspecialchars($receta['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (file_exists($qr_filename)): ?>
            <div style="text-align: center; margin-top: 20px;">
                <h3>Código QR Generado:</h3>
                <img src="<?php echo htmlspecialchars($qr_filename); ?>" alt="Código QR de la Receta">
            </div>
        <?php else: ?>
            <p style="text-align: center; color: red;">No se encontró el código QR para esta receta.</p>
        <?php endif; ?>

    <?php else: ?>
        <p>No se encontraron recetas para esta fecha.</p>
    <?php endif; ?>
</body>
</html>

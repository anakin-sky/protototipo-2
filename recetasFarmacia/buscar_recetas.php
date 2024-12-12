<?php
require_once('../setup.php');

session_start();

// Consulta para obtener las fechas únicas de las recetas
$sql_fechas = "SELECT DISTINCT fecha_creacion FROM guardar_receta WHERE id_usuario = ?";
$stmt_fechas = $conn->prepare($sql_fechas);
$paciente_id = $_GET['id'] ?? null;

if (!$paciente_id) {
    die("ID de paciente no proporcionado.");
}

$stmt_fechas->bind_param('i', $paciente_id);
$stmt_fechas->execute();
$result_fechas = $stmt_fechas->get_result();

$fechas = [];
while ($row_fecha = $result_fechas->fetch_assoc()) {
    $fechas[] = $row_fecha['fecha_creacion'];
}

$stmt_fechas->close();

// Manejar el cambio de estado a 'inactivo' si se envía la solicitud
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_recibida'])) {
    $fecha_recibida = $_POST['fecha_recibida'];
    $sql_update = "UPDATE guardar_receta SET estado = 'inactivo' WHERE id_usuario = ? AND fecha_creacion = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('is', $paciente_id, $fecha_recibida);
    if ($stmt_update->execute()) {
        $message = "Recetas marcadas como recibidas para la fecha $fecha_recibida";
    } else {
        $message = "Error al actualizar las recetas: " . $stmt_update->error;
    }
    $stmt_update->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recetas por Fecha</title>
    <link rel="stylesheet" href="style_receta.css">
    <style>
        .buscar-fecha {
            margin: 20px 0;
            text-align: center;
        }
        .buscar-fecha input {
            padding: 8px;
            width: 300px;
            font-size: 16px;
        }
        .recibida-form {
            display: inline;
        }
        /* Estilos del cuadro de diálogo personalizado */
        .dialog-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .dialog-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .dialog-box button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .dialog-box button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body style="background-image: url('../img/medico_img.jpg')">
    <h1>Seleccionar Fecha de Recetas</h1>
    <div class="buscar-fecha">
        <input type="text" id="buscarFechaInput" placeholder="Buscar por fecha...">
    </div>
    <div>
        <a href="buscar_paciente.php" class="volver_mi" style="margin-left: 20px">Volver a Buscar Paciente</a>
        <a href="../index.php" class="volver_mi" style="margin-left: 20px">Volver al inicio</a>
    </div>
    <table id="tablaFechas">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Ver Recetas</th>
                <th>Recibida</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fechas as $fecha) : ?>
                <tr>
                    <td><?php echo $fecha; ?></td>
                    <td>
                        <a href="ver_receta_paciente.php?id=<?php echo $paciente_id; ?>&fecha=<?php echo $fecha; ?>">Ver Recetas</a>
                    </td>
                    <td>
                        <form method="post" class="recibida-form" onsubmit="return confirm('¿Está seguro de que desea marcar todas las recetas de esta fecha como recibidas?');">
                            <input type="hidden" name="fecha_recibida" value="<?php echo $fecha; ?>">
                            <input type="submit" value="Marcar como Recibida">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Cuadro de diálogo personalizado -->
    <div class="dialog-overlay" id="dialog-overlay">
        <div class="dialog-box">
            <p id="dialog-message"></p>
            <button onclick="closeDialog()">Aceptar</button>
        </div>
    </div>

    <script>
        // Mostrar mensaje si hay uno definido en PHP
        <?php if (!empty($message)): ?>
            document.getElementById('dialog-message').textContent = "<?php echo htmlspecialchars($message); ?>";
            document.getElementById('dialog-overlay').style.display = 'flex';
        <?php endif; ?>

        function closeDialog() {
            document.getElementById('dialog-overlay').style.display = 'none';
            window.location.href = window.location.href; // Recargar la página
        }

        // Filtrar fechas
        document.getElementById('buscarFechaInput').addEventListener('input', function() {
            const filter = this.value.toUpperCase();
            const table = document.getElementById('tablaFechas');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        });
    </script>
</body>
</html>

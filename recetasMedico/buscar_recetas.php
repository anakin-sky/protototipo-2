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
        .button-wrapper {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }
        .volver_mi {
            padding: 10px 20px;
            text-align: center;
            border-radius: 4px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .volver_mi:first-of-type {
            background-color: #4C9FF0;
            color: white;
        }
        .volver_mi:first-of-type:hover {
            background-color: #3A8AD1;
        }
        .volver_mi:last-of-type {
            background-color: #fd7b7b;
            color: white;
        }
        .volver_mi:last-of-type:hover {
            background-color: #ee4242;
        }
    </style>
</head>
<body style="background-image: url('../img/medico_img.jpg')">
    <h1>Seleccionar Fecha de Recetas</h1>
    <div class="buscar-fecha">
        <input type="text" id="buscarFechaInput" placeholder="Buscar por fecha...">
    </div>
    <div class="button-wrapper">
        <a href="buscar_paciente.php" class="volver_mi">Volver a Buscar Paciente</a>
        <a href="../index.php" class="volver_mi">Volver al inicio</a>
    </div>
    <table id="tablaFechas">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Ver Recetas</th>
                <th>Editar Recetas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fechas as $fecha) : ?>
                <tr>
                    <td><?php echo $fecha; ?></td>
                    <td>
                        <a href="ver_receta_2.php?id=<?php echo $paciente_id; ?>&fecha=<?php echo $fecha; ?>">Ver Recetas</a>
                    </td>
                    <td>
                        <a href="editar_recetas.php?id=<?php echo $paciente_id; ?>&fecha=<?php echo $fecha; ?>">Editar Recetas</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
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

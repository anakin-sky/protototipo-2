<?php
require_once('../setup.php');

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    header("Location: ../prueba_log/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];

// Consulta para obtener el rol del usuario
$sql_rol = "SELECT rol FROM usuarios WHERE id = ?";
$stmt_rol = $conn->prepare($sql_rol);
$stmt_rol->bind_param('i', $usuario_id);
$stmt_rol->execute();
$result_rol = $stmt_rol->get_result();

if ($result_rol->num_rows > 0) {
    $row_rol = $result_rol->fetch_assoc();
    $rol_usuario = $row_rol['rol'];

    // Si el usuario no es paciente, redirigir
    if ($rol_usuario !== 'paciente') {
        header("Location: ../index.php");
        exit();
}
} else {
    header("Location: ../index.php");
    exit();
}

$stmt_rol->close();

// Consulta para obtener las fechas únicas de las recetas del usuario
$sql_fechas = "SELECT DISTINCT fecha_creacion FROM guardar_receta WHERE id_usuario = ?";
$stmt_fechas = $conn->prepare($sql_fechas);
$stmt_fechas->bind_param('i', $usuario_id);
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
    <title>Mis Recetas por Fecha</title>
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
    </style>
</head>
<body style="background-image: url('../img/medico_img.jpg')">
    <h1>Seleccionar Fecha de Mis Recetas</h1>
    <div class="buscar-fecha">
        <input type="text" id="buscarFechaInput" placeholder="Buscar por fecha...">
    </div>
    <div>
        <a href="../index.php" class="volver_mi" style="margin-left: 20px">Volver al inicio</a>
    </div>
    <table id="tablaFechas">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Ver Recetas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fechas as $fecha) : ?>
                <tr>
                    <td><?php echo $fecha; ?></td>
                    <td>
                        <a href="ver_receta_paciente.php?id=<?php echo $usuario_id; ?>&fecha=<?php echo $fecha; ?>">Ver Recetas</a>
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

<?php
require_once('../setup.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../prueba_log/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];

$sql_receta = "SELECT * FROM guardar_receta WHERE id_usuario = $usuario_id";
$result_receta = $conn->query($sql_receta);

if ($result_receta === false) {
    die("Error en la consulta SQL de la receta: " . $conn->error);
}

$recetas = [];
while ($row_receta = $result_receta->fetch_assoc()) {
    $recetas[] = $row_receta;
}

$qrFilePath = "../img/QR/receta_paciente_{$usuario_id}.png";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mi Receta</title>
    <link rel="stylesheet" href="style_Preceta.css">
</head>
<body>
    <header class="encabezado">
        <h1>Mi Receta</h1>
    </header>
    <br>
    <a href="../" class="volver_mi" style="margin-left: 20px">Volver</a>
    <br><br>
    <table class="tabla_estilo">
        <thead>
            <tr>
                <th>Rut</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Nombre Medicamento</th>
                <th>Uso Medicamento</th>
                <th>Concentración</th>
                <th>Comprimidos</th>
                <th>Frecuencia</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recetas as $receta) : ?>
                <tr>
                    <td><?php echo $receta['usuario']; ?></td>
                    <td><?php echo $receta['nombre']; ?></td>
                    <td><?php echo $receta['apellido']; ?></td>
                    <td><?php echo $receta['nombre_medicamento']; ?></td>
                    <td><?php echo $receta['uso_medicamento']; ?></td>
                    <td><?php echo $receta['dosis']; ?></td>
                    <td><?php echo $receta['cantidad']; ?></td>
                    <td><?php echo $receta['frecuencia'] . " horas"; ?></td>
                    <td><?php echo $receta['fecha_creacion']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (file_exists($qrFilePath)): ?>
            <h2 style="text-align: center; color: #4a90e2;">Código QR de la Receta</h2>
            <table class="table-recetas" style="margin: 0 auto; width: 300px;">
                <thead>
                    <tr>
                        <th>QR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <img src="<?php echo $qrFilePath; ?>" alt="QR de la receta" id="qr-code" style="display: block; margin: 0 auto;">
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
</body>
</html>

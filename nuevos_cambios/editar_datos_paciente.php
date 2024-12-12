<?php
require_once('../setup.php');

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../prueba_log/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];

// Recuperar datos actuales del usuario
$sql = "SELECT nombre, apellido, correo, direccion FROM usuarios WHERE id = $usuario_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre_actual = $row['nombre'];
    $apellido_actual = $row['apellido'];
    $correo_actual = $row['correo'];
    $direccion_actual = $row['direccion'];
} else {
    echo "Error al recuperar los datos del usuario.";
    exit();
}

// Actualizar datos si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = $conn->real_escape_string(trim($_POST['nombre']));
    $nuevo_apellido = $conn->real_escape_string(trim($_POST['apellido']));
    $nuevo_correo = $conn->real_escape_string(trim($_POST['correo']));
    $nueva_direccion = $conn->real_escape_string(trim($_POST['direccion']));

    if (!empty($nuevo_nombre) && !empty($nuevo_apellido) && !empty($nuevo_correo) && !empty($nueva_direccion)) {
        $update_sql = "UPDATE usuarios SET nombre = '$nuevo_nombre', apellido = '$nuevo_apellido', correo = '$nuevo_correo', direccion = '$nueva_direccion' WHERE id = $usuario_id";
        if ($conn->query($update_sql) === TRUE) {
            echo "<p>Datos actualizados exitosamente.</p>";
            header("Location: ../paciente.php");
            exit();
        } else {
            echo "Error al actualizar los datos: " . $conn->error;
        }
    } else {
        echo "<p>Por favor, completa todos los campos.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Datos Personales</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('../img/paciente_img.png');
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .formulario-editar {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .formulario-editar h1 {
            margin-bottom: 20px;
        }
        .formulario-editar input[type="text"], .formulario-editar input[type="email"], .formulario-editar input[type="submit"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .formulario-editar input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .formulario-editar input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .boton-regresar {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #007BFF;
        }
    </style>
</head>
<body>
    <div class="formulario-editar">
        <h1>Editar Datos Personales</h1>
        <form method="post" action="">
            <label for="nombre">Nombre:</label><br>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre_actual); ?>" required><br>
            <label for="apellido">Apellido:</label><br>
            <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido_actual); ?>" required><br>
            <label for="correo">Correo Electrónico:</label><br>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo_actual); ?>" required><br>
            <label for="direccion">Dirección:</label><br>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion_actual); ?>" required><br>
            <input type="submit" value="Actualizar Datos">
        </form>
        <a href="../paciente.php" class="boton-regresar">Regresar</a>
    </div>
</body>
</html>

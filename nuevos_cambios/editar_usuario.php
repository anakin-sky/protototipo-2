<?php
require_once('../setup.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../prueba_log/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];

// Recuperar datos actuales del usuario
$sql = "SELECT nombre, apellido, correo, direccion, numero, ciudad, comuna, nacimiento FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre_actual = $row['nombre'];
    $apellido_actual = $row['apellido'];
    $correo_actual = $row['correo'];
    $direccion_actual = $row['direccion'];
    $numero_actual = $row['numero'];
    $ciudad_actual = $row['ciudad'];
    $comuna_actual = $row['comuna'];
    $nacimiento_actual = $row['nacimiento'];
} else {
    echo "Error al recuperar los datos del usuario.";
    exit();
}

$stmt->close();

// Actualizar datos si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_apellido = trim($_POST['apellido']);
    $nuevo_correo = trim($_POST['correo']);
    $nueva_direccion = trim($_POST['direccion']);
    $nuevo_numero = trim($_POST['numero']);
    $nueva_ciudad = trim($_POST['ciudad']);
    $nueva_comuna = trim($_POST['comuna']);
    $nuevo_nacimiento = $_POST['nacimiento'];

    $update_sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, direccion = ?, numero = ?, ciudad = ?, comuna = ?, nacimiento = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ssssssssi', $nuevo_nombre, $nuevo_apellido, $nuevo_correo, $nueva_direccion, $nuevo_numero, $nueva_ciudad, $nueva_comuna, $nuevo_nacimiento, $usuario_id);

    if ($stmt->execute()) {
        echo "<p>Datos actualizados exitosamente.</p>";
        header("Location: ../index.php");
        exit();
    } else {
        echo "Error al actualizar los datos: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Datos del Usuario</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
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
        .formulario-editar input, 
        .formulario-editar select {
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
        .boton-regresar:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        const comunas = {
            "Provincia de Iquique": ["Iquique", "Alto Hospicio"],
            "Provincia del Tamarugal": ["Pozo Almonte", "Pica", "Huara", "Camiña", "Colchane"]
        };

        function actualizarComunas() {
            const provincia = document.getElementById('ciudad').value;
            const comunaSelect = document.getElementById('comuna');
            comunaSelect.innerHTML = "";

            comunas[provincia].forEach(comuna => {
                const option = document.createElement('option');
                option.value = comuna;
                option.textContent = comuna;
                comunaSelect.appendChild(option);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            actualizarComunas();
            document.getElementById('ciudad').addEventListener('change', actualizarComunas);
        });
    </script>
</head>
<body>
    <div class="formulario-editar">
        <h1>Editar Datos del Usuario</h1>
        <form method="post" action="">
            <label for="nombre">Nombre:</label><br>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre_actual); ?>" required><br>

            <label for="apellido">Apellido:</label><br>
            <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($apellido_actual); ?>" required><br>

            <label for="correo">Correo Electrónico:</label><br>
            <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($correo_actual); ?>" required><br>

            <label for="direccion">Dirección:</label><br>
            <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($direccion_actual); ?>"><br>

            <label for="numero">Número de Casa:</label><br>
            <input type="text" id="numero" name="numero" value="<?= htmlspecialchars($numero_actual); ?>" required><br>

            <label for="ciudad">Provincia:</label><br>
            <select id="ciudad" name="ciudad" required>
                <option value="Provincia de Iquique" <?= $ciudad_actual === 'Provincia de Iquique' ? 'selected' : '' ?>>Provincia de Iquique</option>
                <option value="Provincia del Tamarugal" <?= $ciudad_actual === 'Provincia del Tamarugal' ? 'selected' : '' ?>>Provincia del Tamarugal</option>
            </select><br>

            <label for="comuna">Comuna:</label><br>
            <select id="comuna" name="comuna" required>
                <option value="<?= htmlspecialchars($comuna_actual); ?>" selected><?= htmlspecialchars($comuna_actual); ?></option>
            </select><br>

            <label for="nacimiento">Fecha de Nacimiento:</label><br>
            <input type="date" id="nacimiento" name="nacimiento" value="<?= htmlspecialchars($nacimiento_actual); ?>" required><br>

            <input type="submit" value="Actualizar Datos">
        </form>
        <a href="../index.php" class="boton-regresar">Regresar</a>
    </div>
</body>
</html>

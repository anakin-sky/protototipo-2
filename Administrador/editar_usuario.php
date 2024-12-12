<?php
require_once('../setup.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../prueba_log/login.php");
    exit();
}

$usuario_id = $_GET['id'] ?? null;

if (!$usuario_id) {
    die("ID de usuario no proporcionado.");
}

$sql = "SELECT * FROM usuarios WHERE id = $usuario_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Usuario no encontrado.");
}

$usuario = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../style.css">
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
        .formulario-editar label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }
        .formulario-editar input[type="text"], 
        .formulario-editar input[type="email"], 
        .formulario-editar input[type="password"], 
        .formulario-editar select {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .formulario-editar input[type="submit"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            background-color: #007BFF;
            color: white;
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
        const comunasPorProvincia = {
            "Provincia de Iquique": ["Iquique", "Alto Hospicio"],
            "Provincia del Tamarugal": ["Huara", "Camiña", "Colchane", "Pica", "Pozo Almonte"]
        };

        function actualizarComunas() {
            const provinciaSelect = document.getElementById("ciudad");
            const comunaSelect = document.getElementById("comuna");
            const provinciaSeleccionada = provinciaSelect.value;

            comunaSelect.innerHTML = "";

            if (comunasPorProvincia[provinciaSeleccionada]) {
                comunasPorProvincia[provinciaSeleccionada].forEach(comuna => {
                    const option = document.createElement("option");
                    option.value = comuna;
                    option.textContent = comuna;
                    comunaSelect.appendChild(option);
                });
            }
        }
    </script>
</head>
<body>
    <div class="formulario-editar">
        <h1>Editar Usuario</h1>
        <form action="proceso_editar_usuario.php" method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>

            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($usuario['direccion']) ?>" required>

            <label for="numero">Número de Casa:</label>
            <input type="text" id="numero" name="numero" value="<?= htmlspecialchars($usuario['numero']) ?>" required>

            <label for="ciudad">Provincia:</label>
            <select id="ciudad" name="ciudad" onchange="actualizarComunas()" required>
                <option value="Provincia de Iquique" <?= $usuario['ciudad'] === 'Provincia de Iquique' ? 'selected' : '' ?>>Provincia de Iquique</option>
                <option value="Provincia del Tamarugal" <?= $usuario['ciudad'] === 'Provincia del Tamarugal' ? 'selected' : '' ?>>Provincia del Tamarugal</option>
            </select>

            <label for="comuna">Comuna:</label>
            <select id="comuna" name="comuna" required>
                <option value="<?= htmlspecialchars($usuario['comuna']) ?>" selected><?= htmlspecialchars($usuario['comuna']) ?></option>
            </select>

            <label for="nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="nacimiento" name="nacimiento" value="<?= htmlspecialchars($usuario['nacimiento']) ?>" required style="width: calc(100% - 20px); padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc;">

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="medico" <?= $usuario['rol'] === 'medico' ? 'selected' : '' ?>>Medico</option>
                <option value="farmaceutico" <?= $usuario['rol'] === 'farmaceutico' ? 'selected' : '' ?>>Farmaceutico</option>
                <option value="paciente" <?= $usuario['rol'] === 'paciente' ? 'selected' : '' ?>>Paciente</option>
            </select>

            <label for="clave">Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" id="clave" name="password">

            <input type="submit" value="Guardar Cambios">
            <a href="../Administrador/index.php" class="boton-regresar">Regresar</a>
        </form>
    </div>
</body>
</html>

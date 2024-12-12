<?php
require_once('setup.php');

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: prueba_log/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];

// Recuperar datos actuales del usuario y su rol
$sql = "SELECT nombre, apellido, rol FROM usuarios WHERE id = $usuario_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $rol = $row['rol'];

    // Redirigir según el rol del usuario
    switch ($rol) {
        case 'medico':
            header("Location: index.php");
            exit();
        case 'farmaceutico':
            header("Location: Farmacia/index.php");
            exit();
        case 'admin':
            header("Location: Administrador/index.php");
            exit();
        case 'paciente':
            // Continúa mostrando el panel del paciente
            break;
        default:
            echo "Rol no reconocido. Contacte al administrador.";
            exit();
    }
} else {
    echo "Error al recuperar los datos del usuario.";
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel del Paciente</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('img/paciente_img.png');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        }

        header {
            background-color: rgba(128, 128, 128, 0.7);
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
        }

        .botones_opciones {
            display: flex;
            gap: 30px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .boton-contenedor {
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .boton-contenedor:hover {
            transform: translateY(-10px);
        }

        .boton-opcion {
            width: 180px;
            height: 180px;
            display: block;
            background-size: cover;
            background-position: center;
            border-radius: 12px;
            margin: 0 auto;
        }

        .boton-medicamentos {
            background-image: url('img/tabla.png'); /* Imagen para "Lista de Medicamentos" */
        }

        .boton-recetas {
            background-image: url('img/ver_receta_2.png'); /* Imagen para "Mi Receta" */
        }

        .boton-texto {
            margin-top: 10px;
            font-size: 1.2em;
            font-weight: bold;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
            -webkit-text-stroke: 0.5px black;
        }

        .cerrar_sesion {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #FF4B4B;
            color: white;
            padding: 12px 18px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .cerrar_sesion:hover {
            background-color: #FF1F1F;
        }

        .editar_usuario {
            margin-top: 30px;
            text-align: center;
        }

        .editar_usuario a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.3s, color 0.3s;
        }

        .editar_usuario a:hover {
            background-color: rgba(0, 123, 255, 0.9);
            color: white;
        }
    </style>
</head>
<body>
    <header>
        Panel del Paciente - Bienvenido, <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?>
    </header>
    <a href="prueba_log/logout.php" class="cerrar_sesion">Cerrar Sesión</a>
    <main>
        <div class="botones_opciones">
            <div class="boton-contenedor">
                <a href="listamedicamentos/listado_medicamentos.php" class="boton-opcion boton-medicamentos"></a>
                <div class="boton-texto">Lista de Medicamentos</div>
            </div>
            <div class="boton-contenedor">
                <a href="miReceta/buscar_recetas.php" class="boton-opcion boton-recetas"></a>
                <div class="boton-texto">Mi Receta</div>
            </div>
        </div>
        <div class="editar_usuario">
            <a href="nuevos_cambios/editar_usuario.php" style="text-decoration: underline;">Editar mis datos personales</a>
        </div>
    </main>
</body>
</html>

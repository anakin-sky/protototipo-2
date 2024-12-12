<?php
require_once('setup.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: prueba_log/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$sql = "SELECT nombre, apellido, rol FROM usuarios WHERE id = $usuario_id"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $rol = $row['rol'];

    switch ($rol) {
        case 'medico':
            if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
                header("Location: index.php");
                exit();
            }
            break;
        case 'paciente':
            header("Location: paciente.php");
            exit();
        case 'admin':
            header("Location: Administrador/index.php");
            exit();
        case 'farmaceutico':
            header("Location: Farmacia/index.php");
            exit();
        default:
            header("Location: ../index.php");
            exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel de Control del Médico</title>
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
        .encabezado {
            text-align: center;
            margin-bottom: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 15px 20px;
            border-radius: 10px;
        }
        .encabezado h1 {
            font-size: 2.5em;
            margin: 0;
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
        .boton-crear-receta {
            background-image: url('img/crear.png'); /* Imagen para "Crear Receta" */
        }
        .boton-ver-recetas {
            background-image: url('img/ver_receta_2.png'); /* Imagen para "Ver Recetas" */
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
    <header class="encabezado">
        <h1>Panel del Médico - Bienvenido, <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></h1>
    </header>
    <a href="prueba_log/logout.php" class="cerrar_sesion">Cerrar Sesión</a>
    <main>
        <div class="botones_opciones">
            <div class="boton-contenedor">
                <a href="listamedicamentos/listado_medicamentos.php" class="boton-opcion boton-medicamentos"></a>
                <div class="boton-texto">Lista de Medicamentos</div>
            </div>
            <div class="boton-contenedor">
                <a href="recetas/crear_receta_3.php" class="boton-opcion boton-crear-receta"></a>
                <div class="boton-texto">Crear Receta</div>
            </div>
            <div class="boton-contenedor">
                <a href="recetasMedico/buscar_paciente.php" class="boton-opcion boton-ver-recetas"></a>
                <div class="boton-texto">Ver Recetas</div>
            </div>
        </div>
        <div class="editar_usuario">
            <a href="nuevos_cambios/editar_usuario.php" style="text-decoration: underline;">Editar datos personales</a>
        </div>
        
        <h1>segunda prueba de github, veamos sis funciona bien</h1>

    </main>
</body>
</html>

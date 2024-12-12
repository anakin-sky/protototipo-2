<?php
require_once('../setup.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../prueba_log/login.php");
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

    // Verificar que el usuario sea administrador
    if ($rol !== 'admin') {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../prueba_log/login.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-image: url('../img/paciente_img.png');
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
            background-image: url('../img/tabla.png'); /* Imagen para "Lista de Medicamentos" */
        }
        .boton-usuarios {
            background-image: url('../img/edicion.png'); /* Imagen para "Gestionar Usuarios" */
        }
        .boton-eliminados {
            background-image: url('../img/editar.png'); /* Imagen para "Gestionar Usuarios" */
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
        <h1>Panel de Administración - Bienvenido, <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></h1>
    </header>
    <a href="../prueba_log/logout.php" class="cerrar_sesion">Cerrar Sesión</a>
    <main>
        <div class="botones_opciones">
            <div class="boton-contenedor">
                <a href="gestionar_med.php" class="boton-opcion boton-medicamentos"></a>
                <div class="boton-texto">Lista de Medicamentos</div>
            </div>
            <div class="boton-contenedor">
                <a href="buscar_usuario.php" class="boton-opcion boton-usuarios"></a>
                <div class="boton-texto">Gestionar Usuarios</div>
            </div>
            <!--<div class="boton-contenedor">
                <a href="med_eliminados.php" class="boton-opcion boton-eliminados"></a>
                <div class="boton-texto">Medicamentos eliminados</div>
            </div>-->
        </div>
        <div class="editar_usuario">
            <a href="../nuevos_cambios/editar_usuario.php" style="text-decoration: underline;">Editar datos personales</a>
        </div>
    </main>
</body>
</html>
<?php
// Recibe los datos de la URL
$usuario = $_GET['usuario'];
$nombre = $_GET['nombre'];
$apellido = $_GET['apellido'];
$id_medicamento = $_GET['id_medicamento'];
$nombre_medicamento = $_GET['nombre_medicamento'];
$uso_medicamento = $_GET['uso_medicamento'];

// Prepara los datos para enviar a Python
$datos_para_python = "$usuario;$nombre;$apellido;$id_medicamento;$nombre_medicamento;$uso_medicamento";

// Ejecuta el script Python con los datos como argumentos
$output = [];
$pythonScriptPath = 'generar_qr.py'; // Reemplaza con la ruta correcta a tu script Python
exec("python $pythonScriptPath $datos_para_python", $output);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Código QR</title>
    <link rel="stylesheet" href="style_receta.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        
        .btn-volver {
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            padding: 9px 25px;
            background-color: #dc3545;
            color: white;
            border: 2px solid #dc3545;
        }

        .btn-volver:hover {
            background-color: #a40010;
        }

    </style>
</head>
<body>
    <?php
    echo '<img id="qr-code" src="data:image/png;base64,' . implode("", $output) . '" alt="Codigo QR">'; ?>

    <a href="../" class="btn-volver">Volver</a>
</body>
</html>

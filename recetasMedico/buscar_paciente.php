<?php
require_once('../setup.php');

session_start();

// Consulta para obtener todos los pacientes
$sql_usuarios = "SELECT id, usuario, nombre, apellido FROM usuarios WHERE rol = 'paciente'";
$result_usuarios = $conn->query($sql_usuarios);

if ($result_usuarios === false) {
    die("Error en la consulta SQL de usuarios: " . $conn->error);
}

$pacientes = [];
while ($row = $result_usuarios->fetch_assoc()) {
    $pacientes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Paciente</title>
    <link rel="stylesheet" href="style_receta.css">
    <style>
        body {
            background-image: url('../img/medico_img.jpg');
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
        .volver_mi {
            display: block;
            margin: 20px auto;
            text-align: center;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            width: 120px;
        }
        .search-container {
            text-align: center;
            margin: 20px;
        }
        input[type="text"] {
            width: 50%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #f2f2f2;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Buscar Paciente</h1>
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Buscar paciente por RUT, nombre o apellido..." onkeyup="buscarPaciente()">
        <a href="../" class="volver_mi" style="margin-left: 20px;">Volver</a>
    </div>
    <table id="pacientesTable">
        <thead>
            <tr>
                <th>RUT</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Ver Receta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pacientes as $paciente) : ?>
                <tr>
                    <td><?= $paciente['usuario']; ?></td>
                    <td><?= $paciente['nombre']; ?></td>
                    <td><?= $paciente['apellido']; ?></td>
                    <td><a href="buscar_recetas.php?id=<?= $paciente['id']; ?>">Ver Recetas</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Función para filtrar los pacientes en tiempo real
        function buscarPaciente() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('pacientesTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) { // Comienza desde 1 para omitir el encabezado
                const cells = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        }
    </script>
</body>
</html>

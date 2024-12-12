<?php
require_once('../setup.php');
session_start();

// Verificar si el usuario está autenticado
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
} else {
    header("Location: ../index.php");
    exit();
}

// Consulta para obtener todos los medicamentos activos
$sql = "SELECT * FROM lista_medicamentos WHERE estado = 'activo'";
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta SQL: " . $conn->error);
}

$medicamentos = [];

while ($row = $result->fetch_assoc()) {
    $medicamentos[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Medicamentos</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-image: url('../img/paciente_img.png');
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        header {
            text-align: center;
            padding: 20px;
        }
        .cerrar-sesion {
            position: absolute;
            right: 20px;
            top: 20px;
            border: 1px solid black;
            border-radius: 4px;
            padding: 10px 20px;
            text-decoration: none;
            color: black;
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
        .mi-receta {
            display: block;
            margin: 10px auto;
            text-align: center;
            text-decoration: none;
            border: 1px solid black;
            border-radius: 4px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Lista de Medicamentos</h1>
        <a href="../prueba_log/logout.php" class="cerrar-sesion">Cerrar Sesión</a>
    </header>
    
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Buscar medicamento..." onkeyup="buscarMedicamento()">
        <a href="../index.php" class="volver_mi" style="margin-left: 20px;">Volver</a>
    </div>

    <main>
        <table id="medicamentosTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Uso</th>
                    <th>Similar a</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicamentos as $medicamento): ?>
                    <tr>
                        <td><?= htmlspecialchars($medicamento['id']) ?></td>
                        <td><?= htmlspecialchars($medicamento['nombre']) ?></td>
                        <td><?= htmlspecialchars($medicamento['uso']) ?></td>
                        <td><?= htmlspecialchars($medicamento['similar_a']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <script>
        // Función para filtrar los medicamentos en tiempo real
        function buscarMedicamento() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('medicamentosTable');
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

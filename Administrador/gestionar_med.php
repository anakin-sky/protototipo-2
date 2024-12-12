<?php
require_once('../setup.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../prueba_log/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$sql = "SELECT rol FROM usuarios WHERE id = $usuario_id"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rol = $row['rol'];

    if ($rol !== "admin") {
        header("Location: ../index.php");
        exit();
    }
}

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
<html>
<head>
    <title>Gestionar Medicamentos</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
        }

        .encabezado {
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
        }

        .tabla_estilo {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .tabla_estilo th, .tabla_estilo td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .tabla_estilo th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
        }

        .tabla_estilo tr:hover {
            background-color: #f1f1f1;
        }

        .tabla_estilo tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .boton-accion, .boton-eliminar-seleccionados {
            display: inline-block;
            padding: 10px 15px;
            margin: 10px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            background-color: #4CAF50;
            transition: background-color 0.3s ease;
        }

        .boton-accion:hover {
            background-color: #45a049;
        }

        .boton-eliminar {
            background-color: #e74c3c;
        }

        .boton-eliminar:hover {
            background-color: #c0392b;
        }

        .boton-eliminar-seleccionados {
            background-color: #e67e22;
        }

        .boton-eliminar-seleccionados:hover {
            background-color: #d35400;
        }

        .volver-boton {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            text-decoration: none;
            background-color: #4CAF50;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .volver-boton:hover {
            background-color: #45a049;
            transform: translateY(-3px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }

        .volver-boton:active {
            background-color: #3e8e41;
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .boton-ver-eliminados {
            display: inline-block;
            padding: 10px 15px;
            margin: 10px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            background-color: #e74c3c; /* Color azul */
            transition: background-color 0.3s ease;
        }

        .boton-ver-eliminados:hover {
            background-color: #c0392b; /* Azul más oscuro al pasar el cursor */
        }

    </style>
</head>
<body>
    <header class="encabezado">
        <h1>Gestionar Medicamentos</h1>
    </header>
    <main>
        <div style="text-align: center; margin-top: 20px;">
            <button type="button" class="boton-eliminar-seleccionados" onclick="eliminarSeleccionados()">Eliminar seleccionados</button>
            <button type="button" class="boton-ver-eliminados" onclick="window.location.href='med_eliminados.php'">Medicamentos Eliminados</button>
            <a href="index.php" class="volver-boton">Volver</a>
        </div>
        <form id="formEliminarSeleccionados">
            <table class="tabla_estilo">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" title="Seleccionar todos"></th>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Uso</th>
                        <th>Similar a</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaMedicamentos">
                    <?php foreach ($medicamentos as $medicamento): ?>
                        <tr id="row-<?= $medicamento['id'] ?>">
                            <td>
                                <input type="checkbox" name="medicamento_ids[]" value="<?= $medicamento['id'] ?>">
                            </td>
                            <td><?= $medicamento['id'] ?></td>
                            <td><?= $medicamento['nombre'] ?></td>
                            <td><?= $medicamento['uso'] ?></td>
                            <td><?= $medicamento['similar_a'] ?></td>
                            <td>
                                <a href="../edicion/editar_medicamento.php?id=<?= $medicamento['id'] ?>" class="boton-accion">Editar</a>
                                <button type="button" class="boton-accion boton-eliminar" onclick="eliminarMedicamento(<?= $medicamento['id'] ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </main>

    <script>
        // Seleccionar o deseleccionar todos los checkboxes
        document.getElementById("selectAll").addEventListener("change", function() {
            const checkboxes = document.querySelectorAll("input[name='medicamento_ids[]']");
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        // Eliminar un medicamento específico
        function eliminarMedicamento(id) {
            fetch('eliminar_seleccionados.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ medicamento_ids: [id] })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`row-${id}`).remove();
                } else {
                    alert('Error al eliminar el medicamento.');
                }
            });
        }

        // Eliminar medicamentos seleccionados
        function eliminarSeleccionados() {
            const checkboxes = document.querySelectorAll("input[name='medicamento_ids[]']:checked");
            const ids = Array.from(checkboxes).map(checkbox => checkbox.value);

            if (ids.length === 0) {
                alert('Selecciona al menos un medicamento para eliminar.');
                return;
            }

            fetch('eliminar_seleccionados.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ medicamento_ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ids.forEach(id => document.getElementById(`row-${id}`).remove());
                } else {
                    alert('Error al eliminar los medicamentos seleccionados.');
                }
            });
        }
    </script>
</body>
</html>

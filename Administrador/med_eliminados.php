<?php
require_once('../setup.php');

$sql = "SELECT * FROM lista_medicamentos WHERE estado = 'eliminado'";
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta SQL: " . $conn->error);
}

$medicamentos = [];
while ($row = $result->fetch_assoc()) {
    $medicamentos[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Medicamentos Eliminados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="encabezado">
        <h1>Lista de Medicamentos Eliminados</h1>
    </header>
    <main>
        <br>
        <div class="buscador-container">
            <input type="text" id="buscador" class="buscador-input" placeholder="Buscar medicamento, uso o similar...">
        </div>

        <div class="volver-container">
            <a href="index.php" class="volver-boton">Volver</a>
        </div>

        <form action="crear.php" method="post" target="_blank">
            <table class="tabla_estilo">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Uso</th>
                        <th>Similar a</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tablaMedicamentos">
                    <?php foreach ($medicamentos as $medicamento): ?>
                        <tr>
                            <td><?= $medicamento['id'] ?></td>
                            <td><?= $medicamento['nombre'] ?></td>
                            <td><?= $medicamento['uso'] ?></td>
                            <td><?= $medicamento['similar_a'] ?></td>
                            <td class="edits">
                                <a href="../edicion/proceso_reincorporar_medicamento.php?id=<?= $medicamento['id'] ?>" class="reincorporar-boton">Agregar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </main>

    <script>
        document.getElementById("buscador").addEventListener("keyup", function() {
            const filter = this.value.toUpperCase();
            const rows = document.querySelectorAll("#tablaMedicamentos tr");

            rows.forEach(row => {
                const nombre = row.querySelector("td:nth-child(2)").textContent.toUpperCase();
                const uso = row.querySelector("td:nth-child(3)").textContent.toUpperCase();
                const similar = row.querySelector("td:nth-child(4)").textContent.toUpperCase();
                
                if (nombre.includes(filter) || uso.includes(filter) || similar.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>

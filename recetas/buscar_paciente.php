<?php
require_once('../setup.php');

if (isset($_GET['query'])) {
    $search = $conn->real_escape_string($_GET['query']);

    $sql = "SELECT id, usuario, nombre, apellido 
            FROM usuarios 
            WHERE rol = 'paciente' 
            AND (usuario LIKE '%$search%' OR nombre LIKE '%$search%' OR apellido LIKE '%$search%')
            LIMIT 10";

    $result = $conn->query($sql);

    $pacientes = [];
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = [
            'id' => $row['id'],
            'text' => $row['usuario'] . ' - ' . $row['nombre'] . ' ' . $row['apellido']
        ];
    }

    echo json_encode($pacientes);
}
?>
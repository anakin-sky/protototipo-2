<?php
require_once('../setup.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$ids = $data['medicamento_ids'] ?? [];

if (!empty($ids)) {
    $ids = array_map('intval', $ids);
    $id_list = implode(',', $ids);

    $sql = "UPDATE lista_medicamentos SET estado = 'eliminado' WHERE id IN ($id_list)";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No IDs provided']);
}

$conn->close();

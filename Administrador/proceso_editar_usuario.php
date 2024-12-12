<?php
require_once('../setup.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../prueba_log/login.php");
    exit();
}

$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$apellido = $_POST['apellido'] ?? null;
$correo = $_POST['correo'] ?? null;
$direccion = $_POST['direccion'] ?? null;
$numero = $_POST['numero'] ?? null;
$ciudad = $_POST['ciudad'] ?? null;
$comuna = $_POST['comuna'] ?? null;
$nacimiento = $_POST['nacimiento'] ?? null;
$rol = $_POST['rol'] ?? null;
$password = $_POST['password'] ?? null;

if (!$id || !$nombre || !$apellido || !$correo || !$rol || !$ciudad || !$comuna || !$nacimiento || !$direccion || !$numero) {
    die("Faltan campos obligatorios.");
}

$sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, direccion = ?, numero = ?, ciudad = ?, comuna = ?, nacimiento = ?, rol = ?";
$params = [$nombre, $apellido, $correo, $direccion, $numero, $ciudad, $comuna, $nacimiento, $rol];

if ($password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql .= ", clave = ?";
    $params[] = $hashed_password;
}

$sql .= " WHERE id = ?";
$params[] = $id;

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);

if ($stmt->execute()) {
    header("Location: ../Administrador/index.php?mensaje=Usuario actualizado exitosamente");
} else {
    die("Error al actualizar el usuario: " . $conn->error);
}

$conn->close();
?>

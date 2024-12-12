<?php
require_once('../setup.php');

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function validarRut($rutCompleto) {
    $rut = preg_replace('/[^k0-9]/i', '', $rutCompleto);
    if (strlen($rut) < 3) {
        return false;
    }
    $cuerpo = substr($rut, 0, -1);
    $dv = strtoupper(substr($rut, -1));
    $suma = 0;
    $factor = 2;

    for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
        $suma += $cuerpo[$i] * $factor;
        $factor = $factor == 7 ? 2 : $factor + 1;
    }
    $dvEsperado = 11 - ($suma % 11);
    $dvEsperado = $dvEsperado == 11 ? '0' : ($dvEsperado == 10 ? 'K' : $dvEsperado);

    return $dv == $dvEsperado;
}

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$usuario = $_POST['usuario'];
$correo = $_POST['correo'];
$direccion = $_POST['direccion'];
$numero = $_POST['numero'];
$ciudad = $_POST['ciudad'];
$comuna = $_POST['comuna'];
$nacimiento = $_POST['nacimiento'];
$clave = $_POST['clave'];

if (!validarRut($usuario)) {
    header('Location: registro_usuario.php?error=rut_invalido');
    exit();
}

$sql_verificar_rut = "SELECT id FROM usuarios WHERE usuario=?";
$stmt = $conn->prepare($sql_verificar_rut);
$stmt->bind_param('s', $usuario);
$stmt->execute();
$result_verificar = $stmt->get_result();
if ($result_verificar->num_rows > 0) {
    header('Location: registro_usuario.php?error=rut_existente');
    exit();
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{12,}$/', $clave)) {
    header('Location: registro_usuario.php?error=clave_insegura');
    exit();
}

$clave_hash = password_hash($clave, PASSWORD_DEFAULT);
$rol = 'paciente';
$sql = "INSERT INTO usuarios (nombre, apellido, usuario, correo, direccion, numero, clave, rol, ciudad, comuna, nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssssssss', $nombre, $apellido, $usuario, $correo, $direccion, $numero, $clave_hash, $rol, $ciudad, $comuna, $nacimiento);

if ($stmt->execute()) {
    header('Location: registro_usuario.php?success=1');
} else {
    header('Location: registro_usuario.php?error=registro_fallido');
}
$conn->close();
?>

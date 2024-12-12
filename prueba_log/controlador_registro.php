<?php
require_once('../setup.php');

// Verificar conexión a la base de datos
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para validar el RUT
function validarRut($rutCompleto) {
    $rut = preg_replace('/[^k0-9]/i', '', $rutCompleto); // Eliminar caracteres no válidos
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

// Recoger datos del formulario
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
$confirmar_clave = $_POST['confirmar_clave'];

// Validar que las contraseñas coincidan
if ($clave !== $confirmar_clave) {
    header('Location: registro_usuario.php?error=clave_no_coincide');
    exit();
}

// Validar formato del RUT
if (!validarRut($usuario)) {
    header('Location: registro_usuario.php?error=rut_invalido');
    exit();
}

// Verificar si el RUT ya está registrado
$sql_verificar_rut = "SELECT id FROM usuarios WHERE usuario=?";
$stmt = $conn->prepare($sql_verificar_rut);
$stmt->bind_param('s', $usuario);
$stmt->execute();
$result_verificar = $stmt->get_result();
if ($result_verificar->num_rows > 0) {
    header('Location: registro_usuario.php?error=rut_existente');
    exit();
}

// Verificar que la contraseña cumpla con los requisitos de seguridad
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{12,}$/', $clave)) {
    header('Location: registro_usuario.php?error=clave_insegura');
    exit();
}

// Encriptar la contraseña
$clave_hash = password_hash($clave, PASSWORD_DEFAULT);

// Definir el rol por defecto (paciente)
$rol = 'paciente';

// Insertar los datos del usuario en la base de datos
$sql = "INSERT INTO usuarios (nombre, apellido, usuario, correo, direccion, numero, clave, rol, ciudad, comuna, nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssssssss', $nombre, $apellido, $usuario, $correo, $direccion, $numero, $clave_hash, $rol, $ciudad, $comuna, $nacimiento);

if ($stmt->execute()) {
    // Redirigir con éxito
    header('Location: registro_usuario.php?success=1');
} else {
    // Redirigir en caso de error
    header('Location: registro_usuario.php?error=registro_fallido');
}

// Cerrar la conexión
$conn->close();
?>

<?php
require_once('../setup.php');
session_start();

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

// Validar si el usuario existe en la base de datos
$sql = "SELECT id, nombre, apellido, clave, rol FROM usuarios WHERE usuario='$usuario'";
$result = $conn->query($sql);

$error_login = false;

// Si se encuentra el usuario
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Verificar si la contraseña es correcta
    if (password_verify($clave, $row['clave'])) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['nombre'] = $row['nombre']; // Guardar el nombre en la sesión
        $_SESSION['apellido'] = $row['apellido']; // Guardar el apellido en la sesión
        $_SESSION['rol'] = $row['rol'];

        // Redirigir según el rol
        switch ($row['rol']) {
            case 'admin':
                header('Location: ../Administrador/index.php');
                break;
            case 'medico':
                header('Location: ../index.php');
                break;
            case 'paciente':
                header('Location: ../paciente.php');
                break;
            case 'farmaceutico':
                header('Location: ../Farmacia/index.php');
                break;
            default:
                // Redirigir a una página por defecto o mostrar un error si el rol no es reconocido
                header('Location: ../index.php');
                break;
        }
        exit();
    } else {
        // Error de contraseña incorrecta
        $error_login = true;
    }
} else {
    // Error de usuario no encontrado
    $error_login = true;
}

// Si hay un error en el login, redirigir con un mensaje genérico
if ($error_login) {
    header("Location: login.php?error=login");
    exit();
}

$conn->close();
?>

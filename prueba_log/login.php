<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="style_log.css">
</head>
<body>

<div class="login-box">
    <h2>Iniciar Sesión</h2>
    <form action="verificar_login.php" method="post">
        <div class="user-box">
            <label for="usuario">RUT:</label>
            <input type="text" id="usuario" name="usuario" required>
        </div>

        <div class="user-box">
            <label for="clave">Contraseña:</label>
            <input type="password" id="clave" name="clave" required>
            <!-- Mostrar mensaje de error genérico si hay problemas con el RUT o la contraseña -->
            <?php if (isset($_GET['error']) && $_GET['error'] === 'login'): ?>
                <p style="color: red; font-size: 12px;">RUT y/o Contraseña incorrecta</p>
            <?php endif; ?>
        </div>

        <input type="submit" value="Iniciar Sesión">
    </form>

    <p>¿No tienes una cuenta? <a href="registro_usuario.php">Registrarse</a></p>
</div>

</body>
</html>

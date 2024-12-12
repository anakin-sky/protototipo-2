<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="style_log.css">
    <script>
        function calcularDV(rut) {
            let suma = 0;
            let multiplicador = 2;
            for (let i = rut.length - 1; i >= 0; i--) {
                suma += parseInt(rut.charAt(i)) * multiplicador;
                multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
            }
            const dv = 11 - (suma % 11);
            if (dv === 11) return '0';
            if (dv === 10) return 'K';
            return dv.toString();
        }

        function actualizarDV() {
            const rut = document.getElementById('rut').value;
            const dvElement = document.getElementById('dv');
            if (rut.length >= 7) {
                const dv = calcularDV(rut);
                dvElement.value = dv;
                document.getElementById('mensaje-rut').textContent = "Recuerde verificar su RUT";
            } else {
                dvElement.value = '';
                document.getElementById('mensaje-rut').textContent = '';
            }
        }

        function actualizarRUTCompleto() {
            const rut = document.getElementById('rut').value;
            const dv = document.getElementById('dv').value;
            document.getElementById('usuario').value = rut + '-' + dv;
        }

        function validarFormulario(event) {
            const claveInput = document.getElementById('clave');
            const mensajeClave = document.getElementById('descripcion-clave');
            const clave = claveInput.value;

            const requisitos = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{12,}$/;

            if (!requisitos.test(clave)) {
                mensajeClave.style.color = "red";
                mensajeClave.classList.add("parpadeo");
                event.preventDefault();
                setTimeout(() => {
                    mensajeClave.classList.remove("parpadeo");
                }, 3000);
                return;
            } else {
                mensajeClave.style.color = "gray";
                mensajeClave.classList.remove("parpadeo");
            }

            actualizarRUTCompleto();
        }

        // Comunas por provincia
        const comunasPorProvincia = {
            "Provincia de Iquique": ["Iquique", "Alto Hospicio"],
            "Provincia del Tamarugal": ["Huara", "Camiña", "Colchane", "Pica", "Pozo Almonte"]
        };

        function actualizarComunas() {
            const provinciaSelect = document.getElementById("ciudad");
            const comunaSelect = document.getElementById("comuna");
            const provinciaSeleccionada = provinciaSelect.value;

            comunaSelect.innerHTML = "";

            if (comunasPorProvincia[provinciaSeleccionada]) {
                comunasPorProvincia[provinciaSeleccionada].forEach(comuna => {
                    const option = document.createElement("option");
                    option.value = comuna;
                    option.textContent = comuna;
                    comunaSelect.appendChild(option);
                });
            }
        }

        function ocultarMensajeRut() {
            const mensajeRut = document.querySelector('.mensaje-validacion');
            if (mensajeRut) {
                setTimeout(() => {
                    mensajeRut.style.display = 'none';
                }, 10000);
            }
        }

        window.onload = ocultarMensajeRut;
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-box {
            background: #ffffff;
            width: 60%;
            max-width: 800px;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .form-column {
            flex: 1;
        }

        .form-column .user-box {
            margin-bottom: 15px;
        }

        .form-column input[type="text"],
        .form-column input[type="email"],
        .form-column input[type="password"],
        .form-column input[type="date"],
        .form-column select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .rut-container {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .descripcion-campo {
            font-size: 12px;
            color: gray;
            margin-top: 5px;
        }

        .descripcion-campo.parpadeo {
            animation: parpadeo 1s ease-in-out 3;
        }

        @keyframes parpadeo {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
        }

        .mensaje-validacion {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .mensaje-exito {
            color: green;
            font-size: 0.9em;
            margin-top: 10px;
            text-align: center;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="register-box">
    <h2>Registro de Usuario</h2>
    <form id="registroForm" action="controlador_registro.php" method="post" onsubmit="validarFormulario(event)">
        <div class="form-container">
            <div class="form-column">
                <div class="user-box">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="user-box">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="user-box">
                    <label for="usuario">RUT:</label>
                    <div class="rut-container">
                        <input type="text" id="rut" name="rut" oninput="actualizarDV()" maxlength="8" required>
                        <span>-</span>
                        <input type="text" id="dv" name="dv" readonly required>
                    </div>
                    <p class="descripcion-campo">Ingrese su RUT sin puntos</p>
                </div>
                <div class="user-box">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
            </div>
            <div class="form-column">
                <div class="user-box">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Ejemplo: Avenida Principal" required>
                </div>
                <div class="user-box">
                    <label for="numero">Número de Casa:</label>
                    <input type="text" id="numero" name="numero" placeholder="Ejemplo: 123" pattern="\d+" title="Solo números." required>
                </div>
                <div class="user-box">
                    <label for="ciudad">Provincia:</label>
                    <select id="ciudad" name="ciudad" onchange="actualizarComunas()" required>
                        <option value="">Seleccione una provincia</option>
                        <option value="Provincia de Iquique">Provincia de Iquique</option>
                        <option value="Provincia del Tamarugal">Provincia del Tamarugal</option>
                    </select>
                </div>
                <div class="user-box">
                    <label for="comuna">Comuna:</label>
                    <select id="comuna" name="comuna" required>
                        <option value="">Seleccione una comuna</option>
                    </select>
                </div>
                <div class="user-box">
                    <label for="nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="nacimiento" name="nacimiento" required>
                </div>
                <div class="user-box">
                    <label for="clave">Contraseña:</label>
                    <input type="password" id="clave" name="clave" required>
                    <p id="descripcion-clave" class="descripcion-campo">Debe contener al menos 12 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial.</p>
                </div>
            </div>
        </div>
        <input type="submit" value="Registrarse">
        <?php if (isset($_GET['error']) && $_GET['error'] === 'rut_existente'): ?>
            <div class="mensaje-validacion">El RUT ingresado ya existe. Por favor, intente con otro.</div>
        <?php endif; ?>
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="mensaje-exito">Usuario registrado correctamente. Por favor, <a href="login.php">inicie sesión</a>.</div>
        <?php endif; ?>
        <p>¿Ya tienes una cuenta? <a href="login.php" id="iniciarSesion">Iniciar sesión</a></p>
        <input type="hidden" id="usuario" name="usuario">
    </form>
</div>
</body>
</html>

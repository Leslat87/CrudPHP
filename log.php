<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="Jquery\jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="backoffice\css\log.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="js/log.js"></script>
</head>

<body>
    <header class="hero">
        <h1>Se requiere Registro o Inicio de Sesión</h1>
    </header>


    <section class="galeria">
    <form action="backoffice/usuarios.php" method="post">
                    <!-- Selector de lenguaje con opciones para Español e Inglés -->
                    <select name="lenguaje">
                        <option value="es">Español</option>
                        <option value="en">English</option>
                    </select>
        <div id="temp">
            <div id="caja-login">
               
                    <br><br>
                    <!-- Campo de entrada para los datos de usuario -->
                    <label for="Usuario">Usuario: </label>
                    <input type="text" id="user" name="user1" required><br><br>
                    <label for="contraseña">Contraseña: </label>
                    <input type="password" id="pwd" name="pwd1" required><br><br>
                    <input hidden id="option" name="option" value='2'></input>
                    <!-- Botones para registrarse e iniciar sesión -->
                    <input type="submit" id="ini" value="Iniciar Sesión">
                </form>
                <input type="button" id="reg" value="Registro">
            </div>
            <div id="caja-registro">
           
                <!-- Formulario de registro con campos para nombre, apellido, email, usuario y contraseña -->
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" /><br><br>
                <label for="apellido1">Apellido:</label>
                <input type="text" id="apellido1" name="apellido1" /><br><br>
                <label for="email">Email</label>
                <input type="text" id="email" name="email" /><br><br>
                <label for="user">User:</label>
                <input type="text" id="user1" /><br><br>
                <label for="">Contraseña:</label>
                <input type="password" id="pwd1" /><br><br>
                <!-- Botón para enviar el formulario de registro -->
                <input type="button" id="newreg" value="Enviar" />
            </div>
        </div>
    </section>

    <footer class="footer">
        <section class="footer__container container">
            <nav class="nav nav--footer">
                <h2 class="footer__title">Portfolio.</h2>
                <br><br>
            </nav>
        </section>

        <section class="footer__copy container">
            <div class="footer__social">

            </div>

            <h3 class="footer__copyright">Derechos reservados &copy; Ismael Parada</h3>
        </section>
    </footer>
</body>

</html>
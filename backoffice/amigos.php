<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "security.php";
include "lenguaje.php";

include_once "UserManager.php";

$userManager = new UserManager($con);

// Verifica si se ha enviado una solicitud de amistad
if (isset($_POST["nombreUsuarioAmigo"])) {
    $nombreUsuarioAmigo = $_POST["nombreUsuarioAmigo"];
    $userManager->enviarSolicitudAmistad($nombreUsuarioAmigo);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amigos</title>
    <link rel="stylesheet" type="text/css" href="css/amigos.css">
</head>

<body>

    <body>
        <header class="hero">
            <nav class="nav container">
                <!-- Formulario para enviar solicitudes de amistad -->
                <form id="formEnviarSolicitud">
                    <h2>
                        <?php echo $lenguaje->index->header->wela; ?>
                    </h2>
                    <input type="text" id="nombreUsuarioAmigo" name="nombreUsuarioAmigo" value='Nombre de su amigo'
                        required>
                    <button type="button" id="btnEnviarSolicitud">Enviar solicitud de amistad</button>
                    <div id="mensajeEnvioSolicitud"></div>
                </form>


                <!-- Sección para mostrar solicitudes pendientes -->
                <section class="solicitudes-pendientes">
                    <h2>Solicitudes Pendientes</h2>
                    <ul id="lista-solicitudes">
                        <?php
                        // Llamar a la función para cargar las solicitudes pendientes
                $userManager->cargarSolicitudesPendientes();
                   
                        ?>
                        <!-- Aquí se mostrarán dinámicamente las solicitudes pendientes -->
                    </ul>
                </section>

                <!-- Formulario para aceptar o rechazar solicitudes -->
                <form id="form-aceptar-rechazar" style="display: none;">
                    <input type="hidden" id="id-amistad-actual" name="idAmistad" value="">
                    <button type="button" id="btnAceptar">Aceptar</button>
                    <button type="button" id="btnRechazar">Rechazar</button>
                </form>
                <div class="nav__logo">

                    <section class="sidebar">
                        <div id="cajapanel">

                            <section class="Amigos">


                               
                            </section>
                        </div>
                    </section>
                </div>
            </nav>
        </header>

       
        <div class="nav__logo">
            <section class="sidebar">
                <div id="cajapanel">
                    <section class="Amigos">
                        <!-- Cerrar sesión -->
                        <a href="../log.php" id="logout"><input type="button" class="nav__button" value=<?php echo $lenguaje->index->header->logout; ?>></a>
                    </section>
                </div>
            </section>
        </div>
        </nav>
        </header>

        

        <section class="galeria-amigos">
            <?php
            // Mostrar amigos aceptados aquí
            $amigosAceptados = $userManager->obtenerListaAmigos();

            // Verifica si hay amigos aceptados antes de mostrar la galería
            if (!empty($amigosAceptados)) {
                ?>
                <h2>Amigos Aceptados</h2>
                <div class='galeriaf'>
                    <?php foreach ($amigosAceptados as $amigo): ?>
                        <?php
                        // Obtener el estado de conexión del amigo
                        $IDLogin = $amigo['IDLogin'];
                        $estadoConexion = $userManager->usuarioEstaConectado($IDLogin);

                        // Determinar el color del estado de conexión
                        $colorConexion = ($estadoConexion) ? 'green' : 'red';
                        ?>
                        <?php if (isset($amigo['nombre_usuario'], $amigo['fondo_usuario'])): ?>
                            <div class='amigo-container'>
                            <a href='galeria_amigo.php?id=<?php echo $amigo['IDLogin']; ?>'>
                                <!-- Muestra la imagen de fondo con las iniciales -->
                                <div class='fondo-container' style='background-image: url(<?php echo $amigo['fondo_usuario']; ?>);'>
                                    <p class='iniciales'>
                                        <?php echo $amigo['iniciales_nombre']; ?>
                                    </p>
                                </div>
                                <!-- Muestra el nombre del amigo y su estado de conexión -->
                                <p>Nombre:
                                    <?php echo $amigo['nombre_usuario']; ?> - Estado: <span
                                        style='color: <?php echo $colorConexion; ?>;'>
                                        <?php echo ucfirst($estadoConexion ? 'conectado' : 'desconectado'); ?>
                                    </span>
                                </p>
                            </div>
                        <?php else: ?>
                            <p>Error: 'nombre_usuario' o 'fondo_usuario' no definidos en este array</p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php
            } else {
                echo "<p>No tienes amigos aceptados actualmente.</p>";
            }
            ?>
        </section>



        <footer class="footer">
            <section class="footer__container container">
                <nav class="nav nav--footer">
                    <a href="../index.php" class="volver-btn"
                        style="color: white; background-color: black; padding: 10px 20px; border-radius: 5px;">
                        <?php echo $lenguaje->index->header->vol; ?>
                    </a>
                </nav>
            </section>

            <section class="footer__copy container">
                <div class="footer__social">

                </div>

                <h3 class="footer__copyright">Derechos reservados &copy; Ismael Parada</h3>
            </section>
        </footer>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="/js/amigos.js"></script>



    </body>

</html>
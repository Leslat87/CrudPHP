<?php
include "backoffice/security.php"; // El security verifica la sesión en nuestro index
include "backoffice/lenguaje.php"; // El idioma elegido es cargado en todas las secciones mediante el xml
include_once('backoffice/UserManager.php'); // Este arcchivo cargara la el objeto intanciado de usuario que logeo
include $_SERVER['DOCUMENT_ROOT'] . '/backoffice/bd.php'; // Incluye el archivo de conexión a la base de datos

if (session_status() == PHP_SESSION_NONE) { // Verifica si la sesión está iniciada, si no, la inicia
    session_start();
}
$userManager = new UserManager($con);
$nombreUsuario = isset($_SESSION['User']) ? $_SESSION['User'] : '';

?>

<!DOCTYPE html>
<html>

<head>
    <title>Index</title>
    <meta charset="utf-8">
    <script src="Jquery\jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="backoffice\css\index.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="js/index.js"></script>
</head>

<body>

<header class="hero">
    <nav class="nav container">
        <div class="nav__logo">
            <section class="sidebar">
                <h1>
                    <?php echo $lenguaje->index->header->wel; ?>, 
                </h1>
                <!-- Si el usuario es administrador tendra acceso al boton panel de usuarios --> 
                <div id="cajapanel ">
                    <?php if ($_SESSION['admin'] == true) { ?>
                        <a href="/backoffice/panel.php"><input id="botonpanel"  class="nav__button" type="button"
                                value="<?php echo $lenguaje->index->header->usu; ?>"></a><br>
                    <?php } ?>
                    <a href="/backoffice/galeria.php"><input id="botonsubir"  class="nav__button" type="button"
                            value="<?php echo $lenguaje->index->header->arch; ?>"></a><br>
                    <a href="/backoffice/amigos.php"><input id="botonamigos"  class="nav__button" type="button"
                            value="<?php echo $lenguaje->index->header->ami; ?>"></a><br>
                    <input type="button" id="logout"  class="nav__button" value="<?php echo $lenguaje->index->header->logout; ?>">
                </div>
            </section>
        </div>
    </nav>
</header>

<h1>FAVORITOS DE LA GALERIA</h1>
<section class="galeriaa">
    <div class="image-row1">
        <?php
        $imageDirectory = "/backoffice/images/art/";

        if (isset($_SESSION['IDLogin'])) { 
            $IDLogin = $_SESSION['IDLogin']; // Obtiene la IDLogin de la sesión
        
            // Consulta las imágenes marcadas como favoritas para el usuario actual
            $query = "SELECT * FROM archivos WHERE id_usuario = $IDLogin AND papelera = 0 AND favoritos = 1";
            $result = mysqli_query($con, $query);

            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    echo '<div class="image-row">'; 
                    while ($row = mysqli_fetch_assoc($result)) {
                        
                        $id_archivo=$row['id_archivo'];
                        $image = $row['nombre_archivo'];
                        $descripcion = $userManager->obtenerDescripcionImagen($id_archivo);
                        ?>
                        <div class="card"> <!-- Agregamos la clase 'card' para la tarjeta -->
                            <img src="<?php echo $imageDirectory . $image; ?>">
                            <div class="image-description">
                                <?php echo $descripcion . "&#10084";
                                 
                                     ?>
                            </div>
                        </div>
                        <?php
                    }
                    echo '</div>';
                } else {
                    echo "No se encontraron imágenes favoritas asociadas a este usuario.";
                }
            } else {
                echo "Error al ejecutar la consulta: " . mysqli_error($con);
            }
        }
        ?>
    </div>
</section>

<h1>GALERIA</h1>
<section class="galeria">
    <div class="image-row">
        <?php
        $imageDirectory = "/backoffice/images/art/"; 
        
        if (isset($_SESSION['IDLogin'])) {
            $IDLogin = $_SESSION['IDLogin'];
            // Consulta las imágenes asociadas a la IDLogin
            $query = "SELECT * FROM archivos WHERE id_usuario = $IDLogin AND papelera = 0 ORDER BY favoritos DESC";
            $result = mysqli_query($con, $query);

            if ($result) { 
                if (mysqli_num_rows($result) > 0) { 
                    echo '<div class="image-row">'; 
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id_archivo=$row['id_archivo'];
                        $image = $row['nombre_archivo'];
                        $descripcion = $userManager->obtenerDescripcionImagen($id_archivo);
                        ?>
                        <div class="card"> 
                            <img src="<?php echo $imageDirectory . $image; ?>">
                            <div class="image-description">
                                <?php echo $descripcion; ?>
                            </div>
                        </div>
                        <?php
                    }
                    echo '</div>';
                } else {
                    echo "No se encontraron imágenes asociadas a este usuario.";
                }
            } else {
                echo "Error al ejecutar la consulta: " . mysqli_error($con);
            }
        }
        ?>
    </div>
</section>

<section class="FotosC">
    <div id="fotosCompartidasContainer">
        <h2>FOTOS COMPARTIDAS</h2>
        <div class="gallery">
            <?php
            $rutaBase = "/backoffice/images/art/";

            if (isset($_SESSION['IDLogin'])) {
                $IDLogin = $_SESSION['IDLogin'];

                $UserManager = new UserManager($con);
                $fotosCompartidas = $UserManager->getFotosCompartidasPorUsuario($IDLogin);

                foreach ($fotosCompartidas as $foto) {
                    ?>
                    <div class="card">
                        <img src="<?php echo $rutaBase . $foto['nombre_archivo']; ?>"
                            alt="<?php echo $foto['descripcion']; ?>">
                        <div class="container">
                            <p>Compartida por: <?php echo $foto['nombre_usuario']; ?></p>
                            <p>Nombre de archivo: <?php echo $foto['nombre_archivo']; ?></p>
                            <p>Descripción: <?php echo $foto['descripcion']; ?></p>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</section>




<footer class="footer">
        <section class="footer__container container">
            <nav class="nav nav--footer">
                
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

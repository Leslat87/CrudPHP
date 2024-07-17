<?php
// Incluir archivos necesarios
include "security.php"; 
include "lenguaje.php"; 
include_once('UserManager.php'); 
include $_SERVER['DOCUMENT_ROOT'] . '/backoffice/bd.php'; 

// Verificar si la sesión está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userManager = new UserManager($con);
$nombreUsuario = isset($_SESSION['User']) ? $_SESSION['User'] : '';

// Obtener el ID del amigo cuya galería se mostrará
$idAmigo = isset($_GET['id']) ? $_GET['id'] : null;

// Verificar si se proporcionó un ID de amigo válido
if (!$idAmigo) {
    // Redireccionar o mostrar un mensaje de error
    header("Location: error.php");
    exit;
}

// Obtener la galería de fotos del amigo específico usando UserManager
$images = $userManager->obtenerArchivosDeAmigo($idAmigo);
// Obtener el nombre del amigo
$nombreAmigo = $userManager->obtenerNombreUsuarioPorId($idAmigo);

// Verificar si se encontraron fotos
if (!empty($images)) {
    // Mostrar las fotos
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Galería de Amigo</title>
        <!-- Agrega aquí tus estilos CSS si es necesario -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="/js/galeria_amigo.js"></script>
        <link rel="stylesheet" type="text/css" href="./css\galeria_amigo.css">
    
    </head>
    <body>
    <header class="hero">
    <nav class="nav container">
        <div class="nav__logo">
            <section class="sidebar">
                <h1>
                    <?php echo $lenguaje->index->header->welo; ?>,
                </h1>

                <div id="cajapanel ">
                  
                    <a href="/backoffice/amigos.php"><input id="botonamigos" class="nav__button" type="button"
                            value="<?php echo $lenguaje->index->header->ami; ?>"></a><br>
                            <a href="../log.php" id="logout"><input type="button" class="nav__button" value=<?php echo $lenguaje->index->header->logout; ?>></a>
                </div>
            </section>
        </div>
    </nav>
</header>
<h1>Galería de Fotos de <?php echo $nombreAmigo; ?></h1>
<div class="gallery">
    <div class="image-row">
        <?php foreach ($images as $image): ?>
            <div class="card">
                <img src="./images/art/<?php echo $image['nombre_archivo']; ?>" alt="<?php echo $image['descripcion']; ?>">
                <div class="description-container">
                    <div class="description"><?php echo $image['descripcion']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

        <footer class="footer">
    <section class="footer__container container">
        <nav class="nav nav--footer">
            
            <a href="../index.php" class="volver-btn" class="nav__button"
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
    </body>
    </html>
    <?php
} else {
    // Mostrar mensaje de que no se encontraron fotos
    echo "<p>No se encontraron fotos en la galería de este amigo.</p>";
}

// Cerrar la conexión
$con->close();
?>

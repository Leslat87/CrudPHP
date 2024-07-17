<?php
include "security.php";
include "lenguaje.php";
include_once('UserManager.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$userManager = new UserManager($con);
?>

<!DOCTYPE html>
<html>

<head>
    <title>galeria</title>
    <meta charset="utf-8">
    <script src="/Jquery\jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="./css\index.css">
    <link rel="stylesheet" type="text/css" href="./css\galeria.css">
  

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
                    <?php if ($_SESSION['admin'] == true) { ?>
                        <a href="/backoffice/panel.php"><input id="botonpanel" class="nav__button" type="button"
                                value="<?php echo $lenguaje->index->header->usu; ?>"></a><br>
                    <?php } ?>
                    
                    <a href="/backoffice/amigos.php"><input id="botonamigos" class="nav__button" type="button"
                            value="<?php echo $lenguaje->index->header->ami; ?>"></a><br>
                    <input type="button" id="logout" class="nav__button" value="<?php echo $lenguaje->index->header->logout; ?>">
                </div>
            </section>
        </div>
    </nav>
</header>

    <!-- Sección de la galería -->
    <h1>GESTIÓN DE IMÁGENES</h1>
    <section class="galeria">

        <div class="image-row">

            <?php
            $imageDirectory = "images/art/";
            include($_SERVER['DOCUMENT_ROOT'] . "/backoffice/bd.php");
            

        
            if (isset($_SESSION['IDLogin'])) {
                $IDLogin = $_SESSION['IDLogin'];

                $query = "SELECT * FROM archivos WHERE id_usuario = $IDLogin AND papelera = 0 ORDER BY favoritos DESC";
                
                $result = mysqli_query($con, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    ?>
                    <section class="galeria">
                        <div class="image-row">
                            <?php
                             while ($row = mysqli_fetch_assoc($result)) {
                                
                                $id_archivo=$row['id_archivo'];
                                $image = $row['nombre_archivo'];
                                $descripcion = $userManager->obtenerDescripcionImagen($id_archivo);
                                $esFavorita = $row['favoritos'] == 1;
                                ?>
                                <!-- Contenedor de la imagen -->
                                <div class="card">
                                    <img src="<?php echo $imageDirectory . $image; ?>" alt="<?php echo $descripcion; ?>">
                                    <div class="card-content">
                                    <?php if ($esFavorita) { ?>
                                        <span class="favorito">&#10084;</span>
                                    <?php } ?>
                                        <h2><?php echo $descripcion; ?></h2>

                                    <!-- Formulario para cambiar el nombre de la imagen -->
                                    <form action="usuarios.php" method="post">
                                        <input type="hidden" name="id_archivo" value="<?php echo $id_archivo; ?>">
                                        <input type="hidden" name="option" value="19">
                                        <input type="text" name="nuevo_nombre" placeholder="Nuevo nombre">
                                        <input type="submit" value="<?php echo $lenguaje->index->header->cnom; ?>">
                                    </form>

                                    <!-- Formulario para cambiar la descripción de la imagen -->
                                    <form action="usuarios.php" method="post">
                                        <input type="hidden" name="id_archivo" value="<?php echo $id_archivo; ?>">
                                        <input type="hidden" name="option" value="8">
                                        <input type="text" name="new_description" placeholder="Nueva descripción">
                                        <input type="submit" value="<?php echo $lenguaje->index->header->cdesc; ?>">
                                    </form>

                                    <!-- Formulario para compartir con amigos aceptados -->
                                    <?php
                                    $amigosAceptados = $userManager-> obtenerListaAmigos();
                                    if ($amigosAceptados !== null) {
                                        ?>
                                        <form class="selecionaramigos" id="formCompartir" action="usuarios.php" method="post">
                                            <input type="hidden" name="id_archivo" id="idArchivo" value="<?php echo $id_archivo; ?>">
                                            <input type="hidden" name="idUsuarioOrigen" value="<?php echo $IDLogin; ?>">
                                            <input type="hidden" name="option" value="21">
                                            <label>Selecciona un amigo:</label>
                                            <select name="amigoDestino" id="amigoDestino">
                                                <?php
                                                foreach ($amigosAceptados as $amigo) {
                                                    $nombreAmigo = $amigo['nombre_usuario'];
                                                    $idAmigo = $amigo['id_amigo'];
                                                    echo '<option value="' . $idAmigo . '">' . $nombreAmigo . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <button type="submit" class="botonCompartir" class="nav__button">Compartir</button>
                                        </form>
                                    <?php } ?>

                                    <!-- Formulario para mandar la imagen a la papelera de reciclaje -->
                                    <form class=formPapelera action="usuarios.php" method="post">
                                        <input type="hidden" name="papelera[]" value="<?php echo basename($image); ?>">
                                        <input type="hidden" name="id_archivo" value="<?php echo $id_archivo; ?>">
                                        <input type="hidden" name="option" value="9">
                                        <button type="submit" class="botonpapelera" id="botonpapelera" >
                                            <?php echo $lenguaje->index->header->pap; ?>
                                        </button>
                                    </form>

                                    <!-- Formulario para eliminar la imagen -->
                                    <form action="usuarios.php" method="post">
                                        <input type="hidden" name="eliminar[]" value="<?php echo basename($image); ?>">
                                        <input type="hidden" name="id_archivo" value="<?php echo $id_archivo; ?>">
                                        <input type="hidden" name="option" value="11">
                                        <input type="submit" value="<?php echo $lenguaje->index->header->delf; ?>">
                                    </form>

                                    <!-- Formulario para hacer una foto favorita -->
                                    <form class="formFavoritos" action="usuarios.php" method="post">
                                        <input type="hidden" name="favoritos[]" value="<?php echo basename($image); ?>">
                                        <input type="hidden" name="id_archivo" value="<?php echo $id_archivo; ?>">
                                        <input type="hidden" name="option" value="12">
                                        <button type="submit" class="botonfavoritos" id="botonFavoritos">
                                            <?php echo $lenguaje->index->header->fav; ?>
                                        </button>
                                    </form>

                                    <!-- Formulario para deshacer una foto favorita -->
                                    <form class="formFavoritosn" action="usuarios.php" method="post">
                                        <input type="hidden" name="favoritosn[]" value="<?php echo basename($image); ?>">
                                        <input type="hidden" name="id_archivo" value="<?php echo $id_archivo; ?>">
                                        <input type="hidden" name="option" value="13">
                                        <button type="submit" class="botondeshacerfavorito" id="botonFavoritos">
                                            <?php echo $lenguaje->index->header->favn; ?>
                                        </button>
                                    </form>
                                </div>
                         </div>
                                <?php
                            }
                            ?>
                        </div>
                    </section>
                    <?php
                } else {
                    echo "No se encontraron imágenes asociadas a este usuario.";
                }
            } else {
                echo "Error al ejecutar la consulta: " . mysqli_error($con);
            }
            ?>

        </div>
    </section>

    <section class="FotosP">
    <div id="fotosPapeleraContainer">
        <h2>PAPELERA</h2>
        <div class="gallery">
            <?php
            $imageDirectory = "/backoffice/images/art/";
            if (isset($_SESSION['IDLogin'])) {
                $IDLogin = $_SESSION['IDLogin'];

                $query = "SELECT * FROM archivos WHERE id_usuario = $IDLogin AND papelera = 1 ORDER BY favoritos DESC";
                $result = mysqli_query($con, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    echo '<div class="image-row">'; 
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id_archivo=$row['id_archivo'];
                        $image = $row['nombre_archivo'];
                        $descripcion = $userManager->obtenerDescripcionImagen($id_archivo);

                        ?>
                        <div class="column">
                            <div class="card">
                            <img src="<?= $imageDirectory . $image ?>">
                            <div class="image-description">
                                <?= $descripcion ?>
                            </div>
                            <form class="formRestaurar" action="usuarios.php" method="post">
                                <input type="hidden" name="restaurar[]" value="<?= basename($image) ?>">
                                <input type="hidden" name="id_archivo" value="<?= $id_archivo ?>">
                                <input type="hidden" name="option" value="10">
                                <button type="submit" class="botonrestaurar" id="botonrestaurar" class="nav__button">
                                    <?= $lenguaje->index->header->res ?>
                                </button>
                            </form>
                        </div>
                        <?php
                    }
                    echo '</div>'; 
                } else {
                    echo "No se encontraron imágenes en papelera asociadas a este usuario.";
                }
            } else {
                echo "Error al ejecutar la consulta: " . mysqli_error($con);
            }
            ?>
        </div>
    </section>

    <section>
        <!-- Formulario para subir archivos -->
        <h1>SUBIR ARCHIVOS</h1>
        <form action="usuarios.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="option" value="7">
            <input type="file" name="imagen[]" id="imagen" multiple>
            <input type="submit" class="bsubirarchivos" class="nav__button" value=<?php echo $lenguaje->index->header->charc; ?>>
        </form>
    </section>

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


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="/js/galeria.js"></script>

    
</body>
</hmtl>
<?php
include "security.php";
include "lenguaje.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <link rel="stylesheet" type="text/css" href="css/panel.css">
    <script src="https://kit.fontawesome.com/264d82521d.js" crossorigin="anonymous"></script>
    
</head>

<body>
   
    <header class="hero">
        <nav class="nav container">
            <div class="nav__logo">
                <h2 class="nav__title">Portfolio.</h2>
                <div>
                    <p>
                  
                    <h1>
                        <?php echo $lenguaje->panel->headerUR; ?>
                    </h1>
                    <div id="cajapanel ">
                        <?php if ($_SESSION['admin'] == true) { ?>
                        <?php } ?>
                        <a href="/backoffice/galeria.php"><input id="botonsubir" class="nav__button" type="button"
                                value="<?php echo $lenguaje->index->header->arch; ?>"></a><br>
                        <a href="/backoffice/amigos.php"><input id="botonamigos" class="nav__button" type="button"
                                value="<?php echo $lenguaje->index->header->ami; ?>"></a><br>
                        <a href="../log.php" id="logout"><input type="button" class="nav__button" value=<?php echo $lenguaje->index->header->logout; ?>></a>

                    </div>
    </header>

    <?php
    include($_SERVER['DOCUMENT_ROOT'] . "/backoffice/bd.php");
    $res = mysqli_query($con, "SELECT * FROM users");
    ?>

    <div id="cajaprincipal" class="cajagrande">
        <?php $count = 0;
        while ($array = mysqli_fetch_assoc($res)) { ?>
            <div class="usuario <?php echo ($count % 2 == 0) ? 'par' : 'impar'; ?>">
                <div class="campo">
                    <label>Ck:</label>
                    <input type="checkbox" class="cb" idid="<?php echo $array["IDLogin"] ?>">
                </div>
                <!-- Campo oculto con el ID del usuario -->
                <div class="campo">
                    <label>ID</label>
                    <input id="id_<?php echo $array["IDLogin"] ?>" value="<?php echo $array["IDLogin"] ?>"
                        capturarID="<?php echo $array["IDLogin"] ?>">
                </div>
                <!-- Campo para el nombre del usuario -->
                <div class="campo">
                    <label>
                        <?php echo $lenguaje->preg->name; ?>
                    </label>
                    <input type="text" id="nombre_<?php echo $array["IDLogin"] ?>" value="<?php echo $array["nombre"] ?>">
                </div>
                <!-- Campo para el apellido del usuario -->
                <div class="campo">
                    <label>
                        <?php echo $lenguaje->preg->fname; ?>
                    </label>
                    <input type="text" id="apellidos_<?php echo $array["IDLogin"] ?>"
                        value="<?php echo $array["apellido"] ?>">
                </div>
                <!-- Campo para el email del usuario -->
                <div class="campo">
                    <label>
                        <?php echo $lenguaje->preg->mail; ?>
                    </label>
                    <input type="text" id="email_<?php echo $array["IDLogin"] ?>" value="<?php echo $array["email"] ?>">
                </div>
                <!-- Campo para el nombre de usuario -->
                <div class="campo">
                    <label>
                        <?php echo $lenguaje->index->log->user; ?>
                    </label>
                    <input type="text" id="usuario_<?php echo $array["IDLogin"] ?>" value="<?php echo $array["User"] ?>">
                </div>
                <!-- Campo para la contraseña del usuario -->
                <div class="campo">
                    <label>
                        <?php echo $lenguaje->index->log->pwd; ?>
                    </label>
                    <input type="text" id="contraseña_<?php echo $array["IDLogin"] ?>" value="<?php echo $array["Pass"] ?>">
                </div>
                <!-- Campo para indicar si el usuario es administrador -->
                <div class="campo">
                    <label>
                        <?php echo $lenguaje->panel->admin; ?>
                    </label>
                    <input type="checkbox" class="cb1" id="admin_<?php echo $array["IDLogin"] ?>"
                        admin="<?php echo $array["Admin"] ?>" <?php if ($array["Admin"] == 1) {
                               echo "checked";
                           } ?>>
                </div>
                <!-- Botón para modificar los datos del usuario -->
                <input type="button" class="botonmodificar" idboton="<?php echo $array["IDLogin"] ?>"
                    value="<?php echo $lenguaje->panel->modDat; ?>">
            </div>
            <?php $count++;
        } ?>
    </div>

    <!-- Botón para borrar usuarios -->
    <input type="button" id="botonborrar" value="<?php echo $lenguaje->panel->delB; ?>" class="caja">
  
    <h1>
        <?php echo $lenguaje->panel->header; ?>
    </h1>
    <!-- Caja para añadir usuarios -->
    <div id="cajaañadir" class="subcaja">
        <div class="caja">
            <label>
                <?php echo $lenguaje->preg->name; ?>
            </label>
            <input type="text" id="nombre" value="">
        </div>
        <div class="caja">
            <label>
                <?php echo $lenguaje->preg->fname; ?>
            </label>
            <input type="text" id="apellido1" value="">
        </div>
        <div class="caja">
            <label>
                <?php echo $lenguaje->preg->mail; ?>
            </label>
            <input type="text" id="email" value="">
        </div>
        <div class="caja">
            <label>
                <?php echo $lenguaje->index->log->user; ?>
            </label>
            <input type="text" id="user1" value="">
        </div>
        <div class="caja">
            <label>
                <?php echo $lenguaje->index->log->pwd; ?>
            </label>
            <input type="text" id="pwd1" value="">
        </div>

        <div class="caja">
            <!-- Botón para añadir usuarios -->
            <input type="button" name="addUser" value="<?php echo $lenguaje->panel->addB; ?>" id="botonAñadirUsuario">
        </div>
    </div>

    <footer class="footer">
        <section class="footer__container container">
            <nav class="nav nav--footer">

                <a href="../index.php" class="volver-btn">
                    <?php echo $lenguaje->index->header->vol; ?>
                </a>
            </nav>
        </section>

        <section class="footer__copy container">
            <h3 class="footer__copyright">Derechos reservados &copy; Ismael Parada</h3>
        </section>
    </footer>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="/js/panel.js"></script>
</body>

</html>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('bd.php');
include_once('UserManager.php'); 

// Cargar el archivo de idioma
$lenguaje = isset($_POST['lenguaje']) ? $_POST['lenguaje'] : 'es'; 
$traducciones = simplexml_load_file("locale/$lenguaje.xml");

$userManager = new UserManager($con);
$option = filter_input(INPUT_POST, 'option', FILTER_VALIDATE_INT);

switch ($option) {
    case 1:
        //Crear Usuario
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $apellido = filter_input(INPUT_POST, 'apellido1', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $username = filter_input(INPUT_POST, 'user1', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'pwd1', FILTER_SANITIZE_SPECIAL_CHARS);
        $lenguaje = filter_input(INPUT_POST, 'lenguaje', FILTER_SANITIZE_SPECIAL_CHARS);
        $userManager->createUser($nombre, $apellido, $email, $username, $password, $lenguaje);
        header("Location: ../index.php");
        break;
    case 2:
        //Login
        $username = filter_input(INPUT_POST, 'user1', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'pwd1', FILTER_SANITIZE_SPECIAL_CHARS);
        $lenguaje = filter_input(INPUT_POST, 'lenguaje', FILTER_SANITIZE_SPECIAL_CHARS);
        $userManager->loginUser($username, $password, $lenguaje);
        break;
    case 3:
        //LogOut
        $userManager->logoutUser();
        break;
    case 4:
        //Modificar Usuario
        $nombre = filter_input(INPUT_POST, 'nnombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $apellido = filter_input(INPUT_POST, 'aapellidos', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'eemail', FILTER_SANITIZE_EMAIL);
        $usuario = filter_input(INPUT_POST, 'uusuario', FILTER_SANITIZE_SPECIAL_CHARS);
        $contraseña = filter_input(INPUT_POST, 'ccontraseña', FILTER_SANITIZE_SPECIAL_CHARS);
        $administrador = filter_input(INPUT_POST, 'aadministrador',   FILTER_SANITIZE_NUMBER_INT);
        $id = filter_input(INPUT_POST, 'iid', FILTER_VALIDATE_INT);
        $userManager->modificarUsuario($nombre, $apellido, $email, $usuario, $contraseña, $administrador, $id);
        break;
    case 5:
        //Borrar Usuario para el check de gestion de usuarios
        $arrayDeId = $_POST["arrayDeId"];
        if (!is_array($arrayDeId)) {
            $arrayDeId = array($arrayDeId); // Convertir a array si no lo es
        }
        $userManager->borrarUsuario($arrayDeId);
        break;
    case 6:
        // añadir usuario en el panel de gestion de usuarios
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $apellido = filter_input(INPUT_POST, 'apellido1', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $username = filter_input(INPUT_POST, 'user1', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'pwd1', FILTER_SANITIZE_SPECIAL_CHARS);
        $userManager->añadirUsuarioPanel($nombre, $apellido, $email, $username, $password);
        break;
    case 7:
        // Subir imagenes
        $imagen = $_FILES["imagen"];
        $userManager->subirarchivos($imagen);
        break;
    case 8:
        // Descripcion en imagenes 
        $userManager->cambiarDescripcion();
        break;
    case 9:
        // Papelera
        $imagenesAPapelera = filter_input(INPUT_POST, 'papelera', FILTER_SANITIZE_SPECIAL_CHARS);
        $id_archivo = filter_input(INPUT_POST, 'id_archivo', FILTER_VALIDATE_INT);
        $userManager->mandarAPapelera($imagenesAPapelera, $id_archivo);
        echo "La imagen fue mandada a la papelera con éxito";
        break;
    case 10:
        // Restaurar de Papelera
        $rimagenesAPapelera = filter_input(INPUT_POST, 'restaurar', FILTER_SANITIZE_SPECIAL_CHARS);
        $id_archivo = filter_input(INPUT_POST, 'id_archivo', FILTER_VALIDATE_INT);
        $userManager->restaurardePapelera($rimagenesAPapelera, $id_archivo);
        echo "La imagen fue restaurada con éxito";
        break;
    case 11:
        // Borrar imagen
        $imagenesAEliminar = filter_input(INPUT_POST, 'eliminar', FILTER_SANITIZE_SPECIAL_CHARS);
        $id_archivo = filter_input(INPUT_POST, 'id_archivo', FILTER_VALIDATE_INT);
        $userManager->eliminarFotografía($imagenesAEliminar, $id_archivo);
        break;
    case 12:
        // Favorito
        $imagenesAFavoritos = filter_input(INPUT_POST, 'favoritos', FILTER_SANITIZE_SPECIAL_CHARS);
        $id_archivo = filter_input(INPUT_POST, 'id_archivo', FILTER_VALIDATE_INT);
        $userManager->hacerFavorito($imagenesAFavoritos, $id_archivo);
        echo "La imagen fue marcada como favorita con éxito";
        break;
    case 13:
        // Quitar favoritos
        $imagenesAFavoritosn = filter_input(INPUT_POST, 'favoritosn', FILTER_SANITIZE_SPECIAL_CHARS);
        $id_archivo = filter_input(INPUT_POST, 'id_archivo', FILTER_VALIDATE_INT);
        $userManager->quitarFavorito($imagenesAFavoritosn, $id_archivo);
        echo "La imagen fue quitada de favoritos con éxito";
        break;
    case 14:
        // Solicitudes
        $nombreUsuarioAmigo = filter_input(INPUT_POST, 'nombreUsuarioAmigo', FILTER_SANITIZE_SPECIAL_CHARS);
        $userManager->enviarSolicitudAmistad($nombreUsuarioAmigo);
        echo "Solicitud de amistad enviada correctamente.";
        break;
    case 15:
        // Aceptar Solicitud
        $idAmistad = filter_input(INPUT_POST, 'idAmistad', FILTER_VALIDATE_INT);
        $userManager->aceptarSolicitudAmistad($idAmistad);
        echo "Solicitud de amistad aceptada correctamente.";
        break;
    case 16:
        // Cargar solicitudes en estado pendientes
        $userManager->cargarSolicitudesPendientes();
        break;
    case 17:
        // Rechazar solicitud
        $idAmistad = filter_input(INPUT_POST, 'idAmistad', FILTER_VALIDATE_INT);
        $userManager->rechazarSolicitudAmistad($idAmistad);
        break;
    case 18:
        // Lista de amigos
        $amigosAceptados = $userManager->obtenerListaAmigos();
        break;
    case 19:
        // Cambio de nombre a las fotos para que no tomen el nombre por defecto del archivo
        $userManager->cambiarNombreFoto();
        break;
    case 20:
        // mostrar fotos compartidas
        $fotosCompartidas = $userManager->getFotosCompartidasPorUsuario($_SESSION['IDLogin']);  
        echo json_encode($fotosCompartidas);
        break;
    case 21:
        // Compartir fotos
        $idArchivo = filter_input(INPUT_POST, 'id_archivo', FILTER_VALIDATE_INT);
        $idUsuarioOrigen = filter_input(INPUT_POST, 'idUsuarioOrigen', FILTER_VALIDATE_INT);
        $amigoDestino = filter_input(INPUT_POST, 'amigoDestino', FILTER_SANITIZE_SPECIAL_CHARS);
        $userManager->compartirFotografia($idArchivo,$_SESSION['IDLogin'] , $amigoDestino);
        break;
    case 22:
        // Descripcion de imagen
        $id_archivo = filter_input(INPUT_POST, 'id_archivo', FILTER_VALIDATE_INT);
        $descripcion = $userManager->obtenerDescripcionImagen($id_archivo);
        echo $descripcion;
        break;
}

$userManager->closeConnection();

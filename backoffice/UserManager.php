<?php
include_once "bd.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// este es el contructor de los usuarios y sus funciones 
class UserManager
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function createUser($nombre, $apellidos, $email, $usuario, $contraseña, $lenguaje)
    {

        // Verificar si el usuario ya existe
        $stmt_check_user = $this->db->prepare("SELECT User FROM users WHERE User = ?");
        $stmt_check_user->bind_param("s", $usuario);
        $stmt_check_user->execute();
        $stmt_check_user->store_result();

        // Verificar el número de filas obtenidas
        if ($stmt_check_user->num_rows > 0) {
            echo "<script>alert('El nombre de usuario ya está en uso. Por favor, elija otro.')</script>";
            header("Location: ../log.php");
            exit();
        }
        // El usuario no existe, proceder con la inserción
        $stmt_check_user->close();
    
        $stmt = $this->db->prepare("INSERT INTO users (nombre, apellido, email, User, Pass) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $nombre, $apellidos, $email, $usuario, $contraseña);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            
            // Llamamos al login
            $this->loginUser($usuario, $contraseña, $lenguaje);

            // Actualizar la última actividad del usuario para manejar el conectado o desconectado del panel de amigos
            $this->actualizarActividadUsuario($row['IDLogin']);

            header("Location: ../index.php");
            exit();
        } else {
            echo "<script>alert('Usuario o contraseña incorrectos')</script>";
            header("Location: ../log.php");
            exit();
        }
    }

    public function loginUser($username, $password, $lenguaje)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE User = ? and Pass = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            $_SESSION['IDLogin'] = $row['IDLogin'];
            $_SESSION['user'] = $username;
            $_SESSION['admin'] = $row['Admin'];
            $_SESSION['lenguaje'] = $lenguaje;
            $_SESSION['logged'] = 1;
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['apellido1'] = $row['apellido'];
            $_SESSION['email'] = $row['email'];

            // Actualizar la última actividad del usuario
            $this->actualizarActividadUsuario($row['IDLogin']);

            header("Location: ../index.php");
            exit();
        } else {
            echo "<script>alert('Usuario o contraseña incorrectos')</script>";
            header("Location: ../log.php");
            exit();
        }
    }
    // Modificar informacion del usuario desde el panel de gestión de usuario
    public function modificarUsuario($nombre, $apellido, $email, $usuario, $contraseña, $administrador, $id)
    {

        $consulta = "UPDATE users SET nombre = ?, apellido = ?, email = ?, User = ?, Pass = ?, Admin = ? WHERE IDLogin = ?"; 
        $stmt = $this->db->prepare($consulta); 
        $stmt->bind_param("sssssii", $nombre, $apellido, $email, $usuario, $contraseña, $administrador, $id); 
        $stmt->execute(); 
        $stmt->close();
        $this->db->close(); 
    }

    public function borrarUsuario($idUsuarios)
{
    // Convertir a array si es un único ID para utilizar el ck
    if (!is_array($idUsuarios)) {
        $idUsuarios = array($idUsuarios);
    }

    // Iterar sobre cada ID de usuario y borrarlos uno por uno pues hay que conseguir que un usuario borrado no quede registro en otras tablas en las que pudo intervenir
    foreach ($idUsuarios as $idUsuario) {
    // Eliminar las fotos compartidas por el usuario
    $stmtFotosCompartidas = $this->db->prepare("DELETE FROM fotos_compartidas WHERE id_usuario_origen = ? OR usuario_destino = ?");
    $stmtFotosCompartidas->bind_param("ii", $idUsuario, $idUsuario);
    $stmtFotosCompartidas->execute();
    $stmtFotosCompartidas->close();

    // Eliminar los archivos del usuario de la tabla archivos
    $stmtArchivos = $this->db->prepare("DELETE FROM archivos WHERE id_usuario = ?");
    $stmtArchivos->bind_param("i", $idUsuario);
    $stmtArchivos->execute();
    $stmtArchivos->close();

    // Eliminar las amistades relacionadas con el usuario
    $stmtAmistades = $this->db->prepare("DELETE FROM amigos WHERE id_usuario1 = ? OR id_usuario2 = ?");
    $stmtAmistades->bind_param("ii", $idUsuario, $idUsuario);
    $stmtAmistades->execute();
    $stmtAmistades->close();

    // Eliminar al usuario de la tabla users
    $stmt = $this->db->prepare("DELETE FROM users WHERE IDLogin = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $stmt->close();

    
    return true;
}
}

    

    public function obtenerDescripcionImagen($id_archivo)
    {
        $query = "SELECT descripcion FROM archivos WHERE id_archivo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $id_archivo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            return $row['descripcion'];
        } else {
            return '';
        }
    }

    public function añadirUsuarioPanel($nombre, $apellidos, $email, $usuario, $contraseña)
    {

        $stmt = $this->db->prepare("INSERT INTO users (nombre, apellido, email, User, Pass) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $nombre, $apellidos, $email, $usuario, $contraseña);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res === TRUE) {
            echo "Error al crear el usuario.";
            return true;
        } else {
            echo "El usuario se ha creado correctamente.";
            return false;
        }
    }
    public function subirarchivos($imagen)
    {

        $fileCount = count($imagen["name"]);
        for ($i = 0; $i < $fileCount; $i++) {
            $nombreArchivo = $imagen["name"][$i];
            $nombreUnico = uniqid() . '_' . $nombreArchivo; // Generamos un uniqid y lo concatenamos al nombre del archivo
            $archivonombrado = "images/art/" . basename($nombreUnico);
            $descripcion = $_POST["descripcion"][$i];

            $consulta = "INSERT INTO archivos (id_usuario, nombre_archivo, archivonombrado, descripcion) VALUES (?, ?, ?, ?)";
            $declaracion = $this->db->prepare($consulta);
            $idUsuario = $_SESSION['IDLogin'];
            $declaracion->bind_param("isss", $idUsuario, $nombreUnico, $archivonombrado, $descripcion);
            $declaracion->execute();
            $declaracion->close();

            move_uploaded_file($imagen["tmp_name"][$i], $archivonombrado); // Movemos el archivo cargado a la ruta definida
        }
        $this->db->close();
        header("Location: ../index.php");
    }

    public function cambiarDescripcion()
    {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_archivo = $_POST['id_archivo'];
            $newDescription = $_POST['new_description'];
            $query = "UPDATE archivos SET descripcion = ? WHERE id_archivo = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $newDescription, $id_archivo);
            $stmt->execute();
            header("Location: galeria.php");
            exit();
        }
    }

    public function cambiarNombreFoto()
    {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_archivo = $_POST['id_archivo'];
            $nuevoNombre = $_POST['nuevo_nombre'];
            $query = "UPDATE archivos SET archivonombrado = ? WHERE id_archivo = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $nuevoNombre, $id_archivo);
            $stmt->execute();
            header("Location: galeria.php");
            exit();
        }
    }

    public function mandarAPapelera($imagenesAPapelera, $id_archivo)
    {
        $imagenesAPapelera = $_POST['papelera'];
        $id_archivo = $_POST['id_archivo'];
        $id_usuario = $_SESSION['IDLogin'];

        foreach ($imagenesAPapelera as $imagen) {
            $query = "SELECT * FROM archivos WHERE nombre_archivo=? AND id_archivo=? AND id_usuario=?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sii", $imagen, $id_archivo, $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $query = "UPDATE archivos SET papelera=1 WHERE nombre_archivo = ? AND id_archivo = ? AND id_usuario = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("sii", $imagen, $id_archivo, $id_usuario);
                $stmt->execute();

            } else {
                echo "La foto $imagen no pertenece al usuario";
            }
        }
        header("Location: galeria.php");
    }
    public function restaurardePapelera($rimagenesAPapelera, $id_archivo)
    {
        $rimagenesAPapelera = $_POST['restaurar'];
        $id_archivo = $_POST['id_archivo'];
        $id_usuario = $_SESSION['IDLogin'];
        foreach ($rimagenesAPapelera as $imagen) {
            $query = "SELECT * FROM archivos WHERE nombre_archivo=? AND id_archivo=? AND id_usuario=?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sii", $imagen, $id_archivo, $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $query = "UPDATE archivos SET papelera=0 WHERE nombre_archivo = ? AND id_archivo = ? AND id_usuario = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("sii", $imagen, $id_archivo, $id_usuario);
                $stmt->execute();

            } else {
                echo "La foto $imagen no pertenece al usuario";
            }
        }
        header("Location: galeria.php");
    }

    public function eliminarFotografía($imagenesAEliminar, $id_archivo)
    {
        $id_usuario = $_SESSION['IDLogin']; 
        $imagenesAEliminar = $_POST['eliminar'];
        $directorioImagenes = "./images/art/";

        foreach ($imagenesAEliminar as $imagen) {
            $query = "SELECT * FROM archivos WHERE nombre_archivo=? AND id_usuario=?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $imagen, $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_archivo = $row['id_archivo'];

                // Eliminar las referencias en fotos_compartidas
                $queryDeleteCompartidas = "DELETE FROM fotos_compartidas WHERE id_archivo = ?";
                $stmtDeleteCompartidas = $this->db->prepare($queryDeleteCompartidas);
                $stmtDeleteCompartidas->bind_param("i", $id_archivo);
                $stmtDeleteCompartidas->execute();
                $stmtDeleteCompartidas->close();

                // Eliminar la imagen de la base de datos
                $queryDeleteArchivo = "DELETE FROM archivos WHERE id_archivo = ?";
                $stmtDeleteArchivo = $this->db->prepare($queryDeleteArchivo);
                $stmtDeleteArchivo->bind_param("i", $id_archivo);
                $stmtDeleteArchivo->execute();
                $stmtDeleteArchivo->close();

                // Eliminar el archivo del servidor
                $rutaImagen = $directorioImagenes . $imagen;
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                } else {
                    echo "El archivo $imagen no existe en el servidor";
                }
            } else {
                echo "La foto $imagen no pertenece al usuario, no se puede eliminar";
            }
        }

        header("Location: galeria.php");
    }


    public function hacerFavorito($imagenesAFavoritos, $id_archivo)
    {
        $imagenesAFavoritos = $_POST['favoritos'];
        $id_usuario = $_SESSION['IDLogin'];
        $id_archivo = $_POST['id_archivo'];

        foreach ($imagenesAFavoritos as $imagen) {
            $query = "SELECT * FROM archivos WHERE nombre_archivo = ? AND id_archivo = ? AND id_usuario = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sii", $imagen, $id_archivo, $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $query = "UPDATE archivos SET favoritos=1 WHERE nombre_archivo = ? AND id_archivo = ? AND id_usuario = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("sii", $imagen, $id_archivo, $id_usuario);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                } else {
                    echo "No se actualizó ninguna fila. Puede ser que la imagen ya esté marcada como favorita.";
                }
            } else {
                echo "La foto $imagen no pertenece al usuario, no se puede marcar como favorita.";
            }
        }

        header("Location: ../index.php");
    }


    public function quitarFavorito($imagenesAFavoritosn, $id_archivo)
    {
        $imagenesAFavoritosn = $_POST['favoritosn'];
        $id_usuario = $_SESSION['IDLogin'];
        $id_archivo = $_POST['id_archivo'];

        foreach ($imagenesAFavoritosn as $imagen) {
            $query = "SELECT * FROM archivos WHERE nombre_archivo = ? AND id_archivo = ? AND id_usuario = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sii", $imagen, $id_archivo, $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $query = "UPDATE archivos SET favoritos=0 WHERE nombre_archivo = ? AND id_archivo = ? AND id_usuario = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("sii", $imagen, $id_archivo, $id_usuario);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {

                } else {
                    echo "No se actualizó ninguna fila. Puede ser que la imagen ya no esté marcada como favorita.";
                }
            } else {
                echo "La foto $imagen no pertenece al usuario, no se puede desmarcar como favorita.";
            }
        }

        header("Location: ../index.php");
    }

    public function enviarSolicitudAmistad($nombreUsuarioAmigo)
    {

        $idUsuarioLogueado = $_SESSION['IDLogin'];
        $queryUsuarioAmigo = "SELECT IDLogin FROM users WHERE User LIKE ?";
        $stmtUsuarioAmigo = $this->db->prepare($queryUsuarioAmigo);
        $nombreUsuarioAmigoParam = "%$nombreUsuarioAmigo%";
        $stmtUsuarioAmigo->bind_param("s", $nombreUsuarioAmigoParam);
        $stmtUsuarioAmigo->execute();
        $resultUsuarioAmigo = $stmtUsuarioAmigo->get_result();

        if ($resultUsuarioAmigo->num_rows > 0) {
            $rowUsuarioAmigo = $resultUsuarioAmigo->fetch_assoc();
            $idUsuarioAmigo = $rowUsuarioAmigo['IDLogin'];

            if ($idUsuarioLogueado == $idUsuarioAmigo) {
                echo "No puedes enviar una solicitud de amistad a ti mismo.";
                return;
            } else {
                // Verificar si ya existe una amistad aceptada entre los dos usuarios
                $queryVerificarAmistad = "SELECT estado FROM amigos WHERE (id_usuario1 = ? AND id_usuario2 = ?) OR (id_usuario1 = ? AND id_usuario2 = ?)";
                $stmtVerificarAmistad = $this->db->prepare($queryVerificarAmistad);
                $stmtVerificarAmistad->bind_param("iiii", $idUsuarioLogueado, $idUsuarioAmigo, $idUsuarioAmigo, $idUsuarioLogueado);
                $stmtVerificarAmistad->execute();
                $resultVerificarAmistad = $stmtVerificarAmistad->get_result();

                if ($resultVerificarAmistad->num_rows > 0) {
                    $rowVerificarAmistad = $resultVerificarAmistad->fetch_assoc();
                    $estadoAmistad = $rowVerificarAmistad['estado'];

                    if ($estadoAmistad == 'aceptada') {
                        echo "Ya eres amigo de este usuario.";
                    } elseif ($estadoAmistad == 'rechazada') {
                        // Cambiar el estado de la solicitud de rechazada a pendiente si volvemos a realizar una peticion de amistad
                        $queryActualizarEstado = "UPDATE amigos SET estado = 'pendiente' WHERE (id_usuario1 = ? AND id_usuario2 = ?) OR (id_usuario1 = ? AND id_usuario2 = ?)";
                        $stmtActualizarEstado = $this->db->prepare($queryActualizarEstado);
                        $stmtActualizarEstado->bind_param("iiii", $idUsuarioLogueado, $idUsuarioAmigo, $idUsuarioAmigo, $idUsuarioLogueado);
                        $stmtActualizarEstado->execute();

                        echo "Solicitud de amistad reenviada correctamente.";
                        header("Location: /amigos.php");
                    } else {
                        echo "Ya has enviado una solicitud de amistad a este usuario o está pendiente de aceptación.";
                    }
                } else {
                    // Insertar la solicitud de amistad en la tabla amigos
                    $queryInsertAmistad = "INSERT INTO amigos (id_usuario1, id_usuario2, estado) VALUES (?, ?, 'pendiente')";
                    $stmtInsertAmistad = $this->db->prepare($queryInsertAmistad);
                    $stmtInsertAmistad->bind_param("ii", $idUsuarioLogueado, $idUsuarioAmigo);
                    $stmtInsertAmistad->execute();

                    echo "Solicitud de amistad enviada correctamente.";

                }
            }
        } else {
            echo "Usuario amigo no encontrado.";
        }
        header("Location: /amigos.php");
    }



    // Función para aceptar una solicitud de amistad
    public function aceptarSolicitudAmistad($idAmistad)
    {
        $idUsuarioLogueado = $_SESSION['IDLogin'];

        // Verificar que el usuario logueado sea el receptor de la solicitud
        $queryVerificarSolicitud = "SELECT * FROM amigos WHERE id_amistad = ? AND id_usuario2 = ? AND estado = 'pendiente'";
        $stmtVerificarSolicitud = $this->db->prepare($queryVerificarSolicitud);
        $stmtVerificarSolicitud->bind_param("ii", $idAmistad, $idUsuarioLogueado);
        $stmtVerificarSolicitud->execute();
        $resultVerificarSolicitud = $stmtVerificarSolicitud->get_result();

        if ($resultVerificarSolicitud->num_rows > 0) {
            // Actualizar el estado de la solicitud a 'aceptada'
            $queryAceptarSolicitud = "UPDATE amigos SET estado = 'aceptada' WHERE id_amistad = ?";
            $stmtAceptarSolicitud = $this->db->prepare($queryAceptarSolicitud);
            $stmtAceptarSolicitud->bind_param("i", $idAmistad);
            $stmtAceptarSolicitud->execute();

            echo "Solicitud de amistad aceptada correctamente.";


        } else {
            echo "No puedes aceptar esta solicitud de amistad.";
        }
        header("Location: /amigos.php");
    }
    public function cargarSolicitudesPendientes()
    {
        $idUsuarioLogueado = $_SESSION['IDLogin'];

        $query = "SELECT amigos.id_amistad, amigos.id_usuario1, amigos.id_usuario2, 
                        users.User AS nombre_usuario
                FROM amigos
                JOIN users ON amigos.id_usuario1 = users.IDLogin
                WHERE amigos.id_usuario2 = ? AND amigos.estado = 'pendiente'";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idUsuarioLogueado);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $solicitudes = [];
            while ($row = $result->fetch_assoc()) {
                array_push($solicitudes, $row);
            }
            echo json_encode($solicitudes);
        } else {
            echo json_encode([]);
        }
    }


    public function obtenerSolicitudesAceptadas()
    {
        $idUsuarioLogueado = $_SESSION['IDLogin'];

        $query = "SELECT a.id_amistad, u.User
                  FROM amigos a
                  JOIN users u ON a.id_usuario1 = u.IDLogin
                  WHERE (a.id_usuario1 = ? OR a.id_usuario2 = ?) AND a.estado = 'aceptada'";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $idUsuarioLogueado, $idUsuarioLogueado);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Solicitudes de amistad aceptadas:\n";
            while ($row = $result->fetch_assoc()) {
                echo "ID Amistad: " . $row['id_amistad'] . ", Usuario: " . $row['User'] . "\n";
            }
        } else {
            echo "No tienes solicitudes de amistad aceptadas.\n";
        }
    }

    public function obtenerListaAmigos()
    {
        $usuarioActual = $_SESSION['IDLogin'];

        $query = "SELECT
    CASE
        WHEN a.id_usuario1 = ? THEN u2.User
        ELSE u1.User
    END AS nombre_usuario,
    CASE
        WHEN a.id_usuario1 = ? THEN u2.IDLogin
        ELSE u1.IDLogin
    END AS id_amigo,
    CASE
        WHEN a.id_usuario1 = ? THEN u2.fondo
        ELSE u1.fondo
    END AS fondo_usuario,
    CASE
        WHEN a.id_usuario1 = ? THEN u2.IDLogin
        ELSE u1.IDLogin
    END AS IDLogin
FROM amigos a
LEFT JOIN users u1 ON a.id_usuario1 = u1.IDLogin
LEFT JOIN users u2 ON a.id_usuario2 = u2.IDLogin
WHERE (a.id_usuario1 = ? OR a.id_usuario2 = ?) AND a.estado = 'aceptada'";


        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiiiii", $usuarioActual, $usuarioActual, $usuarioActual, $usuarioActual, $usuarioActual, $usuarioActual);
        $stmt->execute();

        $result = $stmt->get_result();
        // Con esto vamos a crear el icono del usuario para conocer su nombre y su estado de conexion
        $amigos = array();
        while ($row = $result->fetch_assoc()) {
            $inicialesNombre = substr($row['nombre_usuario'], 0, 2); // Obtiene las dos primeras letras del nombre
            $rutaFondo = "images/art/fondo/fondo.png"; // Ruta predeterminada para el fondo
            if (!empty($row['fondo_usuario'])) {
                $rutaFondo = $row['fondo_usuario'];
            }

            // Determinar el estado de conexión del amigo
            $idAmigo = $row['id_amigo'];
            $IDLogin = $row['IDLogin'];
            

            // Agregar el estado de conexión a la estructura de datos del amigo con la funcion usuarioEstaConectado de manera visual
            $amigo = array(
                'nombre_usuario' => $row['nombre_usuario'],
                'id_amigo' => $idAmigo,
                'fondo_usuario' => $rutaFondo,
                'iniciales_nombre' => $inicialesNombre,
                'IDLogin' => $row['IDLogin']
            );

            $amigos[] = $amigo;
        }

        $stmt->close();

        return $amigos;
    }


    public function getFotosCompartidasPorUsuario($idUsuario)
    {
        $query = "SELECT fc.*, u.User AS nombre_usuario FROM fotos_compartidas fc
                  JOIN users u ON fc.id_usuario_origen = u.IDLogin
                  WHERE fc.usuario_destino = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $fotosCompartidas = [];
        while ($row = $result->fetch_assoc()) {
            $fotosCompartidas[] = $row;
        }

        $stmt->close();
        return $fotosCompartidas;
    }



    public function compartirFotografia($idArchivo, $idUsuarioOrigen, $amigoDestino)
    {
    // Verificar si la fotografía ya está compartida con el usuario destino
    $queryExistencia = "SELECT id_archivo FROM fotos_compartidas WHERE id_archivo = ? AND usuario_destino = ?";
    $stmtExistencia = $this->db->prepare($queryExistencia);
    $stmtExistencia->bind_param("ii", $idArchivo, $amigoDestino);
    $stmtExistencia->execute();
    $stmtExistencia->store_result();

    if ($stmtExistencia->num_rows > 0) {
        echo "La fotografía ya está compartida con el usuario destino.";
        return;
    }

        // Verificar si existe una amistad aceptada entre los dos usuarios
        $query = "SELECT estado FROM amigos WHERE (id_usuario1 = ? AND id_usuario2 = ? AND estado = 'aceptada') OR (id_usuario1 = ? AND id_usuario2 = ? AND estado = 'aceptada')";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiii", $idUsuarioOrigen, $amigoDestino, $amigoDestino, $idUsuarioOrigen);
        $stmt->execute();
        $stmt->store_result();

        $estado = '';

        if ($stmt->num_rows > 0) {
            // Obtener el ID del archivo correspondiente
            $queryArchivo = "SELECT id_archivo FROM archivos WHERE id_archivo = ?";
            $stmtArchivo = $this->db->prepare($queryArchivo);
            $stmtArchivo->bind_param("i", $idArchivo);
            $stmtArchivo->execute();
            $stmtArchivo->store_result();

            if ($stmtArchivo->num_rows > 0) {
                // Insertar la fotografía compartida en la tabla fotos_compartidas
                $queryInsert = "INSERT INTO fotos_compartidas (id_usuario_origen, usuario_destino, id_archivo, nombre_archivo, descripcion) 
                                SELECT ?, ?, id_archivo, nombre_archivo, descripcion 
                                FROM archivos 
                                WHERE id_archivo = ?";

                $stmtInsert = $this->db->prepare($queryInsert);
                $stmtInsert->bind_param("iii", $idUsuarioOrigen, $amigoDestino, $idArchivo);
                $stmtInsert->execute();
            } else {
                echo "El archivo no existe.";
            }
        } else {
            echo "La amistad entre los usuarios no está aceptada.";
        }

        header("Location: ./galeria.php");
    }

    function obtenerIdUsuarioPorNombre($nombreUsuarioDestino)
    {
        $query = "SELECT user FROM users WHERE User = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $nombreUsuarioDestino);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();


    }

    //Inversa de la anterior
    function obtenerNombreUsuarioPorId($idUsuario) {
        $query = "SELECT User FROM users WHERE IDLogin = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $stmt->bind_result($nombre);
        
       
        $stmt->fetch();
       
        $stmt->close();
        
        return $nombre;
    }



    public function rechazarSolicitudAmistad($idAmistad)
    {


        $queryRechazarAmistad = "UPDATE amigos SET estado = 'rechazada' WHERE id_amistad = ?";
        $stmtRechazarAmistad = $this->db->prepare($queryRechazarAmistad);
        $stmtRechazarAmistad->bind_param("i", $idAmistad);
        $stmtRechazarAmistad->execute();
        if ($stmtRechazarAmistad->affected_rows > 0) {
            echo "Solicitud de amistad rechazada correctamente.";
            header("Location: /amigos.php");
        } else {
            echo "No se pudo rechazar la solicitud de amistad.";
        }
    }

    public function actualizarActividadUsuario($idUsuario)
    {
        $horaActual = time();
        $consulta = $this->db->prepare("UPDATE users SET last_activity = ? WHERE IDLogin = ?");
        $consulta->bind_param("ii", $horaActual, $idUsuario);
        $consulta->execute();
    }

    public function usuarioEstaConectado($idUsuario)
    {
        $horaActual = time();
        $tiempoInactivo = $horaActual - (2 * 60); // 2 minutos de inactividad para cambiar el estado
        $consulta = $this->db->prepare("SELECT COUNT(*) as conectado FROM users WHERE IDLogin = ? AND last_activity > ?");
        $consulta->bind_param("ii", $idUsuario, $tiempoInactivo);
        $consulta->execute();
        $resultado = $consulta->get_result();
        $fila = $resultado->fetch_assoc();
        return $fila['conectado'] > 0;
    }
    // Esta funcion nos sirve para visitar las galerias de nuetros amigos
    public function obtenerArchivosDeAmigo($idAmigo)
    {
        $query = "SELECT archivos.nombre_archivo, archivos.descripcion
                  FROM archivos
                  WHERE archivos.id_usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idAmigo);
        $stmt->execute();
        $result = $stmt->get_result();

        $images = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $images[] = $row;
            }
        }

        $stmt->close();

        return $images;
    }


    function logoutUser()
    {

        // Actualizar la última actividad antes de cerrar sesión
        if (isset($_SESSION['IDLogin'])) {
            $this->actualizarActividadUsuario($_SESSION['IDLogin']);
        }
        // Cerrar sesión del usuario
        session_unset();
        session_destroy();



    }
    public function closeConnection()
    {
        $this->db->close();
    }


}
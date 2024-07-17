<?php


// Comprobamos si la variable de sesión "lenguaje" es nula y, en caso afirmativo, la establecemos como "es"
if ($_SESSION["lenguaje"] == null) {
    $_SESSION["lenguaje"] = "es";
}

// Cargamos el archivo XML del idioma seleccionado en la variable $lenguaje
$lenguaje = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . "/backoffice/locale/" . $_SESSION['lenguaje'] . ".xml");
<?php
// Establecemos la conexión con la base de datos
$con = mysqli_connect("localhost", "adminfoto", "cesur", "bd");

mysqli_query($con, "set names 'UTF8'");

// Seleccionamos la base de datos a utilizar
$base = mysqli_select_db($con, "bd");

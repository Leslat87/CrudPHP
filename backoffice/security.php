<?php
session_start();

// Verifica si la sesión está iniciada y si la variable 'logged' está establecida
if (isset($_SESSION['logged']) && $_SESSION['logged'] == 1) {
    
} else {
    
    header("Location: /log.php");
    exit(); 
}

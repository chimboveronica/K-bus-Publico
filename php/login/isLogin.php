<?php

// Comprobar si la sesión ya fue iniciada
if (!isset($_SESSION)) {
    session_start();
} else {
    $rutaPrincipal = "index.php";

// Comprobar si esta logueado
    if (!isset($_SESSION["EMPRESAKBUS".$site_city]) ||
            !isset($_SESSION["USUARIOKBUS".$site_city]) ||
            !isset($_SESSION["SESIONKBUS".$site_city])) {
        header("Location: $rutaPrincipal");
        exit();
    }
}


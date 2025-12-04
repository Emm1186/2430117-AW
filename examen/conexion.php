<?php
$conexion = new mysqli("localhost", "sectoruser", "TuPasswordFuerteAqui!", "sector404");
if ($conexion->connect_error) {
    die("Error: " . $conexion->connect_error);
}
?>
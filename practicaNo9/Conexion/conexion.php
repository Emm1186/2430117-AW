<?php
/**
 * CONEXIÓN A LA BASE DE DATOS
 * Sector 404 - Sistema Médico
 */

// Iniciar sesión
session_start();

// Datos de conexión
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "sector404";

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer charset UTF-8
$conexion->set_charset("utf8mb4");

// Función para limpiar datos de entrada (prevenir inyección SQL)
function limpiar_dato($dato) {
    global $conexion;
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $conexion->real_escape_string($dato);
}

// Función para verificar si hay sesión activa
function sesion_activa() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}
?>
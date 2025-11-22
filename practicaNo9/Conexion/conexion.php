<?php
/**
 * CONEXIÓN A LA BASE DE DATOS
 * Sector 404 - Sistema Médico
 */

// Iniciar sesión
session_start();

// Datos de conexión
$servidor = "localhost";
$usuario = "sectoruser";
$password = "TuPasswordFuerteAqui!";
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

// Función para verificar acceso por rol
function verificar_acceso($roles_permitidos = []) {
    // 1. Verificar si hay sesión
    if (!sesion_activa()) {
        header('Location: ../Entrada/login.php');
        exit;
    }

    // 2. Si no se especifican roles, solo se requiere login (acceso general)
    if (empty($roles_permitidos)) {
        return true;
    }

    // 3. Verificar si el rol del usuario está permitido
    // (Asumimos que $_SESSION['rol'] se estableció en el login)
    if (!in_array($_SESSION['rol'], $roles_permitidos)) {
        // Acceso denegado: redirigir a una página de error o al dashboard con mensaje
        echo "<script>
            alert('Acceso denegado. No tienes permisos para ver esta página.');
            window.location.href = '../dashboard.php';
        </script>";
        exit;
    }

    return true;
}
?>
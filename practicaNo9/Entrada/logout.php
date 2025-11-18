<?php
/**
 * LOGOUT - SECTOR 404
 * Cerrar sesión y destruir variables
 */

session_start();
session_unset();
session_destroy();

// Redirigir al login
header('Location: login.php');
exit;
?>
<?php
/**
 * LOGIN - SECTOR 404
 * Sistema de inicio de sesión con base de datos
 */

require_once '../Conexion/conexion.php';

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Obtener datos del formulario
    // Usamos isset para evitar avisos si la variable no viene
    $correo = isset($_POST['correo']) ? limpiar_dato($_POST['correo']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    
    // Validar que no estén vacíos
    if (empty($correo) || empty($contrasena)) {
        $mensaje = 'Por favor completa todos los campos';
        $tipo_mensaje = 'error';
    } else {
        
        // Buscar usuario en la base de datos
        $sql = "SELECT IdUsuario, Correo, Contrasena, Nombre, Rol, Activo 
                FROM usuarios 
                WHERE Correo = ? 
                LIMIT 1";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        // Verificar si existe el usuario
        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();
            
            // Verificar si está activo (0 = inactivo)
            if ($usuario['Activo'] == 0) {
                $mensaje = 'Tu cuenta está inactiva. Contacta al administrador';
                $tipo_mensaje = 'error';
            } 
            // Verificar contraseña (comparación en texto plano)
            else if ($contrasena === $usuario['Contrasena']) {
                
                // ¡Login exitoso! Crear sesión
                $_SESSION['usuario_id'] = $usuario['IdUsuario'];
                $_SESSION['correo'] = $usuario['Correo'];
                $_SESSION['nombre'] = $usuario['Nombre'];
                $_SESSION['rol'] = $usuario['Rol'];
                
                // Actualizar último acceso
                $sql_update = "UPDATE usuarios SET UltimoAcceso = NOW() WHERE IdUsuario = ?";
                $stmt_update = $conexion->prepare($sql_update);
                $stmt_update->bind_param("i", $usuario['IdUsuario']);
                $stmt_update->execute();
                
                // Registrar en bitácora
                $sql_bitacora = "INSERT INTO bitacoraacceso (IdUsuario, AccionRealizada, Modulo) 
                                 VALUES (?, 'Inicio de sesión', 'Login')";
                $stmt_bitacora = $conexion->prepare($sql_bitacora);
                $stmt_bitacora->bind_param("i", $usuario['IdUsuario']);
                $stmt_bitacora->execute();
                
                // Redirigir al dashboard (archivo en la carpeta padre)
                header('Location: ../dashboard.php');
                exit;
                
            } else {
                $mensaje = 'Contraseña incorrecta';
                $tipo_mensaje = 'error';
            }
            
        } else {
            $mensaje = 'No existe una cuenta con ese correo';
            $tipo_mensaje = 'error';
        }
        
        $stmt->close();
    }
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sector 404</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <!-- Contenedor con imagen de fondo -->
    <div class="contenedor-principal" style="background-image: url('../img/fondo-login.jpg');">
        
        <!-- Tarjeta de login -->
        <div class="tarjeta-auth">
            <h1>Sector 404</h1>
            
            <!-- Mensaje de error/éxito -->
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?> mostrar">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulario de login -->
            <form method="POST" action="" class="formulario">
                
                <div class="grupo-input">
                    <label for="correo">Correo electrónico</label>
                    <input 
                        type="email" 
                        id="correo" 
                        name="correo" 
                        placeholder="..."
                        value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>"
                        required
                        autocomplete="email">
                </div>
                
                <div class="grupo-input">
                    <label for="contrasena">Contraseña</label>
                    <input 
                        type="password" 
                        id="contrasena" 
                        name="contrasena" 
                        placeholder="..."
                        required
                        autocomplete="current-password">
                </div>
                
                <button type="submit" class="boton-principal">Iniciar sesión</button>
                
            </form>
            
            <!-- Enlace a registro -->
            <p class="enlace-cambio">
                No tienes cuenta? <a href="registro.php">Crear cuenta</a>
            </p>
        </div>
        
    </div>
</body>
</html>
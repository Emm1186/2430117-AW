<?php
/**
 * REGISTRO - SECTOR 404
 * Sistema de creación de cuentas con base de datos
 */

require_once '../Conexion/conexion.php';

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Obtener datos del formulario
    $nombre = limpiar_dato($_POST['nombre']);
    $correo = limpiar_dato($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    
    // Validar que no estén vacíos
    if (empty($correo) || empty($contrasena)) {
        $mensaje = 'El correo y la contraseña son obligatorios';
        $tipo_mensaje = 'error';
    } 
    // Validar longitud de contraseña
    else if (strlen($contrasena) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres';
        $tipo_mensaje = 'error';
    }
    // Validar formato de correo
    else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El formato del correo no es válido';
        $tipo_mensaje = 'error';
    }
    else {
        
        // Verificar si el correo ya existe
        $sql_verificar = "SELECT IdUsuario FROM Usuarios WHERE Correo = ? LIMIT 1";
        $stmt_verificar = $conexion->prepare($sql_verificar);
        $stmt_verificar->bind_param("s", $correo);
        $stmt_verificar->execute();
        $resultado_verificar = $stmt_verificar->get_result();
        
        if ($resultado_verificar->num_rows > 0) {
            $mensaje = 'Ya existe una cuenta con ese correo electrónico';
            $tipo_mensaje = 'error';
        } else {
            
                // Almacenar contraseña en texto plano (NO RECOMENDADO)
                // Atención: esto guarda la contraseña tal cual en la base de datos.
            
                // Insertar nuevo usuario
                $sql = "INSERT INTO Usuarios (Correo, Contrasena, Nombre, Rol, Activo, FechaCreacion) 
                    VALUES (?, ?, ?, 'Recepcionista', 1, NOW())";
            
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("sss", $correo, $contrasena, $nombre);
            
            if ($stmt->execute()) {
                $mensaje = 'Cuenta creada exitosamente. Redirigiendo...';
                $tipo_mensaje = 'exito';
                
                // Redirigir al login después de 2 segundos
                header("refresh:2;url=login.php");
            } else {
                $mensaje = 'Error al crear la cuenta. Intenta de nuevo';
                $tipo_mensaje = 'error';
            }
            
            $stmt->close();
        }
        
        $stmt_verificar->close();
    }
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Sector 404</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <!-- Contenedor con imagen de fondo -->
    <div class="contenedor-principal" style="background-image: url('../img/fondo-registro.jpg');">
        
        <!-- Tarjeta de registro -->
        <div class="tarjeta-auth">
            <h1>Sector 404</h1>
            
            <!-- Mensaje de error/éxito -->
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?> mostrar">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulario de registro -->
            <form method="POST" action="" class="formulario">
                
                <div class="grupo-input">
                    <label for="nombre">Nombre</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        placeholder="Tu nombre completo"
                        value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                        autocomplete="name">
                </div>
                
                <div class="grupo-input">
                    <label for="correo">Correo electrónico</label>
                    <input 
                        type="email" 
                        id="correo" 
                        name="correo" 
                        placeholder="elAguaw123@gmail.com"
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
                        placeholder="Mínimo 6 caracteres"
                        required
                        autocomplete="new-password">
                </div>
                
                <button type="submit" class="boton-principal">Registrar</button>
                
            </form>
            
            <!-- Enlace a login -->
            <p class="enlace-cambio">
                Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
            </p>
        </div>
        
    </div>
</body>
</html>
<?php

require_once 'Conexion/conexion.php';

// Verificar permisos (Solo Admin y Secretaria pueden gestionar pacientes)
verificar_acceso(['Admin', 'Secretaria']);

if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';

$mensaje = '';
$tipo_mensaje = '';
$paciente_editar = null;

// PROCESAR GUARDADO DE PACIENTE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_paciente'])) {
    $id_paciente = isset($_POST['id_paciente']) ? intval($_POST['id_paciente']) : 0;
    $nombre = limpiar_dato($_POST['nombre_completo']);
    $curp = limpiar_dato($_POST['curp']);
    $fecha_nac = $_POST['fecha_nacimiento'];
    $sexo = limpiar_dato($_POST['sexo']);
    $telefono = limpiar_dato($_POST['telefono']);
    $correo = limpiar_dato($_POST['correo']);
    $direccion = limpiar_dato($_POST['direccion']);
    $contacto_emergencia = limpiar_dato($_POST['contacto_emergencia']);
    $tel_emergencia = limpiar_dato($_POST['telefono_emergencia']);
    $alergias = limpiar_dato($_POST['alergias']);
    $antecedentes = limpiar_dato($_POST['antecedentes']);
    $crear_cuenta = isset($_POST['crear_cuenta']) ? 1 : 0;
    
    if (empty($nombre) || empty($telefono)) {
        $mensaje = 'El nombre y teléfono son obligatorios';
        $tipo_mensaje = 'warning';
    } else {
        if ($id_paciente > 0) {
            // ACTUALIZAR
            $sql = "UPDATE controlpacientes SET 
                    NombreCompleto = ?, CURP = ?, FechaNacimiento = ?, Sexo = ?,
                    Telefono = ?, CorreoElectronico = ?, Direccion = ?,
                    ContactoEmergencia = ?, TelefonoEmergencia = ?, Alergias = ?, AntecedentesMedicos = ?
                    WHERE IdPaciente = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssssssssssi", $nombre, $curp, $fecha_nac, $sexo, $telefono, $correo, 
                             $direccion, $contacto_emergencia, $tel_emergencia, $alergias, $antecedentes, $id_paciente);
            
            if ($stmt->execute()) {
                $mensaje = 'Paciente actualizado correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar: ' . $stmt->error;
                $tipo_mensaje = 'danger';
            }
            $stmt->close();
        } else {
            // CREAR NUEVO
            $sql = "INSERT INTO controlpacientes 
                    (NombreCompleto, CURP, FechaNacimiento, Sexo, Telefono, CorreoElectronico, Direccion,
                     ContactoEmergencia, TelefonoEmergencia, Alergias, AntecedentesMedicos, Estatus)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssssssssss", $nombre, $curp, $fecha_nac, $sexo, $telefono, $correo,
                             $direccion, $contacto_emergencia, $tel_emergencia, $alergias, $antecedentes);
            
            if ($stmt->execute()) {
                $nuevo_id_paciente = $conexion->insert_id;
                $mensaje = 'Paciente registrado correctamente';
                $tipo_mensaje = 'success';
                
                // Si se marcó crear cuenta de usuario
                if ($crear_cuenta && !empty($correo)) {
                    // Generar contraseña temporal
                    $password_temp = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                    $password_hash = password_hash($password_temp, PASSWORD_DEFAULT);
                    
                    // Crear usuario con rol "Paciente" por defecto
                    $sql_user = "INSERT INTO usuarios (Correo, Contrasena, Nombre, Rol, IdPaciente, Activo) 
                                VALUES (?, ?, ?, 'Paciente', ?, 1)";
                    $stmt_user = $conexion->prepare($sql_user);
                    $stmt_user->bind_param("sssi", $correo, $password_hash, $nombre, $nuevo_id_paciente);
                    
                    if ($stmt_user->execute()) {
                        $mensaje .= '<br>✅ Cuenta de usuario creada. Contraseña temporal: <strong>' . $password_temp . '</strong>';
                    } else {
                        $mensaje .= '<br>⚠️ Paciente creado pero hubo un error al crear la cuenta de usuario';
                    }
                    $stmt_user->close();
                }
            } else {
                $mensaje = 'Error al registrar: ' . $stmt->error;
                $tipo_mensaje = 'danger';
            }
            $stmt->close();
        }
    }
}

// ELIMINAR/DESACTIVAR PACIENTE (solo Admin)
if (isset($_GET['eliminar']) && $usuario_rol == 'Admin') {
    $id_eliminar = intval($_GET['eliminar']);
    $sql = "UPDATE controlpacientes SET Estatus = 0 WHERE IdPaciente = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_eliminar);
    
    if ($stmt->execute()) {
        $mensaje = 'Paciente desactivado correctamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al desactivar';
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// CARGAR PARA EDITAR
if (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $sql = "SELECT * FROM controlpacientes WHERE IdPaciente = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $paciente_editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// OBTENER LISTA DE PACIENTES
$sql_pacientes = "SELECT * FROM controlpacientes WHERE Estatus = 1 ORDER BY NombreCompleto";
$resultado_pacientes = $conexion->query($sql_pacientes);

// Convertir a array para JavaScript
$pacientes_array = [];
if ($resultado_pacientes && $resultado_pacientes->num_rows > 0) {
    $resultado_pacientes->data_seek(0);
    while ($row = $resultado_pacientes->fetch_assoc()) {
        $pacientes_array[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Pacientes - Sector 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Header -->
    <header class="encabezado">
        <div class="marca">🏥 Sector 404</div>
        <div class="espacio"></div>
        <div class="usuario">
            <?php if (!empty($usuario_nombre)): ?>
                👤 <?php echo htmlspecialchars($usuario_nombre); ?>
                <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($usuario_rol); ?></span>
            <?php endif; ?>
        </div>
        <?php if (!empty($usuario_nombre)): ?>
            <a href="Entrada/logout.php" class="btn btn-sm btn-outline-danger">Cerrar sesión</a>
        <?php endif; ?>
    </header>

    <div class="contenedor">
        <!-- Sidebar -->
        <nav class="barra-lateral">
            <div class="titulo">📋 Menú</div>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? ' activo' : ''; ?>" href="dashboard.php">🏠 Inicio</a>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'pacientes.php') ? ' activo' : ''; ?>" href="pacientes.php">👥 Control de pacientes</a>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'controlAgenda.php') ? ' activo' : ''; ?>" href="controlAgenda.php">📅 Control de agenda</a>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'medicos.php') ? ' activo' : ''; ?>" href="medicos.php">👨‍⚕️ Control de médicos</a>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'expedientes.php') ? ' activo' : ''; ?>" href="expedientes.php">📋 Expedientes médicos</a>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'especialidades.php') ? ' activo' : ''; ?>" href="especialidades.php">🩺 Especialidades médicas</a>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'tarifas.php') ? ' activo' : ''; ?>" href="tarifas.php">💰 Gestor de tarifas</a>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'pagos.php') ? ' activo' : ''; ?>" href="pagos.php">💳 Pagos</a>
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'reportes.php') ? ' activo' : ''; ?>" href="reportes.php">📊 Reportes</a>
            <hr style="margin: 15px 0; border-color: #ddd;">
            <div class="titulo">⚙️ Administración</div>
            <a class="enlace" href="bitacoras.php">📝 Bitácoras</a>
        </nav>

        <!-- Main Content -->
        <main class="principal">
            
            <div class="header-seccion">
                <h2>👥 Control de Pacientes</h2>
                <p class="text-muted">Gestión completa del registro de pacientes</p>
            </div>

            <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">🔍</span>
                        <input type="text" class="form-control" id="buscarPaciente" placeholder="Buscar por nombre, CURP o teléfono...">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <?php if ($paciente_editar): ?>
                        <a href="pacientes.php" class="btn btn-secondary me-2">❌ Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Formulario de Registro/Edición -->
            <div class="tarjeta mb-4">
                <h5 class="mb-3">
                    <?php echo $paciente_editar ? '✏️ Editar Paciente' : '➕ Registrar Nuevo Paciente'; ?>
                </h5>
                
                <form method="POST" action="">
                    <input type="hidden" name="id_paciente" value="<?php echo $paciente_editar ? $paciente_editar['IdPaciente'] : ''; ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" name="nombre_completo" 
                                   value="<?php echo $paciente_editar ? htmlspecialchars($paciente_editar['NombreCompleto']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">CURP</label>
                            <input type="text" class="form-control" name="curp" maxlength="18"
                                   value="<?php echo $paciente_editar ? htmlspecialchars($paciente_editar['CURP']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" name="fecha_nacimiento"
                                   value="<?php echo $paciente_editar ? $paciente_editar['FechaNacimiento'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Sexo</label>
                            <select class="form-select" name="sexo">
                                <option value="">-</option>
                                <option value="M" <?php echo ($paciente_editar && $paciente_editar['Sexo'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                <option value="F" <?php echo ($paciente_editar && $paciente_editar['Sexo'] == 'F') ? 'selected' : ''; ?>>Femenino</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Teléfono *</label>
                            <input type="tel" class="form-control" name="telefono"
                                   value="<?php echo $paciente_editar ? htmlspecialchars($paciente_editar['Telefono']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo"
                                   value="<?php echo $paciente_editar ? htmlspecialchars($paciente_editar['CorreoElectronico']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <?php if (!$paciente_editar): ?>
                            <label class="form-label d-block">&nbsp;</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="crear_cuenta" id="crearCuenta">
                                <label class="form-check-label" for="crearCuenta">
                                    Crear cuenta de usuario
                                </label>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion"
                                   value="<?php echo $paciente_editar ? htmlspecialchars($paciente_editar['Direccion']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Contacto de Emergencia</label>
                            <input type="text" class="form-control" name="contacto_emergencia"
                                   value="<?php echo $paciente_editar ? htmlspecialchars($paciente_editar['ContactoEmergencia']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Teléfono de Emergencia</label>
                            <input type="tel" class="form-control" name="telefono_emergencia"
                                   value="<?php echo $paciente_editar ? htmlspecialchars($paciente_editar['TelefonoEmergencia']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Alergias</label>
                            <textarea class="form-control" name="alergias" rows="2"><?php echo $paciente_editar ? htmlspecialchars($paciente_editar['Alergias']) : ''; ?></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Antecedentes Médicos</label>
                            <textarea class="form-control" name="antecedentes" rows="2"><?php echo $paciente_editar ? htmlspecialchars($paciente_editar['AntecedentesMedicos']) : ''; ?></textarea>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" name="guardar_paciente" class="btn btn-success">
                                <?php echo $paciente_editar ? '💾 Actualizar Paciente' : '➕ Registrar Paciente'; ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla de Pacientes -->
            <div class="tarjeta">
                <h5 class="mb-3">📋 Lista de Pacientes</h5>
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaPacientes">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>CURP</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Edad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pacientes_array)): ?>
                                <?php foreach ($pacientes_array as $pac): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($pac['NombreCompleto']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($pac['CURP']); ?></td>
                                    <td><?php echo htmlspecialchars($pac['Telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($pac['CorreoElectronico']); ?></td>
                                    <td>
                                        <?php 
                                        if ($pac['FechaNacimiento']) {
                                            $edad = date_diff(date_create($pac['FechaNacimiento']), date_create('today'))->y;
                                            echo $edad . ' años';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="verPaciente(<?php echo $pac['IdPaciente']; ?>)">
                                            👁️ Ver
                                        </button>
                                        <a href="pacientes.php?editar=<?php echo $pac['IdPaciente']; ?>" class="btn btn-sm btn-warning">
                                            ✏️ Editar
                                        </a>
                                        <?php if ($usuario_rol == 'Admin'): ?>
                                        <a href="pacientes.php?eliminar=<?php echo $pac['IdPaciente']; ?>" 
                                           onclick="return confirm('¿Desactivar este paciente?');"
                                           class="btn btn-sm btn-danger">
                                            🗑️
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay pacientes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal Ver Paciente -->
    <div class="modal fade" id="modalVerPaciente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoPaciente">
                    <!-- Se llena dinámicamente con JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Datos para JavaScript -->
    <script>
        const pacientesData = <?php echo json_encode($pacientes_array); ?>;
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/pacientes.js"></script>
</body>
</html>
<?php $conexion->close(); ?>

<?php
/**
 * CONTROL DE MÉDICOS - SECTOR 404
 * CRUD completo de médicos
 */

require_once 'Conexion/conexion.php';

// Verificar permisos (Solo Admin y Secretaria)
verificar_acceso(['Admin', 'Secretaria']);

// Verificar que esté logueado
if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

// Obtener información del usuario (forma sencilla y explícita)
$usuario_nombre = '';
if (isset($_SESSION['nombre']) && !empty($_SESSION['nombre'])) {
    $usuario_nombre = $_SESSION['nombre'];
} else if (isset($_SESSION['correo'])) {
    $usuario_nombre = $_SESSION['correo'];
}
$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';

// Control de acceso por roles: permitir solo ciertos roles para gestionar médicos.
// Roles permitidos: 'Admin' y 'Secretaria'. Si el usuario no tiene permiso,
// lo redirigimos al dashboard con un indicador para mostrar un aviso.
$roles_permitidos = array('Admin', 'Secretaria');
if (!in_array($usuario_rol, $roles_permitidos)) {
    header('Location: dashboard.php?noaccess=1');
    exit;
}


$mensaje = '';
$tipo_mensaje = '';
$medico_editar = null;
// Habilitar o deshabilitar ejecución real del CRUD (false = solo interfaz)
// Habilitar el CRUD para pruebas (solo los módulos solicitados)
$ENABLE_CRUD = true; // Cambiado a true para permitir crear/editar/eliminar médicos

if (!$ENABLE_CRUD) {
    $mensaje = 'Modo demo: las operaciones CRUD están deshabilitadas en esta versión. La interfaz está disponible para diseño y pruebas visuales.';
    $tipo_mensaje = 'warning';
}

// ========================================
// PROCESAR ACCIONES (Crear, Editar, Eliminar)
// ========================================

// ELIMINAR MÉDICO (pendiente si $ENABLE_CRUD == false)
if ($ENABLE_CRUD && isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    
    $sql_eliminar = "UPDATE controlmedico SET Estatus = 0 WHERE IdMedico = ?";
    $stmt = $conexion->prepare($sql_eliminar);
    $stmt->bind_param("i", $id_eliminar);
    
    if ($stmt->execute()) {
        $mensaje = 'Médico eliminado correctamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al eliminar el médico';
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// CARGAR DATOS PARA EDITAR (pendiente si $ENABLE_CRUD == false)
if ($ENABLE_CRUD && isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    
    $sql_editar = "SELECT * FROM controlmedico WHERE IdMedico = ?";
    $stmt = $conexion->prepare($sql_editar);
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $medico_editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// GUARDAR (Crear o Actualizar) - pendiente si $ENABLE_CRUD == false
if ($ENABLE_CRUD && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    
    $id_medico = isset($_POST['id_medico']) ? intval($_POST['id_medico']) : 0;
    $nombre = limpiar_dato($_POST['nombre']);
    $cedula = limpiar_dato($_POST['cedula']);
    $especialidad_id = intval($_POST['especialidad_id']);
    $telefono = limpiar_dato($_POST['telefono']);
    $correo = limpiar_dato($_POST['correo']);
    $horario = limpiar_dato($_POST['horario']);
    $estatus = isset($_POST['estatus']) ? 1 : 0;
    
    // Validar campos obligatorios
    if (empty($nombre) || empty($cedula) || $especialidad_id == 0) {
        $mensaje = 'Completa todos los campos obligatorios';
        $tipo_mensaje = 'warning';
    } else {
        
        if ($id_medico > 0) {
            // ACTUALIZAR
            $sql = "UPDATE controlmedico SET 
                    NombreCompleto = ?, CedulaProfesional = ?, EspecialidadId = ?,
                    Telefono = ?, CorreoElectronico = ?, HorarioAtencion = ?, Estatus = ?
                    WHERE IdMedico = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssisssii", $nombre, $cedula, $especialidad_id, $telefono, $correo, $horario, $estatus, $id_medico);
            
        } else {
            // CREAR NUEVO
            $sql = "INSERT INTO controlmedico (NombreCompleto, CedulaProfesional, EspecialidadId, Telefono, CorreoElectronico, HorarioAtencion, Estatus)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssisssi", $nombre, $cedula, $especialidad_id, $telefono, $correo, $horario, $estatus);
        }
        
        if ($stmt->execute()) {
            $mensaje = $id_medico > 0 ? 'Médico actualizado correctamente' : 'Médico registrado correctamente';
            $tipo_mensaje = 'success';
            $medico_editar = null; // Limpiar formulario
        } else {
            $mensaje = 'Error al guardar: ' . $stmt->error;
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

// ========================================
// OBTENER MÉDICOS Y ESPECIALIDADES
// ========================================

$sql_medicos = "SELECT m.*, e.NombreEspecialidad 
                FROM controlmedico m
                INNER JOIN especialidades e ON m.EspecialidadId = e.IdEspecialidad
                WHERE m.Estatus = 1
                ORDER BY m.IdMedico DESC";
$resultado_medicos = $conexion->query($sql_medicos);

$sql_especialidades = "SELECT * FROM especialidades ORDER BY NombreEspecialidad";
$resultado_especialidades = $conexion->query($sql_especialidades);

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Médicos - Sector 404</title>
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
    <nav class="barra-lateral">
        <div class="titulo"> Menú</div>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? ' activo' : ''; ?>" href="dashboard.php">🏠 Inicio</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'pacientes.php') ? ' activo' : ''; ?>" href="pacientes.php">👥 Control de pacientes</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'controlAgenda.php') ? ' activo' : ''; ?>" href="controlcontrolAgenda.php">📅 Control de agenda</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'medicos.php') ? ' activo' : ''; ?>" href="medicos.php">👨‍⚕️ Control de médicos</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'especialidades.php') ? ' activo' : ''; ?>" href="especialidades.php">🩺 Especialidades médicas</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'tarifas.php') ? ' activo' : ''; ?>" href="tarifas.php">💰 Gestor de tarifas</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'pagos.php') ? ' activo' : ''; ?>" href="pagos.php">💳 Pagos</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'reportes.php') ? ' activo' : ''; ?>" href="reportes.php">📊 Reportes</a>
        <hr style="margin: 15px 0; border-color: #ddd;">
        <div class="titulo">⚙️ Administración</div>
        <a class="enlace" href="bitacoras.php">📝 Bitácoras</a>
    </nav>

        <!-- Contenido principal -->
        <main class="principal">
            
            <div class="header-seccion">
                <h2>👨‍⚕️ Control de Médicos</h2>
                <p class="text-muted">Gestión completa del personal médico</p>
            </div>

            <!-- Mensajes -->
            <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Formulario de Registro/Edición -->
            <div class="tarjeta mb-4">
                <h5 class="mb-3">
                    <?php echo $medico_editar ? '✏️ Editar Médico' : '➕ Registrar Nuevo Médico'; ?>
                </h5>
                
                <form method="POST" action="" id="formMedico">
                    <input type="hidden" name="id_medico" value="<?php echo $medico_editar ? $medico_editar['IdMedico'] : ''; ?>">
                    
                    <div class="row g-3">
                        
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo $medico_editar ? htmlspecialchars($medico_editar['NombreCompleto']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="cedula" class="form-label">Cédula Profesional *</label>
                            <input type="text" class="form-control" id="cedula" name="cedula" 
                                   value="<?php echo $medico_editar ? htmlspecialchars($medico_editar['CedulaProfesional']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="especialidad_id" class="form-label">Especialidad *</label>
                            <select class="form-select" id="especialidad_id" name="especialidad_id" required>
                                <option value="">Seleccionar...</option>
                                <?php 
                                $resultado_especialidades->data_seek(0);
                                while ($esp = $resultado_especialidades->fetch_assoc()): 
                                    $selected = ($medico_editar && $medico_editar['EspecialidadId'] == $esp['IdEspecialidad']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $esp['IdEspecialidad']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($esp['NombreEspecialidad']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?php echo $medico_editar ? htmlspecialchars($medico_editar['Telefono']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   value="<?php echo $medico_editar ? htmlspecialchars($medico_editar['CorreoElectronico']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="horario" class="form-label">Horario de Atención</label>
                            <input type="text" class="form-control" id="horario" name="horario" 
                                   placeholder="Ej: 9:00-17:00" 
                                   value="<?php echo $medico_editar ? htmlspecialchars($medico_editar['HorarioAtencion']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label d-block">Estatus</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="estatus" name="estatus" 
                                       <?php echo (!$medico_editar || $medico_editar['Estatus'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="estatus">Activo</label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" name="guardar" class="btn btn-success">
                                <?php echo $medico_editar ? '💾 Actualizar' : '➕ Registrar'; ?>
                            </button>
                            
                            <?php if ($medico_editar): ?>
                            <a href="medicos.php" class="btn btn-secondary">❌ Cancelar</a>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </form>
            </div>

            <!-- Tabla de Médicos -->
            <div class="tarjeta">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">📋 Lista de Médicos</h5>
                    <input type="text" class="form-control buscar-tabla" placeholder="🔍 Buscar médico..." style="max-width: 300px;">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaMedicos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Cédula</th>
                                <th>Especialidad</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Horario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado_medicos && $resultado_medicos->num_rows > 0): ?>
                                <?php while ($medico = $resultado_medicos->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $medico['IdMedico']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($medico['NombreCompleto']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($medico['CedulaProfesional']); ?></td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($medico['NombreEspecialidad']); ?></span></td>
                                    <td><?php echo htmlspecialchars($medico['Telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($medico['CorreoElectronico']); ?></td>
                                    <td><?php echo htmlspecialchars($medico['HorarioAtencion']); ?></td>
                                    <td>
                                        <a href="medicos.php?editar=<?php echo $medico['IdMedico']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                            Editar
                                        </a>
                                        <a href="medicos.php?eliminar=<?php echo $medico['IdMedico']; ?>" 
                                           onclick="return confirm('¿Confirmar eliminación del médico?');"
                                           class="btn btn-sm btn-danger" 
                                           title="Eliminar">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No hay médicos registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <footer style="display:none;"><!-- Footer placeholder if needed --></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/medicos.js"></script>
</body>
</html>

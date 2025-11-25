<?php

require_once 'Conexion/conexion.php';

// Verificar permisos (Solo Admin y Secretaria)
verificar_acceso(['Admin', 'Secretaria']);

if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';

$mensaje = '';
$tipo_mensaje = '';

// PROCESAR GUARDADO DE EXPEDIENTE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_expediente'])) {
    $id_paciente = intval($_POST['id_paciente']);
    $id_medico = intval($_POST['id_medico']);
    $sintomas = limpiar_dato($_POST['sintomas']);
    $diagnostico = limpiar_dato($_POST['diagnostico']);
    $tratamiento = limpiar_dato($_POST['tratamiento']);
    $receta = limpiar_dato($_POST['receta_medica']);
    $notas = limpiar_dato($_POST['notas_adicionales']);
    $proxima_cita = !empty($_POST['proxima_cita']) ? $_POST['proxima_cita'] : NULL;
    
    if ($id_paciente == 0 || $id_medico == 0) {
        $mensaje = 'Debe seleccionar paciente y médico';
        $tipo_mensaje = 'warning';
    } else {
        $sql = "INSERT INTO expedienteclinico 
                (IdPaciente, IdMedico, Sintomas, Diagnostico, Tratamiento, RecetaMedica, NotasAdicionales, ProximaCita) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iissssss", $id_paciente, $id_medico, $sintomas, $diagnostico, $tratamiento, $receta, $notas, $proxima_cita);
        
        if ($stmt->execute()) {
            $mensaje = 'Expediente médico registrado correctamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al registrar expediente: ' . $stmt->error;
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

// PROCESAR ACTUALIZACIÓN DE EXPEDIENTE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_expediente'])) {
    $id_expediente = intval($_POST['id_expediente']);
    $sintomas = limpiar_dato($_POST['sintomas']);
    $diagnostico = limpiar_dato($_POST['diagnostico']);
    $tratamiento = limpiar_dato($_POST['tratamiento']);
    $receta = limpiar_dato($_POST['receta_medica']);
    $notas = limpiar_dato($_POST['notas_adicionales']);
    $proxima_cita = !empty($_POST['proxima_cita']) ? $_POST['proxima_cita'] : NULL;
    
    $sql = "UPDATE expedienteclinico 
            SET Sintomas = ?, Diagnostico = ?, Tratamiento = ?, RecetaMedica = ?, NotasAdicionales = ?, ProximaCita = ?
            WHERE IdExpediente = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssi", $sintomas, $diagnostico, $tratamiento, $receta, $notas, $proxima_cita, $id_expediente);
    
    if ($stmt->execute()) {
        $mensaje = 'Expediente actualizado correctamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al actualizar: ' . $stmt->error;
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// OBTENER LISTA DE EXPEDIENTES RECIENTES CON TODOS LOS DETALLES
$sql_expedientes = "SELECT e.*, 
                    p.NombreCompleto as Paciente, m.NombreCompleto as Medico
                    FROM expedienteclinico e
                    INNER JOIN controlpacientes p ON e.IdPaciente = p.IdPaciente
                    INNER JOIN controlmedico m ON e.IdMedico = m.IdMedico
                    ORDER BY e.FechaConsulta DESC
                    LIMIT 50";
$resultado_expedientes = $conexion->query($sql_expedientes);

// Convertir a array para usar en JavaScript
$expedientes_array = [];
if ($resultado_expedientes && $resultado_expedientes->num_rows > 0) {
    $resultado_expedientes->data_seek(0);
    while ($row = $resultado_expedientes->fetch_assoc()) {
        $expedientes_array[] = $row;
    }
}

// Listas para formularios
$pacientes = $conexion->query("SELECT IdPaciente, NombreCompleto FROM controlpacientes WHERE Estatus = 1 ORDER BY NombreCompleto");
$medicos = $conexion->query("SELECT IdMedico, NombreCompleto FROM controlmedico WHERE Estatus = 1 ORDER BY NombreCompleto");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expedientes Médicos - Sector 404</title>
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
                <h2>📋 Expedientes Médicos</h2>
                <p class="text-muted">Gestión del historial clínico de pacientes</p>
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
                        <input type="text" class="form-control" id="buscarPaciente" placeholder="Buscar por nombre de paciente...">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoExpediente">
                        ➕ Nuevo Expediente
                    </button>
                </div>
            </div>

            <div class="tarjeta">
                <h5 class="mb-3">Expedientes Recientes</h5>
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaExpedientes">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Síntomas</th>
                                <th>Diagnóstico</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($expedientes_array)): ?>
                                <?php foreach ($expedientes_array as $exp): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($exp['FechaConsulta'])); ?></td>
                                    <td><?php echo htmlspecialchars($exp['Paciente']); ?></td>
                                    <td><?php echo htmlspecialchars($exp['Medico']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($exp['Sintomas'], 0, 50)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars(substr($exp['Diagnostico'], 0, 50)) . '...'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="verExpediente(<?php echo $exp['IdExpediente']; ?>)">
                                            👁️ Ver
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarExpediente(<?php echo $exp['IdExpediente']; ?>)">
                                            ✏️ Editar
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay expedientes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal Nuevo Expediente -->
    <div class="modal fade" id="modalNuevoExpediente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Expediente Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Paciente *</label>
                                <select class="form-select" name="id_paciente" required>
                                    <option value="">Seleccionar...</option>
                                    <?php 
                                    $pacientes->data_seek(0);
                                    while ($p = $pacientes->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $p['IdPaciente']; ?>"><?php echo htmlspecialchars($p['NombreCompleto']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Médico *</label>
                                <select class="form-select" name="id_medico" required>
                                    <option value="">Seleccionar...</option>
                                    <?php 
                                    $medicos->data_seek(0);
                                    while ($m = $medicos->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $m['IdMedico']; ?>"><?php echo htmlspecialchars($m['NombreCompleto']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Síntomas</label>
                            <textarea class="form-control" name="sintomas" rows="2" placeholder="Descripción de síntomas reportados..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Diagnóstico</label>
                            <textarea class="form-control" name="diagnostico" rows="2" placeholder="Diagnóstico médico..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tratamiento</label>
                            <textarea class="form-control" name="tratamiento" rows="2" placeholder="Tratamiento prescrito..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Receta Médica</label>
                            <textarea class="form-control" name="receta_medica" rows="2" placeholder="Medicamentos y dosis..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notas Adicionales</label>
                            <textarea class="form-control" name="notas_adicionales" rows="2" placeholder="Observaciones adicionales..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Próxima Cita</label>
                            <input type="datetime-local" class="form-control" name="proxima_cita">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardar_expediente" class="btn btn-primary">Guardar Expediente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Expediente -->
    <div class="modal fade" id="modalVerExpediente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Expediente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoExpediente">
                    <!-- Se llena dinámicamente con JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Expediente -->
    <div class="modal fade" id="modalEditarExpediente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Expediente Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="formEditarExpediente">
                    <input type="hidden" name="id_expediente" id="edit_id_expediente">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Síntomas</label>
                            <textarea class="form-control" name="sintomas" id="edit_sintomas" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Diagnóstico</label>
                            <textarea class="form-control" name="diagnostico" id="edit_diagnostico" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tratamiento</label>
                            <textarea class="form-control" name="tratamiento" id="edit_tratamiento" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Receta Médica</label>
                            <textarea class="form-control" name="receta_medica" id="edit_receta" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notas Adicionales</label>
                            <textarea class="form-control" name="notas_adicionales" id="edit_notas" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Próxima Cita</label>
                            <input type="datetime-local" class="form-control" name="proxima_cita" id="edit_proxima_cita">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="actualizar_expediente" class="btn btn-primary">Actualizar Expediente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Datos de expedientes para JavaScript (sin necesidad de API) -->
    <script>
        const expedientesData = <?php echo json_encode($expedientes_array); ?>;
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/expedientes.js"></script>
</body>
</html>
<?php $conexion->close(); ?>

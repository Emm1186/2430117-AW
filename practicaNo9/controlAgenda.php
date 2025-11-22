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

// PROCESAR GUARDADO DE CITA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_cita'])) {
    $id_paciente = intval($_POST['id_paciente']);
    $id_medico = intval($_POST['id_medico']);
    $fecha_hora = $_POST['fecha_hora']; // Formato YYYY-MM-DDTHH:MM
    $motivo = limpiar_dato($_POST['motivo']);
    
    if ($id_paciente == 0 || $id_medico == 0 || empty($fecha_hora)) {
        $mensaje = 'Faltan datos obligatorios';
        $tipo_mensaje = 'warning';
    } else {
        $sql = "INSERT INTO controlagenda (IdPaciente, IdMedico, FechaCita, MotivoConsulta, EstadoCita) 
                VALUES (?, ?, ?, ?, 'Programada')";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iiss", $id_paciente, $id_medico, $fecha_hora, $motivo);
        
        if ($stmt->execute()) {
            $mensaje = 'Cita agendada correctamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al agendar: ' . $stmt->error;
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

// OBTENER DATOS PARA EL CALENDARIO (EMBEDDED)

// 1. Citas del mes actual
$sql_citas = "SELECT c.IdCita, c.FechaCita, c.MotivoConsulta, 
              p.NombreCompleto as Paciente, m.NombreCompleto as Medico
              FROM controlagenda c
              INNER JOIN controlpacientes p ON c.IdPaciente = p.IdPaciente
              INNER JOIN controlmedico m ON c.IdMedico = m.IdMedico
              WHERE c.EstadoCita = 'Programada'";
$res_citas = $conexion->query($sql_citas);
$citas_array = [];
while ($row = $res_citas->fetch_assoc()) {
    $citas_array[] = [
        'id' => $row['IdCita'],
        'title' => $row['Paciente'] . ' con ' . $row['Medico'],
        'start' => $row['FechaCita'], // ISO format works for JS
        'description' => $row['MotivoConsulta']
    ];
}

// 2. Listas para el formulario
$pacientes = $conexion->query("SELECT IdPaciente, NombreCompleto FROM controlpacientes WHERE Estatus = 1 ORDER BY NombreCompleto");
$medicos = $conexion->query("SELECT IdMedico, NombreCompleto FROM controlmedico WHERE Estatus = 1 ORDER BY NombreCompleto");

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - Sector 404</title>
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
                <h2>📅 Agenda de Citas</h2>
                <p class="text-muted">Gestiona las citas médicas</p>
            </div>

            <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row">
                <!-- Calendario -->
                <div class="col-lg-8">
                    <div class="tarjeta">
                        <div class="controles-mes">
                            <button class="btn btn-outline-secondary btn-sm" id="btnAnterior">&lt; Anterior</button>
                            <h4 class="mb-0" id="tituloMes">Noviembre 2025</h4>
                            <button class="btn btn-outline-secondary btn-sm" id="btnSiguiente">Siguiente &gt;</button>
                        </div>
                        
                        <div class="calendario-grid" id="gridCalendario">
                            <!-- Se llena con JS -->
                        </div>
                    </div>
                </div>

                <!-- Lista Próximas -->
                <div class="col-lg-4">
                    <div class="tarjeta mb-3">
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalCita">
                            ➕ Agendar Nueva Cita
                        </button>
                    </div>

                    <div class="tarjeta">
                        <h5 class="mb-3">Próximas Citas</h5>
                        <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($citas_array as $cita): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($cita['title']); ?></h6>
                                    </div>
                                    <p class="mb-1 small text-muted"><?php echo date('d/m/Y H:i', strtotime($cita['start'])); ?></p>
                                    <small><?php echo htmlspecialchars($cita['description']); ?></small>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($citas_array)): ?>
                                <div class="text-center text-muted py-3">No hay citas programadas</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal Agendar Cita -->
    <div class="modal fade" id="modalCita" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agendar Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Paciente</label>
                            <select class="form-select" name="id_paciente" required>
                                <option value="">Seleccionar...</option>
                                <?php while ($p = $pacientes->fetch_assoc()): ?>
                                    <option value="<?php echo $p['IdPaciente']; ?>"><?php echo htmlspecialchars($p['NombreCompleto']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Médico</label>
                            <select class="form-select" name="id_medico" required>
                                <option value="">Seleccionar...</option>
                                <?php while ($m = $medicos->fetch_assoc()): ?>
                                    <option value="<?php echo $m['IdMedico']; ?>"><?php echo htmlspecialchars($m['NombreCompleto']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha y Hora</label>
                            <input type="datetime-local" class="form-control" name="fecha_hora" id="inputFechaHora" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motivo</label>
                            <textarea class="form-control" name="motivo" rows="2" placeholder="Ej: Dolor de cabeza..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardar_cita" class="btn btn-primary">Guardar Cita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pasar datos a JS -->
    <script>
        const citasRegistradas = <?php echo json_encode($citas_array); ?>;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/controlAgenda.js"></script>
</body>
</html>

<?php

require_once 'Conexion/conexion.php';

// Verificar que sea un paciente
if (!sesion_activa() || $_SESSION['rol'] != 'Paciente') {
    header('Location: Entrada/login.php');
    exit;
}

$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
$id_paciente = isset($_SESSION['IdPaciente']) ? intval($_SESSION['IdPaciente']) : 0;

if ($id_paciente == 0) {
    die('Error: No se encontró el registro de paciente vinculado a esta cuenta.');
}

// Obtener información del paciente
$sql_paciente = "SELECT * FROM controlpacientes WHERE IdPaciente = ?";
$stmt = $conexion->prepare($sql_paciente);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$paciente = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener próximas citas
$sql_citas = "SELECT a.*, m.NombreCompleto as Medico, e.NombreEspecialidad
              FROM controlagenda a
              INNER JOIN controlmedico m ON a.IdMedico = m.IdMedico
              LEFT JOIN especialidades e ON m.EspecialidadId = e.IdEspecialidad
              WHERE a.IdPaciente = ? AND a.FechaHora >= NOW()
              ORDER BY a.FechaHora ASC
              LIMIT 5";
$stmt = $conexion->prepare($sql_citas);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$citas = $stmt->get_result();
$stmt->close();

// Obtener expedientes médicos
$sql_expedientes = "SELECT e.*, m.NombreCompleto as Medico
                    FROM expedienteclinico e
                    INNER JOIN controlmedico m ON e.IdMedico = m.IdMedico
                    WHERE e.IdPaciente = ?
                    ORDER BY e.FechaConsulta DESC
                    LIMIT 10";
$stmt = $conexion->prepare($sql_expedientes);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$expedientes = $stmt->get_result();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Portal - Sector 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Header -->
    <header class="encabezado">
        <div class="marca">🏥 Sector 404 - Portal del Paciente</div>
        <div class="espacio"></div>
        <div class="usuario">
            👤 <?php echo htmlspecialchars($usuario_nombre); ?>
            <span class="badge bg-info ms-2">Paciente</span>
        </div>
        <a href="Entrada/logout.php" class="btn btn-sm btn-outline-danger">Cerrar sesión</a>
    </header>

    <div class="contenedor">
        <!-- Sidebar simplificado para paciente -->
        <nav class="barra-lateral">
            <div class="titulo">📋 Mi Portal</div>
            <a class="enlace activo" href="dashboard_paciente.php">🏠 Mi Información</a>
            <a class="enlace" href="#citas">📅 Mis Citas</a>
            <a class="enlace" href="#expedientes">📋 Mis Expedientes</a>
        </nav>

        <!-- Main Content -->
        <main class="principal">
            
            <div class="header-seccion">
                <h2>👋 Bienvenido, <?php echo htmlspecialchars($paciente['NombreCompleto']); ?></h2>
                <p class="text-muted">Aquí puedes consultar tu información médica</p>
            </div>

            <!-- Información Personal -->
            <div class="tarjeta mb-4">
                <h5 class="mb-3">📝 Mi Información Personal</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Nombre Completo:</strong><br>
                        <?php echo htmlspecialchars($paciente['NombreCompleto']); ?>
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>CURP:</strong><br>
                        <?php echo htmlspecialchars($paciente['CURP']) ?: '-'; ?>
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Edad:</strong><br>
                        <?php 
                        if ($paciente['FechaNacimiento']) {
                            $edad = date_diff(date_create($paciente['FechaNacimiento']), date_create('today'))->y;
                            echo $edad . ' años';
                        } else {
                            echo '-';
                        }
                        ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Teléfono:</strong><br>
                        <?php echo htmlspecialchars($paciente['Telefono']); ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Correo:</strong><br>
                        <?php echo htmlspecialchars($paciente['CorreoElectronico']) ?: '-'; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Sexo:</strong><br>
                        <?php echo $paciente['Sexo'] == 'M' ? 'Masculino' : ($paciente['Sexo'] == 'F' ? 'Femenino' : '-'); ?>
                    </div>
                    <div class="col-12 mb-3">
                        <strong>Dirección:</strong><br>
                        <?php echo htmlspecialchars($paciente['Direccion']) ?: '-'; ?>
                    </div>
                </div>
                
                <hr>
                <h6>🚨 Contacto de Emergencia</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nombre:</strong><br>
                        <?php echo htmlspecialchars($paciente['ContactoEmergencia']) ?: '-'; ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Teléfono:</strong><br>
                        <?php echo htmlspecialchars($paciente['TelefonoEmergencia']) ?: '-'; ?>
                    </div>
                </div>
            </div>

            <!-- Próximas Citas -->
            <div class="tarjeta mb-4" id="citas">
                <h5 class="mb-3">📅 Mis Próximas Citas</h5>
                <?php if ($citas && $citas->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Médico</th>
                                <th>Especialidad</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cita = $citas->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($cita['FechaHora'])); ?></td>
                                <td><?php echo htmlspecialchars($cita['Medico']); ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($cita['NombreEspecialidad']); ?></span></td>
                                <td><?php echo htmlspecialchars($cita['MotivoConsulta']) ?: '-'; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-3">No tienes citas programadas</p>
                <?php endif; ?>
            </div>

            <!-- Historial de Expedientes -->
            <div class="tarjeta" id="expedientes">
                <h5 class="mb-3">📋 Mi Historial Médico</h5>
                <?php if ($expedientes && $expedientes->num_rows > 0): ?>
                <div class="accordion" id="accordionExpedientes">
                    <?php 
                    $contador = 0;
                    while ($exp = $expedientes->fetch_assoc()): 
                        $contador++;
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?php echo $contador > 1 ? 'collapsed' : ''; ?>" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#exp<?php echo $contador; ?>">
                                <strong><?php echo date('d/m/Y', strtotime($exp['FechaConsulta'])); ?></strong>
                                &nbsp;- Dr. <?php echo htmlspecialchars($exp['Medico']); ?>
                            </button>
                        </h2>
                        <div id="exp<?php echo $contador; ?>" class="accordion-collapse collapse <?php echo $contador == 1 ? 'show' : ''; ?>" 
                             data-bs-parent="#accordionExpedientes">
                            <div class="accordion-body">
                                <div class="mb-2">
                                    <strong>Síntomas:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($exp['Sintomas'])) ?: '-'; ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Diagnóstico:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($exp['Diagnostico'])) ?: '-'; ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Tratamiento:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($exp['Tratamiento'])) ?: '-'; ?>
                                </div>
                                <?php if ($exp['RecetaMedica']): ?>
                                <div class="mb-2">
                                    <strong>Receta Médica:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($exp['RecetaMedica'])); ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($exp['ProximaCita']): ?>
                                <div class="alert alert-info mt-3">
                                    <strong>Próxima Cita:</strong> <?php echo date('d/m/Y H:i', strtotime($exp['ProximaCita'])); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-3">No tienes expedientes médicos registrados</p>
                <?php endif; ?>
            </div>

            <!-- Información Médica -->
            <div class="tarjeta mt-4">
                <h5 class="mb-3">⚕️ Información Médica Importante</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-warning">
                            <strong>⚠️ Alergias:</strong><br>
                            <?php echo nl2br(htmlspecialchars($paciente['Alergias'])) ?: 'Ninguna registrada'; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>📋 Antecedentes Médicos:</strong><br>
                            <?php echo nl2br(htmlspecialchars($paciente['AntecedentesMedicos'])) ?: 'Ninguno registrado'; ?>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conexion->close(); ?>
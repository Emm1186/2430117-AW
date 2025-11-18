<?php
/**
 * DASHBOARD - SECTOR 404
 * Panel principal del sistema
 */

require_once 'Conexion/conexion.php';

// Verificar que esté logueado
if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['nombre'] ?: $_SESSION['correo'];
$usuario_rol = $_SESSION['rol'];

// Obtener estadísticas
$total_pacientes = $conexion->query("SELECT COUNT(*) as total FROM ControlPacientes WHERE Estatus = 1")->fetch_assoc()['total'];
$total_medicos = $conexion->query("SELECT COUNT(*) as total FROM ControlMedico WHERE Estatus = 1")->fetch_assoc()['total'];
$total_especialidades = $conexion->query("SELECT COUNT(*) as total FROM Especialidades")->fetch_assoc()['total'];

// Citas de hoy
$hoy = date('Y-m-d');
$sql_citas_hoy = "SELECT COUNT(*) as total FROM ControlAgenda 
                  WHERE DATE(FechaCita) = ? AND EstadoCita = 'Programada'";
$stmt = $conexion->prepare($sql_citas_hoy);
$stmt->bind_param("s", $hoy);
$stmt->execute();
$citas_hoy = $stmt->get_result()->fetch_assoc()['total'];

// Obtener próximas citas (5 más cercanas)
$sql_proximas = "SELECT ca.IdCita, cp.NombreCompleto as Paciente, cm.NombreCompleto as Medico, 
                 ca.FechaCita, ca.MotivoConsulta
                 FROM ControlAgenda ca
                 INNER JOIN ControlPacientes cp ON ca.IdPaciente = cp.IdPaciente
                 INNER JOIN ControlMedico cm ON ca.IdMedico = cm.IdMedico
                 WHERE ca.FechaCita >= NOW() AND ca.EstadoCita = 'Programada'
                 ORDER BY ca.FechaCita ASC
                 LIMIT 5";
$resultado_citas = $conexion->query($sql_proximas);

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sector 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Header con menú -->
    <header class="encabezado">
        <div class="marca">🏥 Sector 404</div>
        <div class="espacio"></div>
        <div class="usuario">
            👤 <?php echo htmlspecialchars($usuario_nombre); ?>
            <span class="badge bg-secondary ms-2"><?php echo $usuario_rol; ?></span>
        </div>
        <a href="Entrada/logout.php" class="btn btn-sm btn-outline-danger">Cerrar sesión</a>
    </header>

    <div class="contenedor">
        <!-- Sidebar -->
        <nav class="barra-lateral">
            <div class="titulo">📋 Menú</div>
            <a class="enlace activo" href="dashboard.php">🏠 Inicio</a>
            <a class="enlace" href="pacientes.php">👥 Control de pacientes</a>
            <a class="enlace" href="agenda.php">📅 Control de agenda</a>
            <a class="enlace" href="medicos.php">👨‍⚕️ Control de médicos</a>
            <a class="enlace" href="especialidades.php">🩺 Especialidades médicas</a>
            <a class="enlace" href="tarifas.php">💰 Gestor de tarifas</a>
            <a class="enlace" href="pagos.php">💳 Pagos</a>
            <a class="enlace" href="reportes.php">📊 Reportes</a>
            
            <?php if ($usuario_rol == 'Admin'): ?>
            <hr style="margin: 15px 0; border-color: #ddd;">
            <div class="titulo">⚙️ Administración</div>
            <a class="enlace" href="bitacoras.php">📝 Bitácoras</a>
            <?php endif; ?>
        </nav>

        <!-- Contenido principal -->
        <main class="principal">
            
            <!-- Bienvenida -->
            <div class="header-dashboard">
                <h1 class="mb-2">¡Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?>!</h1>
                <p class="mb-0 opacity-75">Panel de control - Sistema de Gestión Médica</p>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row g-4 mb-4">
                
                <div class="col-md-3">
                    <div class="stat-card position-relative">
                        <div class="stat-icon">👥</div>
                        <div class="stat-label">Pacientes</div>
                        <div class="stat-number"><?php echo $total_pacientes; ?></div>
                        <a href="pacientes.php" class="btn btn-sm btn-outline-primary mt-2">Ver todos</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card position-relative">
                        <div class="stat-icon">👨‍⚕️</div>
                        <div class="stat-label">Médicos</div>
                        <div class="stat-number"><?php echo $total_medicos; ?></div>
                        <a href="medicos.php" class="btn btn-sm btn-outline-success mt-2">Ver todos</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card position-relative">
                        <div class="stat-icon">📅</div>
                        <div class="stat-label">Citas Hoy</div>
                        <div class="stat-number"><?php echo $citas_hoy; ?></div>
                        <a href="agenda.php" class="btn btn-sm btn-outline-info mt-2">Ver agenda</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card position-relative">
                        <div class="stat-icon">🩺</div>
                        <div class="stat-label">Especialidades</div>
                        <div class="stat-number"><?php echo $total_especialidades; ?></div>
                        <a href="especialidades.php" class="btn btn-sm btn-outline-warning mt-2">Ver todas</a>
                    </div>
                </div>

            </div>

            <!-- Próximas citas -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="tarjeta">
                        <h4 class="mb-3">📅 Próximas Citas</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Médico</th>
                                        <th>Fecha y Hora</th>
                                        <th>Motivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($resultado_citas && $resultado_citas->num_rows > 0): ?>
                                        <?php while ($cita = $resultado_citas->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cita['Paciente']); ?></td>
                                            <td><?php echo htmlspecialchars($cita['Medico']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($cita['FechaCita'])); ?></td>
                                            <td><?php echo htmlspecialchars($cita['MotivoConsulta']); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No hay citas programadas</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Acceso rápido -->
                <div class="col-lg-4">
                    <div class="quick-access">
                        <h5 class="mb-3">⚡ Acceso Rápido</h5>
                        
                        <a href="agenda.php" class="quick-btn text-decoration-none">
                            📅 Nueva Cita
                        </a>
                        
                        <a href="pacientes.php" class="quick-btn text-decoration-none">
                            👤 Nuevo Paciente
                        </a>
                        
                        <?php if ($usuario_rol == 'Admin'): ?>
                        <a href="medicos.php" class="quick-btn text-decoration-none">
                            👨‍⚕️ Nuevo Médico
                        </a>
                        <?php endif; ?>
                        
                        <a href="pagos.php" class="quick-btn text-decoration-none">
                            💳 Registrar Pago
                        </a>
                        
                        <a href="reportes.php" class="quick-btn text-decoration-none">
                            📊 Ver Reportes
                        </a>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>
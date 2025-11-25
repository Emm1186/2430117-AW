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

// Obtener información del usuario (forma sencilla para principiantes)
$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = '';
if (isset($_SESSION['nombre']) && !empty($_SESSION['nombre'])) {
    $usuario_nombre = $_SESSION['nombre'];
} else if (isset($_SESSION['correo'])) {
    $usuario_nombre = $_SESSION['correo'];
}
$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';

// Obtener estadísticas de forma clara: ejecutar la consulta y comprobar resultado
$total_pacientes = 0;
$res = $conexion->query("SELECT COUNT(*) as total FROM controlpacientes WHERE Estatus = 1");
if ($res) {
    $fila = $res->fetch_assoc();
    $total_pacientes = isset($fila['total']) ? $fila['total'] : 0;
}

$total_medicos = 0;
$res = $conexion->query("SELECT COUNT(*) as total FROM controlmedico WHERE Estatus = 1");
if ($res) {
    $fila = $res->fetch_assoc();
    $total_medicos = isset($fila['total']) ? $fila['total'] : 0;
}

$total_especialidades = 0;
$res = $conexion->query("SELECT COUNT(*) as total FROM especialidades");
if ($res) {
    $fila = $res->fetch_assoc();
    $total_especialidades = isset($fila['total']) ? $fila['total'] : 0;
}

// Citas de hoy
$hoy = date('Y-m-d');
$sql_citas_hoy = "SELECT COUNT(*) as total FROM controlagenda 
                  WHERE DATE(FechaCita) = ? AND EstadoCita = 'Programada'";
$stmt = $conexion->prepare($sql_citas_hoy);
$stmt->bind_param("s", $hoy);
$stmt->execute();
$citas_hoy = ($stmt->get_result()->fetch_assoc()['total']) ?? 0;

// Obtener próximas citas (5 más cercanas)
$sql_proximas = "SELECT ca.IdCita, cp.NombreCompleto as Paciente, cm.NombreCompleto as Medico, 
                 ca.FechaCita, ca.MotivoConsulta
                 FROM controlagenda ca
                 INNER JOIN controlpacientes cp ON ca.IdPaciente = cp.IdPaciente
                 INNER JOIN controlmedico cm ON ca.IdMedico = cm.IdMedico
                 WHERE ca.FechaCita >= NOW() AND ca.EstadoCita = 'Programada'
                 ORDER BY ca.FechaCita ASC
                 LIMIT 5";
$resultado_citas = $conexion->query($sql_proximas);

// La conexión se cerrará al final del flujo (partial footer puede necesitarla)
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

        <!-- Contenido principal -->
        <main class="principal">
            
            <!-- Bienvenida -->
            <div class="header-dashboard">
                <h1 class="mb-2">¡Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?>!</h1>
                <p class="mb-0 opacity-75">Panel de control - Sistema de Gestión Médica</p>
            </div>

            <!-- Aviso si usuario fue redirigido por falta de permisos -->
            <?php if (isset($_GET['noaccess'])): ?>
            <div class="alert alert-warning" role="alert">
                No tienes permisos para acceder al módulo solicitado. Si crees que esto es un error, contacta al administrador.
            </div>
            <?php endif; ?>

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
                        
                        <?php if (in_array($usuario_rol, array('Admin','Secretaria'))): ?>
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

    <footer style="display:none;"><!-- Footer placeholder if needed --></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/medicos.js"></script>
</body>
</html>
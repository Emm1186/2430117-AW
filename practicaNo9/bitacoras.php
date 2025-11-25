<?php

require_once 'Conexion/conexion.php';

// Solo Admin puede ver bitácoras
verificar_acceso(['Admin']);

if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';

// Obtener bitácoras con información del usuario
$sql = "SELECT b.*, u.Nombre, u.Correo, u.Rol
        FROM bitacoraacceso b
        INNER JOIN usuarios u ON b.IdUsuario = u.IdUsuario
        ORDER BY b.FechaAcceso DESC
        LIMIT 100";
$resultado = $conexion->query($sql);

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácoras - Sector 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Header -->
    <header class="encabezado">
        <div class="marca">🏥 Sector 404</div>
        <div class="espacio"></div>
        <div class="usuario">
            👤 <?php echo htmlspecialchars($usuario_nombre); ?>
            <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($usuario_rol); ?></span>
        </div>
        <a href="Entrada/logout.php" class="btn btn-sm btn-outline-danger">Cerrar sesión</a>
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
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'bitacoras.php') ? ' activo' : ''; ?>" href="bitacoras.php">📝 Bitácoras</a>
        </nav>

        <!-- Main Content -->
        <main class="principal">
            
            <div class="header-seccion">
                <h2>📝 Bitácoras de Acceso</h2>
                <p class="text-muted">Historial de inicios de sesión en el sistema (últimos 100 registros)</p>
            </div>

            <div class="tarjeta">
                <div class="alert alert-info">
                    <strong>ℹ️ Información:</strong> Esta bitácora registra automáticamente cada inicio de sesión exitoso en el sistema.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Fecha y Hora</th>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Acción</th>
                                <th>Módulo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado && $resultado->num_rows > 0): ?>
                                <?php while ($log = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $log['IdBitacora']; ?></td>
                                    <td>
                                        <strong><?php echo date('d/m/Y', strtotime($log['FechaAcceso'])); ?></strong><br>
                                        <small class="text-muted"><?php echo date('H:i:s', strtotime($log['FechaAcceso'])); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['Nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($log['Correo']); ?></td>
                                    <td>
                                        <?php 
                                        $badge_class = 'bg-secondary';
                                        if ($log['Rol'] == 'Admin') $badge_class = 'bg-danger';
                                        else if ($log['Rol'] == 'Secretaria') $badge_class = 'bg-primary';
                                        else if ($log['Rol'] == 'Paciente') $badge_class = 'bg-info';
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo htmlspecialchars($log['Rol']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['AccionRealizada']); ?></td>
                                    <td><span class="badge bg-success"><?php echo htmlspecialchars($log['Modulo']); ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No hay registros en la bitácora
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($resultado && $resultado->num_rows > 0): ?>
                <div class="alert alert-secondary mt-3">
                    <strong>Total de registros mostrados:</strong> <?php echo $resultado->num_rows; ?> (últimos 100)
                </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

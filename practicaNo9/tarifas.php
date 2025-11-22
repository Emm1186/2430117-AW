<?php
/**
 * GESTOR DE TARIFAS - SECTOR 404
 * Permite asociar tarifas/servicios a médicos específicos
 */

require_once 'Conexion/conexion.php';

// Verificar sesión
if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

$usuario_nombre = '';
if (isset($_SESSION['nombre']) && !empty($_SESSION['nombre'])) {
    $usuario_nombre = $_SESSION['nombre'];
} else if (isset($_SESSION['correo'])) {
    $usuario_nombre = $_SESSION['correo'];
}
$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';

$mensaje = '';
$tipo_mensaje = '';
$tarifa_editar = null;

// ========================================
// PROCESAR ACCIONES
// ========================================

// ELIMINAR
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    $sql = "DELETE FROM gestortarifas WHERE IdTarifa = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_eliminar);
    if ($stmt->execute()) {
        $mensaje = 'Tarifa eliminada correctamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al eliminar: ' . $stmt->error;
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// CARGAR PARA EDITAR
if (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $sql = "SELECT * FROM gestortarifas WHERE IdTarifa = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $tarifa_editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// GUARDAR (Crear o Actualizar)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    $id_tarifa = isset($_POST['id_tarifa']) ? intval($_POST['id_tarifa']) : 0;
    $id_medico = intval($_POST['id_medico']);
    $descripcion = limpiar_dato($_POST['descripcion']);
    $costo = floatval($_POST['costo']);
    
    if ($id_medico == 0 || empty($descripcion) || $costo <= 0) {
        $mensaje = 'Por favor complete todos los campos correctamente';
        $tipo_mensaje = 'warning';
    } else {
        if ($id_tarifa > 0) {
            // Actualizar
            $sql = "UPDATE gestortarifas SET IdMedico = ?, DescripcionServicio = ?, CostoBase = ? WHERE IdTarifa = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("isdi", $id_medico, $descripcion, $costo, $id_tarifa);
        } else {
            // Insertar
            $sql = "INSERT INTO gestortarifas (IdMedico, DescripcionServicio, CostoBase) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("isd", $id_medico, $descripcion, $costo);
        }
        
        if ($stmt->execute()) {
            $mensaje = $id_tarifa > 0 ? 'Tarifa actualizada correctamente' : 'Tarifa registrada correctamente';
            $tipo_mensaje = 'success';
            $tarifa_editar = null; // Limpiar formulario
        } else {
            $mensaje = 'Error al guardar: ' . $stmt->error;
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

// ========================================
// CONSULTAS
// ========================================

// Obtener lista de médicos para el select
$sql_medicos = "SELECT IdMedico, NombreCompleto, EspecialidadId FROM controlmedico WHERE Estatus = 1 ORDER BY NombreCompleto";
$resultado_medicos = $conexion->query($sql_medicos);

// Obtener lista de tarifas
$sql_tarifas = "SELECT t.*, m.NombreCompleto as NombreMedico, e.NombreEspecialidad 
                FROM gestortarifas t
                LEFT JOIN controlmedico m ON t.IdMedico = m.IdMedico
                LEFT JOIN especialidades e ON m.EspecialidadId = e.IdEspecialidad
                WHERE t.IdMedico IS NOT NULL
                ORDER BY m.NombreCompleto, t.DescripcionServicio";
$resultado_tarifas = $conexion->query($sql_tarifas);

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Tarifas - Sector 404</title>
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
            <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'agenda.php') ? ' activo' : ''; ?>" href="agenda.php">📅 Control de agenda</a>
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
                <h2>💰 Gestor de Tarifas por Médico</h2>
                <p class="text-muted">Asigna costos de servicios a cada médico</p>
            </div>

            <!-- Mensajes -->
            <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="tarjeta mb-4">
                <h5 class="mb-3">
                    <?php echo $tarifa_editar ? '✏️ Editar Tarifa' : '➕ Nueva Tarifa'; ?>
                </h5>
                
                <form method="POST" action="">
                    <input type="hidden" name="id_tarifa" value="<?php echo $tarifa_editar ? $tarifa_editar['IdTarifa'] : ''; ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="id_medico" class="form-label">Médico *</label>
                            <select class="form-select" id="id_medico" name="id_medico" required>
                                <option value="">Seleccionar Médico...</option>
                                <?php 
                                if ($resultado_medicos):
                                    $resultado_medicos->data_seek(0);
                                    while ($med = $resultado_medicos->fetch_assoc()): 
                                        $selected = ($tarifa_editar && $tarifa_editar['IdMedico'] == $med['IdMedico']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $med['IdMedico']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($med['NombreCompleto']); ?>
                                    </option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label for="descripcion" class="form-label">Descripción del Servicio / Enfermedad *</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" 
                                   placeholder="Ej: Consulta General, Tratamiento de Gripe..."
                                   value="<?php echo $tarifa_editar ? htmlspecialchars($tarifa_editar['DescripcionServicio']) : ''; ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label for="costo" class="form-label">Costo ($) *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="costo" name="costo" 
                                       value="<?php echo $tarifa_editar ? htmlspecialchars($tarifa_editar['CostoBase']) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" name="guardar" class="btn btn-success">
                                <?php echo $tarifa_editar ? '💾 Actualizar Tarifa' : '➕ Agregar Tarifa'; ?>
                            </button>
                            <?php if ($tarifa_editar): ?>
                                <a href="tarifas.php" class="btn btn-secondary">❌ Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla -->
            <div class="tarjeta">
                <h5 class="mb-3">📋 Tarifas Registradas</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Médico</th>
                                <th>Especialidad</th>
                                <th>Servicio / Enfermedad</th>
                                <th>Costo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado_tarifas && $resultado_tarifas->num_rows > 0): ?>
                                <?php while ($row = $resultado_tarifas->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['NombreMedico']); ?></strong></td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($row['NombreEspecialidad']); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['DescripcionServicio']); ?></td>
                                    <td class="text-success fw-bold">$<?php echo number_format($row['CostoBase'], 2); ?></td>
                                    <td>
                                        <a href="tarifas.php?editar=<?php echo $row['IdTarifa']; ?>" class="btn btn-sm btn-warning">✏️</a>
                                        <a href="tarifas.php?eliminar=<?php echo $row['IdTarifa']; ?>" 
                                           onclick="return confirm('¿Eliminar esta tarifa?');"
                                           class="btn btn-sm btn-danger">🗑️</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No hay tarifas registradas asociadas a médicos.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

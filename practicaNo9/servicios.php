<?php
/**
 * SERVICIOS (Tarifas) - CRUD sencillo
 * Tabla: gestortarifas
 */

require_once 'Conexion/conexion.php';

// Verificar permisos (Solo Admin y Secretaria)
verificar_acceso(['Admin', 'Secretaria']);

if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $descripcion = isset($_POST['descripcion']) ? limpiar_dato($_POST['descripcion']) : '';
    $costo = isset($_POST['costo']) ? floatval($_POST['costo']) : 0.0;
    $especialidad_id = isset($_POST['especialidad_id']) ? intval($_POST['especialidad_id']) : null;

    if (empty($descripcion) || $costo <= 0) {
        $mensaje = 'Descripción y costo son obligatorios';
        $tipo_mensaje = 'warning';
    } else {
        if ($id > 0) {
            $sql = "UPDATE gestortarifas SET DescripcionServicio = ?, CostoBase = ?, EspecialidadId = ? WHERE IdTarifa = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sdii', $descripcion, $costo, $especialidad_id, $id);
        } else {
            $sql = "INSERT INTO gestortarifas (DescripcionServicio, CostoBase, EspecialidadId) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sdi', $descripcion, $costo, $especialidad_id);
        }
        if ($stmt->execute()) {
            $mensaje = $id > 0 ? 'Servicio actualizado' : 'Servicio agregado';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error: ' . $stmt->error;
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

if (isset($_GET['eliminar']) && $usuario_rol == 'Admin') {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM gestortarifas WHERE IdTarifa = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $mensaje = 'Servicio eliminado';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al eliminar: ' . $stmt->error;
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// Cargar para editar
$editar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $sql = "SELECT * FROM gestortarifas WHERE IdTarifa = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Listar servicios y especialidades para selector
$resultados = $conexion->query("SELECT g.*, e.NombreEspecialidad FROM gestortarifas g LEFT JOIN especialidades e ON g.EspecialidadId = e.IdEspecialidad ORDER BY g.IdTarifa DESC");
$especialidades = $conexion->query("SELECT * FROM especialidades ORDER BY NombreEspecialidad");

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Servicios - Sector 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="encabezado">
        <div class="marca">🏥 Sector 404</div>
        <div class="espacio"></div>
        <div class="usuario">
            <?php 
                $usuario_nombre = '';
                if (isset($_SESSION['nombre']) && !empty($_SESSION['nombre'])) {
                    $usuario_nombre = $_SESSION['nombre'];
                } else if (isset($_SESSION['correo'])) {
                    $usuario_nombre = $_SESSION['correo'];
                }
                $usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
            ?>
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
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'especialidades.php') ? ' activo' : ''; ?>" href="especialidades.php">🩺 Especialidades médicas</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'tarifas.php') ? ' activo' : ''; ?>" href="tarifas.php">💰 Gestor de tarifas</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'pagos.php') ? ' activo' : ''; ?>" href="pagos.php">💳 Pagos</a>
        <a class="enlace<?php echo (basename($_SERVER['PHP_SELF']) == 'reportes.php') ? ' activo' : ''; ?>" href="reportes.php">📊 Reportes</a>
        <hr style="margin: 15px 0; border-color: #ddd;">
        <div class="titulo">⚙️ Administración</div>
        <a class="enlace" href="bitacoras.php">📝 Bitácoras</a>
    </nav>

    <main class="principal">
        <div class="header-seccion">
            <h2>🧾 Servicios / Tarifas</h2>
            <p class="text-muted">Administrar servicios y costos</p>
        </div>

        <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="tarjeta mb-4">
            <h5><?php echo $editar ? '✏️ Editar Servicio' : '➕ Nuevo Servicio'; ?></h5>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $editar ? $editar['IdTarifa'] : ''; ?>">
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <input class="form-control" name="descripcion" value="<?php echo $editar ? htmlspecialchars($editar['DescripcionServicio']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Costo</label>
                    <input class="form-control" name="costo" type="number" step="0.01" value="<?php echo $editar ? htmlspecialchars($editar['CostoBase']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Especialidad (opcional)</label>
                    <select name="especialidad_id" class="form-select">
                        <option value="">Ninguna</option>
                        <?php if ($especialidades): while ($e = $especialidades->fetch_assoc()): ?>
                            <option value="<?php echo $e['IdEspecialidad']; ?>" <?php echo ($editar && $editar['EspecialidadId'] == $e['IdEspecialidad']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['NombreEspecialidad']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <button name="guardar" class="btn btn-primary">Guardar</button>
            </form>
        </div>

        <div class="tarjeta">
            <h5>📋 Lista de Servicios</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Descripción</th><th>Costo</th><th>Especialidad</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if ($resultados && $resultados->num_rows > 0): while ($r = $resultados->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $r['IdTarifa']; ?></td>
                            <td><?php echo htmlspecialchars($r['DescripcionServicio']); ?></td>
                            <td><?php echo number_format($r['CostoBase'],2); ?></td>
                            <td><?php echo htmlspecialchars($r['NombreEspecialidad']); ?></td>
                            <td>
                                <a href="servicios.php?editar=<?php echo $r['IdTarifa']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="servicios.php?eliminar=<?php echo $r['IdTarifa']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminar servicio?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" class="text-center text-muted">No hay servicios</td></tr>
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
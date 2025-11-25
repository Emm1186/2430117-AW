<?php
/**
 * ESPECIALIDADES - CRUD sencillo
 * Página simple para administrar especialidades.
 * Comentarios y estructura pensados para quienes aprenden PHP.
 */

require_once 'Conexion/conexion.php';

// Verificar permisos (Solo Admin y Secretaria)
verificar_acceso(['Admin', 'Secretaria']);

if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
// Solo Admin puede modificar (pero ver pueden otros)

$mensaje = '';
$tipo_mensaje = '';

// Crear / Editar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = isset($_POST['nombre']) ? limpiar_dato($_POST['nombre']) : '';
    $descripcion = isset($_POST['descripcion']) ? limpiar_dato($_POST['descripcion']) : '';

    if (empty($nombre)) {
        $mensaje = 'El nombre de la especialidad es obligatorio';
        $tipo_mensaje = 'warning';
    } else {
        if ($id > 0) {
            $sql = "UPDATE especialidades SET NombreEspecialidad = ?, Descripcion = ? WHERE IdEspecialidad = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('ssi', $nombre, $descripcion, $id);
        } else {
            $sql = "INSERT INTO especialidades (NombreEspecialidad, Descripcion) VALUES (?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('ss', $nombre, $descripcion);
        }

        if ($stmt->execute()) {
            $mensaje = $id > 0 ? 'Especialidad actualizada' : 'Especialidad agregada';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al guardar: ' . $stmt->error;
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

// Eliminar (borrado físico, sencillo)
if (isset($_GET['eliminar']) && $usuario_rol == 'Admin') {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM especialidades WHERE IdEspecialidad = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $mensaje = 'Especialidad eliminada';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al eliminar: ' . $stmt->error;
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// Cargar para editar
$especialidad_editar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $sql = "SELECT * FROM especialidades WHERE IdEspecialidad = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $especialidad_editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Listar
$resultado = $conexion->query("SELECT * FROM especialidades ORDER BY NombreEspecialidad");

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Especialidades - Sector 404</title>
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

    <main class="principal">
        <div class="header-seccion">
            <h2>🩺 Especialidades</h2>
            <p class="text-muted">Gestión sencilla de especialidades médicas</p>
        </div>

        <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="tarjeta mb-4">
            <h5><?php echo $especialidad_editar ? '✏️ Editar Especialidad' : '➕ Nueva Especialidad'; ?></h5>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $especialidad_editar ? $especialidad_editar['IdEspecialidad'] : ''; ?>">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input class="form-control" name="nombre" value="<?php echo $especialidad_editar ? htmlspecialchars($especialidad_editar['NombreEspecialidad']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" name="descripcion"><?php echo $especialidad_editar ? htmlspecialchars($especialidad_editar['Descripcion']) : ''; ?></textarea>
                </div>
                <button name="guardar" class="btn btn-primary">Guardar</button>
            </form>
        </div>

        <div class="tarjeta">
            <h5>📋 Lista</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($r = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $r['IdEspecialidad']; ?></td>
                                    <td><?php echo htmlspecialchars($r['NombreEspecialidad']); ?></td>
                                    <td><?php echo htmlspecialchars($r['Descripcion']); ?></td>
                                    <td>
                                        <a href="especialidades.php?editar=<?php echo $r['IdEspecialidad']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                        <a href="especialidades.php?eliminar=<?php echo $r['IdEspecialidad']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminar especialidad?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">No hay especialidades</td></tr>
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

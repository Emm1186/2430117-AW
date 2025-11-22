<?php
/**
 * PAGOS - CRUD sencillo para gestorpagos
 * Tabla: gestorpagos
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

// Crear/Editar pago (para simplicidad, no validamos relaciones complejas aquí)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $idcita = isset($_POST['idcita']) ? intval($_POST['idcita']) : 0;
    $idpaciente = isset($_POST['idpaciente']) ? intval($_POST['idpaciente']) : 0;
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0.0;
    $metodo = isset($_POST['metodo']) ? limpiar_dato($_POST['metodo']) : '';
    $referencia = isset($_POST['referencia']) ? limpiar_dato($_POST['referencia']) : '';
    $estatus = isset($_POST['estatus']) ? limpiar_dato($_POST['estatus']) : 'Pagado';

    if ($monto <= 0) {
        $mensaje = 'El monto debe ser mayor a 0';
        $tipo_mensaje = 'warning';
    } else {
        if ($id > 0) {
            $sql = "UPDATE gestorpagos SET IdCita = ?, IdPaciente = ?, Monto = ?, MetodoPago = ?, Referencia = ?, EstatusPago = ? WHERE IdPago = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('iidsssi', $idcita, $idpaciente, $monto, $metodo, $referencia, $estatus, $id);
        } else {
            $sql = "INSERT INTO gestorpagos (IdCita, IdPaciente, Monto, MetodoPago, Referencia, EstatusPago) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('iidsss', $idcita, $idpaciente, $monto, $metodo, $referencia, $estatus);
        }
        if ($stmt->execute()) {
            $mensaje = $id > 0 ? 'Pago actualizado' : 'Pago registrado';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error: ' . $stmt->error;
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

// Anular pago (no eliminar, solo cambiar estatus)
if (isset($_GET['anular']) && $usuario_rol == 'Admin') {
    $id = intval($_GET['anular']);
    $sql = "UPDATE gestorpagos SET EstatusPago = 'Anulado' WHERE IdPago = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $mensaje = 'Pago anulado';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error: ' . $stmt->error;
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// Cargar para editar
$editar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $sql = "SELECT * FROM gestorpagos WHERE IdPago = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Listar (mostramos los últimos 100)
$resultados = $conexion->query("SELECT gp.*, p.NombreCompleto AS Paciente FROM gestorpagos gp LEFT JOIN controlpacientes p ON gp.IdPaciente = p.IdPaciente ORDER BY gp.IdPago DESC LIMIT 100");

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pagos - Sector 404</title>
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
            <h2>💳 Pagos</h2>
            <p class="text-muted">Registrar y gestionar pagos</p>
        </div>

        <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="tarjeta mb-4">
            <h5><?php echo $editar ? '✏️ Editar Pago' : '➕ Nuevo Pago'; ?></h5>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $editar ? $editar['IdPago'] : ''; ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Id Cita</label>
                        <input class="form-control" name="idcita" value="<?php echo $editar ? $editar['IdCita'] : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Id Paciente</label>
                        <input class="form-control" name="idpaciente" value="<?php echo $editar ? $editar['IdPaciente'] : ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Monto</label>
                        <input class="form-control" name="monto" type="number" step="0.01" value="<?php echo $editar ? $editar['Monto'] : ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Método</label>
                        <input class="form-control" name="metodo" value="<?php echo $editar ? htmlspecialchars($editar['MetodoPago']) : ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Referencia</label>
                        <input class="form-control" name="referencia" value="<?php echo $editar ? htmlspecialchars($editar['Referencia']) : ''; ?>">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Estatus</label>
                    <input class="form-control" name="estatus" value="<?php echo $editar ? htmlspecialchars($editar['EstatusPago']) : 'Pagado'; ?>">
                </div>
                <button name="guardar" class="btn btn-primary mt-3">Guardar</button>
            </form>
        </div>

        <div class="tarjeta">
            <h5>📋 Últimos pagos</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Paciente</th><th>Monto</th><th>Método</th><th>Fecha</th><th>Estatus</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if ($resultados && $resultados->num_rows > 0): while ($r = $resultados->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $r['IdPago']; ?></td>
                            <td><?php echo htmlspecialchars($r['Paciente']); ?></td>
                            <td><?php echo number_format($r['Monto'],2); ?></td>
                            <td><?php echo htmlspecialchars($r['MetodoPago']); ?></td>
                            <td><?php echo $r['FechaPago']; ?></td>
                            <td><?php echo htmlspecialchars($r['EstatusPago']); ?></td>
                            <td>
                                <a href="pagos.php?editar=<?php echo $r['IdPago']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="pagos.php?anular=<?php echo $r['IdPago']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anular pago?')">Anular</a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No hay pagos</td></tr>
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
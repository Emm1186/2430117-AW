<?php
/**
 * REPORTES - Versión simplificada
 * Genera reportes usando HTML para PDF (impresión del navegador) y CSV para Excel
 */

require_once 'Conexion/conexion.php';

// Verificar permisos
verificar_acceso(['Admin', 'Secretaria']);

if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}

$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
$usuario_rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';

// Obtener estadísticas
$stats = [];
$stats['pagos'] = $conexion->query("SELECT COUNT(*) as total FROM gestorpagos")->fetch_assoc()['total'];
$stats['pacientes'] = $conexion->query("SELECT COUNT(*) as total FROM controlpacientes WHERE Estatus = 1")->fetch_assoc()['total'];
$stats['medicos'] = $conexion->query("SELECT COUNT(*) as total FROM controlmedico WHERE Estatus = 1")->fetch_assoc()['total'];
$stats['citas'] = $conexion->query("SELECT COUNT(*) as total FROM controlagenda")->fetch_assoc()['total'];
$stats['bitacoras'] = $conexion->query("SELECT COUNT(*) as total FROM bitacoraacceso")->fetch_assoc()['total'];

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sector 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .report-card {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
        }
        .report-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .report-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        .report-icon {
            font-size: 2rem;
            margin-right: 15px;
        }
    </style>
</head>
<body>

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
        <nav class="barra-lateral">
            <div class="titulo">📋 Menú</div>
            <a class="enlace" href="dashboard.php">🏠 Inicio</a>
            <a class="enlace" href="pacientes.php">👥 Control de pacientes</a>
            <a class="enlace" href="controlAgenda.php">📅 Control de agenda</a>
            <a class="enlace" href="medicos.php">👨‍⚕️ Control de médicos</a>
            <a class="enlace" href="expedientes.php">📋 Expedientes médicos</a>
            <a class="enlace" href="especialidades.php">🩺 Especialidades médicas</a>
            <a class="enlace" href="tarifas.php">💰 Gestor de tarifas</a>
            <a class="enlace" href="pagos.php">💳 Pagos</a>
            <a class="enlace activo" href="reportes.php">📊 Reportes</a>
            <hr style="margin: 15px 0; border-color: #ddd;">
            <div class="titulo">⚙️ Administración</div>
            <a class="enlace" href="bitacoras.php">📝 Bitácoras</a>
        </nav>

        <main class="principal">
            <div class="header-seccion">
                <h2>📊 Reportes del Sistema</h2>
                <p class="text-muted">Genera y descarga reportes en PDF o Excel</p>
            </div>

            <!-- Reporte de Pagos -->
            <div class="report-card">
                <div class="report-header">
                    <div class="report-icon">💳</div>
                    <div>
                        <h4 class="mb-0">Reporte de Pagos</h4>
                        <small class="text-muted">Total: <?php echo $stats['pagos']; ?> registros</small>
                    </div>
                </div>
                <form id="formPagos">
                    <input type="hidden" name="tipo" value="pagos">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estatus</label>
                            <select class="form-select" name="estatus">
                                <option value="">Todos</option>
                                <option value="Pagado">Pagado</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Anulado">Anulado</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="text-end">
                    <button class="btn btn-danger" onclick="exportar('pagos', 'pdf')">📄 PDF</button>
                    <button class="btn btn-success" onclick="exportar('pagos', 'excel')">📊 Excel</button>
                </div>
            </div>

            <!-- Reporte de Pacientes -->
            <div class="report-card">
                <div class="report-header">
                    <div class="report-icon">👥</div>
                    <div>
                        <h4 class="mb-0">Reporte de Pacientes</h4>
                        <small class="text-muted">Total: <?php echo $stats['pacientes']; ?> registros</small>
                    </div>
                </div>
                <form id="formPacientes">
                    <input type="hidden" name="tipo" value="pacientes">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Género</label>
                            <select class="form-select" name="genero">
                                <option value="">Todos</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estatus</label>
                            <select class="form-select" name="estatus">
                                <option value="">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="text-end">
                    <button class="btn btn-danger" onclick="exportar('pacientes', 'pdf')">📄 PDF</button>
                    <button class="btn btn-success" onclick="exportar('pacientes', 'excel')">📊 Excel</button>
                </div>
            </div>

            <!-- Reporte de Médicos -->
            <div class="report-card">
                <div class="report-header">
                    <div class="report-icon">👨‍⚕️</div>
                    <div>
                        <h4 class="mb-0">Reporte de Médicos</h4>
                        <small class="text-muted">Total: <?php echo $stats['medicos']; ?> registros</small>
                    </div>
                </div>
                <form id="formMedicos">
                    <input type="hidden" name="tipo" value="medicos">
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Estatus</label>
                            <select class="form-select" name="estatus">
                                <option value="">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="text-end">
                    <button class="btn btn-danger" onclick="exportar('medicos', 'pdf')">📄 PDF</button>
                    <button class="btn btn-success" onclick="exportar('medicos', 'excel')">📊 Excel</button>
                </div>
            </div>

            <!-- Reporte de Agenda -->
            <div class="report-card">
                <div class="report-header">
                    <div class="report-icon">📅</div>
                    <div>
                        <h4 class="mb-0">Reporte de Agenda</h4>
                        <small class="text-muted">Total: <?php echo $stats['citas']; ?> registros</small>
                    </div>
                </div>
                <form id="formAgenda">
                    <input type="hidden" name="tipo" value="agenda">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="">Todos</option>
                                <option value="Programada">Programada</option>
                                <option value="Completada">Completada</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="text-end">
                    <button class="btn btn-danger" onclick="exportar('agenda', 'pdf')">📄 PDF</button>
                    <button class="btn btn-success" onclick="exportar('agenda', 'excel')">📊 Excel</button>
                </div>
            </div>

            <!-- Reporte de Bitácora -->
            <div class="report-card">
                <div class="report-header">
                    <div class="report-icon">📝</div>
                    <div>
                        <h4 class="mb-0">Reporte de Bitácora de Acceso</h4>
                        <small class="text-muted">Total: <?php echo $stats['bitacoras']; ?> registros</small>
                    </div>
                </div>
                <form id="formBitacora">
                    <input type="hidden" name="tipo" value="bitacora">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="rol">
                                <option value="">Todos</option>
                                <option value="Admin">Admin</option>
                                <option value="Secretaria">Secretaria</option>
                                <option value="Paciente">Paciente</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="text-end">
                    <button class="btn btn-danger" onclick="exportar('bitacora', 'pdf')">📄 PDF</button>
                    <button class="btn btn-success" onclick="exportar('bitacora', 'excel')">📊 Excel</button>
                </div>
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportar(tipo, formato) {
            const form = document.getElementById('form' + tipo.charAt(0).toUpperCase() + tipo.slice(1));
            const formData = new FormData(form);
            formData.append('formato', formato);
            
            const params = new URLSearchParams(formData);
            window.open('generar_reporte.php?' + params.toString(), '_blank');
        }
    </script>
</body>
</html>

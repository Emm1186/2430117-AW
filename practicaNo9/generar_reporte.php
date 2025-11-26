<?php
/**
 * GENERAR REPORTE - Versión simplificada
 * PDF: Usa HTML que el navegador puede imprimir a PDF
 * Excel: Genera CSV
 */

require_once 'Conexion/conexion.php';

verificar_acceso(['Admin', 'Secretaria']);

if (!sesion_activa()) {
    die('Acceso no autorizado');
}

$tipo = $_GET['tipo'] ?? '';
$formato = $_GET['formato'] ?? 'pdf';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$estatus = $_GET['estatus'] ?? '';
$genero = $_GET['genero'] ?? '';
$estado = $_GET['estado'] ?? '';
$rol = $_GET['rol'] ?? '';

// Obtener datos según tipo
$datos = [];
$titulo = '';

switch($tipo) {
    case 'pagos':
        $titulo = 'Reporte de Pagos';
        $sql = "SELECT gp.IdPago, gp.FechaPago, p.NombreCompleto as Paciente, 
                gp.Monto, gp.MetodoPago, gp.Referencia, gp.EstatusPago
                FROM gestorpagos gp
                LEFT JOIN controlpacientes p ON gp.IdPaciente = p.IdPaciente WHERE 1=1";
        if ($fecha_inicio) $sql .= " AND DATE(gp.FechaPago) >= '$fecha_inicio'";
        if ($fecha_fin) $sql .= " AND DATE(gp.FechaPago) <= '$fecha_fin'";
        if ($estatus) $sql .= " AND gp.EstatusPago = '$estatus'";
        $sql .= " ORDER BY gp.FechaPago DESC";
        break;
        
    case 'pacientes':
        $titulo = 'Reporte de Pacientes';
        $sql = "SELECT IdPaciente, NombreCompleto, FechaNacimiento, Genero, 
                Telefono, Correo, Direccion, Estatus FROM controlpacientes WHERE 1=1";
        if ($genero) $sql .= " AND Genero = '$genero'";
        if ($estatus !== '') $sql .= " AND Estatus = " . intval($estatus);
        $sql .= " ORDER BY NombreCompleto";
        break;
        
    case 'medicos':
        $titulo = 'Reporte de Médicos';
        $sql = "SELECT IdMedico, NombreCompleto, Especialidad, Telefono, Correo, Cedula, Estatus 
                FROM controlmedico WHERE 1=1";
        if ($estatus !== '') $sql .= " AND Estatus = " . intval($estatus);
        $sql .= " ORDER BY NombreCompleto";
        break;
        
    case 'agenda':
        $titulo = 'Reporte de Agenda';
        $sql = "SELECT ca.IdCita, ca.FechaCita, p.NombreCompleto as Paciente,
                m.NombreCompleto as Medico, ca.MotivoConsulta, ca.EstadoCita
                FROM controlagenda ca
                INNER JOIN controlpacientes p ON ca.IdPaciente = p.IdPaciente
                INNER JOIN controlmedico m ON ca.IdMedico = m.IdMedico WHERE 1=1";
        if ($fecha_inicio) $sql .= " AND DATE(ca.FechaCita) >= '$fecha_inicio'";
        if ($fecha_fin) $sql .= " AND DATE(ca.FechaCita) <= '$fecha_fin'";
        if ($estado) $sql .= " AND ca.EstadoCita = '$estado'";
        $sql .= " ORDER BY ca.FechaCita DESC";
        break;
        
    case 'bitacora':
        $titulo = 'Reporte de Bitácora de Acceso';
        $sql = "SELECT b.IdBitacora, b.FechaAcceso, u.Nombre, u.Correo, u.Rol,
                b.AccionRealizada, b.Modulo
                FROM bitacoraacceso b
                INNER JOIN usuarios u ON b.IdUsuario = u.IdUsuario WHERE 1=1";
        if ($fecha_inicio) $sql .= " AND DATE(b.FechaAcceso) >= '$fecha_inicio'";
        if ($fecha_fin) $sql .= " AND DATE(b.FechaAcceso) <= '$fecha_fin'";
        if ($rol) $sql .= " AND u.Rol = '$rol'";
        $sql .= " ORDER BY b.FechaAcceso DESC";
        break;
        
    default:
        die('Tipo de reporte no válido');
}

$resultado = $conexion->query($sql);
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $datos[] = $row;
    }
}

if ($formato == 'excel') {
    generarExcel($tipo, $titulo, $datos);
} else {
    generarPDF($tipo, $titulo, $datos);
}

$conexion->close();

function generarExcel($tipo, $titulo, $datos) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="reporte_' . $tipo . '_' . date('Ymd') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
    
    fputcsv($output, [$titulo]);
    fputcsv($output, ['Fecha: ' . date('d/m/Y H:i')]);
    fputcsv($output, []);
    
    if (!empty($datos)) {
        fputcsv($output, array_keys($datos[0]));
        foreach ($datos as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit;
}

function generarPDF($tipo, $titulo, $datos) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $titulo; ?></title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { text-align: center; color: #333; }
            .fecha { text-align: center; color: #666; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background: #4CAF50; color: white; padding: 10px; text-align: left; }
            td { border: 1px solid #ddd; padding: 8px; }
            tr:nth-child(even) { background: #f2f2f2; }
            @media print {
                button { display: none; }
            }
        </style>
    </head>
    <body>
        <h1><?php echo $titulo; ?></h1>
        <div class="fecha">Fecha de generación: <?php echo date('d/m/Y H:i'); ?></div>
        
        <button onclick="window.print()" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; margin-bottom: 10px;">
            Imprimir / Guardar como PDF
        </button>
        
        <?php if (!empty($datos)): ?>
        <table>
            <thead>
                <tr>
                    <?php foreach (array_keys($datos[0]) as $columna): ?>
                        <th><?php echo htmlspecialchars($columna); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $row): ?>
                <tr>
                    <?php foreach ($row as $valor): ?>
                        <td><?php echo htmlspecialchars($valor); ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="text-align: center; color: #999;">No hay datos para mostrar</p>
        <?php endif; ?>
    </body>
    </html>
    <?php
    exit;
}
?>

<?php
// Incluir conexión a base de datos
include 'conexion.php';

$mensaje = "";

// Agregar institución
if ($_POST['accion'] == 'add_inst') {
    $nombre = $_POST['inst_nombre'];
    $conexion->query("INSERT INTO institucionesbancarias (NombreInstitucion) VALUES ('$nombre')");
    $mensaje = "Institución agregada";
}

// Eliminar institución
if ($_POST['accion'] == 'del_inst') {
    $id = $_POST['inst_id'];
    $conexion->query("DELETE FROM institucionesbancarias WHERE IdInstitucion = $id");
    $mensaje = "Institución eliminada";
}

// Agregar cliente
if ($_POST['accion'] == 'add_client') {
    $rfc = $_POST['rfc'];
    $nombre = $_POST['nombre'];
    $dir = $_POST['direccion'];
    $clabe = $_POST['clabe'];
    $inst = $_POST['institucion'];
    $saldo = $_POST['saldo'];
    
    $sql = "INSERT INTO clientesbancarios (RFC, Nombre, Direccion, CLABE, IdInstitucion, SaldoBancario) VALUES ('$rfc', '$nombre', '$dir', '$clabe', $inst, $saldo)";
    if ($conexion->query($sql)) {
        $mensaje = "Cliente agregado";
    } else {
        $mensaje = "Error: " . $conexion->error;
    }
}

// Eliminar cliente
if ($_POST['accion'] == 'del_client') {
    $id = $_POST['client_id'];
    $conexion->query("DELETE FROM clientesbancarios WHERE IdCliente = $id");
    $mensaje = "Cliente eliminado";
}

// Obtener instituciones y clientes
$inst_result = $conexion->query("SELECT * FROM institucionesbancarias");
$client_result = $conexion->query("SELECT c.*, i.NombreInstitucion FROM clientesbancarios c LEFT JOIN institucionesbancarias i ON c.IdInstitucion = i.IdInstitucion");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sistema Bancario</title>
</head>
<body>
    <h1>Sistema Bancario</h1>
    <?php if ($mensaje) echo "<p>$mensaje</p>"; ?>

    <h2>Instituciones Bancarias</h2>
    
    <h3>Agregar Institución</h3>
    <form method="POST">
        <input type="hidden" name="accion" value="add_inst">
        <input type="text" name="inst_nombre" placeholder="Nombre institución" required>
        <button type="submit">Agregar</button>
    </form>

    <h3>Listado</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acción</th>
        </tr>
        <?php while ($row = $inst_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['IdInstitucion']; ?></td>
            <td><?php echo $row['NombreInstitucion']; ?></td>
            <td>
                <!-- Formulario para eliminar con confirmación -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar?');">
                    <input type="hidden" name="accion" value="del_inst">
                    <input type="hidden" name="inst_id" value="<?php echo $row['IdInstitucion']; ?>">
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <hr>

    <h2>Clientes Bancarios</h2>
    
    <h3>Agregar Cliente</h3>
    <form method="POST">
        <input type="hidden" name="accion" value="add_client">
        RFC: <input type="text" name="rfc" maxlength="13" required><br>
        Nombre: <input type="text" name="nombre" required><br>
        Dirección: <input type="text" name="direccion" required><br>
        CLABE: <input type="text" name="clabe" maxlength="17" required><br>
        Institución: <select name="institucion" required>
            <option value="">-- Seleccionar --</option>
            <?php 
            // Cargar instituciones dinámicamente
            $inst_result2 = $conexion->query("SELECT * FROM institucionesbancarias");
            while ($inst = $inst_result2->fetch_assoc()): 
            ?>
            <option value="<?php echo $inst['IdInstitucion']; ?>"><?php echo $inst['NombreInstitucion']; ?></option>
            <?php endwhile; ?>
        </select><br>
        Saldo: <input type="number" name="saldo" step="0.01" required><br>
        <button type="submit">Agregar</button>
    </form>

    <h3>Listado</h3>
    <table border="1">
        <tr>
            <th>RFC</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>CLABE</th>
            <th>Institución</th>
            <th>Saldo</th>
            <th>Acción</th>
        </tr>
        <?php while ($row = $client_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['RFC']; ?></td>
            <td><?php echo $row['Nombre']; ?></td>
            <td><?php echo $row['Direccion']; ?></td>
            <td><?php echo $row['CLABE']; ?></td>
            <td><?php echo $row['NombreInstitucion']; ?></td>
            <td><?php echo $row['SaldoBancario']; ?></td>
            <td>
                <!-- Formulario para eliminar con confirmación -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar?');">
                    <input type="hidden" name="accion" value="del_client">
                    <input type="hidden" name="client_id" value="<?php echo $row['IdCliente']; ?>">
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>

<?php
require_once 'Conexion/conexion.php';

echo "<h1>Actualización de Base de Datos</h1>";

// Add IdMedico column if it doesn't exist
$sql = "SHOW COLUMNS FROM gestortarifas LIKE 'IdMedico'";
$result = $conexion->query($sql);

if ($result->num_rows == 0) {
    echo "<p>Agregando columna <code>IdMedico</code>...</p>";
    $sql_alter = "ALTER TABLE gestortarifas ADD COLUMN IdMedico INT(11) DEFAULT NULL AFTER EspecialidadId";
    if ($conexion->query($sql_alter) === TRUE) {
        echo "<p style='color:green'>Columna <code>IdMedico</code> agregada correctamente.</p>";
        
        // Add Foreign Key
        $sql_fk = "ALTER TABLE gestortarifas ADD CONSTRAINT gestortarifas_ibfk_2 FOREIGN KEY (IdMedico) REFERENCES controlmedico(IdMedico)";
        if ($conexion->query($sql_fk) === TRUE) {
            echo "<p style='color:green'>Llave foránea agregada correctamente.</p>";
        } else {
            echo "<p style='color:red'>Error agregando llave foránea: " . $conexion->error . "</p>";
        }
    } else {
        echo "<p style='color:red'>Error agregando columna: " . $conexion->error . "</p>";
    }
} else {
    echo "<p style='color:blue'>La columna <code>IdMedico</code> ya existe. No se requieren cambios.</p>";
}

echo "<p><a href='tarifas.php'>Ir a Gestor de Tarifas</a></p>";

$conexion->close();
?>

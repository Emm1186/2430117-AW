<?php
require_once 'Conexion/conexion.php';

// Add IdMedico column if it doesn't exist
$sql = "SHOW COLUMNS FROM gestortarifas LIKE 'IdMedico'";
$result = $conexion->query($sql);

if ($result->num_rows == 0) {
    echo "Adding IdMedico column...\n";
    $sql_alter = "ALTER TABLE gestortarifas ADD COLUMN IdMedico INT(11) DEFAULT NULL AFTER EspecialidadId";
    if ($conexion->query($sql_alter) === TRUE) {
        echo "Column IdMedico added successfully.\n";
        
        // Add Foreign Key
        $sql_fk = "ALTER TABLE gestortarifas ADD CONSTRAINT gestortarifas_ibfk_2 FOREIGN KEY (IdMedico) REFERENCES controlmedico(IdMedico)";
        if ($conexion->query($sql_fk) === TRUE) {
            echo "Foreign key added successfully.\n";
        } else {
            echo "Error adding foreign key: " . $conexion->error . "\n";
        }
    } else {
        echo "Error adding column: " . $conexion->error . "\n";
    }
} else {
    echo "Column IdMedico already exists.\n";
}

$conexion->close();
?>

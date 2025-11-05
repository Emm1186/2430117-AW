<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repositorio de Actividades</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>

<header>
    <h1>Repositorio de Actividades</h1>
    <span class="autor">Emm</span>
</header>

<div class="container">
    <div id="lista-proyectos">
        <?php
            // Carpeta donde están las prácticas
            $carpeta = 'practicas/';

            // Verifica si existe la carpeta
            if (is_dir($carpeta)) {
                // Escanea el contenido
                $archivos = scandir($carpeta);

                // Recorre y muestra solo carpetas válidas
                foreach ($archivos as $archivo) {
                    if ($archivo !== '.' && $archivo !== '..') {
                        // Variable que puede usar la plantilla
                        $nombre = $archivo;
                        $ruta = $carpeta . $archivo;
                        include 'plantilla.php';
                    }
                }
            } else {
                echo "<p style='text-align:center;'>No se encontró la carpeta <b>practicas/</b>.</p>";
            }
        ?>
    </div>
</div>

</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($titulo) ?></h1>
        <div id="lista-proyectos">
            <?php foreach ($proyectos as $proyecto): ?>
                <a class="proyecto-btn" href="<?= htmlspecialchars($proyecto) ?>/">
                    <?= htmlspecialchars($proyecto) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
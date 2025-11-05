<?php

$titulo = "Mis Proyectos";
$items_a_ignorar = ['.', '..', '.git', 'plantilla.php'];
$items = scandir('.');
$proyectos = [];

foreach ($items as $item) {
    if (is_dir($item) && !in_array($item, $items_a_ignorar)) {
        $proyectos[] = $item;
    }
}

sort($proyectos);
require 'plantilla.php';

?>
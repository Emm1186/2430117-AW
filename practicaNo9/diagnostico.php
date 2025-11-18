<?php
/**
 * DIAGNÓSTICO TEMPORAL - Sector 404
 * Verifica conexión BD, sesiones, usuarios y errores comunes
 * 
 * USO: Sube este archivo al droplet, accede vía navegador y luego BÓRRALO
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Diagnóstico - Sector 404</h1>";
echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";

// 1) Verificar PHP y extensiones
echo "=== PHP INFO ===\n";
echo "Versión PHP: " . phpversion() . "\n";
echo "mysqli disponible: " . (extension_loaded('mysqli') ? '✓ SÍ' : '✗ NO') . "\n";
echo "session disponible: " . (extension_loaded('session') ? '✓ SÍ' : '✗ NO') . "\n\n";

// 2) Verificar sesiones
echo "=== SESIONES ===\n";
@session_start();
echo "session_save_path: " . ini_get('session.save_path') . "\n";
echo "session_id: " . session_id() . "\n";
echo "session.use_cookies: " . ini_get('session.use_cookies') . "\n";
echo "_SESSION disponible: " . (isset($_SESSION) ? '✓ SÍ' : '✗ NO') . "\n\n";

// 3) Verificar conexión BD
echo "=== CONEXIÓN BASE DE DATOS ===\n";
require_once __DIR__ . '/Conexion/conexion.php';

if ($conexion->connect_error) {
    echo "❌ ERROR DE CONEXIÓN: " . $conexion->connect_error . "\n";
} else {
    echo "✓ Conexión OK\n";
    echo "Host: " . (defined('SERVIDOR') ? SERVIDOR : 'localhost') . "\n";
    echo "Base de datos: sector404\n";
    echo "Charset: " . $conexion->get_charset()->charset . "\n\n";
    
    // 4) Verificar tabla usuarios
    echo "=== TABLA USUARIOS ===\n";
    $r = $conexion->query("SELECT COUNT(*) AS total FROM usuarios");
    if ($r) {
        $f = $r->fetch_assoc();
        echo "Total usuarios: " . $f['total'] . "\n";
        
        // Mostrar usuarios
        $usuarios = $conexion->query("SELECT IdUsuario, Correo, Nombre, Activo FROM usuarios");
        while ($u = $usuarios->fetch_assoc()) {
            echo "  - {$u['IdUsuario']}: {$u['Correo']} ({$u['Nombre']}) - Activo: {$u['Activo']}\n";
        }
    } else {
        echo "❌ Error consultando usuarios: " . $conexion->error . "\n";
    }
    echo "\n";
    
    // 5) Listar todas las tablas disponibles
    echo "=== TABLAS DISPONIBLES ===\n";
    $tablas = $conexion->query("SHOW TABLES");
    if ($tablas) {
        $nombres_tablas = [];
        while ($t = $tablas->fetch_row()) {
            echo "  - {$t[0]}\n";
            $nombres_tablas[] = strtolower($t[0]);
        }
        echo "\n";
        
        // 6) Verificar tabla bitacora (con nombre flexible)
        echo "=== TABLA BITÁCORA ===\n";
        $tabla_bitacora = null;
        foreach ($nombres_tablas as $t) {
            if (strpos($t, 'bitacora') !== false || strpos($t, 'bitácora') !== false) {
                $tabla_bitacora = $t;
                break;
            }
        }
        
        if ($tabla_bitacora) {
            $r = $conexion->query("SELECT COUNT(*) AS total FROM " . $tabla_bitacora);
            if ($r) {
                $f = $r->fetch_assoc();
                echo "Total registros en '{$tabla_bitacora}': " . $f['total'] . "\n";
            } else {
                echo "❌ Error: " . $conexion->error . "\n";
            }
        } else {
            echo "⚠️ No se encontró tabla de bitácora.\n";
        }
    } else {
        echo "❌ Error listando tablas: " . $conexion->error . "\n";
    }
    echo "\n";
}

// 7) Verificar archivos críticos
echo "=== ARCHIVOS CRÍTICOS ===\n";
$archivos = [
    'Conexion/conexion.php' => __DIR__ . '/Conexion/conexion.php',
    'Entrada/login.php' => __DIR__ . '/Entrada/login.php',
    'dashboard.php' => __DIR__ . '/dashboard.php',
    'styles.css' => __DIR__ . '/styles.css',
];
foreach ($archivos as $nombre => $ruta) {
    echo "$nombre: " . (file_exists($ruta) ? '✓ EXISTE' : '✗ NO EXISTE') . "\n";
}
echo "\n";

// 7) Verificar permisos y paths
echo "=== PATHS Y PERMISOS ===\n";
echo "Directorio actual: " . __DIR__ . "\n";
echo "DocumentRoot (probable): " . dirname(__DIR__) . "\n";
echo "Permiso lectura login.php: " . (is_readable(__DIR__ . '/Entrada/login.php') ? '✓ SÍ' : '✗ NO') . "\n";
echo "Permiso lectura conexion.php: " . (is_readable(__DIR__ . '/Conexion/conexion.php') ? '✓ SÍ' : '✗ NO') . "\n\n";

// 8) Probar redirect (simular login)
echo "=== TEST REDIRECT ===\n";
echo "¿Se puede usar header()?: " . (headers_sent() ? '✗ NO (headers ya enviados)' : '✓ SÍ') . "\n";
echo "Ubicación dashboard.php: " . (file_exists(__DIR__ . '/dashboard.php') ? '✓ EXISTE' : '✗ NO EXISTE') . "\n\n";

// 9) Verificar variables de entorno (si existen)
echo "=== VARIABLES DE ENTORNO (si existen) ===\n";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'no definida') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ?: 'no definida') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'no definida') . "\n\n";

echo "</pre>";

echo "<h3>📋 Checklist de Acción:</h3>";
echo "<ul>";
echo "<li>Si ves ❌ arriba, nota el error exacto y reporta.</li>";
echo "<li>Prueba login manualmente con un usuario que conozcas (p. ej., correo: Eem@gmail.com con contraseña en texto plano).</li>";
echo "<li>Revisa los logs del servidor: <code>sudo tail -f /var/log/apache2/error.log</code> o <code>nginx error.log</code>.</li>";
echo "<li>Elimina este archivo cuando termines: <code>rm diagnostico.php</code></li>";
echo "</ul>";

$conexion->close();
?>
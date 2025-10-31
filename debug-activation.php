<?php
/**
 * Script temporal para depurar errores de activación
 *
 * INSTRUCCIONES:
 * 1. Copia este archivo a la raíz de tu WordPress
 * 2. Accede a: http://tudominio.com/debug-activation.php
 * 3. Verás el error exacto si hay algún problema con el plugin
 */

// Cargar WordPress
require_once('wp-load.php');

// Verificar que el usuario tenga permisos
if (!current_user_can('activate_plugins')) {
    die('No tienes permisos para ver esta información.');
}

echo '<h1>Debug de Activación - MTZ Slider</h1>';
echo '<style>body{font-family:Arial;padding:20px;} .error{color:red;} .success{color:green;} pre{background:#f0f0f0;padding:10px;border:1px solid #ccc;}</style>';

// 1. Verificar que WordPress está cargado
echo '<h2>1. Verificación de WordPress</h2>';
if (defined('ABSPATH')) {
    echo '<p class="success">✓ WordPress cargado correctamente</p>';
    echo '<p>ABSPATH: ' . ABSPATH . '</p>';
} else {
    echo '<p class="error">✗ WordPress no se cargó correctamente</p>';
}

// 2. Verificar constantes del plugin
echo '<h2>2. Verificación de Constantes del Plugin</h2>';
$plugin_file = ABSPATH . 'wp-content/plugins/mtz-slider/mtz-slider.php';
if (file_exists($plugin_file)) {
    echo '<p class="success">✓ Archivo del plugin encontrado</p>';
    echo '<p>Ruta: ' . $plugin_file . '</p>';

    // Incluir el plugin para verificar constantes
    if (!defined('MTZ_SLIDER_PLUGIN_DIR')) {
        define('MTZ_SLIDER_VERSION', '2.2.1');
        define('MTZ_SLIDER_PLUGIN_DIR', plugin_dir_path($plugin_file));
        define('MTZ_SLIDER_PLUGIN_URL', plugin_dir_url($plugin_file));
        define('MTZ_SLIDER_PLUGIN_FILE', $plugin_file);
    }

    echo '<p class="success">✓ Constantes definidas:</p>';
    echo '<ul>';
    echo '<li>MTZ_SLIDER_VERSION: ' . (defined('MTZ_SLIDER_VERSION') ? MTZ_SLIDER_VERSION : 'NO DEFINIDA') . '</li>';
    echo '<li>MTZ_SLIDER_PLUGIN_DIR: ' . (defined('MTZ_SLIDER_PLUGIN_DIR') ? MTZ_SLIDER_PLUGIN_DIR : 'NO DEFINIDA') . '</li>';
    echo '</ul>';
} else {
    echo '<p class="error">✗ Archivo del plugin NO encontrado en: ' . $plugin_file . '</p>';
}

// 3. Verificar archivo de base de datos
echo '<h2>3. Verificación de Archivos del Plugin</h2>';
$database_file = MTZ_SLIDER_PLUGIN_DIR . 'includes/class-mtz-slider-database.php';
if (file_exists($database_file)) {
    echo '<p class="success">✓ Archivo de base de datos encontrado</p>';

    // Intentar cargar el archivo
    ob_start();
    $error_occurred = false;
    try {
        require_once $database_file;
        $output = ob_get_clean();

        if (class_exists('MTZ_Slider_Database')) {
            echo '<p class="success">✓ Clase MTZ_Slider_Database cargada correctamente</p>';
        } else {
            echo '<p class="error">✗ La clase MTZ_Slider_Database NO existe después de cargar el archivo</p>';
            $error_occurred = true;
        }

        if (!empty($output)) {
            echo '<p><strong>Salida del archivo:</strong></p>';
            echo '<pre>' . esc_html($output) . '</pre>';
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo '<p class="error">✗ Error al cargar el archivo: ' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
        $error_occurred = true;
    } catch (Error $e) {
        ob_end_clean();
        echo '<p class="error">✗ Error fatal al cargar el archivo: ' . $e->getMessage() . '</p>';
        echo '<p><strong>Archivo:</strong> ' . $e->getFile() . '</p>';
        echo '<p><strong>Línea:</strong> ' . $e->getLine() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
        $error_occurred = true;
    }
} else {
    echo '<p class="error">✗ Archivo de base de datos NO encontrado en: ' . $database_file . '</p>';
    $error_occurred = true;
}

// 4. Verificar versión de PHP
echo '<h2>4. Verificación de Versión de PHP</h2>';
$php_version = phpversion();
echo '<p>Versión de PHP: ' . $php_version . '</p>';
if (version_compare($php_version, '7.4', '>=')) {
    echo '<p class="success">✓ Versión de PHP compatible (requiere 7.4+)</p>';
} else {
    echo '<p class="error">✗ Versión de PHP incompatible (requiere 7.4+, tienes ' . $php_version . ')</p>';
}

// 5. Verificar versión de WordPress
echo '<h2>5. Verificación de WordPress</h2>';
global $wp_version;
echo '<p>Versión de WordPress: ' . $wp_version . '</p>';
if (version_compare($wp_version, '5.8', '>=')) {
    echo '<p class="success">✓ Versión de WordPress compatible (requiere 5.8+)</p>';
} else {
    echo '<p class="error">✗ Versión de WordPress incompatible (requiere 5.8+, tienes ' . $wp_version . ')</p>';
}

// 6. Intentar simular la activación
echo '<h2>6. Simulación de Activación</h2>';
if (!$error_occurred && class_exists('MTZ_Slider_Database')) {
    try {
        global $wpdb;
        $database = new MTZ_Slider_Database();
        echo '<p class="success">✓ Instancia de MTZ_Slider_Database creada</p>';

        // Verificar permisos de BD sin crear tablas realmente
        echo '<p>Verificando permisos de base de datos...</p>';
        $test_query = "SHOW TABLES LIKE 'test_table_check'";
        $result = $wpdb->query($test_query);
        if ($result !== false) {
            echo '<p class="success">✓ Permisos de base de datos OK</p>';
        } else {
            echo '<p class="error">✗ Problema con permisos de base de datos</p>';
        }

    } catch (Exception $e) {
        echo '<p class="error">✗ Error al crear instancia: ' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } catch (Error $e) {
        echo '<p class="error">✗ Error fatal al crear instancia: ' . $e->getMessage() . '</p>';
        echo '<p><strong>Archivo:</strong> ' . $e->getFile() . '</p>';
        echo '<p><strong>Línea:</strong> ' . $e->getLine() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
} else {
    echo '<p class="error">No se puede simular la activación porque hay errores previos</p>';
}

// 7. Mostrar errores de PHP si existen
echo '<h2>7. Errores de PHP</h2>';
$error_log = ABSPATH . 'wp-content/debug.log';
if (file_exists($error_log)) {
    $log_content = file_get_contents($error_log);
    $recent_errors = array_slice(explode("\n", $log_content), -20);
    echo '<p>Últimos 20 errores del log:</p>';
    echo '<pre>' . esc_html(implode("\n", $recent_errors)) . '</pre>';
} else {
    echo '<p>No se encontró debug.log. ¿Tienes WP_DEBUG_LOG habilitado en wp-config.php?</p>';
}

echo '<hr>';
echo '<h2>Instrucciones para Habilitar Debug</h2>';
echo '<ol>';
echo '<li>Abre el archivo <code>wp-config.php</code> en la raíz de WordPress</li>';
echo '<li>Agrega estas líneas ANTES de "That\'s all, stop editing!":</li>';
echo '<pre>';
echo "define('WP_DEBUG', true);\n";
echo "define('WP_DEBUG_LOG', true);\n";
echo "define('WP_DEBUG_DISPLAY', false);\n";
echo "define('SCRIPT_DEBUG', true);\n";
echo '</pre>';
echo '<li>Guarda el archivo</li>';
echo '<li>Intenta activar el plugin nuevamente</li>';
echo '<li>Revisa el archivo <code>wp-content/debug.log</code> para ver el error exacto</li>';
echo '</ol>';


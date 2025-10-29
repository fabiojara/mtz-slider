<?php
/**
 * Script temporal para debug del contador de imágenes
 */

require_once('../../../../wp-load.php');

global $wpdb;
$images_table = $wpdb->prefix . 'mtz_slider_images';
$sliders_table = $wpdb->prefix . 'mtz_slider_sliders';

echo "<h1>Debug: Contador de Imágenes</h1>";

// Verificar sliders
echo "<h2>Sliders:</h2>";
$sliders = $wpdb->get_results("SELECT * FROM {$sliders_table}");
foreach ($sliders as $slider) {
    echo "ID: {$slider->id}, Nombre: {$slider->name}<br>";
}

// Verificar imágenes del slider ID 2
echo "<h2>Imágenes del Slider ID 2:</h2>";
$images = $wpdb->get_results("SELECT * FROM {$images_table} WHERE slider_id = 2");
echo "Total encontradas: " . count($images) . "<br>";

foreach ($images as $image) {
    echo "ID: {$image->id}, URL: {$image->image_url}<br>";
}

// Contador con query
$count = $wpdb->get_var("SELECT COUNT(*) FROM {$images_table} WHERE slider_id = 2 AND is_active = 1");
echo "<h2>Contador de la query:</h2>";
echo "Count: {$count}<br>";


<?php
/**
 * Manejo de base de datos para MTZ Slider
 *
 * @package MTZ_Slider
 */

if (!defined('ABSPATH')) {
    exit;
}

class MTZ_Slider_Database {

    /**
     * Tablas
     */
    private $sliders_table;
    private $images_table;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->sliders_table = $wpdb->prefix . 'mtz_slider_sliders';
        $this->images_table = $wpdb->prefix . 'mtz_slider_images';
    }

    /**
     * Crear tablas en la base de datos
     */
    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Tabla de sliders
        $sql_sliders = "CREATE TABLE IF NOT EXISTS {$this->sliders_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            autoplay tinyint(1) DEFAULT 1,
            speed int(11) DEFAULT 5000,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Tabla de imágenes (con referencia al slider)
        $sql_images = "CREATE TABLE IF NOT EXISTS {$this->images_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            slider_id bigint(20) unsigned NOT NULL,
            image_id bigint(20) unsigned NOT NULL,
            image_url varchar(255) NOT NULL,
            image_title varchar(255) DEFAULT '',
            image_description text DEFAULT '',
            image_alt varchar(255) DEFAULT '',
            sort_order int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY slider_id (slider_id),
            KEY image_id (image_id),
            KEY is_active (is_active),
            KEY sort_order (sort_order)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_sliders);
        dbDelta($sql_images);

        // Actualizar estructura si la tabla existe sin slider_id
        $this->update_table_structure();

        // Migración de datos antiguos si existe
        $this->migrate_old_data();
    }

    /**
     * Actualizar estructura de la tabla de imágenes
     */
    private function update_table_structure() {
        global $wpdb;

        // Verificar si la columna slider_id existe
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM {$this->images_table} LIKE 'slider_id'");

        if (empty($column_exists)) {
            // Agregar la columna slider_id
            $wpdb->query("ALTER TABLE {$this->images_table} ADD COLUMN slider_id bigint(20) unsigned NOT NULL DEFAULT 1 AFTER id");

            // Agregar índice para slider_id
            $wpdb->query("ALTER TABLE {$this->images_table} ADD INDEX slider_id (slider_id)");

            error_log('Columna slider_id agregada a la tabla wp_mtz_slider_images');
        }
    }

    /**
     * Migrar datos de versión antigua
     */
    private function migrate_old_data() {
        global $wpdb;

        // Verificar si hay datos en la tabla antigua
        $old_table = $wpdb->prefix . 'mtz_slider_images';
        $old_images = $wpdb->get_results("SELECT * FROM {$old_table} WHERE slider_id IS NULL OR slider_id = 0");

        if (!empty($old_images)) {
            // Crear un slider por defecto
            $default_slider = $wpdb->insert(
                $this->sliders_table,
                array(
                    'name' => 'Slider Principal',
                    'autoplay' => 1,
                    'speed' => 5000,
                    'is_active' => 1
                ),
                array('%s', '%d', '%d', '%d')
            );

            if ($default_slider) {
                $slider_id = $wpdb->insert_id;

                // Migrar imágenes al nuevo slider
                foreach ($old_images as $image) {
                    $wpdb->update(
                        $old_table,
                        array('slider_id' => $slider_id),
                        array('id' => $image->id),
                        array('%d'),
                        array('%d')
                    );
                }
            }
        }
    }

    // ==================================================
    // MÉTODOS PARA SLIDERS
    // ==================================================

    /**
     * Obtener todos los sliders
     */
    public function get_sliders() {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * FROM {$this->sliders_table} ORDER BY created_at DESC",
            ARRAY_A
        );
    }

    /**
     * Obtener slider por ID
     */
    public function get_slider($id) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->sliders_table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );
    }

    /**
     * Crear nuevo slider
     */
    public function create_slider($name, $autoplay = 1, $speed = 5000) {
        global $wpdb;

        // Asegurarse de que las tablas existan
        $this->ensure_tables_exist();

        $result = $wpdb->insert(
            $this->sliders_table,
            array(
                'name' => sanitize_text_field($name),
                'autoplay' => intval($autoplay),
                'speed' => intval($speed),
                'is_active' => 1
            ),
            array('%s', '%d', '%d', '%d')
        );

        if ($result === false) {
            error_log('Error al crear slider: ' . $wpdb->last_error);
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Asegurar que las tablas existan
     */
    private function ensure_tables_exist() {
        global $wpdb;

        // Verificar si las tablas existen
        $slider_table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$this->sliders_table}'");
        $images_table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$this->images_table}'");

        if (!$slider_table_exists || !$images_table_exists) {
            $this->create_tables();
        }
    }

    /**
     * Actualizar slider
     */
    public function update_slider($id, $data) {
        global $wpdb;

        $update_data = array();
        if (isset($data['name'])) {
            $update_data['name'] = sanitize_text_field($data['name']);
        }
        if (isset($data['autoplay'])) {
            $update_data['autoplay'] = intval($data['autoplay']);
        }
        if (isset($data['speed'])) {
            $update_data['speed'] = intval($data['speed']);
        }
        if (isset($data['is_active'])) {
            $update_data['is_active'] = intval($data['is_active']);
        }

        return $wpdb->update(
            $this->sliders_table,
            $update_data,
            array('id' => intval($id)),
            null,
            array('%d')
        );
    }

    /**
     * Eliminar slider
     */
    public function delete_slider($id) {
        global $wpdb;

        // Primero eliminar todas las imágenes del slider
        $wpdb->delete(
            $this->images_table,
            array('slider_id' => intval($id)),
            array('%d')
        );

        // Luego eliminar el slider
        return $wpdb->delete(
            $this->sliders_table,
            array('id' => intval($id)),
            array('%d')
        );
    }

    // ==================================================
    // MÉTODOS PARA IMÁGENES
    // ==================================================

    /**
     * Obtener imágenes de un slider
     */
    public function get_slider_images($slider_id) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT * FROM {$this->images_table}
            WHERE slider_id = %d AND is_active = %d
            ORDER BY sort_order ASC, created_at ASC",
            $slider_id,
            1
        );

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Obtener imagen por ID
     */
    public function get_image($id) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->images_table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );
    }

    /**
     * Insertar imagen
     */
    public function insert_image($slider_id, $data) {
        global $wpdb;

        // Asegurar que las tablas existan
        $this->ensure_tables_exist();

        $defaults = array(
            'image_id' => 0,
            'image_url' => '',
            'image_title' => '',
            'image_description' => '',
            'image_alt' => '',
            'sort_order' => 0,
            'is_active' => 1,
        );

        $data = wp_parse_args($data, $defaults);

        // Sanitizar datos
        $insert_data = array(
            'slider_id' => intval($slider_id),
            'image_id' => intval($data['image_id']),
            'image_url' => esc_url_raw($data['image_url']),
            'image_title' => sanitize_text_field($data['image_title']),
            'image_description' => sanitize_textarea_field($data['image_description']),
            'image_alt' => sanitize_text_field($data['image_alt']),
            'sort_order' => intval($data['sort_order']),
            'is_active' => intval($data['is_active']),
        );

        error_log('Intentando insertar imagen en slider_id: ' . $slider_id);
        error_log('Datos: ' . print_r($insert_data, true));

        $result = $wpdb->insert(
            $this->images_table,
            $insert_data,
            array('%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d')
        );

        if ($result === false) {
            error_log('Error al insertar imagen: ' . $wpdb->last_error);
            error_log('Query fallida: ' . $wpdb->last_query);
            return false;
        }

        error_log('Imagen insertada correctamente con ID: ' . $wpdb->insert_id);

        return $wpdb->insert_id;
    }

    /**
     * Actualizar imagen
     */
    public function update_image($id, $data) {
        global $wpdb;

        // Sanitizar datos
        $clean_data = array();
        foreach ($data as $key => $value) {
            if ($key === 'image_description') {
                $clean_data[$key] = sanitize_textarea_field($value);
            } elseif ($key === 'image_url') {
                $clean_data[$key] = esc_url_raw($value);
            } else {
                $clean_data[$key] = sanitize_text_field($value);
            }
        }

        return $wpdb->update(
            $this->images_table,
            $clean_data,
            array('id' => $id),
            null,
            array('%d')
        );
    }

    /**
     * Eliminar imagen
     */
    public function delete_image($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->images_table,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Actualizar orden de imágenes
     */
    public function update_image_order($images) {
        global $wpdb;

        $wpdb->query('START TRANSACTION');

        foreach ($images as $index => $image_id) {
            $wpdb->update(
                $this->images_table,
                array('sort_order' => $index),
                array('id' => intval($image_id)),
                array('%d'),
                array('%d')
            );
        }

        $wpdb->query('COMMIT');

        return true;
    }

    /**
     * Obtener el número de imágenes de un slider
     */
    public function get_image_count($slider_id) {
        global $wpdb;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->images_table} WHERE slider_id = %d AND is_active = 1",
                $slider_id
            )
        );
    }
}

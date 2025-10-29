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
     * Nombre de la tabla
     */
    private $table_name;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mtz_slider_images';
    }
    
    /**
     * Crear tablas en la base de datos
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
            KEY image_id (image_id),
            KEY is_active (is_active),
            KEY sort_order (sort_order)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Obtener todas las imágenes del slider
     */
    public function get_slider_images() {
        global $wpdb;
        
        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
            WHERE is_active = %d 
            ORDER BY sort_order ASC, created_at ASC",
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
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );
    }
    
    /**
     * Insertar imagen
     */
    public function insert_image($data) {
        global $wpdb;
        
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
        $data = array_map('sanitize_text_field', $data);
        $data['image_description'] = sanitize_textarea_field($data['image_description']);
        
        $result = $wpdb->insert(
            $this->table_name,
            $data,
            array('%d', '%s', '%s', '%s', '%s', '%d', '%d')
        );
        
        return $result ? $wpdb->insert_id : false;
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
            } else {
                $clean_data[$key] = sanitize_text_field($value);
            }
        }
        
        return $wpdb->update(
            $this->table_name,
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
            $this->table_name,
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
                $this->table_name,
                array('sort_order' => $index),
                array('id' => $image_id),
                array('%d'),
                array('%d')
            );
        }
        
        $wpdb->query('COMMIT');
        
        return true;
    }
}


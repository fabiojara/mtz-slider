<?php
/**
 * API REST para MTZ Slider
 *
 * @package MTZ_Slider
 */

if (!defined('ABSPATH')) {
    exit;
}

class MTZ_Slider_API {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Registrar rutas REST
     */
    public function register_routes() {
        $namespace = 'mtz-slider/v1';

        // ============ RUTAS PARA SLIDERS ============

        // Obtener todos los sliders
        register_rest_route($namespace, '/sliders', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_sliders'),
            'permission_callback' => array($this, 'check_permissions'),
        ));

        // Crear nuevo slider
        register_rest_route($namespace, '/sliders', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_slider'),
            'permission_callback' => array($this, 'check_permissions'),
        ));

        // Actualizar slider
        register_rest_route($namespace, '/sliders/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_slider'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'id' => array('required' => true, 'validate_callback' => function($param) {
                    return is_numeric($param);
                }),
            ),
        ));

        // Eliminar slider
        register_rest_route($namespace, '/sliders/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_slider'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'id' => array('required' => true, 'validate_callback' => function($param) {
                    return is_numeric($param);
                }),
            ),
        ));

        // ============ RUTAS PARA IMÁGENES ============

        // Obtener imágenes de un slider
        register_rest_route($namespace, '/sliders/(?P<id>\d+)/images', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_slider_images'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'id' => array('required' => true, 'validate_callback' => function($param) {
                    return is_numeric($param);
                }),
            ),
        ));

        // Crear nueva imagen
        register_rest_route($namespace, '/images', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_image'),
            'permission_callback' => array($this, 'check_permissions'),
        ));

        // Actualizar imagen
        register_rest_route($namespace, '/images/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_image'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'id' => array('required' => true, 'validate_callback' => function($param) {
                    return is_numeric($param);
                }),
            ),
        ));

        // Eliminar imagen
        register_rest_route($namespace, '/images/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_image'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'id' => array('required' => true, 'validate_callback' => function($param) {
                    return is_numeric($param);
                }),
            ),
        ));

        // Actualizar orden de imágenes
        register_rest_route($namespace, '/images/order', array(
            'methods' => 'POST',
            'callback' => array($this, 'update_order'),
            'permission_callback' => array($this, 'check_permissions'),
        ));
    }

    /**
     * Verificar permisos
     */
    public function check_permissions() {
        return current_user_can('manage_options');
    }

    // ============ MÉTODOS PARA SLIDERS ============

    /**
     * Obtener sliders
     */
    public function get_sliders() {
        $database = new MTZ_Slider_Database();
        $sliders = $database->get_sliders();

        // Agregar contador de imágenes a cada slider
        foreach ($sliders as &$slider) {
            $img_count = $database->get_image_count($slider['id']);
            $slider['image_count'] = intval($img_count);
            $slider['shortcode'] = '[mtz_slider id="' . $slider['id'] . '"]';
        }

        return new WP_REST_Response($sliders, 200);
    }

    /**
     * Crear slider
     */
    public function create_slider($request) {
        $database = new MTZ_Slider_Database();

        $params = $request->get_json_params();

        $name = isset($params['name']) ? sanitize_text_field($params['name']) : 'Nuevo Slider';
        $autoplay = isset($params['autoplay']) ? intval($params['autoplay']) : 1;
        $speed = isset($params['speed']) ? intval($params['speed']) : 5000;

        $result = $database->create_slider($name, $autoplay, $speed);

        if ($result) {
            $slider = $database->get_slider($result);
            $slider['shortcode'] = '[mtz_slider id="' . $result . '"]';
            return new WP_REST_Response(array('success' => true, 'data' => $slider), 201);
        } else {
            return new WP_Error('create_failed', 'Error al crear el slider', array('status' => 500));
        }
    }

    /**
     * Actualizar slider
     */
    public function update_slider($request) {
        $database = new MTZ_Slider_Database();

        $id = $request->get_param('id');
        $params = $request->get_json_params();

        $result = $database->update_slider($id, $params);

        if ($result !== false) {
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('update_failed', 'Error al actualizar el slider', array('status' => 500));
        }
    }

    /**
     * Eliminar slider
     */
    public function delete_slider($request) {
        $database = new MTZ_Slider_Database();

        $id = $request->get_param('id');
        $result = $database->delete_slider($id);

        if ($result) {
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('delete_failed', 'Error al eliminar el slider', array('status' => 500));
        }
    }

    // ============ MÉTODOS PARA IMÁGENES ============

    /**
     * Obtener imágenes de un slider
     */
    public function get_slider_images($request) {
        $database = new MTZ_Slider_Database();
        $slider_id = $request->get_param('id');
        $images = $database->get_slider_images($slider_id);

        return new WP_REST_Response($images, 200);
    }

    /**
     * Crear imagen
     */
    public function create_image($request) {
        $database = new MTZ_Slider_Database();

        $params = $request->get_json_params();

        error_log('API create_image - Parámetros recibidos: ' . print_r($params, true));

        $slider_id = isset($params['slider_id']) ? intval($params['slider_id']) : 0;

        if (!$slider_id) {
            error_log('Error: slider_id no proporcionado');
            return new WP_Error('invalid_data', 'slider_id es requerido', array('status' => 400));
        }

        $data = array(
            'image_id' => isset($params['image_id']) ? intval($params['image_id']) : 0,
            'image_url' => isset($params['image_url']) ? esc_url_raw($params['image_url']) : '',
            'link_url' => isset($params['link_url']) ? esc_url_raw($params['link_url']) : '',
            'image_title' => isset($params['image_title']) ? sanitize_text_field($params['image_title']) : '',
            'image_description' => isset($params['image_description']) ? sanitize_textarea_field($params['image_description']) : '',
            'image_alt' => isset($params['image_alt']) ? sanitize_text_field($params['image_alt']) : '',
            'sort_order' => isset($params['sort_order']) ? intval($params['sort_order']) : 0,
            'is_active' => isset($params['is_active']) ? intval($params['is_active']) : 1,
        );

        error_log('API create_image - Intentando insertar imagen con data: ' . print_r($data, true));

        $result = $database->insert_image($slider_id, $data);

        if ($result) {
            error_log('API create_image - Imagen creada exitosamente con ID: ' . $result);
            return new WP_REST_Response(array('success' => true, 'id' => $result), 201);
        } else {
            error_log('API create_image - Error al crear la imagen');
            return new WP_Error('create_failed', 'Error al crear la imagen', array('status' => 500));
        }
    }

    /**
     * Actualizar imagen
     */
    public function update_image($request) {
        $database = new MTZ_Slider_Database();

        $id = $request->get_param('id');
        $params = $request->get_json_params();

        $data = array();

        if (isset($params['image_title'])) {
            $data['image_title'] = sanitize_text_field($params['image_title']);
        }
        if (isset($params['image_description'])) {
            $data['image_description'] = sanitize_textarea_field($params['image_description']);
        }
        if (isset($params['image_alt'])) {
            $data['image_alt'] = sanitize_text_field($params['image_alt']);
        }
        if (isset($params['link_url'])) {
            $data['link_url'] = esc_url_raw($params['link_url']);
        }
        if (isset($params['sort_order'])) {
            $data['sort_order'] = intval($params['sort_order']);
        }
        if (isset($params['is_active'])) {
            $data['is_active'] = intval($params['is_active']);
        }

        $result = $database->update_image($id, $data);

        if ($result !== false) {
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('update_failed', 'Error al actualizar la imagen', array('status' => 500));
        }
    }

    /**
     * Eliminar imagen
     */
    public function delete_image($request) {
        $database = new MTZ_Slider_Database();

        $id = $request->get_param('id');
        $result = $database->delete_image($id);

        if ($result) {
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('delete_failed', 'Error al eliminar la imagen', array('status' => 500));
        }
    }

    /**
     * Actualizar orden
     */
    public function update_order($request) {
        $database = new MTZ_Slider_Database();

        $params = $request->get_json_params();
        $images = isset($params['images']) ? $params['images'] : array();

        if (!is_array($images)) {
            return new WP_Error('invalid_data', 'Datos inválidos', array('status' => 400));
        }

        $result = $database->update_image_order($images);

        if ($result) {
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('update_failed', 'Error al actualizar el orden', array('status' => 500));
        }
    }
}

// Inicializar API
new MTZ_Slider_API();

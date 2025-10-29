<?php
/**
 * Plugin Name: MTZ Slider
 * Plugin URI: https://github.com/tu-usuario/mtz-slider
 * Description: Slider moderno y responsive para WordPress con gestión de imágenes desde el panel administrativo
 * Version: 1.0.0
 * Author: Tu Nombre
 * Author URI: https://tusitio.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mtz-slider
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('MTZ_SLIDER_VERSION', '1.0.0');
define('MTZ_SLIDER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MTZ_SLIDER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MTZ_SLIDER_PLUGIN_FILE', __FILE__);

/**
 * Clase principal del plugin
 */
class MTZ_Slider {

    private static $instance = null;

    /**
     * Patrón Singleton
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Inicializar hooks
     */
    private function init_hooks() {
        // Hook de activación
        register_activation_hook(MTZ_SLIDER_PLUGIN_FILE, array($this, 'activate'));

        // Hook de desactivación
        register_deactivation_hook(MTZ_SLIDER_PLUGIN_FILE, array($this, 'deactivate'));

        // Cargar archivos
        add_action('plugins_loaded', array($this, 'load_dependencies'));

        // Cargar scripts y estilos
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));

        // Agregar menú de administración
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Registro de shortcode
        add_shortcode('mtz_slider', array($this, 'render_slider_shortcode'));
    }

    /**
     * Cargar dependencias
     */
    public function load_dependencies() {
        require_once MTZ_SLIDER_PLUGIN_DIR . 'includes/class-mtz-slider-database.php';
        require_once MTZ_SLIDER_PLUGIN_DIR . 'includes/class-mtz-slider-api.php';
        require_once MTZ_SLIDER_PLUGIN_DIR . 'includes/class-mtz-slider-public.php';

        if (is_admin()) {
            require_once MTZ_SLIDER_PLUGIN_DIR . 'includes/class-mtz-slider-admin.php';
        }
    }

    /**
     * Activar plugin
     */
    public function activate() {
        // Cargar la clase de base de datos primero
        require_once MTZ_SLIDER_PLUGIN_DIR . 'includes/class-mtz-slider-database.php';

        $database = new MTZ_Slider_Database();
        $database->create_tables();
        flush_rewrite_rules();
    }

    /**
     * Desactivar plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Encolar scripts del administrador
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'mtz-slider') === false) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('mtz-slider-admin', MTZ_SLIDER_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-api'), MTZ_SLIDER_VERSION, true);
        wp_enqueue_style('mtz-slider-admin', MTZ_SLIDER_PLUGIN_URL . 'assets/css/admin.css', array(), MTZ_SLIDER_VERSION);

        wp_localize_script('mtz-slider-admin', 'mtzSlider', array(
            'apiUrl' => rest_url('mtz-slider/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'strings' => array(
                'selectImages' => __('Seleccionar imágenes', 'mtz-slider'),
                'addImages' => __('Agregar imágenes', 'mtz-slider'),
                'removeImage' => __('Eliminar imagen', 'mtz-slider'),
                'saveChanges' => __('Guardar cambios', 'mtz-slider'),
                'saved' => __('Cambios guardados', 'mtz-slider'),
                'error' => __('Error al guardar', 'mtz-slider'),
            )
        ));
    }

    /**
     * Encolar scripts públicos
     */
    public function enqueue_public_scripts() {
        wp_enqueue_style('mtz-slider-public', MTZ_SLIDER_PLUGIN_URL . 'assets/css/public.css', array(), MTZ_SLIDER_VERSION);
        wp_enqueue_script('mtz-slider-public', MTZ_SLIDER_PLUGIN_URL . 'assets/js/public.js', array('jquery'), MTZ_SLIDER_VERSION, true);

        wp_localize_script('mtz-slider-public', 'mtzSliderPublic', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mtz-slider-public'),
        ));
    }

    /**
     * Agregar menú de administración
     */
    public function add_admin_menu() {
        add_menu_page(
            __('MTZ Slider', 'mtz-slider'),
            __('MTZ Slider', 'mtz-slider'),
            'manage_options',
            'mtz-slider',
            array($this, 'render_admin_page'),
            'dashicons-images-alt2',
            30
        );
    }

    /**
     * Renderizar página de administración
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        include MTZ_SLIDER_PLUGIN_DIR . 'admin/views/admin-page.php';
    }

    /**
     * Renderizar shortcode del slider
     */
    public function render_slider_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'autoplay' => true,
            'speed' => 5000,
        ), $atts);

        $database = new MTZ_Slider_Database();
        $images = $database->get_slider_images();

        if (empty($images)) {
            return '';
        }

        ob_start();
        include MTZ_SLIDER_PLUGIN_DIR . 'public/views/slider.php';
        return ob_get_clean();
    }
}

// Inicializar plugin
MTZ_Slider::get_instance();


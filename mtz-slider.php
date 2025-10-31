<?php
/**
 * Plugin Name: MTZ Slider
 * Plugin URI: https://github.com/fabiojara/mtz-slider
 * Description: Slider moderno y responsive para WordPress. Crea múltiples sliders y gestiona imágenes desde el panel administrativo
 * Version: 2.3.5
 * Author: Fabio Jara
 * Author URI: https://github.com/fabiojara
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
define('MTZ_SLIDER_VERSION', '2.3.5');
define('MTZ_SLIDER_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
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

        // Hook para Elementor
        add_action('elementor/frontend/after_enqueue_scripts', array($this, 'enqueue_public_assets'));
        add_action('elementor/frontend/before_render', array($this, 'maybe_enqueue_for_elementor'));

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
            // Cargar actualizador solo en admin
            require_once MTZ_SLIDER_PLUGIN_DIR . 'includes/class-mtz-slider-updater.php';
            new MTZ_Slider_Updater();
        }
    }

    /**
     * Activar plugin
     */
    public function activate() {
        // Verificar que las constantes estén definidas
        if (!defined('MTZ_SLIDER_PLUGIN_DIR')) {
            wp_die(__('Error: Las constantes del plugin no están definidas correctamente.', 'mtz-slider'));
        }

        // Cargar la clase de base de datos primero
        $database_file = MTZ_SLIDER_PLUGIN_DIR . 'includes/class-mtz-slider-database.php';

        // Verificar que el archivo existe antes de intentar cargarlo
        if (!file_exists($database_file)) {
            // Intentar rutas alternativas por si hay problemas con la estructura
            $alternatives = array(
                dirname(MTZ_SLIDER_PLUGIN_DIR) . '/mtz-slider/includes/class-mtz-slider-database.php',
                dirname(dirname(MTZ_SLIDER_PLUGIN_DIR)) . '/mtz-slider/includes/class-mtz-slider-database.php',
            );

            $found = false;
            foreach ($alternatives as $alt_path) {
                if (file_exists($alt_path)) {
                    $database_file = $alt_path;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                wp_die(sprintf(
                    __('Error: No se encontró el archivo de base de datos. Ruta buscada: %s. Verifica que el plugin se haya instalado correctamente con todos sus archivos.', 'mtz-slider'),
                    $database_file
                ));
            }
        }

        require_once $database_file;

        // Verificar que la clase existe
        if (!class_exists('MTZ_Slider_Database')) {
            wp_die(__('Error: La clase MTZ_Slider_Database no se pudo cargar.', 'mtz-slider'));
        }

        try {
            $database = new MTZ_Slider_Database();
            $database->create_tables();
            flush_rewrite_rules();
        } catch (Exception $e) {
            wp_die(sprintf(__('Error al activar el plugin: %s', 'mtz-slider'), $e->getMessage()));
        }
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

        // Cargar fuente Poppins para el admin
        wp_enqueue_style('google-fonts-poppins', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);

        // Encolar Lucide Icons para el admin
        wp_enqueue_script('lucide', 'https://unpkg.com/lucide@latest/dist/umd/lucide.js', array(), 'latest', false);

        wp_enqueue_script('mtz-slider-admin', MTZ_SLIDER_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-api', 'lucide'), MTZ_SLIDER_VERSION, true);
        wp_enqueue_style('mtz-slider-admin', MTZ_SLIDER_PLUGIN_URL . 'assets/css/admin.css', array('google-fonts-poppins'), MTZ_SLIDER_VERSION);

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
        // Verificar en contenido del post y en Elementor
        if (!$this->page_has_slider_shortcode()) {
            return;
        }

        $this->enqueue_public_assets();
    }

    private function page_has_slider_shortcode() {
        if (is_admin()) return false;

        // Siempre cargar en frontend para evitar problemas con builders
        // Los assets solo se ejecutarán si hay un slider presente
        return true;
    }

    /**
     * Verificar si hay shortcode en Elementor antes de renderizar
     */
    public function maybe_enqueue_for_elementor($element) {
        if ($element->get_settings('text') && has_shortcode($element->get_settings('text'), 'mtz_slider')) {
            $this->enqueue_public_assets();
        }
    }

    private function enqueue_public_assets() {
        // Evitar cargar múltiples veces
        if (wp_style_is('mtz-slider-public', 'enqueued')) {
            return;
        }

        // Fuente Poppins
        wp_enqueue_style('google-fonts-poppins', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);

        // Cache busting por filemtime (o versión del plugin)
        $css_path = MTZ_SLIDER_PLUGIN_DIR . 'assets/css/public.css';
        $js_path  = MTZ_SLIDER_PLUGIN_DIR . 'assets/js/public.js';
        $css_ver  = file_exists($css_path) ? filemtime($css_path) : MTZ_SLIDER_VERSION;
        $js_ver   = file_exists($js_path) ? filemtime($js_path) : MTZ_SLIDER_VERSION;

        wp_enqueue_style('mtz-slider-public', MTZ_SLIDER_PLUGIN_URL . 'assets/css/public.css', array('google-fonts-poppins'), $css_ver);

        // Lucide Icons - cargar antes del slider script
        wp_enqueue_script('lucide', 'https://unpkg.com/lucide@latest/dist/umd/lucide.js', array(), 'latest', false);

        // Frontend sin jQuery
        wp_enqueue_script('mtz-slider-public', MTZ_SLIDER_PLUGIN_URL . 'assets/js/public.js', array('lucide'), $js_ver, true);

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
            __('MTZ Slider by Mantiz Technology SAS', 'mtz-slider'),
            __('MTZ Slider', 'mtz-slider'),
            'manage_options',
            'mtz-slider',
            array($this, 'render_admin_page'),
            'dashicons-images-alt2',
            30
        );

        // Submenu para actualizaciones
        add_submenu_page(
            'mtz-slider',
            __('Actualizaciones', 'mtz-slider'),
            __('Actualizaciones', 'mtz-slider'),
            'manage_options',
            'mtz-slider-updates',
            array($this, 'render_updates_page')
        );
    }

    /**
     * Renderizar página de administración
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Obtener sliders
        $database = new MTZ_Slider_Database();
        $sliders = $database->get_sliders();

        // Obtener el slider actual desde la URL
        $current_slider_id = isset($_GET['slider']) ? intval($_GET['slider']) : null;
        $current_slider = null;

        if ($current_slider_id) {
            $current_slider = $database->get_slider($current_slider_id);
        } elseif (!empty($sliders)) {
            $current_slider = $sliders[0];
            $current_slider_id = $current_slider['id'];
        }

        // Variables para la vista
        $view_data = array(
            'sliders' => $sliders,
            'current_slider' => $current_slider,
            'current_slider_id' => $current_slider_id,
        );

        extract($view_data);

        include MTZ_SLIDER_PLUGIN_DIR . 'admin/views/admin-page.php';
    }

    /**
     * Renderizar página de actualizaciones
     */
    public function render_updates_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Si hay parámetro refresh, borrar cache
        if (isset($_GET['refresh']) && $_GET['refresh'] === '1') {
            delete_transient('mtz_slider_all_releases');
        }

        // Obtener releases desde GitHub
        $releases = $this->get_github_releases();
        $current_version = MTZ_SLIDER_VERSION;

        // Obtener mensaje de error si existe
        $error_message = get_transient('mtz_slider_releases_error');
        if ($error_message === false) {
            $error_message = '';
        }

        include MTZ_SLIDER_PLUGIN_DIR . 'admin/views/updates-page.php';
    }

    /**
     * Obtener releases desde GitHub
     *
     * @return array
     */
    private function get_github_releases() {
        $cache_key = 'mtz_slider_all_releases';
        $error_key = 'mtz_slider_releases_error';
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        // Intentar primero con releases, luego con tags si no funciona
        $api_urls = array(
            'https://api.github.com/repos/fabiojara/mtz-slider/releases',
            'https://api.github.com/repos/fabiojara/mtz-slider/tags'
        );

        // Configuración para wp_remote_get
        $args = array(
            'timeout' => 15,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress-MTZ-Slider',
            ),
            'sslverify' => true, // Verificar SSL por defecto
        );

        // En local, permitir desactivar verificación SSL si es necesario
        if (defined('WP_DEBUG') && WP_DEBUG && defined('MTZ_SLIDER_ALLOW_UNVERIFIED_SSL') && MTZ_SLIDER_ALLOW_UNVERIFIED_SSL) {
            $args['sslverify'] = false;
        }

        $response = null;
        $api_url = '';

        // Intentar con releases primero
        foreach ($api_urls as $url) {
            $response = wp_remote_get($url, $args);
            if (!is_wp_error($response)) {
                $response_code = wp_remote_retrieve_response_code($response);
                if ($response_code === 200) {
                    $api_url = $url;
                    break;
                }
            }
        }

        // Si no funcionó con wp_remote_get, usar fallback con curl
        if (empty($api_url) && function_exists('curl_init')) {
            foreach ($api_urls as $url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_USERAGENT, 'WordPress-MTZ-Slider');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/vnd.github.v3+json'));

                // Si es local y hay problemas SSL
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                }

                $body = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code === 200 && !empty($body)) {
                    $api_url = $url;
                    // Crear respuesta simulada para continuar con el flujo
                    $response = array(
                        'body' => $body,
                        'response' => array(
                            'code' => 200,
                            'message' => 'OK'
                        )
                    );
                    break;
                }
            }
        }

        $releases = array();
        $error_message = '';

        if (!empty($api_url) && !empty($response)) {
            $body = wp_remote_retrieve_body($response);
            if (empty($body)) {
                // Si es respuesta simulada de curl
                $body = isset($response['body']) ? $response['body'] : '';
            }

            $data = json_decode($body, true);

            if (is_array($data) && !empty($data)) {
                foreach ($data as $item) {
                    // Manejar releases
                    if (isset($item['tag_name'])) {
                        $tag_name = $item['tag_name'];
                        $version = ltrim($tag_name, 'v');

                        // Si es un release, tiene más información
                        if (isset($item['published_at'])) {
                            $releases[] = array(
                                'version' => $version,
                                'tag' => $tag_name,
                                'name' => isset($item['name']) ? $item['name'] : $tag_name,
                                'published_at' => $item['published_at'],
                                'body' => isset($item['body']) ? $item['body'] : '',
                                'zip_url' => isset($item['zipball_url']) ? $item['zipball_url'] : 'https://github.com/fabiojara/mtz-slider/archive/refs/tags/' . $tag_name . '.zip',
                                'html_url' => isset($item['html_url']) ? $item['html_url'] : 'https://github.com/fabiojara/mtz-slider/releases/tag/' . $tag_name,
                                'prerelease' => isset($item['prerelease']) ? $item['prerelease'] : false,
                                'draft' => isset($item['draft']) ? $item['draft'] : false,
                            );
                        }
                        // Si es un tag, construir información básica
                        elseif (strpos($api_url, '/tags') !== false) {
                            // Para tags, obtener información del commit
                            $commit_date = '';
                            if (isset($item['commit']) && isset($item['commit']['commit']) && isset($item['commit']['commit']['author'])) {
                                $commit_date = $item['commit']['commit']['author']['date'];
                            } elseif (isset($item['commit']) && isset($item['commit']['sha'])) {
                                // Si no hay fecha del commit, usar fecha actual aproximada
                                $commit_date = date('Y-m-d\TH:i:s\Z');
                            }

                            if (!empty($tag_name)) {
                                $releases[] = array(
                                    'version' => $version,
                                    'tag' => $tag_name,
                                    'name' => $tag_name,
                                    'published_at' => $commit_date ? $commit_date : date('Y-m-d\TH:i:s\Z'),
                                    'body' => '',
                                    'zip_url' => 'https://github.com/fabiojara/mtz-slider/archive/refs/tags/' . $tag_name . '.zip',
                                    'html_url' => 'https://github.com/fabiojara/mtz-slider/releases/tag/' . $tag_name,
                                    'prerelease' => false,
                                    'draft' => false,
                                );
                            }
                        }
                    }
                }
            }
        } else {
            // Si no se pudo conectar, obtener error
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
            } elseif (!empty($response)) {
                $response_code = wp_remote_retrieve_response_code($response);
                $error_message = 'HTTP ' . $response_code . ': ' . wp_remote_retrieve_response_message($response);
            } else {
                $error_message = 'No se pudo conectar con la API de GitHub. Verifica tu conexión a internet.';
            }
            set_transient($error_key, $error_message, 10 * MINUTE_IN_SECONDS);
        }

        // Guardar error si existe y no se obtuvieron releases
        if (!empty($error_message) && empty($releases)) {
            set_transient($error_key, $error_message, 10 * MINUTE_IN_SECONDS);
        } else {
            delete_transient($error_key);
        }

        // Ordenar releases por versión (más reciente primero)
        if (!empty($releases)) {
            usort($releases, function($a, $b) {
                return version_compare($b['version'], $a['version']);
            });
        }

        // Cachear por 1 hora (incluso si está vacío para no consultar repetidamente)
        set_transient($cache_key, $releases, 1 * HOUR_IN_SECONDS);

        return $releases;
    }

    /**
     * Renderizar shortcode del slider
     */
    public function render_slider_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 1,
            'autoplay' => null,
            'speed' => null,
        ), $atts);

        $database = new MTZ_Slider_Database();
        $slider = $database->get_slider(intval($atts['id']));

        if (!$slider || !$slider['is_active']) {
            return '';
        }

        // Usar configuración del slider o parámetros del shortcode
        $autoplay = $atts['autoplay'] !== null ? filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN) : $slider['autoplay'];
        $speed = $atts['speed'] !== null ? intval($atts['speed']) : $slider['speed'];
        $animation_effect = isset($slider['animation_effect']) ? $slider['animation_effect'] : 'fade';

        // Convertir autoplay de int a bool
        $autoplay = $autoplay == 1 || $autoplay === true || $autoplay === 'true';

        $images = $database->get_slider_images(intval($atts['id']));

        if (empty($images)) {
            return '';
        }

        // Pasar imágenes y configuración al template
        $template_data = array(
            'images' => $images,
            'autoplay' => $autoplay,
            'speed' => $speed,
            'animation_effect' => $animation_effect,
            'atts' => $atts
        );

        extract($template_data);

        // Asegurar assets cuando se use el shortcode en PHP/plantillas
        $this->enqueue_public_assets();

        ob_start();
        include MTZ_SLIDER_PLUGIN_DIR . 'public/views/slider.php';
        return ob_get_clean();
    }
}

// Inicializar plugin
MTZ_Slider::get_instance();


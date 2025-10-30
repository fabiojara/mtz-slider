<?php
/**
 * Clase para manejar actualizaciones automáticas desde GitHub
 *
 * @package MTZ_Slider
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase MTZ_Slider_Updater
 */
class MTZ_Slider_Updater {

    /**
     * URL del repositorio de GitHub
     */
    private $github_repo = 'fabiojara/mtz-slider';

    /**
     * Slug del plugin
     */
    private $slug = 'mtz-slider';

    /**
     * Constructor
     */
    public function __construct() {
        // Hooks de WordPress para actualizaciones
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('upgrader_post_install', array($this, 'post_install'), 10, 3);
    }

    /**
     * Verificar actualizaciones desde GitHub
     *
     * @param object $transient Transiente de actualizaciones
     * @return object
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        // Obtener información de la última versión
        $release_info = $this->get_latest_release();

        if ($release_info && version_compare(MTZ_SLIDER_VERSION, $release_info['version'], '<')) {
            $plugin_data = get_plugin_data(MTZ_SLIDER_PLUGIN_FILE);
            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->plugin = plugin_basename(MTZ_SLIDER_PLUGIN_FILE);
            $obj->new_version = $release_info['version'];
            $obj->url = $this->get_repo_url();
            $obj->package = $release_info['zip_url'];
            $obj->tested = '6.4'; // Versión de WordPress probada
            $obj->requires_php = '7.4';
            $transient->response[$obj->plugin] = $obj;
        }

        return $transient;
    }

    /**
     * Obtener información del plugin para la vista de detalles
     *
     * @param object|false $result Resultado del filtro
     * @param string $action Acción solicitada
     * @param object $args Argumentos
     * @return object|false
     */
    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return false;
        }

        if (!isset($args->slug) || $args->slug !== $this->slug) {
            return $result;
        }

        $release_info = $this->get_latest_release();
        if (!$release_info) {
            return $result;
        }

        $plugin_info = new stdClass();
        $plugin_info->name = 'MTZ Slider';
        $plugin_info->slug = $this->slug;
        $plugin_info->version = $release_info['version'];
        $plugin_info->author = 'Fabio Jara';
        $plugin_info->author_profile = 'https://github.com/fabiojara';
        $plugin_info->homepage = $this->get_repo_url();
        $plugin_info->requires = '5.8';
        $plugin_info->tested = '6.4';
        $plugin_info->requires_php = '7.4';
        $plugin_info->last_updated = $release_info['published_at'];
        $plugin_info->sections = array(
            'description' => 'Slider moderno y responsive para WordPress. Crea múltiples sliders y gestiona imágenes desde el panel administrativo.',
            'changelog' => $release_info['changelog'],
        );
        $plugin_info->download_link = $release_info['zip_url'];

        return $plugin_info;
    }

    /**
     * Obtener información de la última versión desde GitHub
     *
     * @return array|false
     */
    private function get_latest_release() {
        $cache_key = 'mtz_slider_latest_release';
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $api_url = sprintf('https://api.github.com/repos/%s/releases/latest', $this->github_repo);

        $response = wp_remote_get($api_url, array(
            'timeout' => 15,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress-MTZ-Slider',
            ),
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['tag_name']) || !isset($data['zipball_url'])) {
            return false;
        }

        // Extraer versión del tag (ej: v2.2.1 -> 2.2.1)
        $version = ltrim($data['tag_name'], 'v');

        // Buscar archivo ZIP en assets o usar zipball_url
        $zip_url = $data['zipball_url'];

        // Si hay assets, buscar el archivo ZIP
        if (isset($data['assets']) && is_array($data['assets'])) {
            foreach ($data['assets'] as $asset) {
                if (isset($asset['browser_download_url']) &&
                    preg_match('/\.zip$/i', $asset['browser_download_url'])) {
                    $zip_url = $asset['browser_download_url'];
                    break;
                }
            }
        }

        $release_info = array(
            'version' => $version,
            'zip_url' => $zip_url,
            'published_at' => isset($data['published_at']) ? $data['published_at'] : '',
            'changelog' => isset($data['body']) ? $this->format_changelog($data['body']) : '',
        );

        // Cachear por 12 horas
        set_transient($cache_key, $release_info, 12 * HOUR_IN_SECONDS);

        return $release_info;
    }

    /**
     * Formatear changelog
     *
     * @param string $body Cuerpo del release
     * @return string
     */
    private function format_changelog($body) {
        if (empty($body)) {
            return 'No hay información de cambios disponible.';
        }

        // Convertir markdown básico a HTML
        $changelog = $body;
        $changelog = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $changelog);
        $changelog = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $changelog);
        $changelog = preg_replace('/\n- (.*?)(\n|$)/', "\n• $1\n", $changelog);
        $changelog = nl2br($changelog);

        return $changelog;
    }

    /**
     * Obtener URL del repositorio
     *
     * @return string
     */
    private function get_repo_url() {
        return sprintf('https://github.com/%s', $this->github_repo);
    }

    /**
     * Post-instalación: ajustar estructura del plugin
     *
     * @param bool $response Respuesta de instalación
     * @param array $hook_extra Información extra
     * @param array $result Resultado de la instalación
     * @return bool
     */
    public function post_install($response, $hook_extra, $result) {
        global $wp_filesystem;

        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== plugin_basename(MTZ_SLIDER_PLUGIN_FILE)) {
            return $response;
        }

        $install_directory = plugin_dir_path(MTZ_SLIDER_PLUGIN_FILE);

        // Si el ZIP de GitHub viene con estructura diferente, buscar el directorio correcto
        if (isset($result['destination'])) {
            $destination = $result['destination'];

            // Si el destino tiene una subcarpeta (común con zipball de GitHub)
            $directories = $wp_filesystem->dirlist($destination);
            if ($directories) {
                foreach ($directories as $dir => $info) {
                    if ($info['type'] === 'd' && strpos($dir, $this->slug) !== false) {
                        // Encontramos el directorio del plugin
                        $plugin_dir = trailingslashit($destination) . $dir;

                        // Mover contenido del subdirectorio al destino final
                        $files = $wp_filesystem->dirlist($plugin_dir);
                        if ($files) {
                            foreach ($files as $file => $file_info) {
                                $source = trailingslashit($plugin_dir) . $file;
                                $dest = trailingslashit($install_directory) . $file;

                                if ($file_info['type'] === 'd') {
                                    $wp_filesystem->mkdir($dest);
                                    $this->copy_directory($source, $dest, $wp_filesystem);
                                } else {
                                    $wp_filesystem->copy($source, $dest, true);
                                }
                            }
                        }

                        // Limpiar directorio temporal
                        $wp_filesystem->rmdir($destination, true);
                        break;
                    }
                }
            }

            // Si no hay subdirectorio, mover directamente
            if ($wp_filesystem->exists($destination) && $destination !== $install_directory) {
                $wp_filesystem->move($destination, $install_directory);
            }
        }

        $result['destination'] = $install_directory;

        if ($wp_filesystem->exists($install_directory)) {
            $result['destination_name'] = basename($install_directory);
        }

        return $response;
    }

    /**
     * Copiar directorio recursivamente
     *
     * @param string $source Directorio fuente
     * @param string $dest Directorio destino
     * @param WP_Filesystem_Base $wp_filesystem Instancia de WP_Filesystem
     */
    private function copy_directory($source, $dest, $wp_filesystem) {
        $files = $wp_filesystem->dirlist($source);
        if ($files) {
            foreach ($files as $file => $file_info) {
                $source_path = trailingslashit($source) . $file;
                $dest_path = trailingslashit($dest) . $file;

                if ($file_info['type'] === 'd') {
                    $wp_filesystem->mkdir($dest_path);
                    $this->copy_directory($source_path, $dest_path, $wp_filesystem);
                } else {
                    $wp_filesystem->copy($source_path, $dest_path, true);
                }
            }
        }
    }
}


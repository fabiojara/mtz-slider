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

        // Inicializar sistema de archivos si no está disponible
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        $install_directory = plugin_dir_path(MTZ_SLIDER_PLUGIN_FILE);

        // Si el ZIP de GitHub viene con estructura diferente, buscar el directorio correcto
        if (isset($result['destination']) && $wp_filesystem) {
            $destination = trailingslashit($result['destination']);

            // Verificar si el destino ya es el directorio correcto del plugin
            if ($destination === trailingslashit($install_directory)) {
                $result['destination'] = $install_directory;
                return $response;
            }

            // El zipball_url de GitHub crea una estructura como: mtz-slider-2.3.0/mtz-slider/
            // o simplemente: mtz-slider-2.3.0/
            $directories = $wp_filesystem->dirlist($destination);

            if ($directories && is_array($directories)) {
                $plugin_found = false;

                // Buscar el directorio que contiene el plugin
                foreach ($directories as $dir => $info) {
                    if (isset($info['type']) && $info['type'] === 'd') {
                        $subdir = trailingslashit($destination) . $dir;
                        $subdirs = $wp_filesystem->dirlist($subdir);

                        // Verificar si este subdirectorio contiene mtz-slider.php
                        if ($subdirs && is_array($subdirs)) {
                            foreach ($subdirs as $file => $file_info) {
                                if ($file === 'mtz-slider.php' && isset($file_info['type']) && $file_info['type'] === 'f') {
                                    // Encontramos el directorio del plugin (caso: mtz-slider-2.3.0/mtz-slider/)
                                    $plugin_dir = $subdir;
                                    $plugin_found = true;

                                    // Asegurar que el directorio de destino existe
                                    if (!$wp_filesystem->exists($install_directory)) {
                                        $wp_filesystem->mkdir($install_directory, FS_CHMOD_DIR);
                                    }

                                    // Copiar todos los archivos del subdirectorio al destino
                                    $this->copy_all_files($plugin_dir, $install_directory, $wp_filesystem);

                                    // Limpiar directorio temporal
                                    $wp_filesystem->rmdir($destination, true);
                                    break 2;
                                }
                            }
                        }

                        // También verificar si este directorio es directamente el plugin (caso: mtz-slider-2.3.0/ contiene mtz-slider.php)
                        if (isset($subdirs[$this->slug]) || (isset($subdirs['mtz-slider.php']) && isset($subdirs['mtz-slider.php']['type']) && $subdirs['mtz-slider.php']['type'] === 'f')) {
                            // Este es el directorio del plugin directamente
                            if (file_exists($subdir . '/mtz-slider.php')) {
                                $plugin_dir = $subdir;
                                $plugin_found = true;

                                // Asegurar que el directorio de destino existe
                                if (!$wp_filesystem->exists($install_directory)) {
                                    $wp_filesystem->mkdir($install_directory, FS_CHMOD_DIR);
                                }

                                // Copiar todos los archivos al destino
                                $this->copy_all_files($plugin_dir, $install_directory, $wp_filesystem);

                                // Limpiar directorio temporal
                                $wp_filesystem->rmdir($destination, true);
                                break;
                            }
                        }
                    }
                }

                // Si no encontramos estructura anidada, verificar si el destino directamente contiene mtz-slider.php
                if (!$plugin_found) {
                    $files = $wp_filesystem->dirlist($destination);
                    if ($files && isset($files['mtz-slider.php']) && isset($files['mtz-slider.php']['type']) && $files['mtz-slider.php']['type'] === 'f') {
                        // El destino es directamente el plugin
                        $this->copy_all_files($destination, $install_directory, $wp_filesystem);
                        $wp_filesystem->rmdir($destination, true);
                        $plugin_found = true;
                    }
                }
            }
        }

        // Asegurar que el resultado apunta al directorio correcto
        $result['destination'] = $install_directory;

        if ($wp_filesystem && $wp_filesystem->exists($install_directory)) {
            $result['destination_name'] = basename($install_directory);
        }

        return $response;
    }

    /**
     * Copiar todos los archivos de un directorio a otro
     *
     * @param string $source Directorio fuente
     * @param string $dest Directorio destino
     * @param WP_Filesystem_Base $wp_filesystem Instancia de WP_Filesystem
     */
    private function copy_all_files($source, $dest, $wp_filesystem) {
        $source = untrailingslashit($source);
        $dest = untrailingslashit($dest);

        $files = $wp_filesystem->dirlist($source);

        if ($files && is_array($files)) {
            foreach ($files as $file => $file_info) {
                if (!isset($file_info['type'])) {
                    continue;
                }

                $source_path = trailingslashit($source) . $file;
                $dest_path = trailingslashit($dest) . $file;

                if ($file_info['type'] === 'd') {
                    // Es un directorio, crearlo y copiar recursivamente
                    if (!$wp_filesystem->exists($dest_path)) {
                        $wp_filesystem->mkdir($dest_path, FS_CHMOD_DIR);
                    }
                    $this->copy_all_files($source_path, $dest_path, $wp_filesystem);
                } else {
                    // Es un archivo, copiarlo
                    $wp_filesystem->copy($source_path, $dest_path, true);
                }
            }
        }
    }

}



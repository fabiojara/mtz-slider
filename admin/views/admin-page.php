<?php
/**
 * Página de administración de MTZ Slider
 *
 * @package MTZ_Slider
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap mtz-slider-admin">
    <div class="mtz-slider-header-actions">
        <h1 class="mtz-slider-title">
            <span class="dashicons dashicons-images-alt2"></span>
            <?php esc_html_e('MTZ Slider', 'mtz-slider'); ?>
            <span class="mtz-version-badge">v<?php echo MTZ_SLIDER_VERSION; ?></span>
        </h1>
        <button type="button" class="button" id="mtz-toggle-help">
            <i data-lucide="help-circle"></i>
            <?php esc_html_e('¿Cómo usar este plugin?', 'mtz-slider'); ?>
        </button>
    </div>

    <!-- Modal de ayuda -->
    <div id="mtz-help-modal" class="mtz-modal" style="display: none;">
        <div class="mtz-modal-content mtz-help-modal-content">
            <div class="mtz-modal-header">
                <h2><i data-lucide="help-circle"></i> <?php esc_html_e('¿Cómo usar este plugin?', 'mtz-slider'); ?></h2>
                <button class="mtz-modal-close">&times;</button>
            </div>
            <div class="mtz-modal-body">
                <div class="mtz-help-sections">
                    <div class="mtz-help-section">
                        <h3><i data-lucide="settings"></i> <?php esc_html_e('Paso 1: Crear un Slider', 'mtz-slider'); ?></h3>
                        <p><?php esc_html_e('Haz clic en "Crear Nuevo Slider" en la barra lateral y asígnale un nombre único.', 'mtz-slider'); ?></p>
                    </div>
                    
                    <div class="mtz-help-section">
                        <h3><i data-lucide="image-plus"></i> <?php esc_html_e('Paso 2: Agregar Imágenes', 'mtz-slider'); ?></h3>
                        <p><?php esc_html_e('Selecciona el slider de la lista y haz clic en "Agregar Imágenes" para añadir tus imágenes.', 'mtz-slider'); ?></p>
                    </div>
                    
                    <div class="mtz-help-section">
                        <h3><i data-lucide="code"></i> <?php esc_html_e('Paso 3: Copiar y Usar el Shortcode', 'mtz-slider'); ?></h3>
                        <p><?php esc_html_e('Cada slider tiene su propio shortcode. Copia el shortcode que aparece junto a cada slider y pégalo en tus páginas.', 'mtz-slider'); ?></p>
                        <code class="mtz-code-block">[mtz_slider id="1"]</code>
                        <p><strong><?php esc_html_e('Ejemplos:', 'mtz-slider'); ?></strong></p>
                        <ul>
                            <li><code>[mtz_slider id="1"]</code> - <?php esc_html_e('Usar slider con ID 1', 'mtz-slider'); ?></li>
                            <li><code>[mtz_slider id="2" speed="3000"]</code> - <?php esc_html_e('Slider 2 con velocidad personalizada', 'mtz-slider'); ?></li>
                            <li><code>[mtz_slider id="3" autoplay="false"]</code> - <?php esc_html_e('Slider 3 sin reproducción automática', 'mtz-slider'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="mtz-help-section">
                        <h3><i data-lucide="layout"></i> <?php esc_html_e('Ubicaciones', 'mtz-slider'); ?></h3>
                        <p><?php esc_html_e('Puedes usar el slider en:', 'mtz-slider'); ?></p>
                        <ul>
                            <li>✓ <?php esc_html_e('Páginas y Entradas', 'mtz-slider'); ?></li>
                            <li>✓ <?php esc_html_e('Plantillas personalizadas', 'mtz-slider'); ?></li>
                            <li>✓ <?php esc_html_e('Widgets (si soportan shortcodes)', 'mtz-slider'); ?></li>
                            <li>✓ <?php esc_html_e('Código PHP:', 'mtz-slider'); ?> <code>&lt;?php echo do_shortcode('[mtz_slider id="1"]'); ?&gt;</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de sliders -->
    <div class="mtz-sliders-container">
        <!-- Lista de sliders en sidebar -->
        <div class="mtz-sliders-sidebar">
            <div class="mtz-sliders-header">
                <h3><?php esc_html_e('Mis Sliders', 'mtz-slider'); ?></h3>
                <button type="button" id="mtz-create-slider" class="button button-primary">
                    <i data-lucide="plus"></i>
                    <?php esc_html_e('Crear Nuevo Slider', 'mtz-slider'); ?>
                </button>
            </div>

            <div class="mtz-sliders-list" id="mtz-sliders-list">
                <?php if (empty($sliders)): ?>
                    <div class="mtz-empty-sliders">
                        <span class="dashicons dashicons-images-alt2"></span>
                        <p><?php esc_html_e('No hay sliders. ¡Crea uno nuevo!', 'mtz-slider'); ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($sliders as $slider_item): ?>
                        <?php
                        $is_active = isset($current_slider_id) && $current_slider_id == $slider_item['id'];
                        $shortcode = '[mtz_slider id="' . $slider_item['id'] . '"]';
                        ?>
                        <div class="mtz-slider-item <?php echo $is_active ? 'active' : ''; ?>" data-slider-id="<?php echo esc_attr($slider_item['id']); ?>">
                            <div class="mtz-slider-item-header">
                                <h4><?php echo esc_html($slider_item['name']); ?></h4>
                                <button class="mtz-slider-item-delete" data-id="<?php echo esc_attr($slider_item['id']); ?>" title="<?php esc_attr_e('Eliminar', 'mtz-slider'); ?>">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                            <div class="mtz-slider-item-info">
                                <div class="mtz-slider-item-stat">
                                    <span class="dashicons dashicons-images-alt2"></span>
                                    <?php
                                    $img_count = isset($slider_item['image_count']) ? $slider_item['image_count'] : 0;
                                    printf(_n('%d imagen', '%d imágenes', $img_count, 'mtz-slider'), $img_count);
                                    ?>
                                </div>
                            </div>
                            <div class="mtz-slider-item-shortcode">
                                <input type="text" readonly value="<?php echo esc_attr($shortcode); ?>" class="mtz-shortcode-input">
                                <button class="button button-small mtz-copy-shortcode" data-shortcode="<?php echo esc_attr($shortcode); ?>">
                                    <i data-lucide="copy"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contenido del slider actual -->
        <div class="mtz-slider-content" id="mtz-slider-content">
            <?php if ($current_slider): ?>
                <input type="hidden" id="mtz-current-slider-id" value="<?php echo esc_attr($current_slider_id); ?>">

                <div class="mtz-slider-header">
                    <h2><?php echo esc_html($current_slider['name']); ?></h2>
                    <p class="description">
                        <?php esc_html_e('Gestiona las imágenes de tu slider. Arrastra para reordenar.', 'mtz-slider'); ?>
                    </p>
                </div>

                <div class="mtz-slider-toolbar">
                    <button type="button" id="mtz-add-images" class="button button-primary button-large">
                        <i data-lucide="image-plus"></i>
                        <?php esc_html_e('Agregar Imágenes', 'mtz-slider'); ?>
                    </button>
                    <div class="mtz-slider-notice" id="mtz-notice"></div>
                </div>

                <div class="mtz-slider-grid" id="mtz-slider-grid">
                    <div class="mtz-slider-empty-state">
                        <span class="dashicons dashicons-images-alt2"></span>
                        <p><?php esc_html_e('No hay imágenes en el slider. Haz clic en "Agregar Imágenes" para comenzar.', 'mtz-slider'); ?></p>
                    </div>
                </div>

                <div class="mtz-slider-loading" id="mtz-loading" style="display: none;">
                    <div class="spinner is-active"></div>
                </div>
            <?php else: ?>
                <div class="mtz-no-slider">
                    <span class="dashicons dashicons-images-alt2"></span>
                    <h3><?php esc_html_e('¡Crea tu primer slider!', 'mtz-slider'); ?></h3>
                    <p><?php esc_html_e('Haz clic en el botón "Crear Nuevo Slider" para comenzar.', 'mtz-slider'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para crear/editar slider -->
    <div id="mtz-slider-modal" class="mtz-modal" style="display: none;">
        <div class="mtz-modal-content">
            <div class="mtz-modal-header">
                <h2 id="mtz-modal-title"><?php esc_html_e('Crear Nuevo Slider', 'mtz-slider'); ?></h2>
                <button class="mtz-modal-close">&times;</button>
            </div>
            <div class="mtz-modal-body">
                <form id="mtz-slider-form">
                    <input type="hidden" id="mtz-slider-id" />

                    <div class="mtz-form-field">
                        <label for="mtz-slider-name"><?php esc_html_e('Nombre del Slider', 'mtz-slider'); ?> *</label>
                        <input type="text" id="mtz-slider-name" class="regular-text" required placeholder="<?php esc_attr_e('Ej: Slider Principal, Home Banner, etc.', 'mtz-slider'); ?>" />
                    </div>

                    <div class="mtz-form-field">
                        <label>
                            <input type="checkbox" id="mtz-slider-autoplay" checked />
                            <?php esc_html_e('Activar reproducción automática', 'mtz-slider'); ?>
                        </label>
                    </div>

                    <div class="mtz-form-field">
                        <label for="mtz-slider-speed"><?php esc_html_e('Velocidad (milisegundos)', 'mtz-slider'); ?></label>
                        <input type="number" id="mtz-slider-speed" value="5000" min="1000" max="20000" step="500" />
                        <p class="description"><?php esc_html_e('Tiempo que cada imagen permanece visible antes de cambiar.', 'mtz-slider'); ?></p>
                    </div>

                    <div class="mtz-form-actions">
                        <button type="submit" class="button button-primary">
                            <?php esc_html_e('Guardar', 'mtz-slider'); ?>
                        </button>
                        <button type="button" class="button mtz-modal-cancel">
                            <?php esc_html_e('Cancelar', 'mtz-slider'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar imagen -->
    <div id="mtz-image-modal" class="mtz-modal" style="display: none;">
        <div class="mtz-modal-content">
            <div class="mtz-modal-header">
                <h2><?php esc_html_e('Editar Imagen', 'mtz-slider'); ?></h2>
                <button class="mtz-modal-close">&times;</button>
            </div>
            <div class="mtz-modal-body">
                <form id="mtz-image-form">
                    <input type="hidden" id="mtz-image-id" />
                    <input type="hidden" id="mtz-image-url" />

                    <div class="mtz-form-field">
                        <label for="mtz-image-title"><?php esc_html_e('Título', 'mtz-slider'); ?></label>
                        <input type="text" id="mtz-image-title" class="regular-text" />
                    </div>

                    <div class="mtz-form-field">
                        <label for="mtz-image-alt"><?php esc_html_e('Texto Alternativo (ALT)', 'mtz-slider'); ?></label>
                        <input type="text" id="mtz-image-alt" class="regular-text" />
                    </div>

                    <div class="mtz-form-field">
                        <label for="mtz-image-description"><?php esc_html_e('Descripción', 'mtz-slider'); ?></label>
                        <textarea id="mtz-image-description" rows="3" class="large-text"></textarea>
                    </div>

                    <div class="mtz-form-field">
                        <label>
                            <input type="checkbox" id="mtz-image-active" />
                            <?php esc_html_e('Activar imagen', 'mtz-slider'); ?>
                        </label>
                    </div>

                    <div class="mtz-form-actions">
                        <button type="submit" class="button button-primary">
                            <?php esc_html_e('Guardar', 'mtz-slider'); ?>
                        </button>
                        <button type="button" class="button mtz-modal-cancel">
                            <?php esc_html_e('Cancelar', 'mtz-slider'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

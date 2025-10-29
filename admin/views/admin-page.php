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
    <h1 class="mtz-slider-title">
        <span class="dashicons dashicons-images-alt2"></span>
        <?php esc_html_e('MTZ Slider', 'mtz-slider'); ?>
    </h1>
    
    <div class="mtz-slider-container">
        <div class="mtz-slider-header">
            <p class="description">
                <?php esc_html_e('Gestiona las imágenes de tu slider. Arrastra para reordenar.', 'mtz-slider'); ?>
            </p>
        </div>
        
        <div class="mtz-slider-toolbar">
            <button type="button" id="mtz-add-images" class="button button-primary button-large">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php esc_html_e('Agregar Imágenes', 'mtz-slider'); ?>
            </button>
            <button type="button" id="mtz-save-changes" class="button button-secondary button-large">
                <span class="dashicons dashicons-saved"></span>
                <?php esc_html_e('Guardar Cambios', 'mtz-slider'); ?>
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


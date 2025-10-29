<?php
/**
 * Template del Slider
 *
 * @package MTZ_Slider
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="mtz-slider-wrapper">
    <div class="mtz-slider"
         data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
         data-speed="<?php echo esc_attr($speed); ?>">

        <div class="mtz-slider-track">
            <?php foreach ($images as $image): ?>
                <div class="mtz-slide" style="background-image: url('<?php echo esc_url($image['image_url']); ?>');">
                    <?php if (!empty($image['image_title']) || !empty($image['image_description'])): ?>
                        <div class="mtz-slide-content">
                            <?php if (!empty($image['image_title'])): ?>
                                <h2 class="mtz-slide-title"><?php echo esc_html($image['image_title']); ?></h2>
                            <?php endif; ?>
                            <?php if (!empty($image['image_description'])): ?>
                                <p class="mtz-slide-description"><?php echo esc_html($image['image_description']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="mtz-slider-prev" aria-label="<?php esc_attr_e('Anterior', 'mtz-slider'); ?>">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
        </button>
        <button class="mtz-slider-next" aria-label="<?php esc_attr_e('Siguiente', 'mtz-slider'); ?>">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
        </button>

        <div class="mtz-slider-dots"></div>

        <?php if ($autoplay): ?>
            <button class="mtz-slider-pause-play" aria-label="<?php esc_attr_e('Pausar/Reproducir', 'mtz-slider'); ?>">
                <span class="dashicons dashicons-controls-pause"></span>
            </button>
        <?php endif; ?>
    </div>
</div>


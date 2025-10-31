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

<?php
$slider_id = isset($atts['id']) ? intval($atts['id']) : 1;
$unique_id = 'mtz-slider-' . $slider_id;
?>
<div class="mtz-slider-wrapper" id="<?php echo esc_attr($unique_id); ?>">
    <div class="mtz-slider"
         id="<?php echo esc_attr($unique_id); ?>-container"
         data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
         data-speed="<?php echo esc_attr($speed); ?>"
         data-animation-effect="<?php echo esc_attr(isset($animation_effect) ? $animation_effect : 'fade'); ?>"
         data-slider-id="<?php echo esc_attr($slider_id); ?>">

        <div class="mtz-slider-track">
            <?php foreach ($images as $image): ?>
                <div class="mtz-slide">
                    <img
                        class="mtz-slide-img"
                        src="<?php echo esc_url($image['image_url']); ?>"
                        alt="<?php echo esc_attr($image['image_alt']); ?>"
                        loading="lazy"
                        srcset="<?php echo esc_url($image['image_url']); ?> 1200w"
                        sizes="(max-width: 768px) 100vw, 100vw"
                    />
                    <?php if (!empty($image['image_title']) || !empty($image['image_description'])): ?>
                        <div class="mtz-slide-content">
                            <?php if (!empty($image['image_title'])): ?>
                                <h2 class="mtz-slide-title"><?php echo esc_html($image['image_title']); ?></h2>
                            <?php endif; ?>
                            <?php if (!empty($image['image_description'])): ?>
                                <p class="mtz-slide-description"><?php echo esc_html($image['image_description']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($image['link_url'])): ?>
                                <a class="mtz-slide-button" href="<?php echo esc_url($image['link_url']); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Conocer más...', 'mtz-slider'); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="mtz-slider-prev" aria-label="<?php esc_attr_e('Anterior', 'mtz-slider'); ?>">
            <i data-lucide="chevron-left"></i>
        </button>
        <button class="mtz-slider-next" aria-label="<?php esc_attr_e('Siguiente', 'mtz-slider'); ?>">
            <i data-lucide="chevron-right"></i>
        </button>

        <div class="mtz-slider-dots"></div>
    </div>
</div>


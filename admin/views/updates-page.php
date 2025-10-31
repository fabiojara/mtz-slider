<?php
/**
 * Página de actualizaciones de MTZ Slider
 *
 * @package MTZ_Slider
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap mtz-slider-admin">
    <div class="mtz-slider-header-actions">
        <div class="mtz-slider-header-title">
            <h1 class="mtz-slider-title">
                <i data-lucide="download"></i>
                <?php esc_html_e('Actualizaciones', 'mtz-slider'); ?>
            </h1>
            <p class="mtz-slider-author-link">
                <a href="https://mantiztechnology.com" target="_blank" rel="noopener noreferrer">
                    <i data-lucide="external-link"></i>
                    <?php esc_html_e('www.mantiztechnology.com', 'mtz-slider'); ?>
                </a>
            </p>
        </div>
        <a href="<?php echo admin_url('admin.php?page=mtz-slider'); ?>" class="button">
            <i data-lucide="arrow-left"></i>
            <?php esc_html_e('Volver a Sliders', 'mtz-slider'); ?>
        </a>
    </div>

    <div class="mtz-updates-container">
        <!-- Versión actual -->
        <div class="mtz-current-version-card">
            <div class="mtz-version-card-header">
                <h2>
                    <i data-lucide="info"></i>
                    <?php esc_html_e('Versión Actual', 'mtz-slider'); ?>
                </h2>
            </div>
            <div class="mtz-version-card-body">
                <div class="mtz-version-info">
                    <span class="mtz-version-label"><?php esc_html_e('Versión instalada:', 'mtz-slider'); ?></span>
                    <span class="mtz-version-number">v<?php echo esc_html($current_version); ?></span>
                </div>
            </div>
        </div>

        <!-- Lista de actualizaciones -->
        <div class="mtz-releases-list">
            <div class="mtz-releases-header">
                <h2>
                    <i data-lucide="package"></i>
                    <?php esc_html_e('Actualizaciones Publicadas', 'mtz-slider'); ?>
                </h2>
                <button type="button" class="button" id="mtz-refresh-releases">
                    <i data-lucide="refresh-cw"></i>
                    <?php esc_html_e('Actualizar', 'mtz-slider'); ?>
                </button>
            </div>

            <?php if (empty($releases)): ?>
                <div class="mtz-no-releases">
                    <i data-lucide="package-x"></i>
                    <p><?php esc_html_e('No se pudieron cargar las actualizaciones desde GitHub.', 'mtz-slider'); ?></p>
                    <p class="mtz-subtext"><?php esc_html_e('Verifica tu conexión a internet o intenta más tarde.', 'mtz-slider'); ?></p>
                </div>
            <?php else: ?>
                <div class="mtz-releases-grid">
                    <?php foreach ($releases as $release): ?>
                        <?php
                        $is_current = version_compare($release['version'], $current_version, '==');
                        $is_newer = version_compare($release['version'], $current_version, '>');
                        $is_older = version_compare($release['version'], $current_version, '<');
                        
                        $date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($release['published_at']));
                        ?>
                        <div class="mtz-release-card <?php echo $is_current ? 'current' : ($is_newer ? 'newer' : 'older'); ?>">
                            <div class="mtz-release-header">
                                <div class="mtz-release-title">
                                    <h3>
                                        <?php if ($is_current): ?>
                                            <i data-lucide="check-circle" class="current-icon"></i>
                                        <?php elseif ($is_newer): ?>
                                            <i data-lucide="arrow-up-circle" class="newer-icon"></i>
                                        <?php else: ?>
                                            <i data-lucide="package" class="older-icon"></i>
                                        <?php endif; ?>
                                        <?php echo esc_html($release['name']); ?>
                                    </h3>
                                    <span class="mtz-release-tag">v<?php echo esc_html($release['version']); ?></span>
                                </div>
                                <?php if ($release['prerelease']): ?>
                                    <span class="mtz-badge mtz-badge-prerelease"><?php esc_html_e('Pre-release', 'mtz-slider'); ?></span>
                                <?php endif; ?>
                                <?php if ($release['draft']): ?>
                                    <span class="mtz-badge mtz-badge-draft"><?php esc_html_e('Borrador', 'mtz-slider'); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="mtz-release-body">
                                <div class="mtz-release-meta">
                                    <span class="mtz-release-date">
                                        <i data-lucide="calendar"></i>
                                        <?php echo esc_html($date); ?>
                                    </span>
                                </div>
                                <?php if (!empty($release['body'])): ?>
                                    <div class="mtz-release-notes">
                                        <?php echo wp_kses_post(wpautop($release['body'])); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mtz-release-actions">
                                    <?php if ($is_newer): ?>
                                        <a href="<?php echo admin_url('update-core.php'); ?>" class="button button-primary">
                                            <i data-lucide="download"></i>
                                            <?php esc_html_e('Actualizar ahora', 'mtz-slider'); ?>
                                        </a>
                                    <?php elseif ($is_current): ?>
                                        <span class="button button-disabled">
                                            <i data-lucide="check"></i>
                                            <?php esc_html_e('Instalada', 'mtz-slider'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url($release['html_url']); ?>" target="_blank" rel="noopener noreferrer" class="button">
                                        <i data-lucide="external-link"></i>
                                        <?php esc_html_e('Ver en GitHub', 'mtz-slider'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('mtz-refresh-releases');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i data-lucide="refresh-cw" class="spinning"></i> <?php esc_html_e('Actualizando...', 'mtz-slider'); ?>';
            
            // Recargar la página después de borrar el cache
            // Recargar página con parámetro para refrescar
            const url = new URL(window.location.href);
            url.searchParams.set('refresh', '1');
            window.location.href = url.toString();
        });
    }
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>



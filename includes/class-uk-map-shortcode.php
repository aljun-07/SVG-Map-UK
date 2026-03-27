<?php
defined( 'ABSPATH' ) || exit;

/**
 * Handles the [uk_interactive_map] shortcode.
 *
 * Full-width map only — no side panel.
 * Hovering a region shows a tooltip with the region name, project count,
 * and clickable project links.
 *
 * Attributes:
 *   width = CSS max-width of the whole widget (default: 1200px)
 */
class UK_Map_Shortcode {

    public static function register(): void {
        add_shortcode( 'uk_interactive_map', [ __CLASS__, 'render' ] );
    }

    public static function render( array $atts ): string {
        $atts = shortcode_atts(
            [ 'width' => '1200px' ],
            $atts,
            'uk_interactive_map'
        );

        self::enqueue_assets();

        $width = esc_attr( $atts['width'] );
        $svg   = self::inline_svg();

        ob_start();
        ?>
        <div class="ukm-wrap" style="max-width:<?php echo $width; ?>;">
            <?php echo $svg; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /* -------------------------------------------------------
       Asset enqueuing
    ------------------------------------------------------- */
    private static function enqueue_assets(): void {
        wp_enqueue_style(
            'uk-interactive-map',
            UKM_PLUGIN_URL . 'assets/css/uk-map.css',
            [],
            UKM_VERSION
        );

        wp_register_script(
            'uk-interactive-map',
            UKM_PLUGIN_URL . 'assets/js/uk-map.js',
            [],
            UKM_VERSION,
            true
        );

        $saved    = get_option( 'ukm_region_data', [] );
        $regions  = UK_Map_Data::merge_with_defaults( is_array( $saved ) ? $saved : [] );
        $settings = get_option( 'ukm_settings', [] );

        wp_localize_script(
            'uk-interactive-map',
            'ukmData',
            [
                'regions'       => $regions,
                'markerIcon'    => $settings['marker_icon']    ?? '',
                'markerSize'    => $settings['marker_size']    ?? 32,
                'markerColor'   => $settings['marker_color']   ?? '#e74c3c',
                'mapColor'      => $settings['map_color']      ?? '#6f9c76',
                'selectedColor' => $settings['selected_color'] ?? '#2271b1',
                'inactiveColor' => $settings['inactive_color'] ?? '#a8c5ad',
            ]
        );

        wp_enqueue_script( 'uk-interactive-map' );
    }

    /* -------------------------------------------------------
       Inline SVG
    ------------------------------------------------------- */
    private static function inline_svg(): string {
        $file = UKM_PLUGIN_DIR . 'assets/uk-map.svg';

        if ( ! file_exists( $file ) ) {
            return '<p>' . esc_html__( 'Map file not found.', 'uk-interactive-map' ) . '</p>';
        }

        $svg = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

        if ( false === $svg ) {
            return '';
        }

        return wp_kses( $svg, self::allowed_svg_tags() );
    }

    /**
     * Allowlist of SVG tags and attributes for wp_kses sanitization.
     */
    private static function allowed_svg_tags(): array {
        $common = [
            'id'    => true,
            'class' => true,
            'style' => true,
        ];

        return [
            'svg' => array_merge( $common, [
                'xmlns'           => true,
                'baseprofile'     => true,
                'fill'            => true,
                'height'          => true,
                'width'           => true,
                'viewbox'         => true,
                'viewBox'         => true,
                'version'         => true,
                'stroke'          => true,
                'stroke-linecap'  => true,
                'stroke-linejoin' => true,
                'stroke-width'    => true,
                'aria-label'      => true,
                'role'            => true,
            ] ),
            'g' => array_merge( $common, [
                'fill'         => true,
                'stroke'       => true,
                'stroke-width' => true,
                'transform'    => true,
            ] ),
            'path' => array_merge( $common, [
                'd'            => true,
                'name'         => true,
                'fill'         => true,
                'stroke'       => true,
                'stroke-width' => true,
                'tabindex'     => true,
                'role'         => true,
                'aria-label'   => true,
            ] ),
            'circle' => array_merge( $common, [
                'cx'          => true,
                'cy'          => true,
                'r'           => true,
                'name'        => true,
                'fill'        => true,
                'stroke'      => true,
                'stroke-width'=> true,
                'tabindex'    => true,
                'role'        => true,
                'aria-label'  => true,
            ] ),
            'text' => array_merge( $common, [
                'x'           => true,
                'y'           => true,
                'dx'          => true,
                'dy'          => true,
                'font-size'   => true,
                'font-family' => true,
                'text-anchor' => true,
                'fill'        => true,
                'transform'   => true,
            ] ),
            'tspan' => array_merge( $common, [
                'x'  => true,
                'y'  => true,
                'dx' => true,
                'dy' => true,
            ] ),
            'title' => [],
            'desc'  => [],
        ];
    }
}

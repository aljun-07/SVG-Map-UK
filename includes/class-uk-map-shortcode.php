<?php
defined( 'ABSPATH' ) || exit;

/**
 * Handles the [uk_interactive_map] shortcode.
 *
 * Attributes:
 *   mode    = modal | popover | tooltip  (default: modal)
 *   width   = CSS width value            (default: 100%)
 *   height  = CSS max-width value        (default: 560px)
 */
class UK_Map_Shortcode {

    public static function register(): void {
        add_shortcode( 'uk_interactive_map', [ __CLASS__, 'render' ] );
    }

    public static function render( array $atts ): string {
        $atts = shortcode_atts(
            [
                'mode'   => 'modal',
                'width'  => '100%',
                'height' => '560px',
            ],
            $atts,
            'uk_interactive_map'
        );

        $mode = in_array( $atts['mode'], [ 'modal', 'popover', 'tooltip' ], true )
            ? $atts['mode']
            : 'modal';

        self::enqueue_assets( $mode );

        $svg    = self::get_svg();
        $width  = esc_attr( $atts['width'] );
        $height = esc_attr( $atts['height'] );

        ob_start();
        ?>
        <div class="ukm-wrap"
             data-ukm-mode="<?php echo esc_attr( $mode ); ?>"
             style="width:<?php echo $width; ?>;max-width:<?php echo $height; ?>;">
            <?php echo $svg; // already sanitised SVG ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /* -------------------------------------------------------
       Asset enqueuing
    ------------------------------------------------------- */
    private static function enqueue_assets( string $mode ): void {
        wp_enqueue_style(
            'uk-interactive-map',
            UKM_PLUGIN_URL . 'assets/css/uk-map.css',
            [],
            UKM_VERSION
        );

        $region_data = get_option( 'ukm_region_data', UK_Map_Data::defaults() );

        wp_enqueue_script(
            'uk-interactive-map',
            UKM_PLUGIN_URL . 'assets/js/uk-map.js',
            [],
            UKM_VERSION,
            true
        );

        wp_localize_script(
            'uk-interactive-map',
            'ukmData',
            [
                'regions' => $region_data,
                'mode'    => $mode,
            ]
        );
    }

    /* -------------------------------------------------------
       SVG loading
    ------------------------------------------------------- */
    private static function get_svg(): string {
        $path = UKM_PLUGIN_DIR . 'assets/uk-map.svg';

        if ( ! file_exists( $path ) ) {
            return '<p>' . esc_html__( 'Map file not found.', 'uk-interactive-map' ) . '</p>';
        }

        $svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions

        // Basic sanitisation: allow only expected SVG tags/attributes.
        // For a fully locked-down environment swap this for wp_kses with a custom tag allowlist.
        if ( function_exists( 'wp_kses' ) ) {
            $allowed = self::allowed_svg_tags();
            $svg     = wp_kses( $svg, $allowed );
        }

        return $svg;
    }

    /**
     * Allowed SVG tags/attributes for wp_kses().
     */
    private static function allowed_svg_tags(): array {
        $global_attrs = [
            'id'           => true,
            'class'        => true,
            'style'        => true,
            'data-region'  => true,
            'aria-label'   => true,
            'role'         => true,
            'tabindex'     => true,
            'title'        => true,
        ];

        return [
            'svg'  => array_merge( $global_attrs, [
                'xmlns'   => true,
                'viewbox' => true,
                'width'   => true,
                'height'  => true,
            ] ),
            'path' => array_merge( $global_attrs, [ 'd' => true ] ),
            'text' => array_merge( $global_attrs, [ 'x' => true, 'y' => true, 'dx' => true, 'dy' => true ] ),
            'g'    => $global_attrs,
        ];
    }
}

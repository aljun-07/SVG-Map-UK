<?php
defined( 'ABSPATH' ) || exit;

/**
 * Admin settings page — lets editors configure each region's
 * display name, description, hex colour, stats and link.
 */
class UK_Map_Admin {

    private const OPTION_KEY  = 'ukm_region_data';
    private const MENU_SLUG   = 'uk-interactive-map';
    private const NONCE_KEY   = 'ukm_settings_nonce';

    /* -------------------------------------------------------
       Menu + page
    ------------------------------------------------------- */
    public static function register_menu(): void {
        add_options_page(
            __( 'UK Interactive Map', 'uk-interactive-map' ),
            __( 'UK Map', 'uk-interactive-map' ),
            'manage_options',
            self::MENU_SLUG,
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function register_settings(): void {
        register_setting(
            'ukm_settings_group',
            self::OPTION_KEY,
            [ 'sanitize_callback' => [ __CLASS__, 'sanitize_data' ] ]
        );
    }

    /* -------------------------------------------------------
       Page render
    ------------------------------------------------------- */
    public static function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'uk-interactive-map' ) );
        }

        $data    = get_option( self::OPTION_KEY, UK_Map_Data::defaults() );
        $slugs   = UK_Map_Data::slugs();
        $saved   = isset( $_GET['settings-updated'] ) && $_GET['settings-updated']; // phpcs:ignore
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'UK Interactive Map Settings', 'uk-interactive-map' ); ?></h1>

            <?php if ( $saved ) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e( 'Settings saved.', 'uk-interactive-map' ); ?></p>
                </div>
            <?php endif; ?>

            <p><?php esc_html_e( 'Configure each region\'s label, description, colour, stats and link. Use the shortcode [uk_interactive_map] to embed the map.', 'uk-interactive-map' ); ?></p>
            <p><strong><?php esc_html_e( 'Shortcode options:', 'uk-interactive-map' ); ?></strong>
                <code>[uk_interactive_map]</code> &bull;
                <code>[uk_interactive_map mode="modal"]</code> &bull;
                <code>[uk_interactive_map mode="popover"]</code> &bull;
                <code>[uk_interactive_map mode="tooltip"]</code>
            </p>

            <form method="post" action="options.php">
                <?php settings_fields( 'ukm_settings_group' ); ?>
                <?php wp_nonce_field( 'ukm_save_settings', self::NONCE_KEY ); ?>

                <div id="ukm-admin-tabs">
                    <ul class="ukm-tab-list" style="display:flex;flex-wrap:wrap;gap:6px;margin:16px 0 0;padding:0;list-style:none;">
                        <?php foreach ( $slugs as $i => $slug ) : ?>
                            <li>
                                <button type="button"
                                        class="button ukm-tab-btn<?php echo $i === 0 ? ' button-primary' : ''; ?>"
                                        data-tab="ukm-tab-<?php echo esc_attr( $slug ); ?>">
                                    <?php echo esc_html( $data[ $slug ]['name'] ?? $slug ); ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php foreach ( $slugs as $i => $slug ) : ?>
                        <?php $r = $data[ $slug ] ?? []; ?>
                        <div id="ukm-tab-<?php echo esc_attr( $slug ); ?>"
                             class="ukm-tab-panel"
                             style="<?php echo $i !== 0 ? 'display:none;' : ''; ?>margin-top:12px;">

                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row"><label><?php esc_html_e( 'Display Name', 'uk-interactive-map' ); ?></label></th>
                                    <td>
                                        <input type="text" class="regular-text"
                                               name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][name]"
                                               value="<?php echo esc_attr( $r['name'] ?? '' ); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label><?php esc_html_e( 'Description', 'uk-interactive-map' ); ?></label></th>
                                    <td>
                                        <textarea class="large-text" rows="4"
                                                  name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][description]"><?php echo esc_textarea( $r['description'] ?? '' ); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label><?php esc_html_e( 'Fill Colour', 'uk-interactive-map' ); ?></label></th>
                                    <td>
                                        <input type="color" class="ukm-color-input"
                                               name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][color]"
                                               value="<?php echo esc_attr( $r['color'] ?? '#4a90d9' ); ?>">
                                        <span class="description"><?php esc_html_e( 'Region fill colour.', 'uk-interactive-map' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label><?php esc_html_e( 'Link URL', 'uk-interactive-map' ); ?></label></th>
                                    <td>
                                        <input type="url" class="large-text"
                                               name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][link]"
                                               value="<?php echo esc_url( $r['link'] ?? '' ); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label><?php esc_html_e( 'Link Label', 'uk-interactive-map' ); ?></label></th>
                                    <td>
                                        <input type="text" class="regular-text"
                                               name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][link_label]"
                                               value="<?php echo esc_attr( $r['link_label'] ?? '' ); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label><?php esc_html_e( 'Stats', 'uk-interactive-map' ); ?></label></th>
                                    <td>
                                        <?php
                                        $stats = $r['stats'] ?? [];
                                        $idx   = 0;
                                        foreach ( $stats as $label => $value ) :
                                        ?>
                                            <div class="ukm-stat-row" style="display:flex;gap:8px;margin-bottom:6px;">
                                                <input type="text" placeholder="Label" style="width:160px"
                                                       name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][stats][<?php echo esc_attr( $idx ); ?>][label]"
                                                       value="<?php echo esc_attr( $label ); ?>">
                                                <input type="text" placeholder="Value" style="width:160px"
                                                       name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][stats][<?php echo esc_attr( $idx ); ?>][value]"
                                                       value="<?php echo esc_attr( $value ); ?>">
                                            </div>
                                        <?php
                                            $idx++;
                                        endforeach;
                                        // extra blank rows for new entries
                                        for ( $e = 0; $e < 2; $e++ ) :
                                        ?>
                                            <div class="ukm-stat-row" style="display:flex;gap:8px;margin-bottom:6px;">
                                                <input type="text" placeholder="Label" style="width:160px"
                                                       name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][stats][<?php echo esc_attr( $idx ); ?>][label]"
                                                       value="">
                                                <input type="text" placeholder="Value" style="width:160px"
                                                       name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $slug ); ?>][stats][<?php echo esc_attr( $idx ); ?>][value]"
                                                       value="">
                                            </div>
                                        <?php
                                            $idx++;
                                        endfor;
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php submit_button( __( 'Save All Regions', 'uk-interactive-map' ) ); ?>
            </form>
        </div>

        <script>
        (function(){
            var btns = document.querySelectorAll('.ukm-tab-btn');
            btns.forEach(function(btn){
                btn.addEventListener('click', function(){
                    btns.forEach(function(b){ b.classList.remove('button-primary'); });
                    document.querySelectorAll('.ukm-tab-panel').forEach(function(p){ p.style.display='none'; });
                    btn.classList.add('button-primary');
                    document.getElementById(btn.dataset.tab).style.display = '';
                });
            });
        })();
        </script>
        <?php
    }

    /* -------------------------------------------------------
       Sanitization
    ------------------------------------------------------- */
    public static function sanitize_data( $input ): array {
        if ( ! is_array( $input ) ) {
            return UK_Map_Data::defaults();
        }

        $clean  = [];
        $slugs  = UK_Map_Data::slugs();

        foreach ( $slugs as $slug ) {
            $r = $input[ $slug ] ?? [];

            $stats_raw = $r['stats'] ?? [];
            $stats     = [];
            foreach ( $stats_raw as $row ) {
                $label = sanitize_text_field( $row['label'] ?? '' );
                $value = sanitize_text_field( $row['value'] ?? '' );
                if ( $label !== '' && $value !== '' ) {
                    $stats[ $label ] = $value;
                }
            }

            $clean[ $slug ] = [
                'name'        => sanitize_text_field( $r['name']       ?? '' ),
                'description' => sanitize_textarea_field( $r['description'] ?? '' ),
                'color'       => sanitize_hex_color( $r['color']       ?? '#4a90d9' ) ?: '#4a90d9',
                'link'        => esc_url_raw( $r['link']               ?? '' ),
                'link_label'  => sanitize_text_field( $r['link_label'] ?? '' ),
                'stats'       => $stats,
            ];
        }

        return $clean;
    }
}

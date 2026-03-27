<?php
defined( 'ABSPATH' ) || exit;

/**
 * Admin settings page for the UK Interactive Map plugin.
 *
 * Two-column layout: searchable region list (left) + JS-rendered editor (right).
 * Each region can have multiple projects (title, url).
 * Saves are done per-region via AJAX to avoid PHP max_input_vars limits.
 */
class UK_Map_Admin {

    private const OPTION_KEY = 'ukm_region_data';
    private const MENU_SLUG  = 'uk-interactive-map';
    private const NONCE_ACT  = 'ukm_save_region';

    /* -------------------------------------------------------
       Hooks registration
    ------------------------------------------------------- */
    public static function register_menu(): void {
        add_menu_page(
            __( 'UK Interactive Map', 'uk-interactive-map' ),
            __( 'UK Map', 'uk-interactive-map' ),
            'manage_options',
            self::MENU_SLUG,
            [ __CLASS__, 'render_page' ],
            'dashicons-location-alt',
            58
        );
    }

    public static function enqueue_admin_scripts( string $hook ): void {
        if ( $hook !== 'toplevel_page_' . self::MENU_SLUG ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_style(
            'uk-interactive-map-admin',
            UKM_PLUGIN_URL . 'assets/css/uk-map-admin.css',
            [],
            UKM_VERSION
        );

        $saved    = get_option( self::OPTION_KEY, [] );
        $regions  = UK_Map_Data::merge_with_defaults( is_array( $saved ) ? $saved : [] );
        $settings = get_option( 'ukm_settings', [] );

        wp_add_inline_script(
            'jquery',
            'var ukmAdmin = ' . wp_json_encode( [
                'regions'     => $regions,
                'regionNames' => UK_Map_Data::region_names(),
                'nonce'       => wp_create_nonce( self::NONCE_ACT ),
                'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
                'settings'    => [
                    'marker_icon'    => $settings['marker_icon']    ?? '',
                    'marker_size'    => $settings['marker_size']    ?? 32,
                    'marker_color'   => $settings['marker_color']   ?? '#e74c3c',
                    'map_color'      => $settings['map_color']      ?? '#6f9c76',
                    'selected_color' => $settings['selected_color'] ?? '#2271b1',
                    'inactive_color' => $settings['inactive_color'] ?? '#a8c5ad',
                ],
                'strings'     => [
                    'saved'       => __( 'Saved!', 'uk-interactive-map' ),
                    'saving'      => __( 'Saving…', 'uk-interactive-map' ),
                    'saveError'   => __( 'Save failed.', 'uk-interactive-map' ),
                    'chooseImage' => __( 'Choose Image', 'uk-interactive-map' ),
                    'useImage'    => __( 'Use this image', 'uk-interactive-map' ),
                ],
            ] ) . ';',
            'after'
        );
    }

    /* -------------------------------------------------------
       AJAX: save global map settings
    ------------------------------------------------------- */
    public static function ajax_save_settings(): void {
        check_ajax_referer( self::NONCE_ACT, 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Permission denied.' ], 403 );
        }

        $raw = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : ''; // phpcs:ignore
        $input = json_decode( $raw, true );
        if ( ! is_array( $input ) ) {
            wp_send_json_error( [ 'message' => 'Invalid data.' ], 400 );
        }

        $size = absint( $input['marker_size'] ?? 32 );
        if ( $size < 8 )   $size = 8;
        if ( $size > 128 ) $size = 128;

        $clean = [
            'marker_icon'    => esc_url_raw( $input['marker_icon']    ?? '' ),
            'marker_size'    => $size,
            'marker_color'   => self::sanitize_color( $input['marker_color']   ?? '' ) ?: '#e74c3c',
            'map_color'      => self::sanitize_color( $input['map_color']      ?? '' ) ?: '#6f9c76',
            'selected_color' => self::sanitize_color( $input['selected_color'] ?? '' ) ?: '#2271b1',
            'inactive_color' => self::sanitize_color( $input['inactive_color'] ?? '' ) ?: '#a8c5ad',
        ];

        update_option( 'ukm_settings', $clean );
        wp_send_json_success( [ 'data' => $clean ] );
    }

    /* -------------------------------------------------------
       AJAX: save one region
    ------------------------------------------------------- */
    public static function ajax_save_region(): void {
        check_ajax_referer( self::NONCE_ACT, 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Permission denied.' ], 403 );
        }

        $code = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';
        $raw  = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : ''; // phpcs:ignore

        if ( ! $code || ! array_key_exists( $code, UK_Map_Data::defaults() ) ) {
            wp_send_json_error( [ 'message' => 'Invalid region code.' ], 400 );
        }

        $input = json_decode( $raw, true );
        if ( ! is_array( $input ) ) {
            wp_send_json_error( [ 'message' => 'Invalid data.' ], 400 );
        }

        // Sanitize projects array.
        $projects = [];
        if ( isset( $input['projects'] ) && is_array( $input['projects'] ) ) {
            foreach ( $input['projects'] as $p ) {
                if ( ! is_array( $p ) ) continue;
                $projects[] = [
                    'title' => sanitize_text_field( $p['title'] ?? '' ),
                    'url'   => esc_url_raw( $p['url']   ?? '' ),
                ];
            }
        }

        $clean = [
            'name'        => sanitize_text_field( $input['name']        ?? '' ),
            'color'       => self::sanitize_color( $input['color']      ?? '' ),
            'hover_color' => self::sanitize_color( $input['hover_color'] ?? '' ),
            'projects'    => $projects,
        ];

        $all = get_option( self::OPTION_KEY, [] );
        if ( ! is_array( $all ) ) {
            $all = [];
        }
        $all[ $code ] = $clean;
        update_option( self::OPTION_KEY, $all );

        wp_send_json_success( [ 'code' => $code, 'data' => $clean ] );
    }

    /* -------------------------------------------------------
       Page render
    ------------------------------------------------------- */
    public static function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'uk-interactive-map' ) );
        }
        ?>
        <div class="wrap ukm-admin-wrap">
            <h1><?php esc_html_e( 'UK Interactive Map Settings', 'uk-interactive-map' ); ?></h1>
            <p><?php esc_html_e( 'Select a region to manage its projects. Each project can have a title and URL.', 'uk-interactive-map' ); ?> <?php esc_html_e( 'Shortcode:', 'uk-interactive-map' ); ?> <code>[uk_interactive_map]</code></p>

            <!-- Row 1: Marker Settings (full width) -->
            <div id="ukm-global-settings">
                <h2><?php esc_html_e( 'Marker Settings', 'uk-interactive-map' ); ?></h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="ukm-gs-icon"><?php esc_html_e( 'Marker Icon', 'uk-interactive-map' ); ?></label></th>
                        <td>
                            <input type="url" id="ukm-gs-icon" class="large-text" placeholder="https://… (leave blank for default pin)">
                            <div style="margin-top:6px;display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                <button type="button" id="ukm-gs-icon-btn" class="button"><?php esc_html_e( 'Choose Image', 'uk-interactive-map' ); ?></button>
                                <button type="button" id="ukm-gs-icon-remove" class="button" style="display:none;"><?php esc_html_e( 'Remove', 'uk-interactive-map' ); ?></button>
                            </div>
                            <div id="ukm-gs-icon-preview" style="display:none;margin-top:8px;">
                                <img src="" style="max-width:64px;max-height:64px;object-fit:contain;border:1px solid #ddd;border-radius:4px;padding:4px;">
                            </div>
                            <p class="description"><?php esc_html_e( 'Replaces the default teardrop pin. Recommended: 32×32 px PNG or SVG.', 'uk-interactive-map' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ukm-gs-size"><?php esc_html_e( 'Marker Size (px)', 'uk-interactive-map' ); ?></label></th>
                        <td>
                            <input type="number" id="ukm-gs-size" value="32" min="8" max="128" style="width:80px;">
                            <p class="description"><?php esc_html_e( 'Visual size of the map marker in pixels (8–128). Default: 32.', 'uk-interactive-map' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ukm-gs-color"><?php esc_html_e( 'Marker Color', 'uk-interactive-map' ); ?></label></th>
                        <td>
                            <input type="color" id="ukm-gs-color" value="#e74c3c">
                            <p class="description"><?php esc_html_e( 'Used for the default pin. Ignored when a custom icon is set.', 'uk-interactive-map' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ukm-gs-inactive-color"><?php esc_html_e( 'Inactive Marker Color', 'uk-interactive-map' ); ?></label></th>
                        <td>
                            <input type="color" id="ukm-gs-inactive-color" value="#a8c5ad">
                            <p class="description"><?php esc_html_e( 'Fill color applied to all other markers when one is selected.', 'uk-interactive-map' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ukm-gs-map-color"><?php esc_html_e( 'Map Fill Color', 'uk-interactive-map' ); ?></label></th>
                        <td>
                            <input type="color" id="ukm-gs-map-color" value="#6f9c76">
                            <p class="description"><?php esc_html_e( 'Default fill color applied to all map regions.', 'uk-interactive-map' ); ?></p>
                        </td>
                    </tr>
                    <!-- <tr>
                        <th scope="row"><label for="ukm-gs-selected-color"><?php esc_html_e( 'Selected Region Color', 'uk-interactive-map' ); ?></label></th>
                        <td>
                            <input type="color" id="ukm-gs-selected-color" value="#2271b1">
                            <p class="description"><?php esc_html_e( 'Fill color applied to a region when it is clicked/selected on the map.', 'uk-interactive-map' ); ?></p>
                        </td>
                    </tr> -->
                </table>
                <div class="ukm-gs-footer">
                    <button type="button" id="ukm-gs-save" class="button button-primary"><?php esc_html_e( 'Save Marker Settings', 'uk-interactive-map' ); ?></button>
                    <span id="ukm-gs-status" style="font-size:13px;color:#468847;"></span>
                </div>
            </div>

            <!-- Row 2: sidebar (22%) + editor (78%) -->
            <div id="ukm-admin-layout">

                <div id="ukm-region-sidebar">
                    <div id="ukm-sidebar-title"><?php esc_html_e( 'Regions', 'uk-interactive-map' ); ?></div>
                    <input type="search"
                           id="ukm-search"
                           placeholder="<?php esc_attr_e( 'Search regions…', 'uk-interactive-map' ); ?>">
                    <ul id="ukm-region-list"></ul>
                </div>

                <div id="ukm-region-editor">
                    <p class="ukm-placeholder"><?php esc_html_e( '← Select a region to edit it', 'uk-interactive-map' ); ?></p>
                </div>

            </div>
        </div>


        <script>
        (function() {
            'use strict';

            if ( typeof ukmAdmin === 'undefined' ) return;

            var regions     = ukmAdmin.regions;
            var regionNames = ukmAdmin.regionNames;
            var s           = ukmAdmin.strings;
            var currentCode = null;
            var projectCounter = 0;

            /* ---- global marker settings ---- */
            (function() {
                var gs             = ukmAdmin.settings || {};
                var iconInp         = document.getElementById('ukm-gs-icon');
                var sizeInp         = document.getElementById('ukm-gs-size');
                var colorInp        = document.getElementById('ukm-gs-color');
                var mapColorInp     = document.getElementById('ukm-gs-map-color');
                var selectedColInp  = document.getElementById('ukm-gs-selected-color');
                var inactiveColInp  = document.getElementById('ukm-gs-inactive-color');
                var preview        = document.getElementById('ukm-gs-icon-preview');
                var removeBtn      = document.getElementById('ukm-gs-icon-remove');
                var saveBtn        = document.getElementById('ukm-gs-save');
                var status         = document.getElementById('ukm-gs-status');
                var gsMedia        = null;

                /* populate from saved */
                iconInp.value         = gs.marker_icon    || '';
                sizeInp.value         = gs.marker_size    || 32;
                colorInp.value        = gs.marker_color   || '#e74c3c';
                mapColorInp.value     = gs.map_color      || '#6f9c76';
                selectedColInp.value  = gs.selected_color || '#2271b1';
                inactiveColInp.value  = gs.inactive_color || '#a8c5ad';

                if ( gs.marker_icon ) {
                    preview.querySelector('img').src = gs.marker_icon;
                    preview.style.display   = '';
                    removeBtn.style.display = '';
                }

                /* icon URL input live preview */
                iconInp.addEventListener('input', function() {
                    if ( this.value ) {
                        preview.querySelector('img').src = this.value;
                        preview.style.display   = '';
                        removeBtn.style.display = '';
                    } else {
                        preview.style.display   = 'none';
                        removeBtn.style.display = 'none';
                    }
                });

                /* remove icon */
                removeBtn.addEventListener('click', function() {
                    iconInp.value           = '';
                    preview.style.display   = 'none';
                    removeBtn.style.display = 'none';
                });

                /* media picker for icon */
                document.getElementById('ukm-gs-icon-btn').addEventListener('click', function() {
                    if ( ! window.wp || ! wp.media ) return;
                    if ( gsMedia ) { gsMedia.open(); return; }
                    gsMedia = wp.media({ title: s.chooseImage, button: { text: s.useImage }, multiple: false, library: { type: 'image' } });
                    gsMedia.on('select', function() {
                        var att = gsMedia.state().get('selection').first().toJSON();
                        var src = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
                        iconInp.value = src;
                        preview.querySelector('img').src = src;
                        preview.style.display   = '';
                        removeBtn.style.display = '';
                    });
                    gsMedia.open();
                });

                /* save */
                saveBtn.addEventListener('click', function() {
                    saveBtn.disabled   = true;
                    status.textContent = s.saving;

                    var data = {
                        marker_icon:    iconInp.value,
                        marker_size:    parseInt( sizeInp.value, 10 ) || 32,
                        marker_color:   colorInp.value,
                        map_color:      mapColorInp.value,
                        selected_color: selectedColInp.value,
                        inactive_color: inactiveColInp.value,
                    };
                    var body = new URLSearchParams({ action: 'ukm_save_settings', nonce: ukmAdmin.nonce, data: JSON.stringify(data) });

                    fetch( ukmAdmin.ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body.toString() })
                    .then(function(r){ return r.json(); })
                    .then(function(resp) {
                        saveBtn.disabled = false;
                        status.textContent = resp.success ? s.saved : s.saveError;
                        setTimeout(function(){ status.textContent = ''; }, 3000);
                    })
                    .catch(function() { saveBtn.disabled = false; status.textContent = s.saveError; });
                });
            })();

            /* ---- escape helper ---- */
            function esc( str ) {
                return String( str )
                    .replace( /&/g, '&amp;' )
                    .replace( /</g, '&lt;' )
                    .replace( />/g, '&gt;' )
                    .replace( /"/g, '&quot;' );
            }

            /* ---- region list ---- */
            function buildList( filter ) {
                var ul   = document.getElementById( 'ukm-region-list' );
                var frag = document.createDocumentFragment();
                filter   = ( filter || '' ).toLowerCase();

                Object.keys( regionNames ).sort( function(a,b) {
                    return regionNames[a].localeCompare( regionNames[b] );
                }).forEach( function( code ) {
                    var name = ( regions[code] && regions[code].name ) || regionNames[code];
                    if ( filter && name.toLowerCase().indexOf( filter ) === -1 ) return;

                    var li = document.createElement( 'li' );
                    li.dataset.code = code;

                    var count = ( regions[code] && regions[code].projects ) ? regions[code].projects.length : 0;
                    li.innerHTML = esc(name)
                        + ( count ? ' <span style="background:#2271b1;color:#fff;border-radius:10px;padding:1px 6px;font-size:11px;margin-left:4px;">' + count + '</span>' : '' );

                    if ( code === currentCode ) li.classList.add( 'ukm-active' );
                    li.addEventListener( 'click', function() { selectRegion( code ); } );
                    frag.appendChild( li );
                });

                ul.innerHTML = '';
                ul.appendChild( frag );
            }

            function selectRegion( code ) {
                currentCode = code;
                projectCounter = 0;
                buildList( document.getElementById('ukm-search').value );
                renderForm( code );
            }

            /* ---- form render ---- */
            function renderForm( code ) {
                var data       = regions[code] || {};
                var name       = data.name || regionNames[code] || code;
                var color      = data.color || '';
                var hoverColor = data.hover_color || '';
                var projects   = data.projects || [];

                var colorChecked = color      ? '' : ' checked';
                var hoverChecked = hoverColor ? '' : ' checked';
                var colorVal     = color      || '#88A4BC';
                var hoverVal     = hoverColor || '#3B729F';

                var html = '<div class="ukm-editor-title">'
                    + esc(name)
                    + '<span style="font-size:12px;font-weight:400;color:#888;margin-left:6px;">(' + esc(code) + ')</span>'
                    + '</div>'

                    + '<div class="ukm-editor-body">'

                    + '<table class="form-table" role="presentation">'

                    + '<tr><th scope="row"><label for="ukm-f-name">Region Name</label></th>'
                    + '<td><input type="text" id="ukm-f-name" class="regular-text" value="' + esc(name) + '"></td></tr>'

                    + '<tr><th scope="row">Fill Color</th>'
                    + '<td><div style="display:flex;align-items:center;gap:10px;">'
                    + '<input type="color" id="ukm-f-color" value="' + esc(colorVal) + '"' + (color ? '' : ' disabled') + '>'
                    + '<label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#646970;">'
                    + '<input type="checkbox" id="ukm-f-color-default"' + colorChecked + '> Use map default</label>'
                    + '</div></td></tr>'

                    + '<tr><th scope="row">Hover Color</th>'
                    + '<td><div style="display:flex;align-items:center;gap:10px;">'
                    + '<input type="color" id="ukm-f-hover" value="' + esc(hoverVal) + '"' + (hoverColor ? '' : ' disabled') + '>'
                    + '<label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#646970;">'
                    + '<input type="checkbox" id="ukm-f-hover-default"' + hoverChecked + '> Use map default</label>'
                    + '</div></td></tr>'

                    + '</table>'

                    + '<div class="ukm-projects-section">'
                    + '<h3>Projects <span style="font-weight:400;color:#999;font-size:12px;text-transform:none;letter-spacing:0;">(' + projects.length + ')</span></h3>'
                    + '<div id="ukm-projects-list"></div>'
                    + '<button type="button" class="button" id="ukm-add-project">+ Add Project</button>'
                    + '</div>'

                    + '<div class="ukm-editor-footer">'
                    + '<button type="button" id="ukm-save-btn" class="button button-primary">Save Region</button>'
                    + '<span id="ukm-save-status" style="font-size:13px;color:#468847;"></span>'
                    + '</div>'

                    + '</div>';

                document.getElementById('ukm-region-editor').innerHTML = html;

                /* color toggles */
                document.getElementById('ukm-f-color-default').addEventListener('change', function() {
                    document.getElementById('ukm-f-color').disabled = this.checked;
                });
                document.getElementById('ukm-f-hover-default').addEventListener('change', function() {
                    document.getElementById('ukm-f-hover').disabled = this.checked;
                });

                /* render existing projects */
                projects.forEach( function(p) {
                    addProjectRow( p );
                });

                /* add project */
                document.getElementById('ukm-add-project').addEventListener('click', function() {
                    addProjectRow({});
                });

                /* save */
                document.getElementById('ukm-save-btn').addEventListener('click', function() {
                    saveRegion( code );
                });
            }

            /* ---- project repeater row (title + URL only) ---- */
            function addProjectRow( data ) {
                var list = document.getElementById('ukm-projects-list');

                var row = document.createElement('div');
                row.className = 'ukm-project-row';

                row.innerHTML =
                    '<div class="ukm-project-row__header">'
                    + '<strong>Project</strong>'
                    + '<button type="button" class="button ukm-project-remove">Remove</button>'
                    + '</div>'
                    + '<table class="form-table" role="presentation">'

                    + '<tr><th><label>Title</label></th>'
                    + '<td><input type="text" class="regular-text ukm-proj-title" value="' + esc(data.title || '') + '"></td></tr>'

                    + '<tr><th><label>URL</label></th>'
                    + '<td><input type="url" class="large-text ukm-proj-url" value="' + esc(data.url || '') + '" placeholder="https://…"></td></tr>'

                    + '</table>';

                list.appendChild( row );
                renumberProjects();

                /* remove */
                row.querySelector('.ukm-project-remove').addEventListener('click', function() {
                    row.remove();
                    renumberProjects();
                });
            }

            function renumberProjects() {
                var rows = document.querySelectorAll('#ukm-projects-list .ukm-project-row');
                rows.forEach( function(row, i) {
                    row.querySelector('strong').textContent = 'Project ' + (i + 1);
                });
                var heading = document.querySelector('.ukm-projects-section h3');
                if ( heading ) {
                    heading.innerHTML = 'Projects <small style="font-weight:400;color:#888;">(' + rows.length + ')</small>';
                }
            }

            /* ---- AJAX save ---- */
            function saveRegion( code ) {
                var btn    = document.getElementById('ukm-save-btn');
                var status = document.getElementById('ukm-save-status');
                btn.disabled       = true;
                status.textContent = s.saving;

                var useDefaultColor = document.getElementById('ukm-f-color-default').checked;
                var useDefaultHover = document.getElementById('ukm-f-hover-default').checked;

                var projects = [];
                document.querySelectorAll('#ukm-projects-list .ukm-project-row').forEach( function(row) {
                    projects.push({
                        title: row.querySelector('.ukm-proj-title').value,
                        url:   row.querySelector('.ukm-proj-url').value,
                    });
                });

                var data = {
                    name:        document.getElementById('ukm-f-name').value,
                    color:       useDefaultColor ? '' : document.getElementById('ukm-f-color').value,
                    hover_color: useDefaultHover ? '' : document.getElementById('ukm-f-hover').value,
                    projects:    projects,
                };

                var body = new URLSearchParams({
                    action: 'ukm_save_region',
                    nonce:  ukmAdmin.nonce,
                    code:   code,
                    data:   JSON.stringify( data )
                });

                fetch( ukmAdmin.ajaxUrl, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body:    body.toString()
                })
                .then( function(r) { return r.json(); } )
                .then( function(resp) {
                    btn.disabled = false;
                    if ( resp.success ) {
                        regions[ code ] = data;
                        if ( data.name ) regionNames[ code ] = data.name;
                        status.textContent = s.saved;
                        buildList( document.getElementById('ukm-search').value );
                        setTimeout( function() { status.textContent = ''; }, 3000 );
                    } else {
                        status.textContent = s.saveError;
                    }
                })
                .catch( function() {
                    btn.disabled       = false;
                    status.textContent = s.saveError;
                });
            }

            /* ---- search ---- */
            document.getElementById('ukm-search').addEventListener('input', function() {
                buildList( this.value );
            });

            buildList('');

        })();
        </script>
        <?php
    }

    /* -------------------------------------------------------
       Helpers
    ------------------------------------------------------- */
    private static function sanitize_color( string $val ): string {
        if ( $val === '' ) return '';
        $clean = sanitize_hex_color( $val );
        return $clean ?: '';
    }
}

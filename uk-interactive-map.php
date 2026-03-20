<?php
/**
 * Plugin Name: UK Interactive Map
 * Plugin URI:  https://github.com/aljun-07/SVG-Map-UK
 * Description: An interactive SVG map of the UK with clickable/hoverable regions, tooltips, and modal popover cards. No dependencies required.
 * Version:     1.0.0
 * Author:      aljun-07
 * License:     GPL-2.0+
 * Text Domain: uk-interactive-map
 */

defined( 'ABSPATH' ) || exit;

define( 'UKM_VERSION',     '1.0.0' );
define( 'UKM_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'UKM_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

require_once UKM_PLUGIN_DIR . 'includes/class-uk-map-data.php';
require_once UKM_PLUGIN_DIR . 'includes/class-uk-map-shortcode.php';
require_once UKM_PLUGIN_DIR . 'includes/class-uk-map-admin.php';

add_action( 'init', [ 'UK_Map_Shortcode', 'register' ] );
add_action( 'admin_menu', [ 'UK_Map_Admin', 'register_menu' ] );
add_action( 'admin_init', [ 'UK_Map_Admin', 'register_settings' ] );

register_activation_hook( __FILE__, 'ukm_activate' );
function ukm_activate() {
    if ( ! get_option( 'ukm_region_data' ) ) {
        update_option( 'ukm_region_data', UK_Map_Data::defaults() );
    }
}

<?php
/**
 * Plugin Name: UK Interactive Map
 * Plugin URI:  https://github.com/aljun-07/SVG-Map-UK
 * Description: Interactive UK map with clickable regions. Customisable popup showing image, title, location and description. Drop [uk_interactive_map] anywhere in content.
 * Version:     2.0.0
 * Author:      aljun-07
 * License:     GPL-2.0+
 * Text Domain: uk-interactive-map
 */

defined( 'ABSPATH' ) || exit;

define( 'UKM_VERSION',    '2.0.0' );
define( 'UKM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UKM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once UKM_PLUGIN_DIR . 'includes/class-uk-map-data.php';
require_once UKM_PLUGIN_DIR . 'includes/class-uk-map-shortcode.php';
require_once UKM_PLUGIN_DIR . 'includes/class-uk-map-admin.php';

/* -------------------------------------------------------
   Front-end
------------------------------------------------------- */
add_action( 'init', [ 'UK_Map_Shortcode', 'register' ] );

/* -------------------------------------------------------
   Admin
------------------------------------------------------- */
add_action( 'admin_menu',             [ 'UK_Map_Admin', 'register_menu' ] );
add_action( 'admin_enqueue_scripts',  [ 'UK_Map_Admin', 'enqueue_admin_scripts' ] );

/* AJAX handlers */
add_action( 'wp_ajax_ukm_save_region',   [ 'UK_Map_Admin', 'ajax_save_region' ] );
add_action( 'wp_ajax_ukm_save_settings', [ 'UK_Map_Admin', 'ajax_save_settings' ] );

/* -------------------------------------------------------
   Activation
------------------------------------------------------- */
register_activation_hook( __FILE__, 'ukm_activate' );

function ukm_activate(): void {
    // Always write fresh defaults on activation (new region format).
    update_option( 'ukm_region_data', UK_Map_Data::defaults() );
}

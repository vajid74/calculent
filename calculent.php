<?php
/**
 * Plugin Name: Calculent Logic Plugin
 * Plugin URI: https://github.com/vajid74/calculent
 * Description: A comprehensive calculator logic plugin with customizable calculators and advanced features
 * Version: 1.0.0
 * Author: Vajid74
 * Author URI: https://github.com/vajid74
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: calculent
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'CALCULENT_VERSION', '1.0.0' );
define( 'CALCULENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CALCULENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CALCULENT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Load text domain
add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( 'calculent', false, dirname( CALCULENT_PLUGIN_BASENAME ) . '/languages' );
} );

// Include core files
require_once CALCULENT_PLUGIN_DIR . 'includes/class-calculent-loader.php';
require_once CALCULENT_PLUGIN_DIR . 'includes/class-calculent-activator.php';
require_once CALCULENT_PLUGIN_DIR . 'includes/class-calculent-deactivator.php';
require_once CALCULENT_PLUGIN_DIR . 'includes/class-calculent-calculator.php';
require_once CALCULENT_PLUGIN_DIR . 'includes/class-calculent-admin.php';
require_once CALCULENT_PLUGIN_DIR . 'includes/class-calculent-public.php';

// Activation and deactivation hooks
register_activation_hook( __FILE__, array( 'Calculent_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Calculent_Deactivator', 'deactivate' ) );

// Initialize the plugin
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'Calculent_Loader' ) ) {
        $plugin = new Calculent_Loader();
        $plugin->run();
    }
} );

// Plugin action links
add_filter( 'plugin_action_links_' . CALCULENT_PLUGIN_BASENAME, function( $links ) {
    $admin_link = sprintf(
        '<a href="%s">%s</a>',
        admin_url( 'admin.php?page=calculent-settings' ),
        __( 'Settings', 'calculent' )
    );
    array_unshift( $links, $admin_link );
    return $links;
} );

// Plugin row meta
add_filter( 'plugin_row_meta', function( $links, $file ) {
    if ( CALCULENT_PLUGIN_BASENAME === $file ) {
        $links[] = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            'https://github.com/vajid74/calculent/wiki',
            __( 'Documentation', 'calculent' )
        );
    }
    return $links;
}, 10, 2 );

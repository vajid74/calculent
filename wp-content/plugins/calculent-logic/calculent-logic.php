<?php
/**
 * Plugin Name: Calculent Logic
 * Plugin URI: https://github.com/vajid74/calculent
 * Description: Advanced calculation engine for WordPress with REST API, shortcodes, and admin settings.
 * Version: 1.0.0
 * Author: Vajid74
 * Author URI: https://github.com/vajid74
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: calculent-logic
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'CALCULENT_LOGIC_FILE', __FILE__ );
define( 'CALCULENT_LOGIC_DIR', dirname( CALCULENT_LOGIC_FILE ) );
define( 'CALCULENT_LOGIC_URL', plugin_dir_url( CALCULENT_LOGIC_FILE ) );
define( 'CALCULENT_LOGIC_VERSION', '1.0.0' );
define( 'CALCULENT_LOGIC_PREFIX', 'calculent_' );

require_once CALCULENT_LOGIC_DIR . '/includes/functions.php';
require_once CALCULENT_LOGIC_DIR . '/includes/class-calculator.php';
require_once CALCULENT_LOGIC_DIR . '/includes/class-api-handler.php';
require_once CALCULENT_LOGIC_DIR . '/includes/class-shortcode-handler.php';
require_once CALCULENT_LOGIC_DIR . '/includes/class-admin-settings.php';

/**
 * Main plugin bootstrap.
 */
class Calculent_Logic {
    /**
     * Singleton instance.
     *
     * @var Calculent_Logic|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->hooks();
    }

    /**
     * Register WordPress hooks.
     */
    private function hooks() {
        register_activation_hook( CALCULENT_LOGIC_FILE, [ $this, 'activate' ] );
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
        add_action( 'init', [ $this, 'register_shortcodes' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
    }

    /**
     * Plugin activation callback.
     */
    public function activate() {
        if ( ! get_option( CALCULENT_LOGIC_PREFIX . 'settings' ) ) {
            update_option( CALCULENT_LOGIC_PREFIX . 'settings', [
                'enable_api'      => 1,
                'enable_cache'    => 0,
                'cache_duration'  => 3600,
                'decimal_places'  => 2,
                'currency_symbol' => '$',
                'api_key_required'=> 0,
            ] );
        }
    }

    /**
     * Load translations.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'calculent-logic', false, basename( dirname( CALCULENT_LOGIC_FILE ) ) . '/languages' );
    }

    /**
     * Register shortcode handler.
     */
    public function register_shortcodes() {
        $shortcode_handler = new Calculent_Logic_Shortcode_Handler();
        $shortcode_handler->register();
    }

    /**
     * Register REST routes.
     */
    public function register_rest_routes() {
        $settings = get_option( CALCULENT_LOGIC_PREFIX . 'settings', [] );
        if ( empty( $settings['enable_api'] ) ) {
            return;
        }

        $api_handler = new Calculent_Logic_API_Handler();
        $api_handler->register_routes();
    }

    /**
     * Register admin menu and settings.
     */
    public function register_admin_menu() {
        $admin = new Calculent_Logic_Admin_Settings();
        $admin->init();
    }

    /**
     * Enqueue frontend assets for calculators.
     */
    public function enqueue_frontend_assets() {
        wp_register_style(
            'calculent-logic',
            CALCULENT_LOGIC_URL . 'assets/css/calculent-logic.css',
            [],
            CALCULENT_LOGIC_VERSION
        );

        wp_register_script(
            'calculent-logic',
            CALCULENT_LOGIC_URL . 'assets/js/calculent-logic.js',
            [ 'wp-element' ],
            CALCULENT_LOGIC_VERSION,
            true
        );

        wp_localize_script( 'calculent-logic', 'calculentLogic', [
            'restUrl' => esc_url_raw( rest_url( 'calculent/v1/calculate' ) ),
            'nonce'   => wp_create_nonce( 'wp_rest' ),
        ] );
    }
}

Calculent_Logic::get_instance();

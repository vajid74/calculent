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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'CALCULENT_LOGIC_FILE', __FILE__ );
define( 'CALCULENT_LOGIC_DIR', dirname( CALCULENT_LOGIC_FILE ) );
define( 'CALCULENT_LOGIC_URL', plugin_dir_url( CALCULENT_LOGIC_FILE ) );
define( 'CALCULENT_LOGIC_VERSION', '1.0.0' );
define( 'CALCULENT_LOGIC_PREFIX', 'calculent_' );

/**
 * Main Plugin Class
 */
class Calculent_Logic {
    /**
     * Plugin instance
     */
    private static $instance = null;

    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook( CALCULENT_LOGIC_FILE, [ $this, 'activate' ] );
        register_deactivation_hook( CALCULENT_LOGIC_FILE, [ $this, 'deactivate' ] );

        // Plugin initialization
        add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );
        add_action( 'init', [ $this, 'register_shortcodes' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );
    }

    /**
     * Load dependencies
     */
    private function load_dependencies() {
        // Load helper functions
        require_once CALCULENT_LOGIC_DIR . '/includes/class-calculator.php';
        require_once CALCULENT_LOGIC_DIR . '/includes/class-api-handler.php';
        require_once CALCULENT_LOGIC_DIR . '/includes/class-shortcode-handler.php';
        require_once CALCULENT_LOGIC_DIR . '/includes/class-admin-settings.php';
        require_once CALCULENT_LOGIC_DIR . '/includes/functions.php';
    }

    /**
     * Plugin activation hook
     */
    public function activate() {
        // Create necessary database tables
        $this->create_tables();

        // Set default options
        if ( ! get_option( CALCULENT_LOGIC_PREFIX . 'db_version' ) ) {
            update_option( CALCULENT_LOGIC_PREFIX . 'db_version', CALCULENT_LOGIC_VERSION );
        }

        if ( ! get_option( CALCULENT_LOGIC_PREFIX . 'settings' ) ) {
            update_option( CALCULENT_LOGIC_PREFIX . 'settings', [
                'enable_api' => 1,
                'enable_cache' => 1,
                'cache_duration' => 3600,
                'decimal_places' => 2,
                'currency_symbol' => '$',
                'api_key_required' => 0,
            ] );
        }

        // Flush rewrite rules
        flush_rewrite_rules();

        // Log activation
        error_log( 'Calculent Logic plugin activated at ' . current_time( 'mysql' ) );
    }

    /**
     * Plugin deactivation hook
     */
    public function deactivate() {
        // Clear scheduled hooks
        wp_clear_scheduled_hook( CALCULENT_LOGIC_PREFIX . 'clear_cache' );

        // Flush rewrite rules
        flush_rewrite_rules();

        // Log deactivation
        error_log( 'Calculent Logic plugin deactivated at ' . current_time( 'mysql' ) );
    }

    /**
     * Load plugin text domain for translations
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'calculent-logic',
            false,
            dirname( plugin_basename( CALCULENT_LOGIC_FILE ) ) . '/languages'
        );
    }

    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Calculations history table
        $calculations_table = $wpdb->prefix . CALCULENT_LOGIC_PREFIX . 'calculations';
        $sql_calculations = "CREATE TABLE IF NOT EXISTS $calculations_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            calculation_type VARCHAR(100) NOT NULL,
            input_data LONGTEXT NOT NULL,
            result LONGTEXT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY calculation_type (calculation_type),
            KEY created_at (created_at)
        ) $charset_collate;";

        // API logs table
        $logs_table = $wpdb->prefix . CALCULENT_LOGIC_PREFIX . 'api_logs';
        $sql_logs = "CREATE TABLE IF NOT EXISTS $logs_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            api_key VARCHAR(255),
            endpoint VARCHAR(255) NOT NULL,
            method VARCHAR(10) NOT NULL,
            request_data LONGTEXT,
            response_status INT(3),
            response_time FLOAT,
            ip_address VARCHAR(45),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY api_key (api_key),
            KEY endpoint (endpoint),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Saved calculations table
        $saved_table = $wpdb->prefix . CALCULENT_LOGIC_PREFIX . 'saved_calculations';
        $sql_saved = "CREATE TABLE IF NOT EXISTS $saved_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            calculation_type VARCHAR(100) NOT NULL,
            formula TEXT NOT NULL,
            variables LONGTEXT,
            is_public TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY is_public (is_public)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_calculations );
        dbDelta( $sql_logs );
        dbDelta( $sql_saved );
    }

    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        $shortcode_handler = new Calculent_Shortcode_Handler();
        add_shortcode( 'calculent', [ $shortcode_handler, 'handle_calculator' ] );
        add_shortcode( 'calculent_simple', [ $shortcode_handler, 'handle_simple_calculator' ] );
        add_shortcode( 'calculent_advanced', [ $shortcode_handler, 'handle_advanced_calculator' ] );
        add_shortcode( 'calculent_list', [ $shortcode_handler, 'handle_calculations_list' ] );
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        $api_handler = new Calculent_API_Handler();

        // Basic calculation endpoint
        register_rest_route(
            'calculent/v1',
            '/calculate',
            [
                'methods' => 'POST',
                'callback' => [ $api_handler, 'calculate' ],
                'permission_callback' => [ $this, 'check_api_permissions' ],
                'args' => [
                    'type' => [
                        'required' => true,
                        'type' => 'string',
                        'description' => 'Calculation type',
                    ],
                    'data' => [
                        'required' => true,
                        'type' => 'object',
                        'description' => 'Calculation data',
                    ],
                ],
            ]
        );

        // Get calculation history
        register_rest_route(
            'calculent/v1',
            '/history',
            [
                'methods' => 'GET',
                'callback' => [ $api_handler, 'get_history' ],
                'permission_callback' => [ $this, 'check_api_permissions' ],
            ]
        );

        // Get saved calculations
        register_rest_route(
            'calculent/v1',
            '/saved',
            [
                'methods' => 'GET',
                'callback' => [ $api_handler, 'get_saved_calculations' ],
                'permission_callback' => [ $this, 'check_api_permissions' ],
            ]
        );

        // Save calculation
        register_rest_route(
            'calculent/v1',
            '/save',
            [
                'methods' => 'POST',
                'callback' => [ $api_handler, 'save_calculation' ],
                'permission_callback' => [ $this, 'check_api_permissions' ],
            ]
        );

        // Delete calculation
        register_rest_route(
            'calculent/v1',
            '/delete/(?P<id>\d+)',
            [
                'methods' => 'DELETE',
                'callback' => [ $api_handler, 'delete_calculation' ],
                'permission_callback' => [ $this, 'check_api_permissions' ],
            ]
        );

        // Get calculation statistics
        register_rest_route(
            'calculent/v1',
            '/stats',
            [
                'methods' => 'GET',
                'callback' => [ $api_handler, 'get_statistics' ],
                'permission_callback' => [ $this, 'check_api_permissions' ],
            ]
        );

        // Advanced formula evaluation
        register_rest_route(
            'calculent/v1',
            '/evaluate',
            [
                'methods' => 'POST',
                'callback' => [ $api_handler, 'evaluate_formula' ],
                'permission_callback' => [ $this, 'check_api_permissions' ],
                'args' => [
                    'formula' => [
                        'required' => true,
                        'type' => 'string',
                    ],
                    'variables' => [
                        'required' => false,
                        'type' => 'object',
                    ],
                ],
            ]
        );
    }

    /**
     * Check API permissions
     */
    public function check_api_permissions() {
        $settings = get_option( CALCULENT_LOGIC_PREFIX . 'settings', [] );

        // Allow if API is not enabled but check if key is required
        if ( empty( $settings['enable_api'] ) ) {
            return new WP_Error(
                'api_disabled',
                __( 'API is disabled', 'calculent-logic' ),
                [ 'status' => 403 ]
            );
        }

        // Check if API key is required
        if ( ! empty( $settings['api_key_required'] ) ) {
            $api_key = isset( $_SERVER['HTTP_X_API_KEY'] ) ? sanitize_text_field( $_SERVER['HTTP_X_API_KEY'] ) : '';
            $stored_key = get_option( CALCULENT_LOGIC_PREFIX . 'api_key' );

            if ( empty( $api_key ) || $api_key !== $stored_key ) {
                return new WP_Error(
                    'invalid_api_key',
                    __( 'Invalid or missing API key', 'calculent-logic' ),
                    [ 'status' => 401 ]
                );
            }
        }

        return true;
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Calculent Logic', 'calculent-logic' ),
            __( 'Calculent', 'calculent-logic' ),
            'manage_options',
            'calculent-logic',
            [ $this, 'render_admin_page' ],
            'dashicons-calculator',
            30
        );

        add_submenu_page(
            'calculent-logic',
            __( 'Settings', 'calculent-logic' ),
            __( 'Settings', 'calculent-logic' ),
            'manage_options',
            'calculent-logic',
            [ $this, 'render_admin_page' ]
        );

        add_submenu_page(
            'calculent-logic',
            __( 'History', 'calculent-logic' ),
            __( 'History', 'calculent-logic' ),
            'manage_options',
            'calculent-logic-history',
            [ $this, 'render_history_page' ]
        );

        add_submenu_page(
            'calculent-logic',
            __( 'Saved Calculations', 'calculent-logic' ),
            __( 'Saved', 'calculent-logic' ),
            'manage_options',
            'calculent-logic-saved',
            [ $this, 'render_saved_page' ]
        );

        add_submenu_page(
            'calculent-logic',
            __( 'API Logs', 'calculent-logic' ),
            __( 'API Logs', 'calculent-logic' ),
            'manage_options',
            'calculent-logic-logs',
            [ $this, 'render_logs_page' ]
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'calculent_settings_group',
            CALCULENT_LOGIC_PREFIX . 'settings',
            [
                'type' => 'array',
                'sanitize_callback' => [ $this, 'sanitize_settings' ],
            ]
        );

        add_settings_section(
            'calculent_main_section',
            __( 'Main Settings', 'calculent-logic' ),
            [ $this, 'render_settings_section' ],
            'calculent_settings_group'
        );

        add_settings_field(
            'calculent_enable_api',
            __( 'Enable REST API', 'calculent-logic' ),
            [ $this, 'render_checkbox_field' ],
            'calculent_settings_group',
            'calculent_main_section',
            [ 'name' => 'enable_api' ]
        );

        add_settings_field(
            'calculent_enable_cache',
            __( 'Enable Caching', 'calculent-logic' ),
            [ $this, 'render_checkbox_field' ],
            'calculent_settings_group',
            'calculent_main_section',
            [ 'name' => 'enable_cache' ]
        );

        add_settings_field(
            'calculent_cache_duration',
            __( 'Cache Duration (seconds)', 'calculent-logic' ),
            [ $this, 'render_text_field' ],
            'calculent_settings_group',
            'calculent_main_section',
            [ 'name' => 'cache_duration' ]
        );

        add_settings_field(
            'calculent_decimal_places',
            __( 'Decimal Places', 'calculent-logic' ),
            [ $this, 'render_text_field' ],
            'calculent_settings_group',
            'calculent_main_section',
            [ 'name' => 'decimal_places' ]
        );

        add_settings_field(
            'calculent_currency_symbol',
            __( 'Currency Symbol', 'calculent-logic' ),
            [ $this, 'render_text_field' ],
            'calculent_settings_group',
            'calculent_main_section',
            [ 'name' => 'currency_symbol' ]
        );

        add_settings_field(
            'calculent_api_key_required',
            __( 'Require API Key', 'calculent-logic' ),
            [ $this, 'render_checkbox_field' ],
            'calculent_settings_group',
            'calculent_main_section',
            [ 'name' => 'api_key_required' ]
        );
    }

    /**
     * Sanitize settings
     */
    public function sanitize_settings( $settings ) {
        if ( ! is_array( $settings ) ) {
            $settings = [];
        }

        return [
            'enable_api' => ! empty( $settings['enable_api'] ) ? 1 : 0,
            'enable_cache' => ! empty( $settings['enable_cache'] ) ? 1 : 0,
            'cache_duration' => absint( $settings['cache_duration'] ?? 3600 ),
            'decimal_places' => absint( $settings['decimal_places'] ?? 2 ),
            'currency_symbol' => sanitize_text_field( $settings['currency_symbol'] ?? '$' ),
            'api_key_required' => ! empty( $settings['api_key_required'] ) ? 1 : 0,
        ];
    }

    /**
     * Render settings section
     */
    public function render_settings_section() {
        echo '<p>' . esc_html__( 'Configure Calculent Logic plugin settings', 'calculent-logic' ) . '</p>';
    }

    /**
     * Render checkbox field
     */
    public function render_checkbox_field( $args ) {
        $settings = get_option( CALCULENT_LOGIC_PREFIX . 'settings', [] );
        $name = $args['name'];
        $value = ! empty( $settings[ $name ] ) ? 1 : 0;
        ?>
        <input type="checkbox" name="<?php echo esc_attr( CALCULENT_LOGIC_PREFIX . 'settings[' . $name . ']' ); ?>" value="1" <?php checked( $value, 1 ); ?> />
        <?php
    }

    /**
     * Render text field
     */
    public function render_text_field( $args ) {
        $settings = get_option( CALCULENT_LOGIC_PREFIX . 'settings', [] );
        $name = $args['name'];
        $value = $settings[ $name ] ?? '';
        ?>
        <input type="text" name="<?php echo esc_attr( CALCULENT_LOGIC_PREFIX . 'settings[' . $name . ']' ); ?>" value="<?php echo esc_attr( $value ); ?>" />
        <?php
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions', 'calculent-logic' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'calculent_settings_group' );
                do_settings_sections( 'calculent_settings_group' );
                submit_button();
                ?>
            </form>

            <div style="margin-top: 40px;">
                <h2><?php esc_html_e( 'Plugin Information', 'calculent-logic' ); ?></h2>
                <ul>
                    <li><strong><?php esc_html_e( 'Version:', 'calculent-logic' ); ?></strong> <?php echo esc_html( CALCULENT_LOGIC_VERSION ); ?></li>
                    <li><strong><?php esc_html_e( 'Plugin Directory:', 'calculent-logic' ); ?></strong> <?php echo esc_html( CALCULENT_LOGIC_DIR ); ?></li>
                    <li><strong><?php esc_html_e( 'Database Prefix:', 'calculent-logic' ); ?></strong> <?php echo esc_html( CALCULENT_LOGIC_PREFIX ); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Render history page
     */
    public function render_history_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions', 'calculent-logic' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <p><?php esc_html_e( 'View calculation history', 'calculent-logic' ); ?></p>
            <!-- History table will be rendered here -->
        </div>
        <?php
    }

    /**
     * Render saved page
     */
    public function render_saved_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions', 'calculent-logic' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <p><?php esc_html_e( 'View saved calculations', 'calculent-logic' ); ?></p>
            <!-- Saved calculations table will be rendered here -->
        </div>
        <?php
    }

    /**
     * Render logs page
     */
    public function render_logs_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions', 'calculent-logic' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <p><?php esc_html_e( 'View API request logs', 'calculent-logic' ); ?></p>
            <!-- API logs table will be rendered here -->
        </div>
        <?php
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts() {
        if ( ! isset( $_GET['page'] ) || strpos( sanitize_text_field( $_GET['page'] ), 'calculent' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'calculent-admin-css',
            CALCULENT_LOGIC_URL . 'assets/css/admin.css',
            [],
            CALCULENT_LOGIC_VERSION
        );

        wp_enqueue_script(
            'calculent-admin-js',
            CALCULENT_LOGIC_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            CALCULENT_LOGIC_VERSION,
            true
        );

        wp_localize_script(
            'calculent-admin-js',
            'calculentAdmin',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'calculent_admin_nonce' ),
            ]
        );
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'calculent-frontend-css',
            CALCULENT_LOGIC_URL . 'assets/css/frontend.css',
            [],
            CALCULENT_LOGIC_VERSION
        );

        wp_enqueue_script(
            'calculent-frontend-js',
            CALCULENT_LOGIC_URL . 'assets/js/frontend.js',
            [ 'jquery' ],
            CALCULENT_LOGIC_VERSION,
            true
        );

        wp_localize_script(
            'calculent-frontend-js',
            'calculentFrontend',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'rest_url' => rest_url( 'calculent/v1' ),
                'nonce' => wp_create_nonce( 'calculent_frontend_nonce' ),
            ]
        );
    }
}

/**
 * Initialize the plugin
 */
function calculent_logic_init() {
    return Calculent_Logic::get_instance();
}

// Initialize plugin
add_action( 'plugins_loaded', 'calculent_logic_init' );

// Register activation/deactivation hooks at plugin load
if ( ! function_exists( 'register_activation_hook' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

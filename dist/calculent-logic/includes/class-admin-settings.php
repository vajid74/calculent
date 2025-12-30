<?php
/**
 * Admin settings page.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Calculent_Logic_Admin_Settings {
    /**
     * Hook admin actions.
     */
    public function init() {
        add_options_page(
            __( 'Calculent Logic', 'calculent-logic' ),
            __( 'Calculent Logic', 'calculent-logic' ),
            'manage_options',
            'calculent-logic',
            [ $this, 'render_page' ]
        );

        register_setting( 'calculent_logic', CALCULENT_LOGIC_PREFIX . 'settings', [ $this, 'sanitize_settings' ] );

        add_settings_section(
            'calculent_logic_main',
            __( 'API & Behavior', 'calculent-logic' ),
            '__return_false',
            'calculent_logic'
        );

        add_settings_field(
            'enable_api',
            __( 'Enable REST API', 'calculent-logic' ),
            [ $this, 'render_checkbox' ],
            'calculent_logic',
            'calculent_logic_main',
            [ 'key' => 'enable_api' ]
        );

        add_settings_field(
            'enable_cache',
            __( 'Enable cache', 'calculent-logic' ),
            [ $this, 'render_checkbox' ],
            'calculent_logic',
            'calculent_logic_main',
            [ 'key' => 'enable_cache' ]
        );

        add_settings_field(
            'cache_duration',
            __( 'Cache duration (seconds)', 'calculent-logic' ),
            [ $this, 'render_number' ],
            'calculent_logic',
            'calculent_logic_main',
            [ 'key' => 'cache_duration', 'min' => 60 ]
        );

        add_settings_field(
            'decimal_places',
            __( 'Decimal places', 'calculent-logic' ),
            [ $this, 'render_number' ],
            'calculent_logic',
            'calculent_logic_main',
            [ 'key' => 'decimal_places', 'min' => 0, 'max' => 6 ]
        );

        add_settings_field(
            'currency_symbol',
            __( 'Currency symbol', 'calculent-logic' ),
            [ $this, 'render_text' ],
            'calculent_logic',
            'calculent_logic_main',
            [ 'key' => 'currency_symbol' ]
        );
    }

    /**
     * Sanitize settings payload.
     *
     * @param array $settings Settings array.
     *
     * @return array
     */
    public function sanitize_settings( $settings ) {
        return [
            'enable_api'      => isset( $settings['enable_api'] ) ? 1 : 0,
            'enable_cache'    => isset( $settings['enable_cache'] ) ? 1 : 0,
            'cache_duration'  => absint( $settings['cache_duration'] ?? 3600 ),
            'decimal_places'  => absint( $settings['decimal_places'] ?? 2 ),
            'currency_symbol' => sanitize_text_field( $settings['currency_symbol'] ?? '$' ),
            'api_key_required'=> isset( $settings['api_key_required'] ) ? 1 : 0,
        ];
    }

    /**
     * Render admin page.
     */
    public function render_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Calculent Logic', 'calculent-logic' ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'calculent_logic' );
                do_settings_sections( 'calculent_logic' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render a checkbox field.
     */
    public function render_checkbox( $args ) {
        $settings = get_option( CALCULENT_LOGIC_PREFIX . 'settings', [] );
        $key      = $args['key'];
        $checked  = ! empty( $settings[ $key ] ) ? 'checked' : '';
        printf( '<input type="checkbox" name="%1$s[%2$s]" %3$s />', esc_attr( CALCULENT_LOGIC_PREFIX . 'settings' ), esc_attr( $key ), $checked );
    }

    /**
     * Render a numeric field.
     */
    public function render_number( $args ) {
        $settings = get_option( CALCULENT_LOGIC_PREFIX . 'settings', [] );
        $key      = $args['key'];
        $value    = isset( $settings[ $key ] ) ? intval( $settings[ $key ] ) : '';
        $min      = isset( $args['min'] ) ? 'min="' . esc_attr( $args['min'] ) . '"' : '';
        $max      = isset( $args['max'] ) ? 'max="' . esc_attr( $args['max'] ) . '"' : '';
        printf( '<input type="number" name="%1$s[%2$s]" value="%3$s" %4$s %5$s />', esc_attr( CALCULENT_LOGIC_PREFIX . 'settings' ), esc_attr( $key ), esc_attr( $value ), $min, $max );
    }

    /**
     * Render text field.
     */
    public function render_text( $args ) {
        $settings = get_option( CALCULENT_LOGIC_PREFIX . 'settings', [] );
        $key      = $args['key'];
        $value    = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
        printf( '<input type="text" name="%1$s[%2$s]" value="%3$s" />', esc_attr( CALCULENT_LOGIC_PREFIX . 'settings' ), esc_attr( $key ), esc_attr( $value ) );
    }
}

<?php
/**
 * Shortcode handler for embedding calculators.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Calculent_Logic_Shortcode_Handler {
    /**
     * Register shortcodes.
     */
    public function register() {
        add_shortcode( 'calculent_tool', [ $this, 'render_shortcode' ] );
    }

    /**
     * Render the calculator UI.
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [ 'type' => '' ], $atts );
        $type = sanitize_key( $atts['type'] );
        $tool = calculent_logic_get_tool( $type );

        if ( ! $tool ) {
            return '<div class="calculent-tool-error">' . esc_html__( 'Unknown calculator.', 'calculent-logic' ) . '</div>';
        }

        wp_enqueue_style( 'calculent-logic' );
        wp_enqueue_script( 'calculent-logic' );

        ob_start();
        ?>
        <div class="calculent-tool" data-calculent-type="<?php echo esc_attr( $type ); ?>">
            <div class="calculent-tool__header">
                <h3><?php echo esc_html( $tool['label'] ); ?></h3>
                <p class="calculent-tool__summary">&nbsp;</p>
            </div>
            <form class="calculent-tool__form">
                <div class="calculent-tool__grid">
                    <?php foreach ( $tool['fields'] as $field ) : ?>
                        <label class="calculent-field">
                            <span><?php echo esc_html( $field['label'] ); ?></span>
                            <?php echo $this->render_field( $field ); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="calculent-button"><?php esc_html_e( 'Calculate', 'calculent-logic' ); ?></button>
            </form>
            <div class="calculent-tool__result" aria-live="polite"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render a field input.
     *
     * @param array $field Field definition.
     *
     * @return string
     */
    private function render_field( $field ) {
        $name     = esc_attr( $field['name'] );
        $required = ! empty( $field['required'] ) ? 'required' : '';
        $default  = $field['default'] ?? '';

        if ( 'select' === $field['type'] ) {
            $options_markup = '';
            foreach ( $field['options'] as $value => $label ) {
                $options_markup .= sprintf(
                    '<option value="%1$s">%2$s</option>',
                    esc_attr( $value ),
                    esc_html( $label )
                );
            }

            return sprintf(
                '<select name="%1$s" %2$s>%3$s</select>',
                $name,
                $required,
                $options_markup
            );
        }

        if ( 'checkbox' === $field['type'] ) {
            $checked = $default ? 'checked' : '';
            return sprintf(
                '<input type="checkbox" name="%1$s" value="1" %2$s %3$s />',
                $name,
                $required,
                $checked
            );
        }

        $attrs = [
            'min'   => $field['min'] ?? null,
            'max'   => $field['max'] ?? null,
            'step'  => $field['step'] ?? null,
            'value' => $default,
        ];

        $extra = '';
        foreach ( $attrs as $attr => $value ) {
            if ( null !== $value && '' !== $value ) {
                $extra .= sprintf( ' %1$s="%2$s"', esc_attr( $attr ), esc_attr( $value ) );
            }
        }

        return sprintf(
            '<input type="%1$s" name="%2$s" %3$s %4$s />',
            esc_attr( $field['type'] ),
            $name,
            $required,
            trim( $extra )
        );
    }
}

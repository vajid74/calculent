<?php
/**
 * Shared helper functions for Calculent Logic.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Supported calculator definitions.
 *
 * @return array
 */
function calculent_logic_get_tools() {
    return [
        'mortgage' => [
            'label'  => __( 'Mortgage Calculator', 'calculent-logic' ),
            'fields' => [
                [ 'name' => 'principal', 'label' => __( 'Loan Amount', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'min' => 0 ],
                [ 'name' => 'rate', 'label' => __( 'Interest Rate (%)', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'min' => 0, 'step' => '0.01' ],
                [ 'name' => 'years', 'label' => __( 'Term (years)', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'min' => 1 ],
            ],
        ],
        'bmi' => [
            'label'  => __( 'BMI Calculator', 'calculent-logic' ),
            'fields' => [
                [ 'name' => 'weight', 'label' => __( 'Weight (kg)', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'min' => 0, 'step' => '0.1' ],
                [ 'name' => 'height', 'label' => __( 'Height (cm)', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'min' => 0, 'step' => '0.1' ],
            ],
        ],
        'tip' => [
            'label'  => __( 'Tip Calculator', 'calculent-logic' ),
            'fields' => [
                [ 'name' => 'bill', 'label' => __( 'Bill Amount', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'min' => 0, 'step' => '0.01' ],
                [ 'name' => 'percentage', 'label' => __( 'Tip %', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'min' => 0, 'step' => '0.1' ],
                [ 'name' => 'people', 'label' => __( 'Split Between', 'calculent-logic' ), 'type' => 'number', 'required' => false, 'min' => 1, 'step' => '1' ],
            ],
        ],
        'temperature' => [
            'label'  => __( 'Temperature Converter', 'calculent-logic' ),
            'fields' => [
                [ 'name' => 'value', 'label' => __( 'Value', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'step' => '0.1' ],
                [
                    'name'    => 'scale',
                    'label'   => __( 'Scale', 'calculent-logic' ),
                    'type'    => 'select',
                    'options' => [
                        'celsius'    => __( 'Celsius', 'calculent-logic' ),
                        'fahrenheit' => __( 'Fahrenheit', 'calculent-logic' ),
                    ],
                ],
            ],
        ],
        'password-generator' => [
            'label'  => __( 'Password Generator', 'calculent-logic' ),
            'fields' => [
                [ 'name' => 'length', 'label' => __( 'Length', 'calculent-logic' ), 'type' => 'number', 'required' => true, 'min' => 6, 'max' => 64, 'step' => '1', 'default' => 12 ],
                [ 'name' => 'uppercase', 'label' => __( 'Include Uppercase', 'calculent-logic' ), 'type' => 'checkbox', 'default' => true ],
                [ 'name' => 'numbers', 'label' => __( 'Include Numbers', 'calculent-logic' ), 'type' => 'checkbox', 'default' => true ],
                [ 'name' => 'symbols', 'label' => __( 'Include Symbols', 'calculent-logic' ), 'type' => 'checkbox', 'default' => false ],
            ],
        ],
    ];
}

/**
 * Fetch a single tool definition.
 *
 * @param string $type Tool key.
 *
 * @return array|null
 */
function calculent_logic_get_tool( $type ) {
    $tools = calculent_logic_get_tools();
    return $tools[ $type ] ?? null;
}

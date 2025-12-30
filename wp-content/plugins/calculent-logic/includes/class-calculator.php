<?php
/**
 * Calculator service.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Calculent_Logic_Calculator {
    /**
     * Execute a calculation.
     *
     * @param string $type   Calculator type.
     * @param array  $params Input parameters.
     *
     * @return array
     */
    public function calculate( $type, $params ) {
        switch ( $type ) {
            case 'mortgage':
                return $this->calculate_mortgage( $params );
            case 'bmi':
                return $this->calculate_bmi( $params );
            case 'tip':
                return $this->calculate_tip( $params );
            case 'temperature':
                return $this->convert_temperature( $params );
            case 'password-generator':
                return $this->generate_password( $params );
            default:
                return [
                    'error' => __( 'Unsupported calculator type.', 'calculent-logic' ),
                ];
        }
    }

    private function calculate_mortgage( $params ) {
        $principal = isset( $params['principal'] ) ? floatval( $params['principal'] ) : 0;
        $rate      = isset( $params['rate'] ) ? floatval( $params['rate'] ) / 100 : 0;
        $years     = isset( $params['years'] ) ? floatval( $params['years'] ) : 0;

        if ( $principal <= 0 || $rate <= 0 || $years <= 0 ) {
            return [ 'error' => __( 'Please provide principal, rate, and term.', 'calculent-logic' ) ];
        }

        $monthly_rate = $rate / 12;
        $payments     = $years * 12;
        $power        = pow( 1 + $monthly_rate, $payments );
        $payment      = ( $principal * $monthly_rate * $power ) / ( $power - 1 );

        return [
            'result'  => round( $payment, 2 ),
            'summary' => sprintf( __( 'Monthly payment over %d years.', 'calculent-logic' ), $years ),
        ];
    }

    private function calculate_bmi( $params ) {
        $weight = isset( $params['weight'] ) ? floatval( $params['weight'] ) : 0;
        $height = isset( $params['height'] ) ? floatval( $params['height'] ) : 0;

        if ( $weight <= 0 || $height <= 0 ) {
            return [ 'error' => __( 'Please provide weight and height.', 'calculent-logic' ) ];
        }

        $height_m = $height / 100;
        $bmi      = $weight / ( $height_m * $height_m );

        $status = __( 'Healthy', 'calculent-logic' );
        if ( $bmi < 18.5 ) {
            $status = __( 'Underweight', 'calculent-logic' );
        } elseif ( $bmi >= 25 && $bmi < 30 ) {
            $status = __( 'Overweight', 'calculent-logic' );
        } elseif ( $bmi >= 30 ) {
            $status = __( 'Obese', 'calculent-logic' );
        }

        return [
            'result'  => round( $bmi, 2 ),
            'summary' => sprintf( __( 'Status: %s', 'calculent-logic' ), $status ),
        ];
    }

    private function calculate_tip( $params ) {
        $bill       = isset( $params['bill'] ) ? floatval( $params['bill'] ) : 0;
        $percentage = isset( $params['percentage'] ) ? floatval( $params['percentage'] ) : 0;
        $people     = isset( $params['people'] ) && intval( $params['people'] ) > 0 ? intval( $params['people'] ) : 1;

        if ( $bill <= 0 || $percentage < 0 ) {
            return [ 'error' => __( 'Please provide bill amount and tip percentage.', 'calculent-logic' ) ];
        }

        $tip      = $bill * ( $percentage / 100 );
        $total    = $bill + $tip;
        $per_user = $total / $people;

        return [
            'result'  => round( $per_user, 2 ),
            'summary' => sprintf( __( 'Tip: %1$.2f • Total: %2$.2f • Each: %3$.2f', 'calculent-logic' ), $tip, $total, $per_user ),
        ];
    }

    private function convert_temperature( $params ) {
        $value = isset( $params['value'] ) ? floatval( $params['value'] ) : null;
        $scale = $params['scale'] ?? 'celsius';

        if ( null === $value ) {
            return [ 'error' => __( 'Please provide a temperature value.', 'calculent-logic' ) ];
        }

        if ( 'fahrenheit' === $scale ) {
            $celsius    = ( $value - 32 ) * 5 / 9;
            $fahrenheit = $value;
        } else {
            $celsius    = $value;
            $fahrenheit = ( $value * 9 / 5 ) + 32;
        }

        return [
            'result'  => round( $celsius, 2 ),
            'summary' => sprintf( __( '%1$.2f °C / %2$.2f °F', 'calculent-logic' ), $celsius, $fahrenheit ),
        ];
    }

    private function generate_password( $params ) {
        $length    = isset( $params['length'] ) ? max( 6, min( 64, intval( $params['length'] ) ) ) : 12;
        $uppercase = ! empty( $params['uppercase'] );
        $numbers   = ! empty( $params['numbers'] );
        $symbols   = ! empty( $params['symbols'] );

        $chars = 'abcdefghijklmnopqrstuvwxyz';
        if ( $uppercase ) {
            $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ( $numbers ) {
            $chars .= '0123456789';
        }
        if ( $symbols ) {
            $chars .= '!@#$%^&*()_+-={}[]:;,.?';
        }

        if ( empty( $chars ) ) {
            return [ 'error' => __( 'Please choose at least one character set.', 'calculent-logic' ) ];
        }

        $password = '';
        $max      = strlen( $chars ) - 1;
        for ( $i = 0; $i < $length; $i++ ) {
            $password .= $chars[ wp_rand( 0, $max ) ];
        }

        return [
            'result'  => $password,
            'summary' => __( 'Random password generated', 'calculent-logic' ),
        ];
    }
}

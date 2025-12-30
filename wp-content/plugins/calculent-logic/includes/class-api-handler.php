<?php
/**
 * REST API handler for Calculent Logic.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Calculent_Logic_API_Handler {
    /**
     * Register REST routes.
     */
    public function register_routes() {
        register_rest_route(
            'calculent/v1',
            '/calculate',
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'handle_calculation' ],
                'permission_callback' => '__return_true',
                'args'                => [
                    'type'   => [
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_key',
                    ],
                    'payload' => [
                        'required' => false,
                        'type'     => 'array',
                    ],
                ],
            ]
        );
    }

    /**
     * Handle calculation requests.
     *
     * @param WP_REST_Request $request Request object.
     *
     * @return WP_REST_Response
     */
    public function handle_calculation( WP_REST_Request $request ) {
        $type    = $request->get_param( 'type' );
        $payload = $request->get_param( 'payload' ) ?? [];
        $tool    = calculent_logic_get_tool( $type );

        if ( ! $tool ) {
            return rest_ensure_response( [ 'error' => __( 'Unknown calculator type.', 'calculent-logic' ) ] );
        }

        $calculator = new Calculent_Logic_Calculator();
        $result     = $calculator->calculate( $type, $payload );

        return rest_ensure_response( [
            'type'    => $type,
            'result'  => $result['result'] ?? null,
            'summary' => $result['summary'] ?? null,
            'error'   => $result['error'] ?? null,
        ] );
    }
}

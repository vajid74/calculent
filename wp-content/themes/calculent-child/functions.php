<?php
/**
 * Calculent Astra Child Theme
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package Calculent_Astra_Child
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define Constants
 */
if ( ! defined( 'CALCULENT_CHILD_VERSION' ) ) {
	define( 'CALCULENT_CHILD_VERSION', '1.0.0' );
}

if ( ! defined( 'CALCULENT_CHILD_DIR' ) ) {
	define( 'CALCULENT_CHILD_DIR', trailingslashit( get_stylesheet_directory() ) );
}

if ( ! defined( 'CALCULENT_CHILD_URI' ) ) {
	define( 'CALCULENT_CHILD_URI', trailingslashit( get_stylesheet_directory_uri() ) );
}

/**
 * Enqueue Parent and Child Theme Styles
 */
if ( ! function_exists( 'calculent_child_enqueue_styles' ) ) {
	function calculent_child_enqueue_styles() {
		// Load parent theme stylesheet
		wp_enqueue_style(
			'astra-parent-style',
			get_template_directory_uri() . '/style.css',
			array(),
			get_option( 'stylesheet_version' )
		);

		// Load child theme stylesheet
		wp_enqueue_style(
			'calculent-child-style',
			get_stylesheet_uri(),
			array( 'astra-parent-style' ),
			CONSULENT_CHILD_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'calculent_child_enqueue_styles' );

/**
 * Theme Setup
 */
if ( ! function_exists( 'calculent_child_setup' ) ) {
	function calculent_child_setup() {
		// Load text domain for translations
		load_child_theme_textdomain(
			'calculent-child',
			CONSULENT_CHILD_DIR . 'languages'
		);
	}
}
add_action( 'after_setup_theme', 'calculent_child_setup' );

/**
 * Add custom styles
 */
if ( ! function_exists( 'calculent_child_custom_styles' ) ) {
	function calculent_child_custom_styles() {
		?>
		<style type="text/css">
			/* Add your custom styles here */
		</style>
		<?php
	}
}
add_action( 'wp_head', 'calculent_child_custom_styles' );

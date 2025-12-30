<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package Calculent_Astra_Child
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_active_sidebar( 'primary' ) ) {
	?>
	<aside id="secondary" class="sidebar widget-area">
		<?php
			dynamic_sidebar( 'primary' );
		?>
	</aside>
	<?php
}

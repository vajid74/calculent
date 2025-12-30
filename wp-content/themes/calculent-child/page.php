<?php
/**
 * The template for displaying pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#page
 * @package Calculent_Astra_Child
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content', 'page' );
			}
		?>
	</main>
</div>

<?php
get_sidebar();
get_footer();

<?php
/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
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
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
				}
				the_posts_pagination();
			} else {
				get_template_part( 'template-parts/content', 'none' );
			}
		?>
	</main>
</div>

<?php
get_sidebar();
get_footer();

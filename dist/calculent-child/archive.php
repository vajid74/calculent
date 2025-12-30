<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#archive
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
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );

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

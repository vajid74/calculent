<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/template-parts/
 * @package Calculent_Astra_Child
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
			if ( is_singular() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} else {
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			}
			
			if ( 'post' === get_post_type() ) {
				?>
				<div class="entry-meta">
					<?php
						printf(
							esc_html__( 'Posted on %s by %s', 'calculent-child' ),
							the_time( 'F j, Y' ),
						get_the_author()
						);
					?>
				</div>
				<?php
			}
		?>
	</header>

	<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail();
		}
	?>

	<div class="entry-content">
		<?php
			if ( is_singular() ) {
				the_content();
			} else {
				the_excerpt();
				?>
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="read-more">
					<?php esc_html_e( 'Read More', 'calculent-child' ); ?>
				</a>
				<?php
			}
		?>
	</div>

	<?php
		if ( is_singular() ) {
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'calculent-child' ),
					'after'  => '</div>',
				)
			);
		}
	?>

	<footer class="entry-footer">
		<?php
			if ( 'post' === get_post_type() ) {
				$categories = get_the_category();
				if ( ! empty( $categories ) ) {
					echo '<div class="entry-categories">';
					the_category( ', ' );
					echo '</div>';
				}

				$tags = get_the_tags();
				if ( ! empty( $tags ) ) {
					echo '<div class="entry-tags">';
					the_tags( '', ', ', '' );
					echo '</div>';
				}
			}
		?>
	</footer>
</article>

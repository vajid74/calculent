<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/template-parts/
 * @package Calculent_Astra_Child
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'calculent-child' ); ?></h1>
	</header>

	<div class="page-content">
		<?php
			if ( is_home() && current_user_can( 'publish_posts' ) ) {
				printf(
					'<p>' . wp_kses_post(
						__(
							'Ready to publish your first post? <a href="%1$s">Get started here</a>.',
							'calculent-child'
						)
					) . '</p>',
					esc_url( admin_url( 'post-new.php' ) )
				);
			} elseif ( is_search() ) {
				?>
				<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'calculent-child' ); ?></p>
				<?php
				get_search_form();
			} else {
				?>
				<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'calculent-child' ); ?></p>
				<?php
				get_search_form();
			}
		?>
	</div>
</section>

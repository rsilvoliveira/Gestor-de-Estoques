<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package coral-light
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php 
			coral_light_post_thumbnail();
			the_content(); 

			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'coral-light' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php edit_post_link( __( 'Edit', 'coral-light' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

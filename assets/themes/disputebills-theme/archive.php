<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package some_like_it_neat
 */



get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content">


			<header class="page-header">
			<div class="header-text">
				<h1 class="page-title">
					<?php echo get_the_archive_title(); ?>
				</h1>
				<style>
				.page-header {
					height: 350px;
				}
				.subheader p { font-size: 18px; font-size: 1.2rem; line-height: 28.5px; line-height: 1.9rem; }
				</style>
				<?php
					// Show an optional term description.
				if ( function_exists( 'get_the_archive_description' ) ) :
					echo '<div class="subheader taxonomy-description">' .get_the_archive_description(). '</div>';
					/*
					* TO-DO Might remove this code block at some point, since
					*   get_the_archive_description() does the same thing
					*   the below code does
					*/
				elseif ( $term_description = term_description() ) :
						printf( '<div class="subheader taxonomy-description">%s</div>',	$term_description );
				endif;
				?>
				</div>
			</header><!-- .page-header -->


			<div class="post-index-container">
				<div class="post-index-block">
						<?php if ( have_posts() ) : ?>

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>

								<?php
									/* Include the Post-Format-specific template for the content.
									 * If you want to override this in a child theme, then include a file
									 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
									 */
									get_template_part( 'page-templates/template-parts/content', get_post_format() );
								?>

						<?php
				endwhile; ?>

						<?php else : ?>

						<?php get_template_part( 'page-templates/template-parts/content', 'none' ); ?>

						<?php
				endif; ?>
				</div>
				<?php get_sidebar(); ?>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>
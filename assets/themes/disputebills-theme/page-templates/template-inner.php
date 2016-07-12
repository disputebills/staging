<?php
/**
 * Template Name: Inner Page
 *
 *
 * @package some_like_it_neat
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<header class="page-header" style="background: url(<?php the_field('header_image'); ?>) no-repeat center; background-size: cover;">
			<div class="header-text">
			  <h1><?php the_field('header') ?></h1>
			  <p class="subheader"><?php the_field('subheader'); ?></p>
			  <?php if( get_field('display_button') ) : ?>
				  <ul class="header-links">
				    <li><a href="https://app.disputebills.com/clients/sign_up" class="button-white-base">Start my dispute</a></li>
				  </ul>
			  <?php endif; ?>
		  </div>
		</header>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'page-templates/template-parts/content', 'page' ); ?>


			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // end of the loop ?>

	</div><!-- #primary -->


<?php if ( is_active_sidebar( 'cta-1' ) ) : ?>
  <?php dynamic_sidebar( 'cta-1' ); ?>
<?php endif; ?>

<?php get_footer(); ?>

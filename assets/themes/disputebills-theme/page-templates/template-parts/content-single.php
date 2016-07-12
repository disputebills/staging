<?php
/**
 * @package some_like_it_neat
 */
?>
<?php tha_entry_before(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemType="http://schema.org/BlogPosting">
	<?php tha_entry_top(); ?>

	<?php if (has_post_thumbnail( $post->ID ) ): ?>
	<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>

	<?php endif; ?>
	<header class="entry-header page-header"  id="custom-bg" style="background: url('<?php echo $image[0]; ?>') no-repeat center; background-size: cover;">
<!--
		<div class="entry-meta">
			<span class="genericon genericon-time"></span> <?php some_like_it_neat_posted_on(); ?>
		</div>
-->
		<div class="header-text">
			<p><?php the_date(); ?></p>
			<h1 class="entry-title" itemprop="name" ><?php the_title(); ?></h1>
		</div>
	</header><!-- .entry-header -->

	<div class="entry-container" itemprop="articleBody" >

		<?php the_content(); ?>

		<div class="author-block">
			<div class="author-image">
				<?php echo get_avatar( get_the_author_email() ); ?>
			</div>
			<div class="author-text">
				<h6>About the Author</h6>
				<h5><?php the_author_meta('display_name'); ?></h5>
				<p><?php the_author_meta('description'); ?></p>
			</div>
		</div>
	</div><!-- .entry-content -->

	<?php tha_entry_bottom(); ?>
</article><!-- #post-## -->
<?php tha_entry_after(); ?>

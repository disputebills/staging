<?php
/**
 * @package some_like_it_neat
 */
?>

<?php tha_entry_before(); ?>
<article class="post-item" id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemType="http://schema.org/BlogPosting" >
	<?php tha_entry_top(); ?>

	<?php
	   if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail
	   the_post_thumbnail('medium');
	   }
	?>
		<div class="post-preview">
			<header class="post-intro">
				<p class="date"><?php the_date(); ?></p>
				<h3 itemprop="name" ><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			</header><!-- .entry-header -->

			<?php if ( is_search() ) : // Only display Excerpts for Search ?>
			<div class="entry-summary" itemprop="description">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
			<?php else : ?>
			<div class="entry-content post-intro" itemprop="articleBody">
				<div class="teaser">
					<?php the_excerpt(); ?>
					<a href="<?php the_permalink(); ?>" rel="bookmark" class="read-more">Read More</a>
				</div>
			</div><!-- .entry-content -->
			<?php
		endif; ?>
	</div>
	<footer class="entry-meta" itemprop="keywords">
	<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
	<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'some-like-it-neat' ) );
	if ( $categories_list && some_like_it_neat_categorized_blog() ) :
	?>
	<span class="cat-links">
	<?php printf( __( 'Posted in %1$s', 'some-like-it-neat' ), $categories_list ); ?>
	</span>
	<?php
	endif; // End if categories ?>

	<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'some-like-it-neat' ) );
	if ( $tags_list ) :
	?>
	<span class="tags-links">
	<?php printf( __( 'Tagged %1$s', 'some-like-it-neat' ), $tags_list ); ?>
	</span>
	<?php
	endif; // End if $tags_list ?>
	<?php
	endif; // End if 'post' == get_post_type() ?>

	<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="comments-link" itemprop="comment" ><?php comments_popup_link( __( 'Leave a comment', 'some-like-it-neat' ), __( '1 Comment', 'some-like-it-neat' ), __( '% Comments', 'some-like-it-neat' ) ); ?></span>
	<?php
endif; ?>

	<?php edit_post_link( __( 'Edit', 'some-like-it-neat' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
	<?php tha_entry_bottom(); ?>
</article><!-- #post-## -->
<?php tha_entry_after(); ?>

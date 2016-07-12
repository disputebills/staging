<?php
/**
 * This is the template for the home page.
 *
 *
 * @package some_like_it_neat
 */




get_header(); ?>
<style>
.featured-on-logos {
        display: none;
}
@media screen and (min-width: 33em) {
.page-header {
	height: 607px!important;
}
.header-text {
	padding-bottom: 115px!important;
}
.featured-on-logos {
        display: block;
        width: 100%;
	position: absolute;
        bottom: 0;
	z-index: 999;
	background-color: rgba(179, 179, 179, 0.37);
	vertical-align: middle;
	display: table;
	padding: 25px 0;
	background-color: rgba(65, 64, 66, 0.5);
}
.featured-on-logos > img {
	display: table-cell;
	vertical-align: middle;
	-webkit-filter: grayscale(1);
	filter: grayscale(1);
	opacity: 0.6;
	max-width: 68em;
	margin: 0 auto;
	padding: 0 1.33333rem;
}
}
</style>
<header class="page-header">
  <div class="header-text">
    <h1><?php the_field('header'); ?></h1>
    <p class="subheader"><?php the_field('sub_header'); ?></p>
    <ul class="header-links">
      <li><a href="https://app.disputebills.com/clients/sign_up" class="button-white-base">Start my dispute</a></li>
      <li><a href="/how-it-works/" class="button-white-outline">How it works</a></li>
    </ul>
  </div>

<div class="featured-on-logos">
    <img src="http://disputebills.com/assets/uploads/2016/03/dispute-bills-chicago-featured.png">
</div>
</header>

<section class="facts-container">
  <h3>The proof is in the numbers</h3>
  <ul>
    <?php
    if( get_field('stats') ): ?>
      <?php while( has_sub_field('stats') ): ?>
        <li>
          <p class="stat"><?php the_sub_field('stat'); ?></p>
          <p><?php the_sub_field('stat_description'); ?></p>
        </li>
      <?php endwhile; ?>
   <?php endif; ?>
  </ul>
</section>

<section class="features">
  <div class="features-container">
    <h2>Your case is important to us</h2>
    <p class="subheader">Upload your bills, receive real-time updates, and reduce debt by signing-up today!</p>
    <ul class="features-list">
       <?php
        if( get_field('features') ): ?>
         <?php while( has_sub_field('features') ): ?>
          <li class="feature-li-<?php echo ($xyz++%2); ?>">
		<?php $image = get_sub_field('feature_image');
		if( !empty($image) ): ?>
		<img width="30" height="25" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
		<?php endif; ?>
            <div class="feature-text">
              <h5><?php the_sub_field('feature_name'); ?></h5>
              <p><?php the_sub_field('feature_description'); ?></p>
            </div>
          </li>
         <?php endwhile; ?>
      <?php endif; ?>
    </ul>
<img
sizes="(max-width: 554px) 100vw, 554px"
srcset="
http://res.cloudinary.com/lonesome-highway/image/upload/c_scale,w_200/v1460642309/medical-billing-software.png 200w,
http://res.cloudinary.com/lonesome-highway/image/upload/c_scale,w_360/v1460642309/medical-billing-software.png 360w,
http://res.cloudinary.com/lonesome-highway/image/upload/c_scale,w_483/v1460642309/medical-billing-software.png 483w,
http://res.cloudinary.com/lonesome-highway/image/upload/c_scale,w_554/v1460642309/medical-billing-software.png 554w"
src="http://res.cloudinary.com/lonesome-highway/image/upload/v1460642309/medical-billing-software.png"
alt="Medical Bill Web Application" 
class="ui-shot" />
  </div>
</section>

<section class="image-content-blocks">
  <div class="image-block" style="background: url(<?php the_field('savings_tips_image'); ?>) no-repeat center; background-size: cover;"></div>
  <div class="text-block">
    <h2>Savings Tips</h2>
    <ul>
      <?php
        $catquery = new WP_Query( 'cat=5&posts_per_page=3' ); ?>
        <?php while($catquery->have_posts()) : $catquery->the_post(); ?>
        <li>
          <h5><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h5>
          <p><?php echo wp_trim_words( get_the_content(), 16, '...' ); ?></p>
        </li>
      <?php endwhile; ?>
      <?php wp_reset_query(); ?>
    </ul>
    <a href="/blog/" class="button-blue-outline">More Savings Tips</a>
  </div>
</section>

<section class="testimonials">
  <div class="testimonial-container">
    <h2>We value your opinion</h2>
    <ul class="testimonial-block">
       <?php
        if( get_field('testimonials') ): ?>
         <?php while( has_sub_field('testimonials') ): ?>
          <li>
            <cite>
              <img width="60" height="60" src="<?php the_sub_field('testimonial_image'); ?>">
              <h6><?php the_sub_field('testimonial_name'); ?></h6>
              <p><?php the_sub_field('testimonial_location'); ?></p>
            </cite>
            <blockquote><?php the_sub_field('testimonial_quote'); ?></blockquote>
          </li>
         <?php endwhile; ?>
      <?php endif; ?>
    </ul>
  </div>
</section>



<?php if ( is_active_sidebar( 'cta-1' ) ) : ?>
  <?php dynamic_sidebar( 'cta-1' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
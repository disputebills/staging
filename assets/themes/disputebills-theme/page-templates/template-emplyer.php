<?php
/**
 * Template Name: Employer Page
 *
 *
 * @package disputebills
 */



  /* Vars */
  $image = get_field('employer_banner_background_image');

  get_header(); 

  ?>

	<div id="primary" class="content-area">

    <header class="page-header" style="background: url(<?php echo $image['url']; ?>) no-repeat center; background-size: cover;">
  			<div class="header-text">
			  <h1><?php the_field('employer_header') ?></h1>
			  <p class="subheader"><?php the_field('employer_sub_header'); ?></p>
			  <?php if( get_field('employer_button_text') ) : ?>
				  <ul class="header-links">
				    <li><a href="#" class="button-white-base"><?php the_field('employer_button_text'); ?></a></li>
				  </ul>
			  <?php endif; ?>
		  </div>
		</header>

		<section class="facts-container">
  		<ul>
    		<?php if( get_field('employer_stats_repeater') ): ?>
      		<?php while( has_sub_field('employer_stats_repeater') ): ?>
        	<li>
          		<p class="stat"><?php the_sub_field('employer_stat'); ?></p>
          		<p><?php the_sub_field('employer_stat_description'); ?></p>
        	</li>
      		<?php endwhile; ?>
   			<?php endif; ?>
		</ul>
		</section><!-- .facts-container -->


<section class="text-image-container">
<div class="text-block">
<h3></h3>
<h4></h4>
<p></p>
</div>
<div class="image-block">
	<img class="alignnone" src="" />
</div>
</section>

<section class="text-image-container">
<div class="image-block">
	<img class="alignnone" src="" />
</div>
<div class="text-block">
<h3></h3>
<h4></h4>
<p></p>
</div>
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









<section class="image-content-blocks contrast">
<div class="image-block" style="background: url('http://disputebills.com/assets/uploads/2016/06/medical-care-do-communities-give.jpg') no-repeat center; background-size: cover;"></div>
<div class="text-block">
<h2>Our Mission</h2>
<h4>Our number one goal is preventing medical debt. Our mission is much larger.</h4>
<ul class="bullet-list">
 	<li>Clarity. Medical billing and health insurance is a complex process, often leaving patients with an inaccurate bills, stress, and a lack of faith in the healthcare system.</li>
 	<li>Transparency. No more confusion. Know exactly what you owe and why.</li>
 	<li>Peace of Mind. Not only will we reduce your medical debt, we will reduce any stress that comes along with it.</li>
</ul>
</div>
</section>


</div><!-- #primary -->



<?php get_footer(); ?>

<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package some_like_it_neat
 */
?>
<!DOCTYPE html>
<?php tha_html_before(); ?>
<html <?php language_attributes(); ?>>

<head>
   <?php tha_head_top(); ?>
<meta charset="UTF-8">
<meta http-equiv="Accept-CH" content="DPR, Viewport-Width, Width">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link href='https://fonts.googleapis.com/css?family=Noto+Sans:400,700' rel='stylesheet' type='text/css'>
	<?php if ( 'no' === get_theme_mod( 'some-like-it-neat_post_format_support' ) ): ?>
	<style type="text/css">
		h1.entry-title:before {
		    display: none;
		}
	</style>
	<?php endif; ?>
<?php tha_head_bottom(); ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php tha_body_top(); ?>

<div id="page" class="hfeed site">

		<?php tha_header_before(); ?>
		<header id="masthead" class="site-header wrap" itemscope="itemscope" itemtype="http://schema.org/WPHeader">

		<?php tha_header_top(); ?>


				<div class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<img width="200" height="32" src="<?php echo get_template_directory_uri(); ?>/assets/images/dispute-bills-logo.png" alt='Dispute Bills, Chicago, IL' />
					</a>
				</div>

				<div class="sliding-panel-button">
				  <span class="icon-bar"></span>
				  <span class="icon-bar"></span>
                                  <span class="icon-bar"></span>
				</div>

				<div class="sliding-panel-content">
					<nav id="primary-nav" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">

				        <?php
							wp_nav_menu(
								array(
									'theme_location' => 'primary-navigation',
									'menu_class' => 'flexnav', //Adding the class for FlexNav
									'items_wrap' => '<ul data-breakpoint=" '. esc_attr( get_theme_mod( 'some_like_it_neat_mobile_min_width', '768' ) ) .' " id="%1$s" class="%2$s">%3$s</ul>', // Adding data-breakpoint for FlexNav
								)
							);
						?>

					</nav><!-- #site-navigation -->

					<div class="app-links">
						<ul>
							<li class="sign-in"><a href="https://app.disputebills.com" class="link-blue-base">Sign In</a></li>
							<li><a href="https://app.disputebills.com/clients/sign_up" class="button-blue-base">Start My Dispute</a></li>
						</ul>
					</div>
				</div>

<div class="sliding-panel-fade-screen"></div>

			<?php tha_header_bottom(); ?>

		</header><!-- #masthead -->
		<?php tha_header_after(); ?>

		<?php tha_content_before(); ?>

		<main id="main" class="site-main wrap" role="main">
			<?php tha_content_top(); ?>
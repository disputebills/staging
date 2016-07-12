<?php
/**
 * some_like_it_neat functions and definitions
 *
 * @package some_like_it_neat
 */




add_filter( 'get_the_archive_title', function ($title) {

    if ( is_category() ) {

            $title = single_cat_title( '', false );

        } elseif ( is_tag() ) {

            $title = single_tag_title( '', false );

        } elseif ( is_author() ) {

            $title = '<span class="vcard">' . get_the_author() . '</span>' ;

        }

    return $title;

});

if ( ! function_exists( 'some_like_it_neat_setup' ) ) :
	/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
	function some_like_it_neat_setup()
	{

	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
		if ( ! isset( $content_width ) ) {
			$content_width = 640; /* pixels */
		}

		/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on some_like_it_neat, use a find and replace
		* to change 'some-like-it-neat' to the name of your theme in all the template files
		*/
		load_theme_textdomain( 'some-like-it-neat', get_template_directory() . '/library/languages' );

		/**
		* Add default posts and comments RSS feed links to head.
		*/
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Enable HTML5 markup
		 */
		add_theme_support( 'html5', array(
			'comment-list',
			'search-form',
			'comment-form',
			'gallery',
		) );

		/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		*/
		add_theme_support( 'post-thumbnails' );

		/*
		* Enable title tag support for all posts.
		*
		* @link http://codex.wordpress.org/Title_Tag
		*/
		add_theme_support( 'title-tag' );

		/*
		* Add Editor Style for adequate styling in text editor.
		*
		* @link http://codex.wordpress.org/Function_Reference/add_editor_style
		*/
		add_editor_style( '/assets/css/editor-style.css' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menu( 'primary-navigation', __( 'Primary Menu', 'some-like-it-neat' ) );

		// Enable support for Post Formats.
		if ( 'yes' === get_theme_mod( 'some-like-it-neat_post_format_support' ) ) {
			add_theme_support( 'post-formats',
				array(
					'aside',
					'image',
					'video',
					'quote',
					'link',
					'status',
					'gallery',
					'chat',
					'audio'
				) );
		}

		// Enable Support for Jetpack Infinite Scroll
		if ( 'yes' === get_theme_mod( 'some-like-it-neat_infinite_scroll_support' ) ) {
			$scroll_type = get_theme_mod( 'some-like-it-neat_infinite_scroll_type' );
			add_theme_support( 'infinite-scroll', array(
				'type'				=> $scroll_type,
				'footer_widgets'	=> false,
				'container'			=> 'content',
				'wrapper'			=> true,
				'render'			=> false,
				'posts_per_page' 	=> false,
				'render'			=> 'some_like_it_neat_infinite_scroll_render',
			) );

			function some_like_it_neat_infinite_scroll_render() {
				if ( have_posts() ) : while ( have_posts() ) : the_post();
						get_template_part( 'page-templates/template-parts/content', get_post_format() );
				endwhile;
				endif;
			}
		}

		// Setup the WordPress core custom background feature.
		add_theme_support(
			'custom-background', apply_filters(
				'some_like_it_neat_custom_background_args', array(
				'default-color' => 'ffffff',
				'default-image' => '',
				)
			)
		);

		/**
		* Including Theme Hook Alliance (https://github.com/zamoose/themehookalliance).
		*/
		include get_template_directory() . '/library/vendors/theme-hook-alliance/tha-theme-hooks.php' ;

		/**
		* WP Customizer
		*/
		include get_template_directory() . '/library/vendors/customizer/customizer.php';

		/**
		* Implement the Custom Header feature.
		*/
		//require get_template_directory() . '/library/vendors/custom-header.php';

		/**
		* Custom template tags for this theme.
		*/
		include get_template_directory() . '/library/vendors/template-tags.php';

		/**
		* Custom functions that act independently of the theme templates.
		*/
		include get_template_directory() . '/library/vendors/extras.php';

		/**
		* Load Jetpack compatibility file.
		*/
		include get_template_directory() . '/library/vendors/jetpack.php';

		/**
		 * Including TGM Plugin Activation
		 */
		include_once get_template_directory() . '/library/vendors/tgm-plugin-activation/class-tgm-plugin-activation.php' ;

		include_once get_template_directory() . '/library/vendors/tgm-plugin-activation/tgm-plugin-activation.php' ;

	}
endif; // some_like_it_neat_setup
add_action( 'after_setup_theme', 'some_like_it_neat_setup' );

/**
 * Enqueue scripts and styles.
 */
if ( ! function_exists( 'some_like_it_neat_scripts' ) ) :
	function some_like_it_neat_scripts_styles()
	{
	
	    // Theme Stylesheet
	    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '-min';
	    wp_enqueue_style( 'some_like_it_neat-style', get_template_directory_uri() . "/assets/css/style{$suffix}.css", array(), '1.2', 'screen' );	
	    // Nav Scripts
		wp_enqueue_script( 'mobile-nav', 'https://cdn.jsdelivr.net/g/jquery.flexnav@1.3.3,jquery.hoverintent@r7', array( 'jquery' ), '1.3.3', true );
	}
	add_action( 'wp_enqueue_scripts', 'some_like_it_neat_scripts_styles' );
endif; 






/**
 * Register widgetized area and update sidebar with default widgets.
 */
function some_like_it_neat_widgets_init()
{
	register_sidebar(
		array(
		'name'          => __( 'Sidebar', 'some-like-it-neat' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
		)
	);
	register_sidebar(
		array(
		'name'          => __( 'Call to action', 'some-like-it-neat' ),
		'id'            => 'cta-1',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'some_like_it_neat_widgets_init' );







/**
 * Add Singular Post Template Navigation
 */
if ( ! function_exists( 'some_like_it_neat_post_navigation' ) ) :
	function some_like_it_neat_post_navigation() {
		if ( function_exists( 'get_the_post_navigation' ) && is_singular() && !is_page_template( 'page-templates/template-landing-page.php' )  ) {
			echo get_the_post_navigation(
				array(
				'prev_text'    => __( '&larr; %title', 'some-like-it-neat' ),
				'next_text'    => __( '%title &rarr;', 'some-like-it-neat' ),
				'screen_reader_text' => __( 'Page navigation', 'some-like-it-neat' )
				)
			);
		} else {
			wp_link_pages(
				array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'some-like-it-neat' ),
				'after'  => '</div>',
				)
			);
		}
	}
endif;
add_action( 'tha_entry_after', 'some_like_it_neat_post_navigation' );












/**
 * Custom Hooks and Filters
 */

if ( ! function_exists( 'some_like_it_neat_optional_scripts' ) ) :
	function some_like_it_neat_optional_scripts()
	{
		// Link Color
		if ( '' != get_theme_mod( 'some_like_it_neat_add_link_color' )  ) {
		} ?>
			<style type="text/css">
				a { color: <?php echo get_theme_mod( 'some_like_it_neat_add_link_color' ); ?>; }
			</style>
		<?php
	}
	add_action( 'wp_head', 'some_like_it_neat_optional_scripts' );
endif;




if ( ! function_exists( 'some_like_it_neat_add_footer_divs' ) ) :
	function some_like_it_neat_add_footer_divs()
	{
	?>

			<div class="footer-left">
				<?php echo esc_attr( get_theme_mod( 'some_like_it_neat_footer_left', __( '&copy; All Rights Reserved', 'some-like-it-neat' ) ) ); ?>
			</div>
			<div class="footer-right">
				<?php echo esc_attr( get_theme_mod( 'some_like_it_neat_footer_right', 'Footer Content Right' ) );  ?>
			</div>
		<?php
	}
	add_action( 'tha_footer_bottom', 'some_like_it_neat_add_footer_divs' );

endif;


function dispute_flexnav() {
 $handle = 'mobile-nav';
 $list = 'enqueued';
 if (wp_script_is( $handle, $list )) {
    ?>
       <script>jQuery(document).ready(function(e){e(".sliding-panel-button,.sliding-panel-fade-screen,.sliding-panel-close").on("click touchstart",function(n){e(".sliding-panel-content,.sliding-panel-fade-screen").toggleClass("is-visible"),n.preventDefault()}),e(".flexnav").flexNav({animationSpeed:250,transitionOpacity:!0,buttonSelector:".menu-button",hoverIntent:!0,hoverIntentTimeout:350,calcItemWidths:!1})});</script>
    <?php
 } 
}
add_action('bw_after_wp_footer', 'dispute_flexnav');


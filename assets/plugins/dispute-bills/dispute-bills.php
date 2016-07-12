<?php

/**
 * Dispute Bills Functionality and Optimization
 *
 * @link              http://disputebills.com
 * @since             1.0.0
 * @package           Dispute_Bills
 *
 * @wordpress-plugin
 * Plugin Name:       Dispute Bills Addons
 * Plugin URI:        http://disputebills.com/
 * Description:       Optimization of Dispute Bills
 * Version:           1.0.0
 * Author:            Bryan Willis
 * Author URI:        http://github.com/bryanwillis
 * License:           The MIT License
 * License URI:       https://opensource.org/licenses/MIT
 */



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/* avatars */
include_once plugin_dir_path( __FILE__ ) . 'inc/profile-photos.php';



/*
add_filter(  'gettext',  'brw_translate_words_array'  );
add_filter(  'ngettext',  'brw_translate_words_array'  );
function brw_translate_words_array( $translated ) {
     $words = array(
                        'Wordpress' => 'Dispute',
                        'Yuzo' => 'Dispute'
                    );
     $translated = str_ireplace(  array_keys($words),  $words,  $translated );
     return $translated;
}
// */


add_filter('map_meta_cap', 'prevent_user_edit', 10, 4 );
function prevent_user_edit( $required_caps, $cap, $user_id, $args ){
	$protected_user = 3; 
	if ( $user_id === $protected_user )
		return $required_caps;
	        $blocked_caps = array(
		  'delete_user',
		  'edit_user'
		);
	if ( in_array( $cap, $blocked_caps ) && $args[0] === $protected_user )
		$required_caps[] = 'do_not_allow';
	return $required_caps;
}


function update_nag_fix() {
	global $user_login , $user_email;
	get_currentuserinfo();
	if ($user_login !== "bryanwillis") {
		remove_action('admin_notices','update_nag',3);
	}
}
add_action('admin_init', 'update_nag_fix');






add_filter( 'map_meta_cap', function( $caps, $cap ) {
    if( $cap == 'edit_plugins' || $cap == 'edit_themes' )
        $caps[] = 'do_not_allow';
    return $caps;
}, 10, 3 );



function db_custom_menu_page_removing() {
    remove_menu_page( 'cloudinary-image-management-and-manipulation-in-the-cloud-cdn/library.php' );
    remove_submenu_page( 'options-general.php', 'a3-lazy-load' );
    remove_submenu_page( 'options-general.php', 'yuzo-related-post' );
    remove_submenu_page( 'index.php', 'update-core.php' );
    remove_submenu_page( 'wpseo_dashboard', 'wpseo_licenses' );
    remove_submenu_page( 'gf_edit_forms', 'gf_addons' );
    remove_submenu_page( 'gf_edit_forms', 'gf_update' );
    //remove_submenu_page( 'gf_edit_forms', 'gwp_perks' );
}
add_action( 'admin_menu', 'db_custom_menu_page_removing', 999 );




function dont_show_cheatin_page() {
    if ( current_user_can( 'do_not_allow' ) ) {
      wp_safe_redirect( admin_url()); // custom redirect instead of Cheatin 403 page
      exit;
    }
} 
add_action('admin_page_access_denied', 'dont_show_cheatin_page');







/*
function mytest() {
  global $wp_list_table;
  $hidearr = array('plugin-folder/plugin.php');
  $myplugins = $wp_list_table->items;
  foreach ($myplugins as $key => $val) {
    if (in_array($key,$hidearr)) {
      unset($wp_list_table->items[$key]);
    }
  }
}
add_action( 'pre_current_active_plugins', 'mytest' ); // all_plugins
//https://plugins.svn.wordpress.org/quick-configuration-links/trunk/quick_configuration_links.php
// */






class BWEnhancedTextWidget extends WP_Widget {

    /**
     * Widget construction
     */
    function __construct() {
        $widget_ops = array('classname' => 'widget_text enhanced-text-widget', 'description' => __('Text, HTML, CSS, PHP, Flash, JavaScript, Shortcodes', 'enhancedtext'));
        $control_ops = array('width' => 450);
        parent::__construct('BWEnhancedTextWidget', __('Code and Text Widget', 'enhancedtext'), $widget_ops, $control_ops);
    }

    /**
     * Setup the widget output
     */
    function widget( $args, $instance ) {

        if (!isset($args['widget_id'])) {
          $args['widget_id'] = null;
        }

        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
        $titleUrl = empty($instance['titleUrl']) ? '' : $instance['titleUrl'];
        $cssClass = empty($instance['cssClass']) ? '' : $instance['cssClass'];
        $text = apply_filters('widget_enhanced_text', $instance['text'], $instance);
        $hideTitle = !empty($instance['hideTitle']) ? true : false;
        $hideEmpty = !empty($instance['hideEmpty']) ? true : false;
        $newWindow = !empty($instance['newWindow']) ? true : false;
        $filterText = !empty($instance['filter']) ? true : false;
        $bare = !empty($instance['bare']) ? true : false;

        if ( $cssClass ) {
            if( strpos($before_widget, 'class') === false ) {
                $before_widget = str_replace('>', 'class="'. $cssClass . '"', $before_widget);
            } else {
                $before_widget = str_replace('class="', 'class="'. $cssClass . ' ', $before_widget);
            }
        }

        // Parse the text through PHP
        ob_start();
        eval('?>' . $text);
        $text = ob_get_contents();
        ob_end_clean();

        // Run text through do_shortcode
        $text = do_shortcode($text);

        if (!empty($text) || !$hideEmpty) {
            echo $bare ? '' : $before_widget;

            if ($newWindow) $newWindow = "target='_blank'";

            if(!$hideTitle && $title) {
                if($titleUrl) $title = "<a href='$titleUrl' $newWindow>$title</a>";
                echo $bare ? $title : $before_title . $title . $after_title;
            }

            echo $bare ? '' : '<div class="textwidget widget-text">';

            // Echo the content
            echo $filterText ? wpautop($text) : $text;

            echo $bare ? '' : '</div>' . $after_widget;
        }
    }

    /**
     * Run on widget update
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        if ( current_user_can('unfiltered_html') )
            $instance['text'] =  $new_instance['text'];
        else
            $instance['text'] = wp_filter_post_kses($new_instance['text']);
        $instance['titleUrl'] = strip_tags($new_instance['titleUrl']);
        $instance['cssClass'] = strip_tags($new_instance['cssClass']);
        $instance['hideTitle'] = isset($new_instance['hideTitle']);
        $instance['hideEmpty'] = isset($new_instance['hideEmpty']);
        $instance['newWindow'] = isset($new_instance['newWindow']);
        $instance['filter'] = isset($new_instance['filter']);
        $instance['bare'] = isset($new_instance['bare']);

        return $instance;
    }

    /**
     * Setup the widget admin form
     */
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => '',
            'titleUrl' => '',
            'cssClass' => '',
            'text' => ''
        ));
        $title = $instance['title'];
        $titleUrl = $instance['titleUrl'];
        $cssClass = $instance['cssClass'];
        $text = format_to_edit($instance['text']);
?>

        <style>
            .monospace {
                font-family: Consolas, Lucida Console, monospace;
            }
            .etw-credits {
                font-size: 0.9em;
                background: #F7F7F7;
                border: 1px solid #EBEBEB;
                padding: 4px 6px;
            }
        </style>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'enhancedtext'); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('titleUrl'); ?>"><?php _e('URL', 'enhancedtext'); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('titleUrl'); ?>" name="<?php echo $this->get_field_name('titleUrl'); ?>" type="text" value="<?php echo $titleUrl; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('cssClass'); ?>"><?php _e('CSS Classes', 'enhancedtext'); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('cssClass'); ?>" name="<?php echo $this->get_field_name('cssClass'); ?>" type="text" value="<?php echo $cssClass; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Content', 'enhancedtext'); ?>:</label>
            <textarea class="widefat monospace" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('hideTitle'); ?>" name="<?php echo $this->get_field_name('hideTitle'); ?>" type="checkbox" <?php checked(isset($instance['hideTitle']) ? $instance['hideTitle'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('hideTitle'); ?>"><?php _e('Do not display the title', 'enhancedtext'); ?></label>
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('hideEmpty'); ?>" name="<?php echo $this->get_field_name('hideEmpty'); ?>" type="checkbox" <?php checked(isset($instance['hideEmpty']) ? $instance['hideEmpty'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('hideEmpty'); ?>"><?php _e('Do not display empty widgets', 'enhancedtext'); ?></label>
        </p>

        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
            <label for="<?php echo $this->get_field_id('newWindow'); ?>"><?php _e('Open the URL in a new window', 'enhancedtext'); ?></label>
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs to the content', 'enhancedtext'); ?></label>
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('bare'); ?>" name="<?php echo $this->get_field_name('bare'); ?>" type="checkbox" <?php checked(isset($instance['bare']) ? $instance['bare'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('bare'); ?>"><?php _e('Do not output before/after_widget/title', 'enhancedtext'); ?></label>
        </p>

<?php
    }
}

/**
 * Register the widget
 */
function bw_enhanced_text_widget_init() {
    register_widget('BWEnhancedTextWidget');
}
add_action('widgets_init', 'bw_enhanced_text_widget_init');












/**
 * Widgets / Sidebars
 */
if ( !function_exists( 'eval_to_allow_php_in_text_widget' ) ) {
function eval_to_allow_php_in_text_widget($text) {
    ob_start();
    eval('?>'
    .$text);
    $text = ob_get_contents();
    ob_end_clean();
    return $text;
}
add_filter('widget_text', 'eval_to_allow_php_in_text_widget');
add_filter('widget_text', 'do_shortcode');
}

if ( !function_exists( 'hide_widget_title' ) ) {
function hide_widget_title( $widget_title ) {
	if ( substr ( $widget_title, 0, 1 ) == '!' )
		return;
	else 
		return ( $widget_title );
}
add_filter( 'widget_title', 'hide_widget_title' );
add_filter('widget_title', 'do_shortcode');
}

if ( !function_exists( 'enable_widget_shortcodes' ) ) {
function enable_widget_shortcodes() {
	add_filter('widget_text', 'shortcode_unautop');
	add_filter('widget_text', 'do_shortcode', 11);
}
add_action('init', 'enable_widget_shortcodes');
}

if ( !function_exists( 'bw_html_widget_title' ) ) {
add_filter('widget_title', 'do_shortcode');
add_filter( 'widget_title', 'bw_html_widget_title' );
function bw_html_widget_title( $title ) { 
	$title = str_replace( '[', '<', $title );
	$title = str_replace( '[/', '</', $title );
	$title = str_replace( ']', '>', $title );
	return $title;
}
}











/*-----------------------------------------------------------------------------------*/
/* Replace Toolbar Howdy */
/*-----------------------------------------------------------------------------------*/
//*
if ( !function_exists( 'admin_menu_hook_remove_node_replace_howdy' ) ) {
function admin_menu_hook_remove_node_replace_howdy( $wp_admin_bar ) {
    $my_account=$wp_admin_bar->get_node('my-account');
    $newtitle = str_replace( 'Howdy,', '', $my_account->title );            
    $wp_admin_bar->add_node( array(
        'id' => 'my-account',
        'title' => $newtitle,
    ) );
}
add_filter( 'admin_bar_menu', 'admin_menu_hook_remove_node_replace_howdy',25 );
}
// */

// LEFT SIDE FOOTER WORDPRESS
add_filter( 'admin_footer_text', 'admin_footer_text_mbi_sep152014', 99999 );
function admin_footer_text_mbi_sep152014() {
 return '';
 }

// RIGHT SIDE FOOTER UPDATE VERSION
add_filter( 'update_footer', 'update_footer_mbi_sep152014', 999999 );
function update_footer_mbi_sep152014() {
    return '';
}













//*
  // UNREGISTER DEFAULT WP WIDGETS
if ( !function_exists( 'unregister_default_wp_widgets' ) ) {
function unregister_default_wp_widgets() {
	// unregister_widget('WP_Widget_Pages');
	// unregister_widget('WP_Widget_Calendar');
	// unregister_widget('WP_Widget_Archives');
	// unregister_widget('WP_Widget_Meta');
	// unregister_widget('WP_Widget_Search');
	// unregister_widget('WP_Widget_Text');
	// unregister_widget('WP_Widget_Categories');
	// unregister_widget('WP_Widget_Recent_Posts');
	// unregister_widget('WP_Widget_Recent_Comments');
	// unregister_widget('WP_Widget_RSS');
	// unregister_widget('WP_Widget_Tag_Cloud');
	// unregister_widget('WP_Nav_Menu_Widget');
    wp_unregister_sidebar_widget('wpe_widget_powered_by');
}
add_action('widgets_init', 'unregister_default_wp_widgets', 1);
}
// */




									  
/*-----------------------------------------------------------------------------------*/
/* Remove Post Type Metaboxes */
/*-----------------------------------------------------------------------------------*/
//*
if (!function_exists('remove_meta_box_core_post_types')) {
    if (is_admin()):
        function remove_meta_box_core_post_types()
        {
            // PAGE CORE
            //remove_meta_box('submitdiv', 'page', 'side'); // Publish
            //remove_meta_box('pageparentdiv', 'page', 'side'); // Page Attributes
            //remove_meta_box('postimagediv', 'page', 'side'); // Featured Image
            //remove_meta_box('postcustom', 'page', 'normal'); // Custom Fields
            //remove_meta_box('commentstatusdiv', 'page', 'normal'); // Discussion
            //remove_meta_box('slugdiv', 'page', 'normal'); // Slug
            //remove_meta_box('authordiv', 'page', 'normal'); // Author
            
              // POST CORE
          //  remove_meta_box('submitdiv', 'post', 'side'); // Publish
          //  remove_meta_box('formatdiv', 'post', 'side'); // Format
          //  remove_meta_box('categorydiv', 'post', 'side'); // Categories
          //  remove_meta_box('tagsdiv-post_tag', 'post', 'side'); // Tags
          //  remove_meta_box('postimagediv', 'post', 'side'); // Featured Image
          //  remove_meta_box('postexcerpt', 'post', 'normal'); // Excerpt
            // remove_meta_box('trackbacksdiv', 'post', 'normal'); // Send Trackbacks
           // remove_meta_box('postcustom', 'post', 'normal'); // Custom Fields
          //  remove_meta_box('commentstatusdiv', 'post', 'normal'); // Discussion
            //remove_meta_box('slugdiv', 'post', 'normal'); // Slug
        //    remove_meta_box('authordiv', 'post', 'normal'); // Author
            
            // ATTACHMENT CORE
           // remove_meta_box('submitdiv', 'attachment', 'side'); // Publish
           // remove_meta_box('commentstatusdiv', 'attachment', 'normal'); // Discussion
           // remove_meta_box('commentsdiv', 'attachment', 'normal'); // Comments
           // remove_meta_box('slugdiv', 'attachment', 'normal'); // Slug
           // remove_meta_box('authordiv', 'attachment', 'normal'); // Author
        }
        add_action('do_meta_boxes', 'remove_meta_box_core_post_types');
    endif;
}
// */




/*-----------------------------------------------------------------------------------*/
/* Remove Dashboard Metaboxes */
/*-----------------------------------------------------------------------------------*/
//*
if (!function_exists('remove_metabox_core_dashboard')) {
    if (is_admin()):
        function remove_metabox_core_dashboard()
        {
         //   remove_meta_box('dashboard_browser_nag', 'dashboard', 'normal'); // Browser Nag
            //remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // Right Now
            //remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
         //   remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); // Incoming Links
         //   remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); // Plugins
            //remove_meta_box('dashboard_activity', 'dashboard', 'normal'); // Activity
            //remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // Quick Press
            //remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); // Recent Drafts
         //  remove_meta_box('dashboard_secondary', 'dashboard', 'side'); // Secondary

        remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        remove_action('welcome_panel', 'wp_welcome_panel');
        remove_meta_box('dashboard_primary', 'dashboard', 'side'); 
        remove_meta_box('wpe_dify_news_feed', 'dashboard', 'normal'); 

        }
        add_action('wp_dashboard_setup', 'remove_metabox_core_dashboard');
    endif;
}
// */



add_filter('acf/settings/show_admin', 'bw_acf_show_admin');
function bw_acf_show_admin( $show ) {
    return current_user_can('administrator');
}



class BWHideUpdateReminder {
	function __construct() {
		add_action('admin_init', array(&$this, 'check_user'));
	}

	function check_user() {
		global $userdata;
		if (!current_user_can('bwillis'))
			remove_action('admin_notices', 'update_nag', 3);
	}
}
$bwhideupdaterem = new BWHideUpdateReminder();











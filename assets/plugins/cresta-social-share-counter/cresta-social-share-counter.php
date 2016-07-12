<?php
/**
 * Plugin Name: Cresta Social Share Counter
 * Plugin URI: http://crestaproject.com/downloads/cresta-social-share-counter/
 * Description: <strong>*** <a href="http://crestaproject.com/downloads/cresta-social-share-counter/" target="_blank">Get Cresta Social Share Counter PRO</a> ***</strong> Share your posts and pages quickly and easily with Cresta Social Share Count showing the share count.
 * Version: 2.5.6
 * Author: CrestaProject - Rizzo Andrea
 * Author URI: http://crestaproject.com
 * License: GPL2
 */

add_action('admin_menu', 'cresta_social_share_menu');
add_action('wp_enqueue_scripts', 'cresta_social_share_wp_enqueue_scripts');
add_filter('the_content', 'cresta_filter_in_content' ); 
add_shortcode('cresta-social-share', 'add_social_button_in_content' );
add_action('admin_enqueue_scripts', 'cresta_social_share_admin_enqueue_scripts');
add_action('wp_head', 'cresta_social_css_top');

require_once( dirname( __FILE__ ) . '/cresta-metabox.php' );
require_once( dirname( __FILE__ ) . '/class/cresta-share-gp.php' );

function crestaplugin_init() {
	load_plugin_textdomain( 'cresta-social-share-counter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
add_filter( 'init', 'crestaplugin_init' );

function cresta_social_share_menu() {
	global $cresta_options_page;
	$cresta_options_page = add_menu_page( esc_html__( 'Cresta Social Share Counter Settings'), 'CSSC FREE', 'manage_options', 'cresta-social-share-counter.php', 'cresta_social_share_option', esc_url(plugins_url( '/cresta-social-share-counter/images/cssc-icon.png' )), 81 );
}

function cresta_social_setting_link($links) { 
  $settings_link = '<a href="admin.php?page=cresta-social-share-counter.php">'.esc_html__( 'Settings','cresta-social-share-counter').'</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

function cresta_social_share_admin_enqueue_scripts( $hook ) {
	global $cresta_options_page;
	if ( $hook == $cresta_options_page ) {
		wp_enqueue_style( 'cresta-social-admin-style', plugins_url('css/cresta-admin-css.css',__FILE__));
	}
}

function cresta_social_share_wp_enqueue_scripts() {
	$cresta_current_post_type = get_post_type();
	$show_on = explode (',',get_option( 'cresta_social_shares_selected_page' ));
	
	if ( is_singular() && in_array( $cresta_current_post_type, $show_on ) ) {
		
		wp_enqueue_style( 'cresta-social-crestafont', plugins_url('css/csscfont.css',__FILE__));
		wp_enqueue_style( 'cresta-social-wp-style', plugins_url('css/cresta-wp-css.css',__FILE__));
		
		$show_count = get_option('cresta_social_shares_show_counter');
		$show_floatbutton = get_option('cresta_social_shares_show_floatbutton');
		$buttons = explode (',',get_option( 'selected_button' ));
		
		if($show_floatbutton == 1 && $show_count == 1) { 
			$ifmorezero = get_option('cresta_social_shares_show_ifmorezero');
			$theifmore = 'nomore';
			if($ifmorezero == 1 ) {
				$theifmore = 'yesmore';
			}
			wp_enqueue_script( 'cresta-social-counter-js', plugins_url('js/jquery.cresta-social-share-counter.js',__FILE__), array('jquery'), '1.0', true );
			if(in_array('linkedin',$buttons)) {
				$obj=new crestaShareSocialCount (get_permalink());
				wp_localize_script( 'cresta-social-counter-js', 'crestaShareSS', array( 'LinkedinCount' => $obj->get_linkedin() ) );
			}
			wp_localize_script( 'cresta-social-counter-js', 'crestaPermalink', array('thePermalink' => get_permalink(), 'themorezero' => $theifmore ) );
		}
		wp_enqueue_script( 'cresta-social-effect-js', plugins_url('js/jquery.cresta-social-effect.js',__FILE__), array('jquery'), '1.0', true );
		$protocol = is_ssl() ? 'https' : 'http';
		wp_enqueue_style( 'cresta-social-googlefonts', $protocol.'://fonts.googleapis.com/css?family=Noto+Sans:400,700');
	}

	global $post;
	if ( is_singular() && !in_array( $cresta_current_post_type, $show_on ) && has_shortcode( $post->post_content, 'cresta-social-share' ) ) {
		wp_enqueue_style( 'cresta-social-crestafont', plugins_url('css/csscfont.css',__FILE__));
		wp_enqueue_style( 'cresta-social-wp-style', plugins_url('css/cresta-wp-css.css',__FILE__));
		
		$show_count = get_option('cresta_social_shares_show_counter');
		$show_floatbutton = get_option('cresta_social_shares_show_floatbutton');
		$buttons = explode (',',get_option( 'selected_button' ));
		
		if($show_floatbutton == 1 && $show_count == 1) {
			$ifmorezero = get_option('cresta_social_shares_show_ifmorezero');
			$theifmore = 'nomore';
			if($ifmorezero == 1 ) {
				$theifmore = 'yesmore';
			}
			wp_enqueue_script( 'cresta-social-counter-js', plugins_url('js/jquery.cresta-social-share-counter.js',__FILE__), array('jquery'), '1.0', true );
			if(in_array('linkedin',$buttons)) {
				$obj=new crestaShareSocialCount (get_permalink());
				wp_localize_script( 'cresta-social-counter-js', 'crestaShareSS', array( 'LinkedinCount' => $obj->get_linkedin() ) );
			}
			wp_localize_script( 'cresta-social-counter-js', 'crestaPermalink', array('thePermalink' => get_permalink(), 'themorezero' => $theifmore ) );
		}
		wp_enqueue_script( 'cresta-social-effect-js', plugins_url('js/jquery.cresta-social-effect.js',__FILE__), array('jquery'), '1.0', true );
		$protocol = is_ssl() ? 'https' : 'http';
		wp_enqueue_style( 'cresta-social-googlefonts', $protocol.'://fonts.googleapis.com/css?family=Noto+Sans:400,700');
	}
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'cresta_social_setting_link' );

function register_social_button_setting() {
	register_setting( 'csscplugin', 'selected_button','crestasocialshare_options_validate_1' );
	register_setting( 'csscplugin', 'cresta_social_shares_selected_page','crestasocialshare_options_validate_2' );
	register_setting( 'csscplugin', 'cresta_social_shares_float','crestasocialshare_options_validate_3' );
	register_setting( 'csscplugin', 'cresta_social_shares_float_buttons','crestasocialshare_options_validate_4' );
	register_setting( 'csscplugin', 'cresta_social_shares_style','crestasocialshare_options_validate_5' );
	register_setting( 'csscplugin', 'cresta_social_shares_position_top','crestasocialshare_options_validate_6' );
	register_setting( 'csscplugin', 'cresta_social_shares_position_left','crestasocialshare_options_validate_7' );
	register_setting( 'csscplugin', 'cresta_social_shares_twitter_username','crestasocialshare_options_validate_8' );
	register_setting( 'csscplugin', 'cresta_social_shares_show_counter','crestasocialshare_options_validate_9' );
	register_setting( 'csscplugin', 'cresta_social_shares_show_ifmorezero','crestasocialshare_options_validate_10' );
	register_setting( 'csscplugin', 'cresta_social_shares_show_total','crestasocialshare_options_validate_11' );
	register_setting( 'csscplugin', 'cresta_social_shares_total_text','crestasocialshare_options_validate_12' );
	register_setting( 'csscplugin', 'cresta_social_shares_disable_mobile','crestasocialshare_options_validate_13' );
	register_setting( 'csscplugin', 'cresta_social_shares_enable_animation','crestasocialshare_options_validate_14' );
	register_setting( 'csscplugin', 'cresta_social_shares_enable_samecolors','crestasocialshare_options_validate_15' );
	register_setting( 'csscplugin', 'cresta_social_shares_before_content','crestasocialshare_options_validate_16' );
	register_setting( 'csscplugin', 'cresta_social_shares_after_content','crestasocialshare_options_validate_17' );
	register_setting( 'csscplugin', 'cresta_social_shares_show_floatbutton','crestasocialshare_options_validate_18' );
	register_setting( 'csscplugin', 'cresta_social_shares_show_credit','crestasocialshare_options_validate_19' );
	register_setting( 'csscplugin', 'cresta_social_shares_enable_shadow','crestasocialshare_options_validate_20' );
	register_setting( 'csscplugin', 'cresta_social_shares_enable_shadow_buttons','crestasocialshare_options_validate_21' );
	register_setting( 'csscplugin', 'cresta_social_shares_z_index','crestasocialshare_options_validate_22' );
	register_setting( 'csscplugin', 'cresta_social_shares_button_hide_show','crestasocialshare_options_validate_23' );
	register_setting( 'csscplugin', 'cresta_social_shares_custom_css','crestasocialshare_options_validate_24' );
	register_setting( 'csscplugin', 'cresta_social_shares_twitter_shares','crestasocialshare_options_validate_25' );
	
	add_option( 'selected_button', 'facebook,tweet,gplus,pinterest,linkedin' );
	add_option( 'cresta_social_shares_selected_page', 'page,post' );
	add_option( 'cresta_social_shares_float', 'left' );
	add_option( 'cresta_social_shares_float_buttons', 'right' );
	add_option( 'cresta_social_shares_style', 'first_style' );
	add_option( 'cresta_social_shares_position_top', '20' );
	add_option( 'cresta_social_shares_position_left', '20' );
	add_option( 'cresta_social_shares_twitter_username', '' );
	add_option( 'cresta_social_shares_show_counter', '1' );
	add_option( 'cresta_social_shares_show_ifmorezero', '0' );
	add_option( 'cresta_social_shares_show_total', '1' );
	add_option( 'cresta_social_shares_total_text', 'Shares' );
	add_option( 'cresta_social_shares_disable_mobile', '1' );
	add_option( 'cresta_social_shares_enable_animation', '1' );
	add_option( 'cresta_social_shares_enable_samecolors', '0' );
	add_option( 'cresta_social_shares_before_content', '0' );
	add_option( 'cresta_social_shares_after_content', '1' );
	add_option( 'cresta_social_shares_show_floatbutton', '1' );
	add_option( 'cresta_social_shares_show_credit', '0' );
	add_option( 'cresta_social_shares_enable_shadow', '1' );
	add_option( 'cresta_social_shares_enable_shadow_buttons', '0' );
	add_option( 'cresta_social_shares_z_index', '99' );
	add_option( 'cresta_social_shares_button_hide_show', '0' );	
	add_option( 'cresta_social_shares_custom_css', '' );
	add_option( 'cresta_social_shares_twitter_shares', '0' );	
}
add_action('admin_init', 'register_social_button_setting' );

/* Cresta Social Share CounterWP Head Filter */
function cresta_social_css_top() {

	if( is_search() || is_404() || is_archive() ) {
		return;
	}
	
	$show_floatbutton = get_option('cresta_social_shares_show_floatbutton');
	$button_style = get_option('cresta_social_shares_style');
	$buttons_position = get_option('cresta_social_shares_float_buttons');
	$before_content = get_option('cresta_social_shares_before_content');
	$after_content = get_option('cresta_social_shares_after_content');
	$shadow_on_buttons = get_option('cresta_social_shares_enable_shadow_buttons');
	$custom_css = get_option('cresta_social_shares_custom_css');
		
	echo "<style type='text/css'>";
	
	if ($shadow_on_buttons == 1) {
		echo ".cresta-share-icon .sbutton {text-shadow: 1px 1px 0px rgba(0, 0, 0, .4);}";
	}
	
	if ( $show_floatbutton ==1 ) {
		$disable = get_option('cresta_social_shares_disable_mobile');
		$float = get_option('cresta_social_shares_float');
		$position_top =  get_option('cresta_social_shares_position_top');
		$position_left =  get_option('cresta_social_shares_position_left');
		$enable_animation = get_option('cresta_social_shares_enable_animation');
		$z_index = get_option('cresta_social_shares_z_index');

		if($disable == 1) {
			echo "
			@media (max-width : 640px) {
				#crestashareicon {
					display:none !important;
				}
			}";
		}
		echo "
		#crestashareicon {position:fixed; top:".esc_attr($position_top)."%; ".esc_attr($float).":".esc_attr($position_left)."px; float:left;z-index:". esc_attr($z_index) .";}

		#crestashareicon .sbutton {clear:both;";if($enable_animation == 1) { echo 'display:none;'; }  echo "}
		";
		if($float == "right") {
			echo "#crestashareicon .sbutton {float:right;}";
			if ($button_style == "first_style") {
				echo ".cresta-share-icon.first_style .cresta-the-count {left: -11px;}";
			}
			if ($button_style == "second_style") {
				echo ".cresta-share-icon.second_style .cresta-the-count {left: -11px;}";
			}
			if ($button_style == "third_style") {
				echo ".cresta-share-icon.third_style .cresta-the-count {float: left;}";
			}
			if ($button_style == "fourth_style") {
				echo ".cresta-share-icon.fourth_style .cresta-the-count {left: -11px;}";
			}
		} else {
			echo "#crestashareicon .sbutton { float:left;}";
		}
		
	}
	
	if ($before_content == 1 || $after_content == 1 || has_shortcode( $post->post_content, 'cresta-social-share' )) {
		/* Style In Content */
		if ($buttons_position == 'center') {
			echo '#crestashareiconincontent {float: none; margin: 0 auto; display: table;}';
		} else {
			echo '#crestashareiconincontent {float: '. esc_attr($buttons_position) .';}';
		}
	}
	if ($custom_css) {
		echo esc_attr($custom_css);
	}
	echo "</style>";
	
}

/* Cresta Social Share Counter In Content Position */
function cresta_filter_in_content( $content ) { 
	
	$cresta_current_post_type = get_post_type();
	$before_content = get_option('cresta_social_shares_before_content');
	$after_content = get_option('cresta_social_shares_after_content');
	$show_on = explode (',',get_option( 'cresta_social_shares_selected_page' ));

	if ( is_singular() && !in_array( $cresta_current_post_type, $show_on )  ) {
				return $content;
	}
	if (is_front_page() ) {
		if ( 'page' == get_option('show_on_front') && !in_array( 'page', $show_on) ) {
					return $content;
		}
	}
	if( is_search() || is_404() || is_archive() || is_home() || is_feed() ) {
				return $content;
	}
	$checkCrestaMetaBox = get_post_meta(get_the_ID(), '_get_cresta_plugin', true);
	if ( $checkCrestaMetaBox == '1' ) {
		return $content;
	}
	if( in_array( 'get_the_excerpt', $GLOBALS['wp_current_filter'] ) ) {
		return $content;
	}
	$addd_social_button_in_content = do_shortcode( add_social_button_in_content() );
		
	if($before_content == 1) {
		$content = $addd_social_button_in_content.$content;
	}
	if($after_content == 1) {
		$content .= $addd_social_button_in_content;
	}
		
    return $content;
}
function add_social_button_in_content() {
	if ( is_singular() ) {
	$buttons = explode (',',get_option( 'selected_button' ));
	$button_style = get_option('cresta_social_shares_style');
	$cresta_twitter_username = get_option('cresta_social_shares_twitter_username');
	$enable_shadow = get_option('cresta_social_shares_enable_shadow');
	
	global $wp_query; 
	$post = $wp_query->post;
	
	if($enable_shadow == 1) {
		$crestaShadow = 'crestaShadow';
	} else {
		$crestaShadow = '';
	}
	
	if ( '' != get_the_post_thumbnail( $post->ID ) ) {
		$pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		$pinImage = $pinterestimage[0];
	} else {
		$pinImage = plugins_url( '/images/no-image-found.png' , __FILE__ );
	}
	
	$allButtonsSelected = '';
	$theTwitterUsername = '';
	
	if ($cresta_twitter_username) {
		$theTwitterUsername = '&amp;via=' .esc_attr($cresta_twitter_username);
	}
	
	if(in_array('facebook',$buttons)) {
		$allButtonsSelected .= '<div class="sbutton '. esc_attr($crestaShadow) .' facebook-cresta-share" id="facebook-cresta-c"><a rel="nofollow" href="https://www.facebook.com/sharer.php?u='. urlencode(get_permalink( $post->ID )) .'&amp;t='. htmlspecialchars(urlencode(html_entity_decode(the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) ), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'" title="Share to Facebook" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-facebook"></i></a></div>';
	}

	if(in_array('tweet',$buttons)) {
		$allButtonsSelected .= '<div class="sbutton '. esc_attr($crestaShadow) .' twitter-cresta-share" id="twitter-cresta-c"><a rel="nofollow" href="https://twitter.com/intent/tweet?text='. htmlspecialchars(urlencode(html_entity_decode(the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) ), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'&amp;url='. urlencode(get_permalink( $post->ID )) .''. $theTwitterUsername .'" title="Share to Twitter" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-twitter"></i></a></div>';
	}

	if(in_array('gplus',$buttons)) {
		$allButtonsSelected .= '<div class="sbutton '. esc_attr($crestaShadow) .' googleplus-cresta-share" id="googleplus-cresta-c"><a rel="nofollow" href="https://plus.google.com/share?url='. urlencode(get_permalink( $post->ID )) .'" title="Share to Google Plus" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-gplus"></i></a></div>';
	}

	if(in_array('linkedin',$buttons)) {
		$allButtonsSelected .= '<div class="sbutton '. esc_attr($crestaShadow) .' linkedin-cresta-share" id="linkedin-cresta-c"><a rel="nofollow" href="https://www.linkedin.com/shareArticle?mini=true&amp;url='. urlencode(get_permalink( $post->ID )) .'&amp;title='. htmlspecialchars(urlencode(html_entity_decode(the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) ), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'&amp;source='. esc_url( home_url( '/' )) .'" title="Share to LinkedIn" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-linkedin"></i></a></div>';
	}

	if(in_array('pinterest',$buttons)) {
		$allButtonsSelected .= '<div class="sbutton '. esc_attr($crestaShadow) .' pinterest-cresta-share" id="pinterest-cresta-c"><a rel="nofollow" href="https://pinterest.com/pin/create/bookmarklet/?url='.urlencode(get_permalink( $post->ID )) .'&amp;media='. $pinImage .'&amp;description='. htmlspecialchars(urlencode(html_entity_decode(the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) ), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8').'" title="Share to Pinterest" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-pinterest"></i></a></div>';
	}
	
	if(in_array('print',$buttons)) {
		$allButtonsSelected .= '<div class="sbutton '. esc_attr($crestaShadow) .' print-cresta-share" id="print-cresta-c"><a rel="nofollow" href="#" title="Print this page" onclick="window.print();"><i class="cs c-icon-cresta-print"></i></a></div>';
	}
	
	return '<!--www.crestaproject.com Social Button in Content Start--><div id="crestashareiconincontent" class="cresta-share-icon '. esc_attr($button_style) .'">'. $allButtonsSelected .'<div style="clear: both;"></div></div><div style="clear: both;"></div><!--www.crestaproject.com Social Button in Content End-->';
	}
}

/* Cresta Social Share Counter Float Position */
function add_social_button() {
	$show_floatbutton = get_option('cresta_social_shares_show_floatbutton');
	
	if ( $show_floatbutton ==1 ) {
	
	$buttons = explode (',',get_option( 'selected_button' ));
	$show_on = explode (',',get_option( 'cresta_social_shares_selected_page' ));
	$show_count = get_option('cresta_social_shares_show_counter');
	$show_total = get_option ('cresta_social_shares_show_total');
	$total_text = get_option ('cresta_social_shares_total_text');
	$button_style = get_option('cresta_social_shares_style');
	$disable = get_option('cresta_social_shares_disable_mobile');
	$cresta_twitter_username = get_option('cresta_social_shares_twitter_username');
	$show_credit = get_option ('cresta_social_shares_show_credit');
	$enable_shadow = get_option('cresta_social_shares_enable_shadow');
	$enable_sameColors = get_option('cresta_social_shares_enable_samecolors');
	$button_hide_show = get_option('cresta_social_shares_button_hide_show');
	$newTwitter = get_option('cresta_social_shares_twitter_shares');
	global $wp_query;
	$post = $wp_query->post;
	
	if($enable_shadow == 1) {
		$crestaShadow = 'crestaShadow';
	} else {
		$crestaShadow = '';
	}
	
	if($enable_sameColors == 1) {
		$crestaSame = 'sameColors' ;
	} else {
		$crestaSame = '';
	}
	
	if($newTwitter == 1) {
		$theNewTwitter = 'withCount';
	} else {
		$theNewTwitter = 'noCount';
	}
	
	if($disable == 1 && wp_is_mobile()) {
	return;
	} else {

	if( is_page() && !in_array( 'page', $show_on ) ) {
				return;
	}
	if( is_singular('post') && !in_array( 'post', $show_on ) ) {
				return;
	}
	if( is_attachment() && !in_array( 'attachment', $show_on ) ) {
				return;
	}
	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	$post_types = get_post_types( $args, 'names', 'and' ); 
	foreach ( $post_types as $post_type ) { 
		if ( is_singular( $post_type ) && !in_array( $post_type, $show_on )  ) {
			return;
		}
	}
	if (is_front_page() ) {
		if ( 'page' == get_option('show_on_front') && !in_array( 'page', $show_on) ) {
					return;
		}
	}
	if( is_search() || is_404() || is_archive() || is_home() || is_feed() ) {
				return;
	}
	if( in_array( 'get_the_excerpt', $GLOBALS['wp_current_filter'] ) ) {
				return;
	}
	$checkCrestaMetaBox = get_post_meta($post->ID, '_get_cresta_plugin', true);
	if ( $checkCrestaMetaBox == '1' ) {
				return;
	}
	

if ( '' != get_the_post_thumbnail( $post->ID ) ) {
		$pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		$pinImage = $pinterestimage[0];
} else {
		$pinImage = esc_url(plugins_url( '/images/no-image-found.png' , __FILE__ ));
}

echo '<!--www.crestaproject.com Social Button Floating Start--><div id="crestashareicon" class="cresta-share-icon '.esc_attr($crestaSame).' '.esc_attr($button_style) .' '; if($show_count == 1) { echo 'show-count-active'; } echo'">';
if($button_hide_show == 1) {
	echo '<div class="cresta-the-button"><i class="c-icon-cresta-minus"></i></div>';
}

if(in_array('facebook',$buttons)) {
	echo '<div class="sbutton '. esc_attr($crestaShadow) .' facebook-cresta-share float" id="facebook-cresta"><a rel="nofollow" href="https://www.facebook.com/sharer.php?u='. urlencode(get_permalink( $post->ID )) .'&amp;t='. htmlspecialchars(urlencode(html_entity_decode(the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) ), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'" title="Share to Facebook" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-facebook"></i></a></div>';
}
if(in_array('tweet',$buttons)) {
	echo '<div class="sbutton '. esc_attr($crestaShadow) .' twitter-cresta-share float '. esc_attr($theNewTwitter) .'" id="twitter-cresta"><a rel="nofollow" href="https://twitter.com/intent/tweet?text='. htmlspecialchars(urlencode(html_entity_decode(the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) ), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'&amp;url='. urlencode(get_permalink( $post->ID )) .''; if($cresta_twitter_username) { echo '&amp;via=' . esc_attr($cresta_twitter_username) . ''; } echo '" title="Share to Twitter" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-twitter"></i></a></div>';
}

if(in_array('gplus',$buttons)) {
	echo '<div class="sbutton '. esc_attr($crestaShadow) .' googleplus-cresta-share float" id="googleplus-cresta"><a rel="nofollow" href="https://plus.google.com/share?url='. urlencode(get_permalink( $post->ID )) .'" title="Share to Google Plus" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-gplus"></i></a></div>';
}

if(in_array('linkedin',$buttons)) {
	echo '<div class="sbutton '. esc_attr($crestaShadow) .' linkedin-cresta-share float" id="linkedin-cresta"><a rel="nofollow" href="https://www.linkedin.com/shareArticle?mini=true&amp;url='. urlencode(get_permalink( $post->ID )) .'&amp;title='. htmlspecialchars(urlencode(html_entity_decode(the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) ), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'&amp;source='. esc_url( home_url( '/' )) .'" title="Share to LinkedIn" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-linkedin"></i></a></div>';
}

if(in_array('pinterest',$buttons)) {
	echo '<div class="sbutton '. esc_attr($crestaShadow) .' pinterest-cresta-share float" id="pinterest-cresta"><a rel="nofollow" href="https://pinterest.com/pin/create/bookmarklet/?url='.urlencode(get_permalink( $post->ID )) .'&amp;media='. $pinImage .'&amp;description='. htmlspecialchars(urlencode(html_entity_decode(the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) ), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'" title="Share to Pinterest" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-cresta-pinterest"></i></a></div>';
}

if(in_array('print',$buttons)) {
	echo '<div class="sbutton '. esc_attr($crestaShadow) .' print-cresta-share float" id="print-cresta"><a rel="nofollow" href="#" title="Print this page" onclick="window.print();"><i class="cs c-icon-cresta-print"></i></a></div>';
}

if($show_count == 1) {
	echo '<div class="sbutton" id="total-shares">'; if($show_total == 1) { echo '<span class="cresta-the-total-count" id="total-count"><i class="cs c-icon-cresta-spinner animate-spin"></i></span><span class="cresta-the-total-text">' .esc_attr($total_text). '</span>'; } echo '</div>';
}

if($show_credit == 1) {
	echo '<div class="sbutton crestaCredit"><a target="_blank" href="http://crestaproject.com/" title="CrestaProject "><img src="'. esc_url(plugins_url( '/images/by-cr-social.png' , __FILE__ )) .'" alt="CrestaProject" /></a></div>';
}

echo '<div style="clear: both;"></div></div>

<!--www.crestaproject.com Social Button Floating End-->
';
	} //if disable = 1 && wp_is_mobile
} //if show floating buttons is ON
}
add_action('wp_footer', 'add_social_button');



function cresta_social_share_option() {
	ob_start();
	if( isset($_GET['settings-updated']) && $_GET['settings-updated'] ) {
		echo '<div id="message" class="updated"><p>'.esc_html__('Settings Saved...', 'cresta-social-share-counter').'</p></div>';
	}
?>
	
<div class="wrap">
<div id="icon-options-general" class="icon32"></div>
<h2>Cresta Social Share Counter FREE</h2><a class="crestaButtonUpgrade" href="http://crestaproject.com/downloads/cresta-social-share-counter/" target="_blank" title="See Details: Cresta Social Share Counter PRO">Upgrade to PRO Version!</a>

<script type="text/javascript">
jQuery(document).ready(function(){
		
		if ( jQuery('input.crestashowsocialcounter').hasClass('active') ) {
			jQuery('.crestachoosetoshow').show();
		} else {
			jQuery('.crestachoosetoshow').hide();
		}
		
		if ( jQuery('input.crestatwitterenable').hasClass('active') ) {
			jQuery('.crestashowtwittername').show();
		} else {
			jQuery('.crestashowtwittername').hide();
		}
		
		if ( jQuery('input.crestashowsocialtotal').hasClass('active') ) {
			jQuery('.crestachoosetotalshares').show();
		} else {
			jQuery('.crestachoosetotalshares').hide();
		}
	
	jQuery('input.crestashowsocialcounter').on('click', function(){
		if ( jQuery(this).is(':checked') ) {
			jQuery('.crestachoosetoshow').fadeIn();
		} else {
			jQuery('.crestachoosetoshow').fadeOut();
		}
	});
	
	jQuery('input.crestatwitterenable').on('click', function(){
		if ( jQuery(this).is(':checked') ) {
			jQuery('.crestashowtwittername').fadeIn();
		} else {
			jQuery('.crestashowtwittername').fadeOut();
		}
	});
	
	jQuery('input.crestashowsocialtotal').on('click', function(){
		if ( jQuery(this).is(':checked') ) {
			jQuery('.crestachoosetotalshares').fadeIn();
		} else {
			jQuery('.crestachoosetotalshares').fadeOut();
		}
	});
	
});
</script>

<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
            <div class="postbox">
                <div class="inside">
				<form method="post" action="options.php">
				<?php
				settings_fields( 'csscplugin' ); 
				?>
		<h3><div class="dashicons dashicons-visibility space"></div><?php esc_html_e('Select buttons to display on website :', 'cresta-social-share-counter'); ?></h3>
		<table class="form-table">
			<tbody>	
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Choose Buttons', 'cresta-social-share-counter' ); ?></th>
					<td>
					<?php $buttons = explode (',',get_option( 'selected_button' )); ?>
						<ul>
							<li>
								<label><input type="checkbox" <?php if(in_array('facebook',$buttons)) { echo 'checked="checked"'; }?> name="selected_button[]" value="facebook"/>FaceBook</label>
							</li>
							<li>
								<label><input type="checkbox" <?php if(in_array('tweet',$buttons)) { echo 'checked="checked"'; }?> name="selected_button[]" value="tweet" class="crestatwitterenable <?php if(in_array('tweet',$buttons)) { echo 'active'; }?>"/>Twitter <span class="description">(Official counter no longer available, new counter available through newsharecounts.com API)</span></label>
							</li>
							<li class="crestashowtwittername">
								<label><?php esc_html_e('Twitter username (optional):', 'cresta-social-share-counter'); ?> @<input type="text" name="cresta_social_shares_twitter_username" value="<?php echo esc_html(get_option('cresta_social_shares_twitter_username'));?>"/></label>
							</li>
							<li>
								<label><input type="checkbox" <?php if(in_array('gplus',$buttons)) { echo 'checked="checked"'; }?> name="selected_button[]" value="gplus"/>Google Plus</label>
							</li>
							<li>
								<label><input type="checkbox" <?php if(in_array('linkedin',$buttons)) { echo 'checked="checked"'; }?> name="selected_button[]" value="linkedin"/>Linkedin</label>
							</li>
							<li>
								<label><input type="checkbox" <?php if(in_array('pinterest',$buttons)) { echo 'checked="checked"'; }?> name="selected_button[]" value="pinterest"/>Pinterest</label>
							</li>
							<li>
								<label><input type="checkbox" <?php if(in_array('print',$buttons)) { echo 'checked="checked"'; }?> name="selected_button[]" value="print"/>Print Button</label>
							</li>
							<li>
								<label class="crestaDisabled"><input type="checkbox" name="crestaForPRO" disabled />Stumbleupon <span><?php esc_html_e('PRO version', 'cresta-social-share-counter'); ?></span></label>
							</li>
							<li>
								<label class="crestaDisabled"><input type="checkbox" name="crestaForPRO" disabled />Buffer <span><?php esc_html_e('PRO version', 'cresta-social-share-counter'); ?></span></label>
							</li>
							<li>
								<label class="crestaDisabled"><input type="checkbox" name="crestaForPRO" disabled />Reddit <span><?php esc_html_e('PRO version', 'cresta-social-share-counter'); ?></span></label>
							</li>
							<li>
								<label class="crestaDisabled"><input type="checkbox" name="crestaForPRO" disabled />VK.com <span><?php esc_html_e('PRO version', 'cresta-social-share-counter'); ?></span></label>
							</li>
							<li>
								<label class="crestaDisabled"><input type="checkbox" name="crestaForPRO" disabled />OK.ru <span><?php esc_html_e('PRO version', 'cresta-social-share-counter'); ?></span></label>
							</li>
							<li>
								<label class="crestaDisabled"><input type="checkbox" name="crestaForPRO" disabled />Xing <span><?php esc_html_e('PRO version', 'cresta-social-share-counter'); ?></span></label>
							</li>
							<li>
								<label class="crestaDisabled"><input type="checkbox" name="crestaForPRO" disabled />WhatsApp Button <i><?php esc_html_e('(only visible on smartphones Android and iPhone)', 'cresta-social-share-counter'); ?></i> <span><?php _e('PRO version', 'cresta-social-share-counter'); ?></span></label>
							</li>	
							<li>
								<label class="crestaDisabled"><input type="checkbox" name="crestaForPRO" disabled />Email Share Button <span><?php esc_html_e('PRO version', 'cresta-social-share-counter'); ?></span></label>
							</li>	
						</ul>
					</td>
				</tr>
			</tbody>	
		</table>
		<h3><div class="dashicons dashicons-admin-customizer space"></div><?php esc_html_e('Choose buttons style :', 'cresta-social-share-counter'); ?></h3>
		<table class="form-table">
			<tbody>	
				<tr valign="top">
					<ul>
						<li>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "first_style") { echo 'checked="checked"'; }?> value="first_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-1.png' , __FILE__ )); ?>">
							</label>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "second_style") { echo 'checked="checked"'; }?> value="second_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-2.png' , __FILE__ )); ?>">
							</label>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "third_style") { echo 'checked="checked"'; }?> value="third_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-3.png' , __FILE__ )); ?>">
							</label>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "fourth_style") { echo 'checked="checked"'; }?> value="fourth_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-4.png' , __FILE__ )); ?>">
							</label>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "fifth_style") { echo 'checked="checked"'; }?> value="fifth_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-5.png' , __FILE__ )); ?>">
							</label>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "eleventh_style") { echo 'checked="checked"'; }?> value="eleventh_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-11.png' , __FILE__ )); ?>">
							</label>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "twelfth_style") { echo 'checked="checked"'; }?> value="twelfth_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-12.png' , __FILE__ )); ?>">
							</label>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "fourteenth_style") { echo 'checked="checked"'; }?> value="fourteenth_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-14.png' , __FILE__ )); ?>">
							</label>
							<label>
								<input type="radio" name="cresta_social_shares_style" <?php if(get_option('cresta_social_shares_style') == "seventeenth_style") { echo 'checked="checked"'; }?> value="seventeenth_style" >
								<img src="<?php echo esc_url(plugins_url( '/images/cresta-social-share-counter-style-17.png' , __FILE__ )); ?>">
							</label>
						</li>
					</ul>
				</tr>
			</tbody>	
		</table>
		<h3><div class="dashicons dashicons-admin-settings space"></div><?php esc_html_e('Display Setting :', 'cresta-social-share-counter'); ?></h3>
		<table class="form-table">
			<tbody>	
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable reflection on the buttons', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkanim" name="cresta_social_shares_enable_shadow" value="1" <?php checked( get_option('cresta_social_shares_enable_shadow'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable shadow on the buttons', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkanim" name="cresta_social_shares_enable_shadow_buttons" value="1" <?php checked( get_option('cresta_social_shares_enable_shadow_buttons'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable animation', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkanim" name="cresta_social_shares_enable_animation" value="1" <?php checked( get_option('cresta_social_shares_enable_animation'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Social Counter Box: Use the same colors of social network', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkanim" name="cresta_social_shares_enable_samecolors" value="1" <?php checked( get_option('cresta_social_shares_enable_samecolors'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Show Social Counter', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chksocialcounter" name="cresta_social_shares_show_counter" class="crestashowsocialcounter <?php if(get_option('cresta_social_shares_show_counter') == "1") { echo 'active'; }?>" value="1" <?php checked( get_option('cresta_social_shares_show_counter'), '1' ); ?>>
						<span class="description"><?php esc_html_e('Visible only on floating buttons, buy the PRO version to use it in the content', 'cresta-social-share-counter'); ?></span>
					</td>
				</tr>
				<tr valign="top" class="crestachoosetoshow">
					<th scope="row"><?php esc_html_e( 'newsharecounts.com for Twitter Counts', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkanim" name="cresta_social_shares_twitter_shares" value="1" <?php checked( get_option('cresta_social_shares_twitter_shares'), '1' ); ?>>
						<span class="description"><?php esc_html_e('To use newsharecounts.com public API, you have to enter your website url', 'cresta-social-share-counter'); ?> <strong><?php echo esc_url( home_url( '/' ) ); ?></strong> <?php esc_html_e('and sign in using your Twitter Account at their website', 'cresta-social-share-counter'); ?> <a target="_blank" href="http://newsharecounts.com">newsharecounts.com</a></span>
					</td>
				</tr>
				<tr valign="top" class="crestachoosetoshow">
					<th scope="row"><?php esc_html_e( 'Show single shares count only if is more than 0', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkanim" name="cresta_social_shares_show_ifmorezero" value="1" <?php checked( get_option('cresta_social_shares_show_ifmorezero'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top" class="crestachoosetoshow">
					<th scope="row"><?php esc_html_e( 'Show Total Shares', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chksocialtotal" name="cresta_social_shares_show_total" class="crestashowsocialtotal <?php if(get_option('cresta_social_shares_show_total') == "1") { echo 'active'; }?>" value="1" <?php checked( get_option('cresta_social_shares_show_total'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top" class="crestachoosetoshow crestachoosetotalshares">
					<th scope="row"><?php esc_html_e( 'Total Shares Text', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="text" id="chksocialtotal" name="cresta_social_shares_total_text" value="<?php echo esc_html(get_option('cresta_social_shares_total_text'));?>"/>
					</td>
				</tr>
			</tbody>	
		</table>
		<h3><div class="dashicons dashicons-align-center space"></div><?php esc_html_e('Float Position :', 'cresta-social-share-counter'); ?></h3>
		<table class="form-table">
			<tbody>	
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Show Floating Buttons', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkanim" name="cresta_social_shares_show_floatbutton" value="1" <?php checked( get_option('cresta_social_shares_show_floatbutton'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Float Buttons Position', 'cresta-social-share-counter' ); ?></th>
					<td>
						<ul>
							<li>
								<label><input type="radio" name='cresta_social_shares_float' value='left' <?php checked( 'left', get_option('cresta_social_shares_float') ); ?>><?php esc_html_e('Left', 'cresta-social-share-counter'); ?></label>
							</li>
							<li>
								<label><input type="radio" name='cresta_social_shares_float' value='right' <?php checked( 'right', get_option('cresta_social_shares_float') ); ?>><?php esc_html_e('Right', 'cresta-social-share-counter'); ?></label>
							</li>
						</ul>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Distance From Top:', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="text" name="cresta_social_shares_position_top" value="<?php echo esc_attr(get_option('cresta_social_shares_position_top'));?>">%
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Distance From Left or Right:', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="text" name="cresta_social_shares_position_left" value="<?php echo esc_attr(get_option('cresta_social_shares_position_left'));?>">px
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Z-Index:', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="text" name="cresta_social_shares_z_index" value="<?php echo esc_attr(get_option('cresta_social_shares_z_index'));?>">
						<span class="description"><?php esc_html_e('Increase this number if the floating buttons are covered by other items on the screen.', 'cresta-social-share-counter'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable button to hide/show the floating buttons', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkhideshow" name="cresta_social_shares_button_hide_show" value="1" <?php checked( get_option('cresta_social_shares_button_hide_show'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Disable Floating Buttons On Mobile', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkmobile" name="cresta_social_shares_disable_mobile" value="1" <?php checked( get_option('cresta_social_shares_disable_mobile'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Show CrestaProject Credit :)', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkcrestacredit" name="cresta_social_shares_show_credit" value="1" <?php checked( get_option('cresta_social_shares_show_credit'), '1' ); ?>>
					</td>
				</tr>
			</tbody>	
		</table>
		<h3><div class="dashicons dashicons-editor-aligncenter space"></div><?php esc_html_e('Post and Page Position :', 'cresta-social-share-counter'); ?></h3>
		<table class="form-table">
			<tbody>	
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Add Social Buttons before post/page content', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkbeforecontent" name="cresta_social_shares_before_content" value="1" <?php checked( get_option('cresta_social_shares_before_content'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Add Social Buttons after post/page content', 'cresta-social-share-counter' ); ?></th>
					<td>						
						<input type="checkbox" id="chkaftercontent" name="cresta_social_shares_after_content" value="1" <?php checked( get_option('cresta_social_shares_after_content'), '1' ); ?>>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Buttons Position (in content):', 'cresta-social-share-counter' ); ?></th>
					<td>	
						<ul>
							<li>
								<label><input type="radio" name='cresta_social_shares_float_buttons' value='left' <?php checked( 'left', get_option('cresta_social_shares_float_buttons') ); ?>><?php _e('Left', 'cresta-social-share-counter'); ?></label>
							</li>
							<li>
								<label><input type="radio" name='cresta_social_shares_float_buttons' value='right' <?php checked( 'right', get_option('cresta_social_shares_float_buttons') ); ?>><?php _e('Right', 'cresta-social-share-counter'); ?></label>
							</li>
							<li>
								<label><input type="radio" name='cresta_social_shares_float_buttons' value='center' <?php checked( 'center', get_option('cresta_social_shares_float_buttons') ); ?>><?php _e('Center', 'cresta-social-share-counter'); ?></label>
							</li>
						</ul>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Shortcode', 'cresta-social-share-counter' ); ?></th>
					<td>
						<span class="description"><?php esc_html_e('You can place the shortcode', 'cresta-social-share-counter'); ?> <code>[cresta-social-share]</code> <?php esc_html_e('wherever you want to display the social buttons.', 'cresta-social-share-counter'); ?></span>
					</td>
				</tr>
			</tbody>	
		</table>
		<h3><div class="dashicons dashicons-search space"></div><?php esc_html_e('Show on :', 'cresta-social-share-counter'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Show on', 'cresta-social-share-counter' ); ?></th>
					<td>
						<?php
							$show_on = explode (',',get_option( 'cresta_social_shares_selected_page' ));
							$args = array(
								'public'   => true,
							);
							$post_types = get_post_types( $args, 'names', 'and' ); 
							echo '<ul>';
							foreach ( $post_types  as $post_type ) { 
								$post_type_name = get_post_type_object( $post_type );
								?>
									<li>
										<label><input type="checkbox" <?php if(in_array( $post_type ,$show_on)) { echo 'checked="checked"'; }?> name="cresta_social_shares_selected_page[]" value="<?php echo esc_attr($post_type); ?>"/><?php echo esc_attr($post_type_name->labels->singular_name); ?></label>
									</li>
								<?php
							}
							echo '</ul>';
						?>
					</td>
				</tr>
			</tbody>	
		</table>
		<h3><div class="dashicons dashicons-admin-generic space"></div><?php esc_html_e('Advanced :', 'cresta-social-share-counter'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Custom CSS Code', 'cresta-social-share-counter' ); ?></th>
					<td>
						<textarea name="cresta_social_shares_custom_css" class="large-text code" rows="10"><?php echo esc_textarea(get_option('cresta_social_shares_custom_css')); ?></textarea>
						<span class="description"><?php esc_html_e( 'Write here your custom CSS code if you want to customize the style of the buttons', 'cresta-social-share-counter' ); ?></span>
					</td>
				</tr>
			</tbody>	
		</table>
		<?php submit_button(); ?>
</form>
</div> <!-- .inside -->
    </div> <!-- .postbox -->
        </div> <!-- .meta-box-sortables .ui-sortable -->
            </div> <!-- post-body-content -->
  <!-- sidebar -->
                <div id="postbox-container-1" class="postbox-container">
                    <div class="meta-box-sortables">
                        <div class="postbox">
                            <h3><span><div class="dashicons dashicons-star-filled"></div> Rate it!</span></h3>
                            <div class="inside">
								Don't forget to rate <strong>Cresta Social Share Counter</strong> on WordPress Pugins Directory.<br/>
								We really appreciate it ;)
                                <br/>
								<img src="<?php echo esc_url(plugins_url( '/images/5-stars.png' , __FILE__ )); ?>">
								<br/>
								<a class="crestaButton" href="https://wordpress.org/plugins/cresta-social-share-counter/"title="Rate Cresta Social Share Counter on WordPress Plugins Directory" class="btn btn-primary" target="_blank">Rate Cresta Social Share Counter</a>
                            </div> <!-- .inside -->
                        </div> <!-- .postbox -->

                        <div class="postbox" style="border: 2px solid #d54e21;">
                            
                            <h3><span><div class="dashicons dashicons-megaphone"></div> Need more? Get the PRO Version</span></h3>
                            <div class="inside">
                                <a href="http://crestaproject.com/downloads/cresta-social-share-counter/" target="_blank" alt="Get Cresta Social Share Counter PRO"><img src="<?php echo plugins_url( '/images/banner-cresta-social-share-counter-pro.png' , __FILE__ ); ?>"></a><br/>
								Get <strong>Cresta Social Share Counter PRO</strong> for only <strong>6,99€</strong>.<br/>
								<ul>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Email Share Button</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> WhatsApp Share Button</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Stumbleupon Share Button</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Buffer Share Button</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Reddit Share Button</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> VK Share Button</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> OK.ru Share Button</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Xing Share Button</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> More than 30 Effects</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> 17 Exclusive Button Styles</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Social Counter Before / After Content</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Change Colors</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Small Buttons on Content</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Tooltip on the Buttons</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> More than 10 hover animations</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> 20% discount code for all CrestaProject WordPress Themes</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> 1 year updates and support</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> and Much More...</li>
								</ul>
								<a class="crestaButton" href="http://crestaproject.com/downloads/cresta-social-share-counter/" target="_blank" title="More details">More Details</a>
                            </div> <!-- .inside -->
                         </div> <!-- .postbox -->
						 <div class="postbox" style="border: 2px solid #0074a2;">
                            
                            <h3><span><div class="dashicons dashicons-admin-plugins"></div> Cresta Posts Box Plugin</span></h3>
                            <div class="inside">
                                <a href="http://crestaproject.com/downloads/cresta-posts-box/" target="_blank" alt="Get Cresta Posts Box"><img src="<?php echo plugins_url( '/images/banner-cresta-posts-box.png' , __FILE__ ); ?>"></a><br/>
								Show the next or previous post in a box that appears when <strong>the user scrolls to the bottom of a current post</strong>.<br/><br/>
								With <strong>Cresta Posts Box</strong> you can show, in a single page (posts, pages or custom post types), a <strong>small box that allows the reader to go to the next or previous post</strong>. The box appears only when the reader finishes reading the current post.
								<a class="crestaButton" href="http://crestaproject.com/downloads/cresta-posts-box/" target="_blank" title="Cresta Posts Box">Available in FREE and PRO version</a>
                            </div> <!-- .inside -->
                         </div> <!-- .postbox -->
                    </div> <!-- .meta-box-sortables -->
                </div> <!-- #postbox-container-1 .postbox-container -->
            </div> <!-- #post-body .metabox-holder .columns-2 -->
            <br class="clear">
        </div> <!-- #poststuff -->
	</div>
	<?php 
	echo ob_get_clean();
}

/* Validate options */
function crestasocialshare_options_validate_1($input) {
	if($input != '' && is_array($input)) {
		$buttons = implode(',',$input);
		$input = wp_filter_nohtml_kses($buttons); 
	} else {
		$input = 'facebook'; 
	}
	return $input;
}
function crestasocialshare_options_validate_2($input) {
	if($input != '' && is_array($input)) {
		$show_on = implode(',',$input);
		$input = wp_filter_nohtml_kses($show_on); 
	} else {
		$input = 'page,post'; 
	}
	return $input;
}
function crestasocialshare_options_validate_3($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_4($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_5($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_6($input) {
	$input = sanitize_text_field(absint($input));
	return $input;
}
function crestasocialshare_options_validate_7($input) {
	$input = sanitize_text_field(absint($input));
	return $input;
}
function crestasocialshare_options_validate_8($input) {
	$input = sanitize_text_field($input);
	return $input;
}
function crestasocialshare_options_validate_9($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_10($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_11($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_12($input) {
	$input = sanitize_text_field($input);
	return $input;
}
function crestasocialshare_options_validate_13($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_14($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_15($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_16($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_17($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_18($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_19($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_20($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_21($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_22($input) {
	$input = sanitize_text_field(absint($input));
	return $input;
}
function crestasocialshare_options_validate_23($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_24($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
function crestasocialshare_options_validate_25($input) {
	$input = wp_filter_nohtml_kses($input);
	return $input;
}
?>
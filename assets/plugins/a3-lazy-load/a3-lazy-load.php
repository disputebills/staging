<?php
/*
Plugin Name: a3 Lazy Load
Description: Speed up your site and enhance frontend user's visual experience in PC's, Tablets and mobile with a3 Lazy Load.
Version: 1.7.1
Author: a3 Revolution
Author URI: http://www.a3rev.com/
Requires at least: 4.0
Tested up to: 4.5
License: GPLv2 or later
	Copyright © 2011 a3 Revolution Software Development team
	a3 Revolution Software Development team
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/
?>
<?php
define('A3_LAZY_VERSION', '1.7.1');
define('A3_LAZY_LOAD_FILE_PATH', dirname(__FILE__));
define('A3_LAZY_LOAD_DIR_NAME', basename(A3_LAZY_LOAD_FILE_PATH));
define('A3_LAZY_LOAD_FOLDER', dirname(plugin_basename(__FILE__)));
define('A3_LAZY_LOAD_NAME', plugin_basename(__FILE__));
define('A3_LAZY_LOAD_URL', str_replace(array('http:','https:'), '', untrailingslashit(plugins_url('/', __FILE__))));
define('A3_LAZY_LOAD_DIR', WP_CONTENT_DIR . '/plugins/' . A3_LAZY_LOAD_FOLDER);
define('A3_LAZY_LOAD_JS_URL', A3_LAZY_LOAD_URL . '/assets/js');
define('A3_LAZY_LOAD_CSS_URL', A3_LAZY_LOAD_URL . '/assets/css');
define('A3_LAZY_LOAD_IMAGES_URL', A3_LAZY_LOAD_URL . '/assets/images');

include( 'admin/admin-ui.php' );
include( 'admin/admin-interface.php' );

include( 'admin/admin-pages/admin-settings-page.php' );

include( 'admin/admin-init.php' );
include( 'admin/less/sass.php' );

include( 'classes/class-a3-lazy-load.php' );
include( 'classes/class-a3-lazy-load-filter.php' );

include( 'admin/a3-lazy-load-admin.php' );

// Defined this function for check Lazy Load is enabled that 3rd party plugins or theme can use to check
$a3_lazy_load_global_settings = get_option( 'a3_lazy_load_global_settings', array( 'a3l_apply_lazyloadxt' => 1, 'a3l_apply_to_images' => 1, 'a3l_apply_to_videos' => 1 ) );
if ( 1 == $a3_lazy_load_global_settings['a3l_apply_lazyloadxt'] && !is_admin() && ( 1 == $a3_lazy_load_global_settings['a3l_apply_to_images'] || 1 == $a3_lazy_load_global_settings['a3l_apply_to_videos'] ) ) {
	function a3_lazy_load_enable() {
		return true;
	}
}
if ( 1 == $a3_lazy_load_global_settings['a3l_apply_lazyloadxt'] && !is_admin() &&  1 == $a3_lazy_load_global_settings['a3l_apply_to_images'] ) {
	function a3_lazy_load_image_enable() {
		return true;
	}
}
if ( 1 == $a3_lazy_load_global_settings['a3l_apply_lazyloadxt'] && !is_admin() &&  1 == $a3_lazy_load_global_settings['a3l_apply_to_videos'] ) {
	function a3_lazy_load_video_enable() {
		return true;
	}
}

/**
 * Call when the plugin is activated
 */
register_activation_hook(__FILE__, 'a3_lazy_load_activated');
?>

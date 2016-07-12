<?php

/*
Plugin Name: Live Edit
Plugin URI: http://www.elliotcondon.com/
Description: Edit the title, content and any ACF fields from the front end of your website!
Version: 2.1.4
Author: Elliot Condon
Author URI: http://www.elliotcondon.com/
License: GPL
Copyright: Elliot Condon
*/

function live_edit_plugins_loaded() {
	
	// vars
	$version = 4;
	
	
	// detect ACF version
	if( function_exists('acf_get_setting') ) {
		
		$version = acf_get_setting('version');
		$version = substr($version, 0, 1);
		
	}
	
	
	// include compatible plugin version
	include_once("live-edit-v{$version}.php");
	
}

add_action('plugins_loaded', 'live_edit_plugins_loaded');	

?>
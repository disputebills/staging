<?php 

//error_reporting(E_ALL);

/*
Plugin Name: Subscribe To Download For WordPress
Plugin Script: wp-freebie-subscribe.php
Plugin URI: http://tyler.tc
Description: Tap into your sites traffic and turn it into a newsletter / email list super builder!
Version: 1.1.5
Author: Tyler Colwell
Author URI: http://tyler.tc

--- THIS PLUGIN AND ALL FILES INCLUDED ARE COPYRIGHT © TYLER COLWELL 2011. 
YOU MAY NOT MODIFY, RESELL, DISTRIBUTE, OR COPY THIS CODE IN ANY WAY. ---

*/

/*-----------------------------------------------------------------------------------*/
/*	Define Anything Needed
/*-----------------------------------------------------------------------------------*/

define('FREEBIESUB_LOCATION', WP_PLUGIN_URL . '/'.basename(dirname(__FILE__)));
define('FREEBIESUB_PATH', plugin_dir_path(__FILE__));
define('FREEBIESUB_VERSION', '1.1.5');
require_once('inc/tcf_settings_page.php');
require_once('inc/tcf_manage_page.php');
require_once('inc/tcf_bootstrap.php');
		
?>
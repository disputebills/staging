<?php
/**
 * THA Hooks Interface.
 *
 * @package   THA_Hooks_Interface
 * @author    ThematoSoup <contact@thematosoup.com>
 * @license   GPL-2.0+
 * @link      http://thematosoup.com
 * @copyright 2013 ThematoSoup
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$all_tha_hooks = array(
	'WordPress',
	'html',
	'body',
	'head',
	'header',
	'content',
	'entry',
	'comments',
	'sidebar',
	'footer'
);

foreach ( $all_tha_hooks as $hooks_group ) :
	// Delete all the options on plugin uninstall
	delete_option( 'tha_hooks_interface_' . $hooks_group );
endforeach;
<?php
/* "Copyright 2012 a3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
a3 LazyLoad General Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class A3_Lazy_Load_Global_Settings extends A3_Lazy_Load_Admin_UI
{

	/**
	 * @var string
	 */
	private $parent_tab = 'a3-lazy-load';

	/**
	 * @var array
	 */
	private $subtab_data;

	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'a3_lazy_load_global_settings';

	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'a3_lazy_load_global_settings';

	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;

	/**
	 * @var array
	 */
	public $form_fields = array();

	/**
	 * @var array
	 */
	public $form_messages = array();

	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->init_form_fields();
		$this->subtab_init();

		$this->form_messages = array(
				'success_message'	=> __( 'Settings successfully saved.', 'a3_lazy_load' ),
				'error_message'		=> __( 'Error: Settings can not save.', 'a3_lazy_load' ),
				'reset_message'		=> __( 'Settings successfully reseted.', 'a3_lazy_load' ),
			);

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );

		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'after_save_settings' ) );

		add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );

	}

	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {

		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );

	}

	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $a3_lazy_load_admin_interface;

		$a3_lazy_load_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}

	/*-----------------------------------------------------------------------------------*/
	/* after_save_settings()
	/* Process when clean on deletion option is un selected */
	/*-----------------------------------------------------------------------------------*/
	public function after_save_settings() {
		if ( ( isset( $_POST['bt_save_settings'] ) || isset( $_POST['bt_reset_settings'] ) ) && get_option( 'a3_lazy_load_clean_on_deletion' ) == 0  )  {
			$uninstallable_plugins = (array) get_option('uninstall_plugins');
			unset($uninstallable_plugins[A3_LAZY_LOAD_NAME]);
			update_option('uninstall_plugins', $uninstallable_plugins);
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $a3_lazy_load_admin_interface;

		$a3_lazy_load_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}

	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array (
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {

		$subtab_data = array(
			'name'				=> 'global-settings',
			'label'				=> __( 'Global Settings', 'a3_lazy_load' ),
			'callback_function'	=> 'a3_lazy_load_global_settings_form',
		);

		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;

	}

	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {

		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();

		return $subtabs_array;
	}

	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $a3_lazy_load_admin_interface;

		$output = '';
		$output .= $a3_lazy_load_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );

		return $output;
	}

	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {

  		// Define settings
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
     		array(
            	'name' 		=> __( 'Plugin Framework Global Settings', 'a3_lazy_load' ),
            	'id'		=> 'plugin_framework_global_box',
                'type' 		=> 'heading',
                'first_open'=> true,
                'is_box'	=> true,
           	),
           	array(
           		'name'		=> __( 'Customize Admin Setting Box Display', 'a3_lazy_load' ),
           		'desc'		=> __( 'By default each admin panel will open with all Setting Boxes in the CLOSED position.', 'a3_lazy_load' ),
                'type' 		=> 'heading',
           	),
           	array(
				'type' 		=> 'onoff_toggle_box',
			),
           	array(
            	'name' 		=> __( 'House Keeping', 'a3_lazy_load' ),
                'type' 		=> 'heading',
            ),
			array(
				'name' 		=> __( 'Clean up on Deletion', 'a3_lazy_load' ),
				'desc' 		=> __( 'On deletion (not deactivate) the plugin will completely remove all tables and data it created, leaving no trace it was ever here.', 'a3_lazy_load'),
				'id' 		=> 'a3_lazy_load_clean_on_deletion',
				'type' 		=> 'onoff_checkbox',
				'default'	=> '0',
				'separate_option'	=> true,
				'free_version'		=> true,
				'checked_value'		=> '1',
				'unchecked_value'	=> '0',
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),

           	array(
            	'name' 		=> __( 'Lazy Load Activation', 'a3_lazy_load' ),
                'type' 		=> 'heading',
                'id'		=> 'lazy_load_enable_box',
                'is_box'	=> true,
           	),
           	array(
				'name' 		=> __( 'Enable Lazy Load', 'a3_lazy_load' ),
                'id' 		=> 'a3l_apply_lazyloadxt',
                'class'		=> 'a3l_apply_to_load',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),

			array(
				'name'		=> __( 'Lazy Load Images', 'a3_lazy_load' ),
                'type' 		=> 'heading',
                'class'		=> 'a3l_apply_to_load_container',
                'id'		=> 'a3l_apply_to_images_box',
                'is_box'	=> true,
           	),
           	array(
				'name' 		=> __( 'Enable Lazy Load for Images', 'a3_lazy_load' ),
                'id' 		=> 'a3l_apply_to_images',
                'class'		=> 'a3l_apply_to_images',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),

			array(
                'type' 		=> 'heading',
				'class'		=> 'a3l_apply_to_load_images_container'
           	),
			array(
				'name' 		=> __( 'Images in Content', 'a3_lazy_load' ),
                'id' 		=> 'a3l_apply_image_to_content',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),
			array(
				'name' 		=> __( 'Images in Widgets', 'a3_lazy_load' ),
                'id' 		=> 'a3l_apply_image_to_textwidget',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),
			array(
				'name' 		=> __( 'Post Thumbnails', 'a3_lazy_load' ),
                'id' 		=> 'a3l_apply_image_to_postthumbnails',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),
			array(
				'name' 		=> __( 'Gravatars', 'a3_lazy_load' ),
                'id' 		=> 'a3l_apply_image_to_gravatars',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),
			array(
				'name' => __( 'Skip Images Classes', 'a3_lazy_load' ),
				'id' 		=> 'a3l_skip_image_with_class',
				'desc' 		=>  __('Comma separated. Example: "no-lazy, lazy-ignore, image-235"', 'a3_lazy_load'),
				'type' 		=> 'text',
				'default'	=> ""
			),
			array(
				'name' 		=> __( 'Noscript Support', 'a3_lazy_load' ),
                'id' 		=> 'a3l_image_include_noscript',
                'desc'		=> __( 'Turn ON to activate Noscript tag as a fallback to show images for users who have JavaScript disabled in their browser.', 'a3_lazy_load' ),
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),


			array(
				'name'		=> __( 'Lazy Load Videos and iframes', 'a3_lazy_load' ),
                'type' 		=> 'heading',
                'class'		=> 'a3l_apply_to_load_container',
                'id'		=> 'a3l_apply_to_videos_box',
                'is_box'	=> true,
           	),
           	array(
				'name' 		=> __( 'Video and iframes', 'a3_lazy_load' ),
				'desc'		=> sprintf( __( 'Turn ON to activate Lazy Load for <a href="%s" target="_blank">WordPress Embeds</a>, <a href="%s" target="_blank">HTML5 Video</a> and content loaded by iframe from all sources. Note: WordPress Shortcode is not supported.', 'a3_lazy_load' ), 'http://codex.wordpress.org/Embeds/', 'http://www.w3schools.com/html/html5_video.asp' ),
                'id' 		=> 'a3l_apply_to_videos',
                'class'		=> 'a3l_apply_to_videos',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),

			array(
                'type' 		=> 'heading',
				'class'		=> 'a3l_apply_to_load_videos_container'
           	),
			array(
				'name' 		=> __( 'In Content', 'a3_lazy_load' ),
                'id' 		=> 'a3l_apply_video_to_content',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),
			array(
				'name' 		=> __( 'In Widgets', 'a3_lazy_load' ),
                'id' 		=> 'a3l_apply_video_to_textwidget',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),
			array(
				'name' => __( 'Skip Videos Classes', 'a3_lazy_load' ),
				'id' 		=> 'a3l_skip_video_with_class',
				'desc' 		=>  __('Comma separated. Example: "no-lazy, lazy-ignore, video-235"', 'a3_lazy_load'),
				'type' 		=> 'text',
				'default'	=> ""
			),
			array(
				'name' 		=> __( 'Noscript Support', 'a3_lazy_load' ),
                'id' 		=> 'a3l_video_include_noscript',
                'desc'		=> __( 'Turn ON to activate Noscript tag as a fallback to show WordPress Embeds, HTML 5 Video and iframe loaded content for users who have JavaScript disabled in their browser.', 'a3_lazy_load' ),
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),

			array(
				'name'		=> __( 'Script Load Optimization', 'a3_lazy_load' ),
                'type' 		=> 'heading',
                'class'		=> 'a3l_apply_to_load_container',
                'id'		=> 'a3l_script_load_optimization_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Theme Loader Function', 'a3_lazy_load' ),
				'desc'		=> __( 'Your theme must have the wp_footer() function if you select FOOTER load.', 'a3_lazy_load' ),
				'id' 		=> 'a3l_theme_loader',
				'type' 		=> 'switcher_checkbox',
				'default'	=> 'wp_footer',
				'checked_value'		=> 'wp_head',
				'unchecked_value'	=> 'wp_footer',
				'checked_label'		=> __( 'HEADER', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'FOOTER', 'a3_lazy_load' ),
			),

			array(
				'name'		=> __( 'WordPress Mobile Template Plugins', 'a3_lazy_load' ),
                'type' 		=> 'heading',
                'class'		=> 'a3l_apply_to_load_container',
                'id'		=> 'a3l_wordpress_mobile_template_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Disable On WPTouch', 'a3_lazy_load' ),
				'desc' 		=>  __("Disables a3 Lazy Load when the WPTouch mobile theme is used", 'a3_lazy_load'),
                'id' 		=> 'a3l_load_disable_on_wptouch',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),
			array(
				'name' 		=> __( 'Disable On MobilePress', 'a3_lazy_load' ),
				'desc' 		=>  __("Disables a3 Lazy Load when the MobilePress mobile theme is used", 'a3_lazy_load'),
                'id' 		=> 'a3l_load_disable_on_mobilepress',
				'type' 		=> 'onoff_checkbox',
				'default'	=> true,
				'checked_value'		=> true,
				'unchecked_value'	=> false,
				'checked_label'		=> __( 'ON', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'OFF', 'a3_lazy_load' ),
			),

			array(
				'name' 		=> __( 'Effect & Style', 'a3_lazy_load' ),
				'class'		=> 'a3l_apply_to_load_container',
                'type' 		=> 'heading',
                'id'		=> 'a3l_settings_style_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Loading Effect', 'a3_lazy_load' ),
				'id' 		=> 'a3l_effect',
				'type' 		=> 'switcher_checkbox',
				'default'	=> 'spinner',
				'checked_value'		=> 'fadein',
				'unchecked_value'	=> 'spinner',
				'checked_label'		=> __( 'FADE IN', 'a3_lazy_load' ),
				'unchecked_label' 	=> __( 'SPINNER', 'a3_lazy_load' ),
			),
			array(
				'name' 		=> __( 'Loading Background Colour', 'a3_lazy_load' ),
				'id' 		=> 'a3l_effect_background',
				'type' 		=> 'color',
				'default'	=> '#ffffff'
			),

			array(
				'name' 		=> __( 'Image Load Threshold', 'a3_lazy_load' ),
				'class'		=> 'a3l_apply_to_load_container',
                'type' 		=> 'heading',
                'id'		=> 'a3l_image_load_theshold_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Threshold', 'a3_lazy_load' ),
				'id' 		=> 'a3l_edgeY',
				'desc'		=> 'px ' . __( 'Expands visible page area (viewport) in vertical direction by the amount of pixels set. Elements start to load as soon as the reach the threshold instead of when they reach the actual viewport.', 'a3_lazy_load' ),
				'type' 		=> 'text',
				'default'	=> 0,
				'css'		=> 'width: 80px;'
			),

     	));
	}

	public function include_script() {
?>
<script>
(function($) {
	$(document).ready(function() {

		if ( ! $("input.a3l_apply_to_load").is(":checked") ) {
			$(".a3l_apply_to_load_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		}

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.a3l_apply_to_load', function( event, value, status ) {
			$(".a3l_apply_to_load_container").attr('style','display:none;');
			if ( status == 'true' ) {
				$(".a3l_apply_to_load_container").slideDown();
			} else {
				$(".a3l_apply_to_load_container").slideUp();
			}
		});

		if ( $("input.a3l_apply_to_images:checked").val() != '1') {
			$(".a3l_apply_to_load_images_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		}

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.a3l_apply_to_images', function( event, value, status ) {
			$(".a3l_apply_to_load_images_container").attr('style','display:none;');
			if ( status == 'true' ) {
				$(".a3l_apply_to_load_images_container").slideDown();
			} else {
				$(".a3l_apply_to_load_images_container").slideUp();
			}
		});

		if ( $("input.a3l_apply_to_videos:checked").val() != '1' ) {
			$(".a3l_apply_to_load_videos_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		}

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.a3l_apply_to_videos', function( event, value, status ) {
			$(".a3l_apply_to_load_videos_container").attr('style','display:none;');
			if ( status == 'true' ) {
				$(".a3l_apply_to_load_videos_container").slideDown();
			} else {
				$(".a3l_apply_to_load_videos_container").slideUp();
			}
		});


	});
})(jQuery);
</script>
<?php
	}
}

global $a3_lazy_load_global_settings_panel;
$a3_lazy_load_global_settings_panel = new A3_Lazy_Load_Global_Settings();

/**
 * a3_lazy_load_cards_settings_form()
 * Define the callback function to show subtab content
 */
function a3_lazy_load_global_settings_form() {
	global $a3_lazy_load_global_settings_panel;
	$a3_lazy_load_global_settings_panel->settings_form();
}

?>

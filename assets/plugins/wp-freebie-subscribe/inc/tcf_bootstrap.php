<?PHP

/*-----------------------------------------------------------------------------------*/
/*	Install Database
/*-----------------------------------------------------------------------------------*/

function freebiesub_db_install(){
	
	// define needed globals
	global $wpdb;
	global $freebiesub_db_version;

	// create table
	$table_name = $wpdb->prefix . "freebie_subscriber";
	$sql = "CREATE TABLE ".$table_name." (
		`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`first_name` VARCHAR(250) NOT NULL, 
		`last_name` VARCHAR(250) NOT NULL, 
		`phone` VARCHAR(250) NOT NULL, 
		`email` VARCHAR(250) NOT NULL, 
		`ip` VARCHAR(250) NOT NULL, 
		`time` DATETIME NOT NULL,
		`post_id` VARCHAR(250) NOT NULL, 
		`page_url` VARCHAR(250) NOT NULL,
		`event_type` VARCHAR(250) NOT NULL,
		`status` VARCHAR(250) NOT NULL
	) ENGINE = MyISAM;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	// store db version
	add_option("freebiesub_db_version", "1.1.5");
	
}

/*-----------------------------------------------------------------------------------*/
/*	Start JS Loader
/*-----------------------------------------------------------------------------------*/
	
// This will make sure all of the JS is only called once!
function freebiesub_jsloader() {
	
	// Make sure we are not in the admin section
	if (!is_admin() & get_option('freebiesub-enabled') != 3) {

		// Root wp-content path
		$root = get_bloginfo('wpurl')."/wp-content";
		
		// Include them
		wp_enqueue_script('jquery');
		
		// Run Traffic Pop Theme
		wp_deregister_style('freebieCSS');
		wp_register_style('freebieCSS', FREEBIESUB_LOCATION.'/css/freebiesub.css');
		wp_enqueue_style('freebieCSS');
		
	}
	
	// Bootsrtap MCE
	freebiesub_mce();
	
}

/*-----------------------------------------------------------------------------------*/
/*	Current Page Function
/*-----------------------------------------------------------------------------------*/

function freebiesub_current_page(){
	
	$pageURL = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	
	return $pageURL;

}

/*-----------------------------------------------------------------------------------*/
/*	Add tinyMCE Button
/*-----------------------------------------------------------------------------------*/

function freebiesub_mce(){
	
	// Don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
	return;
	
	// Add only in Rich Editor mode
	if( get_user_option('rich_editing') == 'true'){
		// Add cutom button to TinyMCE
		add_filter('mce_external_plugins', "freebiesub_mce_register");
		add_filter('mce_buttons', 'freebiesub_add_button', 0);
	}
	
}
function freebiesub_add_button($buttons) {
   array_push($buttons, "separator", "freebiesubplugin");
   return $buttons;
}
function freebiesub_mce_register($plugin_array) {
   $plugin_array['freebiesubplugin'] = FREEBIESUB_LOCATION."/inc/mce/mce.js";
   return $plugin_array;
} // end tinyMCE

/*-----------------------------------------------------------------------------------*/
/*	Handle Form Submit
/*-----------------------------------------------------------------------------------*/

function freebiesub_form_hooks(){ ?>

	<script type="text/javascript">jQuery(document).ready(function(){jQuery(".freebie-sub-form").submit(function(){var a=jQuery(this),e=a.serialize(),c=a.children("div.replaceArea"),d=c.children("div.replaceArea-error");jQuery(".form-download-button");d.html("");jQuery('input[type="submit"]',a).val("Sending...");jQuery.ajax({type:"POST",url:"<?PHP echo FREEBIESUB_LOCATION; ?>/inc/submit.php",data:e,success:function(b){"true"==b.error?(d.html(b.message),jQuery('input[type="submit"]',a).val("Download")):c.html(b.message)}});return!1});});</script>
	
<?PHP }

/*-----------------------------------------------------------------------------------*/
/*	Shortcode Handler
/*-----------------------------------------------------------------------------------*/

function freebiesub_handle($atts, $content) {
	
	// Extract variables from shortcode tag, set defaults
	extract(shortcode_atts(array(
		"download" => '',
	), $atts));
	
	if($download != ''){
						
		return freebiesub_create_form($download);
		
	} else {
		
		return '<!-- download link not set! -->';
		
	} // end if download set
											
} // End shortcode handler

/*-----------------------------------------------------------------------------------*/
/*	Generate Download Form
/*-----------------------------------------------------------------------------------*/

function freebiesub_create_form($download){
	
	// Get Settings
	global $tcfreebiesub_options;

	// Post Settings
	global $post;
	 
	// Setup Cookie
	$cookie = '';
	if( isset( $_COOKIE['wpfsubdl'] ) ){
		$cookie = $_COOKIE['wpfsubdl'];
	}
	
	// Opts
	$freebiesub_googl_enabled = get_option('freebiesub-googl-enabled');
	$freebiesub_api_key = get_option('freebiesub-api-key');
	$freebiesub_penalty = get_option('freebiesub-penalty');
	$freebiesub_subscribe_heading = stripslashes_deep( get_option('freebiesub-subscribe-heading') );
	$freebiesub_subscribe_message = stripslashes_deep( get_option('freebiesub-subscribe-message') );
	$freebiesub_optin_message = stripslashes_deep( get_option('freebiesub-optin-message') );
	$freebiesub_download_message = stripslashes_deep( get_option('freebiesub-download-message') );
	
	if( $cookie == 'waiting' ){
	
	$return = '
	
    <div class="freebie-sub-box">
    
        <div class="freebie-sub-inner">
        
            <h3>'.__('Hey! Did You Check Your Email?', 'tcsubdl').'</h3>
            <p>'.__('You have already requested a download and the link has been sent to the email you entered. Please note it can take a few minuets for the email to show up (check all your email folders!) Once you click your link you will not have to enter your email for download links anymore. Nice!', 'tcsubdl').'</p><br />
            <p>'.__('Note - If you entered a fake email you can try again in', 'tcsubdl').' '.$freebiesub_penalty.' '.__('minutes with a real one ;)', 'tcsubdl').'</p>
                        
        </div>
        
    </div>
	
    ';
    
	} elseif( $cookie == 'true' ){
	 
		$return = '

			<div class="freebie-sub-box">
			
				<div class="freebie-sub-inner">
				
					<h3>'.$freebiesub_download_message.' <input type="button" onclick="window.location=\''.$download.'\'" value="Download" class="freebie-submit download"></h3>
								
				</div>
				
			</div>
		
		';
    
	} else {
		
		// add Impression
		$time = date( 'Y-m-d H:i:s' );
		freebiesub_add_impression( $time, $post->ID, freebiesub_current_page() );
		
		// Build Form Fields
		$fields = "";
		if( $tcfreebiesub_options['show-first-name'] == 'true' ){
			$fields.="<input type=\"text\" placeholder=\"First Name\" name=\"first_name\" class=\"name\">";
		}
		if( $tcfreebiesub_options['show-last-name'] == 'true' ){
			$fields.="<input type=\"text\" placeholder=\"Last Name\" name=\"last_name\" class=\"name\">";
		}
		if( $tcfreebiesub_options['show-phone'] == 'true' ){
			$fields.="<input type=\"text\" placeholder=\"Phone Number\" name=\"phone\" class=\"name\">";
		}
		
		$return = "
	
		<div class=\"freebie-sub-box\">
		
			<div class=\"freebie-sub-inner\">
			
				<h3>$freebiesub_subscribe_heading</h3>
				<p>$freebiesub_subscribe_message</p>
				
				<form class=\"freebie-sub-form\">
				
					<div class=\"replaceArea\">
					
						<div class=\"replaceArea-error\"></div>
						
						<center>
						
						<input type=\"text\" placeholder=\"Email Address\" name=\"email\" class=\"name\">
						
						".$fields."
						
						<input type=\"submit\" value=\"Download\" class=\"freebie-submit\">
						
						</center>
						
						<label><input class=\"agree\" type=\"checkbox\" checked=\"checked\" value=\"1\" name=\"agree\"><span>$freebiesub_optin_message</span></label>
						
						<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".base64_encode($download)."\">
						<input type=\"hidden\" name=\"page_id\" id=\"page_id\" value=\"".$post->ID."\">
						<input type=\"hidden\" name=\"page_url\" id=\"page_url\" value=\"".freebiesub_current_page()."\">
						
					</div>
					
				</form>
				
			</div>
			
		</div>
		
		";

	} // end if
	
	return $return;
	
} // end main

/*-----------------------------------------------------------------------------------*/
/*	Add Impression to Tracking Table
/*-----------------------------------------------------------------------------------*/

function freebiesub_add_impression($time, $page_id, $page_url){
	
	global $wpdb;
	
	$tablename = $wpdb->prefix . "freebie_subscriber";
	$wpdb->insert( 
		$tablename, 
		array( 
			'time' => $time, 
			'post_id' => $page_id, 
			'page_url' => $page_url,
			'event_type' => 'impression' 
		), 
		array( 
			'%s', 
			'%s', 
			'%s', 
			'%s'
		) 
	);
	
}

/*-----------------------------------------------------------------------------------*/
/*	Start Running Hooks
/*-----------------------------------------------------------------------------------*/

// Installer
register_activation_hook( FREEBIESUB_PATH.'/wp-freebie-subscribe.php', 'freebiesub_db_install' );
// Add the short code to WordPress
add_shortcode("freebiesub", "freebiesub_handle");
// Add hook to include settings CSS
add_action( 'admin_init', 'freebiesub_settings_admin_css' );
// create custom plugin settings menu
add_action( 'admin_menu', 'freebiesub_create_menu' );
// Add Admin Scripts
add_action( 'admin_enqueue_scripts', 'freebiesub_admin_scripts' );
// JS Loader
add_action( 'init', 'freebiesub_jsloader' );
// Form Submit
add_action( 'wp_footer', 'freebiesub_form_hooks' );

?>
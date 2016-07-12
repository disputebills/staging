<?PHP

/*-----------------------------------------------------------------------------------*/
/*	Menu Creation
/*-----------------------------------------------------------------------------------*/

function freebiesub_create_menu() {
	
	// Create Top Level Menu
	$page = add_menu_page( "Freebie Subscribe to Download", "Subscribe To DL", "administrator", __FILE__, "freebiesub_settings_page", 'dashicons-email-alt');
	$page2 = add_submenu_page( __FILE__, "List Manager", "List Manager", "administrator", "freebiesub_manage_page", "freebiesub_manage_page" );
	
	// Adds the tab into the options panel in WordPress Admin area
	//$page = add_options_page("Aweber Traffic Pop Settings", "Aweber Traffic Pop", 'administrator', __FILE__, 'freebiesub_settings_page');
		
	//call register settings function
	add_action( 'admin_init', 'freebiesub_register_settings' );
		
	// Hook style sheet loading
	add_action( 'admin_print_styles-' . $page, 'freebiesub_settings_admin_cssloader' );
	add_action( 'admin_print_styles-' . $page2, 'freebiesub_settings_admin_cssloader' );
	
}

// JS Hooks for Settings
function freebiesub_admin_scripts($hook){
    if( 'toplevel_page_wp-freebie-subscribe/inc/tcf_settings_page' != $hook )
        return;
    wp_enqueue_script( 'easypiechart', FREEBIESUB_LOCATION.'/js/easypiechart.js' );
}
		
/*-----------------------------------------------------------------------------------*/
/*	Add Admin CSS
/*-----------------------------------------------------------------------------------*/

function freebiesub_settings_admin_css(){
				
	/* Register our stylesheet. */
	wp_register_style( 'freebiesubSettings', FREEBIESUB_LOCATION.'/css/tc_framework.css' );
							
} function freebiesub_settings_admin_cssloader(){
	
	// It will be called only on your plugin admin page, enqueue our stylesheet here
	wp_enqueue_style( 'freebiesubSettings' );
	   
}

/*-----------------------------------------------------------------------------------*/
/*	Define Settings
/*-----------------------------------------------------------------------------------*/

global $tcfreebiesub_settings;

$tcfreebiesub_settings = array(
	'bitly-enabled'			=> 'false',
	'bitly-api-key'			=> '',
	'bitly-domain'			=> '',
	'penalty'				=> '3',
	'show-first-name'		=>	'false',
	'show-last-name'		=>	'false',
	'show-phone'			=>	'false',
	'subscribe-heading'		=> 'Want this freebie? Enter your email and get it now!',
	'subscribe-message'		=> 'Simply enter your email address and the download link will be sent right to your inbox.',
	'optin-message'			=> 'YES! Please send me the occasional newsletter with exclusive freebies and content. Your email will never be shared, unsubscribe at any time.',
	'download-message'		=> "It looks like you're already a subscriber :)",
	'email-message'			=> 'Hey there,\n\nHere is the download link you requested. After clicking this link you will not need to enter your email address anymore on the site.\n\nThanks again and enjoy :)\n\n',
	'email-subject'			=> 'Your Download Link',
	'email-name'			=> 'Your Site Name',
	'email-from'			=> 'noreply@yoursite.com',
	'thankyou-message'		=> 'Thank you! You will get an email shortly (within 10 minutes or so) with your download link. You will no longer have to enter your email address to download.',
	'check-email-heading'	=> 'Hey! Did you check your email?',
	'check-email-message'	=> 'You have already requested a download and the link has been sent to the email you entered. Please note it can take a few minuets for the email to show up (check all your email folders!) Once you click your link you will not have to enter your email for download links anymore. Nice!'
);

/*-----------------------------------------------------------------------------------*/
/*	Register Settings
/*-----------------------------------------------------------------------------------*/

function freebiesub_register_settings(){
	global $tcfreebiesub_settings;
	$prefix = 'freebiesub';
	foreach($tcfreebiesub_settings as $setting => $value){
		// Define
		$thisSetting = $prefix.'-'.$setting;
		// Register setting
		register_setting( $prefix.'-settings-group', $thisSetting );
		// Apply default
		add_option( $thisSetting, $value );
	}
}

/*-----------------------------------------------------------------------------------*/
/*	Get Settings
/*-----------------------------------------------------------------------------------*/

function freebiesub_get_settings(){
	// Get Settings	
	global $tcfreebiesub_settings;
	$prefix = 'freebiesub';
	$new_settings = array();
	foreach($tcfreebiesub_settings as $setting => $default){
		// Define
		$thisSetting = $prefix.'-'.$setting;
		$value = get_option( $thisSetting );
		if( !isset($value) ) : $value = ''; endif;
		$new_settings[$setting] = $value;
	}
	return $new_settings;
}

global $tcfreebiesub_options;
$tcfreebiesub_options = freebiesub_get_settings();

/*-----------------------------------------------------------------------------------*/
/*	Ajax save callback
/*-----------------------------------------------------------------------------------*/

add_action('wp_ajax_freebiesub_tc_settings_save', 'freebiesub_tc_settings_save');

function freebiesub_tc_settings_save(){
	
	// Security
	check_ajax_referer('freebiesub_settings_secure', 'security');

	// Setup
	global $tcfreebiesub_settings;
	$prefix = 'freebiesub';

	// Loop through settings
	foreach($tcfreebiesub_settings as $setting => $value){
		
		// Define
		$thisSetting = $prefix.'-'.$setting;
					
		// Register setting
		if( isset( $_POST[$thisSetting] ) ){
			update_option( $thisSetting, stripslashes_deep( $_POST[$thisSetting] ) );
		}
		
	} // end for each
		
}

/*-----------------------------------------------------------------------------------*/
/*	New framework settings page
/*-----------------------------------------------------------------------------------*/

function freebiesub_settings_page(){
	
	// Stat Span Check
	if( isset( $_GET['pagespan'] ) ){
		$pagespan_active = 'true';
	} else {
		$pagespan_active = 'false';
	}

?>

<style>

.tcclear{
	clear:both;
	height:0px;
	padding:0px;
	margin:0px;
}

/*-----------------------------------------------------------------------------------*/
/*	Page Table
/*-----------------------------------------------------------------------------------*/

.freebiesub_page_list td{
	padding:10px 5px;
	color:#868C9C;
	font-weight:bold;
	font-size:12px;
}

.freebiesub_page_list a{
	color:#868C9C;
	font-size:13px;
	text-decoration:none;
}

.freebiesub_page_list a:hover{
	color:#FFF;
}

.freebiesub_page_list .top-row{
	background:#DF5252;
	font-size:12px;
	text-align:center;
	color:#ffffff;
	text-transform:uppercase;
}

.freebiesub_page_list .top-row th{
	padding:10px 20px;
}

.freebiesub_page_list .page-row{
	background:#272A33;
}

.freebiesub_page_list .page-row.alt-row{
	background:#1D2027;
}

.freebiesub_page_list .page-row.alt-row td{
}

.freebiesub_page_list .page_followers{
	background:#C84A4A;
	font-size:12px;
	text-align:center;
	color:#ffffff;
}

.freebiesub_page_list .page-row.alt-row .page_followers{
	background:#DF5252;
}

.freebiesub-page-sorter{
	background:#DF5252;
	color:#ffffff;
	padding:15px 20px;
	margin:20px 0 0 0;
	text-transform:uppercase;
}

.freebiesub-page-sorter span{
	float:left;
}

.freebiesub-page-sorter .stat-filter{
	color:#f29e9e;
	text-transform:uppercase;
	font-size:10px;
	font-weight:bold;
	border:1px solid #f29e9e;
	background-color:#df5252;
	margin:0 0 0 15px;
	padding:3px 10px;
	cursor:pointer;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	text-decoration:none;
	text-shadow:none;
}

.freebiesub-page-sorter .stat-filter:hover{
    color:#fde7e7;
	border:1px solid #fde7e7;
}

.freebiesub-page-sorter .stat-filter.current{
	color:#ffffff;
	border:1px solid #ffffff;
}

</style>

<script>
	
jQuery(document).ready(function(){

/*-----------------------------------------------------------------------------------*/
/*	Options Pages & Layout
/*-----------------------------------------------------------------------------------*/
	  
	jQuery('.options_pages li').click(function(){
		
		var tab_page = 'div#' + jQuery(this).attr('id');
		var old_page = 'div#' + jQuery('.options_pages li.active').attr('id');
		
		// Change button class
		jQuery('.options_pages li.active').removeClass('active');
		jQuery(this).addClass('active');
				
		// Set active tab page
		jQuery(old_page).fadeOut('slow', function(){
			
			jQuery(tab_page).fadeIn('slow', function(){
				
				console.log(tab_page);
				
				if( tab_page == 'div#stats_options' ){
					jQuery('.chart').easyPieChart({
						barColor:	'#DF5252',
						trackColor:	'#f2f2f2',
						scaleColor:	'#DF5252',
						lineCap:	'round',
						lineWidth:	'10',
						size:		'150'
					});
					jQuery('.chart-row2').easyPieChart({
						barColor:	'#FFFFFF',
						trackColor:	'#C84A4A',
						scaleColor:	'#ffffff',
						lineCap:	'round',
						lineWidth:	'5',
						size:		'80'
					});
				} // end if stats page
			
			}); // end new page load
			
		}); // end fade out
		
	});
	
/*-----------------------------------------------------------------------------------*/
/*	Form Submit
/*-----------------------------------------------------------------------------------*/
	
	jQuery('form#plugin-options').submit(function(){
			
		var data = jQuery(this).serialize();
		
		jQuery.post(ajaxurl, data, function(response){
			
			if(response == 0){
				
				// Flash success message and shadow
				var success = jQuery('#success-save');
				var bg = jQuery("#message-bg");
				success.css("position","fixed");
				success.css("top", ((jQuery(window).height() - 65) / 2) + jQuery(window).scrollTop() + "px");
				success.css("left", ((jQuery(window).width() - 257) / 2) + jQuery(window).scrollLeft() + "px");
				bg.css({"height": jQuery(window).height()});
				bg.css({"opacity": .45});
				bg.fadeIn("slow", function(){
					success.fadeIn('slow', function(){
						success.delay(1500).fadeOut('fast', function(){
							bg.fadeOut('fast');
						});
					});
				});
								
			} else {
				
				//error out
				
			}
		
		});
				  
		return false;
	
	});	
	
/*-----------------------------------------------------------------------------------*/
/*	Load Stats On Page Load If Needed
/*-----------------------------------------------------------------------------------*/

	var statSpan = "<?PHP echo $pagespan_active; ?>";
	if( statSpan == 'true' ){
		jQuery('.chart').easyPieChart({
			barColor:	'#DF5252',
			trackColor:	'#f2f2f2',
			scaleColor:	'#DF5252',
			lineCap:	'round',
			lineWidth:	'10',
			size:		'150'
		});
		jQuery('.chart-row2').easyPieChart({
			barColor:	'#FFFFFF',
			trackColor:	'#C84A4A',
			scaleColor:	'#ffffff',
			lineCap:	'round',
			lineWidth:	'5',
			size:		'80'
		});
	} // end stat span loader
	
/*-----------------------------------------------------------------------------------*/
/*	Finished
/*-----------------------------------------------------------------------------------*/
	
});

</script>

<div class="wrap">

    <div id="icon-options-general" class="icon32"><br/></div>
    <h2 class="tc-heading"><?PHP _e('Subscribe To Download for WordPress', 'tcsubdl'); ?> <span id="version">V<?PHP echo FREEBIESUB_VERSION; ?></span> <a href="<?PHP echo FREEBIESUB_LOCATION; ?>/documentation" target="_blank">&raquo; <?PHP _e('View Plugin Documentation', 'tcsubdl'); ?></a></h2>

    <div id="message-bg"></div>
    <div id="success-save"></div>
    
    <div id="tc_framework_wrap">
    
        <div id="content_wrap">
            
            <form id="plugin-options" name="plugin-options" action="/">
            <?PHP settings_fields( 'freebiesub-settings-group' ); ?>
            <input type="hidden" name="action" value="freebiesub_tc_settings_save" />
            <input type="hidden" name="security" value="<?PHP echo wp_create_nonce('freebiesub_settings_secure'); ?>" />
            <!-- Checkbox Fall Backs -->
            <input type="hidden" name="freebiesub-mobile-enabled" id="freebiesub-mobile-enabled" value="false" />
            <input type="hidden" name="freebiesub-jquery-enabled" id="freebiesub-jquery-enabled" value="false" />
            <input type="hidden" name="freebiesub-double-optin" id="freebiesub-double-optin" value="false" />
            <input type="hidden" name="freebiesub-first-name" id="freebiesub-first-name" value="false" />
            <input type="hidden" name="freebiesub-last-name" id="freebiesub-last-name" value="false" />
            <input type="hidden" name="freebiesub-popup-close" id="freebiesub-popup-close" value="false" />
            <input type="hidden" name="freebiesub-popup-close-cookie" id="freebiesub-popup-close-cookie" value="false" />
            <input type="hidden" name="freebiesub-popup-auto-close" id="freebiesub-popup-auto-close" value="false" />
            <input type="hidden" name="freebiesub-advanced-close" id="freebiesub-advanced-close" value="false" />
            <input type="hidden" name="freebiesub-popup-onload" id="freebiesub-popup-onload" value="false" />
    
                <div id="sub_header" class="info">
                    <input type="submit" name="settingsBtn" id="settingsBtn" class="save-options" value="<?PHP _e('Save All Changes', 'freebiesub') ?>" />
                    <div class="sub_header_left"><?PHP _e('Options', 'freebiesub') ?></div>
                </div>
                
                <div id="content">
                
                    <div id="options_content">
                                            
                        <ul class="options_pages">
							<li id="general_options" <?PHP if(!isset($_GET['pagespan'])){ ?>class="active"<?PHP } ?>><a href="#"><span class="fa fa-gear"></span><span class="link_text"><?PHP _e('General Settings', 'freebiesub') ?></span></a></li>
                            <li id="template_options"><a href="#"><span class="fa fa-table"></span><span class="link_text"><?PHP _e('Template Settings', 'freebiesub') ?></span></a></li>
                            <li id="stats_options" <?PHP if(isset($_GET['pagespan'])){ ?>class="active"<?PHP } ?>><a href="#"><span class="fa fa-signal"></span><span class="link_text"><?PHP _e('View Analytics', 'freebiesub') ?></span></a></li>
                        </ul>
                                                
                        <div id="general_options" class="options_page <?PHP if(isset($_GET['pagespan'])){ ?>hide<?PHP } ?>">
                        
                        
                            <div class="option">
                                <h3><?PHP _e('Bit.ly Setup', 'freebiesub') ?></h3>
                                <div class="section">
                                    <div class="element">
                                        <p class="tc-p-first"><?PHP _e('Use Short Links in Emails', 'freebiesub') ?></p>
                                        <select name="freebiesub-bitly-enabled" id="freebiesub-bitly-enabled" class="textfield">
                                            <option value="true" <?PHP if(get_option('freebiesub-bitly-enabled') == 'true'){echo 'selected="selected"';} ?>><?PHP _e('Enabled', 'tcsubdl'); ?></option>
                                            <option value="false" <?PHP if(get_option('freebiesub-bitly-enabled') == 'false'){echo 'selected="selected"';} ?>><?PHP _e('Disabled', 'tcsubdl'); ?></option>
                                        </select>
                                        <p><?PHP _e('API Key', 'freebiesub') ?></p>
                                        <input class="textfield" name="freebiesub-bitly-api-key" type="text" id="freebiesub-bitly-api-key" value="<?php echo get_option('freebiesub-bitly-api-key'); ?>" />
                                        <p><?PHP _e('Domain', 'freebiesub') ?></p>
                                        <input class="textfield" name="freebiesub-bitly-domain" type="text" id="freebiesub-bitly-domain" value="<?php echo get_option('freebiesub-bitly-domain'); ?>" />
                                    </div>
                                    <div class="description"><?PHP _e('Here you can setup link shortening with Bit.ly for the download / verification links. Simple enable, add your API key, and choose which short domain you want to use like a custom domain. By default the plugin will use the domain you have set as the default in your bit.ly account.', 'freebiesub') ?></div>
                                </div>
                            </div>
                                                        
                            
                            <div class="option">
                                <h3><?PHP _e('Fake Email Penalty', 'tcsubdl'); ?></h3>
                                <div class="section">
                                    <div class="element"><input class="textfield" name="freebiesub-penalty" type="text" id="freebiesub-penalty" value="<?php echo get_option('freebiesub-penalty'); ?>" /></div>
                                    <div class="description"><?PHP _e('Enter the number of minuets users should wait if they entered a fake email they could not verify. Default is 3.', 'tcsubdl'); ?></div>
                                </div>
                            </div>
                            
                        </div><!-- END GENERAL OPTIONS -->
                        
                                              
                        <div id="template_options" class="options_page hide">
                        
                        
                            <div class="option">
                                <h3><?PHP _e('Extra Fields', 'freebiesub') ?></h3>
                                <div class="section">
                                    <div class="element">
                                        <p class="tc-p-first"><?PHP _e('Show First Name', 'freebiesub') ?></p>
                                        <select name="freebiesub-show-first-name" id="freebiesub-show-first-name" class="textfield">
                                            <option value="true" <?PHP if(get_option('freebiesub-show-first-name') == 'true'){echo 'selected="selected"';} ?>><?PHP _e('Enabled', 'tcsubdl'); ?></option>
                                            <option value="false" <?PHP if(get_option('freebiesub-show-first-name') == 'false'){echo 'selected="selected"';} ?>><?PHP _e('Disabled', 'tcsubdl'); ?></option>
                                        </select>
                                        <p class="tc-p-first"><?PHP _e('Show Last Name', 'freebiesub') ?></p>
                                        <select name="freebiesub-show-last-name" id="freebiesub-show-last-name" class="textfield">
                                            <option value="true" <?PHP if(get_option('freebiesub-show-last-name') == 'true'){echo 'selected="selected"';} ?>><?PHP _e('Enabled', 'tcsubdl'); ?></option>
                                            <option value="false" <?PHP if(get_option('freebiesub-show-last-name') == 'false'){echo 'selected="selected"';} ?>><?PHP _e('Disabled', 'tcsubdl'); ?></option>
                                        </select>
                                        <p class="tc-p-first"><?PHP _e('Show Phone Field', 'freebiesub') ?></p>
                                        <select name="freebiesub-show-phone" id="freebiesub-show-phone" class="textfield">
                                            <option value="true" <?PHP if(get_option('freebiesub-show-phone') == 'true'){echo 'selected="selected"';} ?>><?PHP _e('Enabled', 'tcsubdl'); ?></option>
                                            <option value="false" <?PHP if(get_option('freebiesub-show-phone') == 'false'){echo 'selected="selected"';} ?>><?PHP _e('Disabled', 'tcsubdl'); ?></option>
                                        </select>                                        
                                    </div>
                                    <div class="description"><?PHP _e('Here you can choose to enable or disable the extra form fields. NOTE: If fields are enabled, they will be required to submit the form.', 'freebiesub') ?></div>
                                </div>
                            </div>

                                              
                            <div class="option">
                                <h3><?PHP _e('Subscribe Heading', 'tcsubdl'); ?></h3>
                                <div class="section">
                                    <div class="element"><input class="textfield" name="freebiesub-subscribe-heading" type="text" id="freebiesub-subscribe-heading" value="<?php echo get_option('freebiesub-subscribe-heading'); ?>" /></div>
                                    <div class="description"><?PHP _e('Enter the heading text that appears if the user has not subscribed yet.', 'tcsubdl'); ?></div>
                                </div>
                            </div>
    
    
                            <div class="option">
                                <h3><?PHP _e('Subscribe Message', 'tcsubdl'); ?></h3>
                                <div class="section">
                                    <div class="element"><textarea class="textfield" name="freebiesub-subscribe-message" cols="" rows="5" id="freebiesub-subscribe-message"><?php echo get_option('freebiesub-subscribe-message'); ?></textarea></div>
                                    <div class="description"><?PHP _e('Enter the message that will appear if the user has not subscribed yet.', 'tcsubdl'); ?></div>
                                </div>
                            </div>
    
    
                            <div class="option">
                                <h3><?PHP _e('Opt-In Message', 'tcsubdl'); ?></h3>
                                <div class="section">
                                    <div class="element"><textarea class="textfield" name="freebiesub-optin-message" cols="" rows="5" id="freebiesub-optin-message"><?php echo get_option('freebiesub-optin-message'); ?></textarea></div>
                                    <div class="description"><?PHP _e('Enter the opt-in / checkbox message in the subscribe area.', 'tcsubdl'); ?></div>
                                </div>
                            </div>
                            

                            <div class="option">
                                <h3><?PHP _e('Thank You Message', 'tcsubdl'); ?></h3>
                                <div class="section">
                                    <div class="element"><textarea class="textfield" name="freebiesub-thankyou-message" cols="" rows="5" id="freebiesub-thankyou-message"><?php echo get_option('freebiesub-thankyou-message'); ?></textarea></div>
                                    <div class="description"><?PHP _e('Enter the message that appears when a valid email submit is made. This is the success message after they hit the subscribe button.', 'tcsubdl'); ?></div>
                                </div>
                            </div>


                            <div class="option">
                                <h3><?PHP _e('Already Subscribed Message', 'tcsubdl'); ?></h3>
                                <div class="section">
                                    <div class="element"><input class="textfield" name="freebiesub-download-message" type="text" id="freebiesub-download-message" value="<?php echo get_option('freebiesub-download-message'); ?>" /></div>
                                    <div class="description"><?PHP _e('Enter the message that appears when the user has been verified and has access to download already.', 'tcsubdl'); ?></div>
                                </div>
                            </div>
                            
                            
                            <div class="option">
                                <h3><?PHP _e('Check Email', 'freebiesub') ?></h3>
                                <div class="section">
                                    <div class="element">
                                        <p class="tc-p-first"><?PHP _e('Check Email Heading', 'freebiesub') ?></p>
                                        <input name="freebiesub-check-email-heading" id="freebiesub-check-email-heading" class="textfield" type="text" value="<?PHP echo get_option('freebiesub-check-email-heading'); ?>" />
                                        <p><?PHP _e('Check Email Message', 'freebiesub') ?></p>
                                        <textarea class="textfield" name="freebiesub-check-email-message" cols="" rows="5" id="freebiesub-check-email-message"><?php echo get_option('freebiesub-check-email-message'); ?></textarea>
                                    </div>
                                    <div class="description"><?PHP _e('Here you can edit the heading and message that appear asking the user to check their email for the verification link.', 'freebiesub') ?></div>
                                </div>
                            </div>


                            <div class="option">
                                <h3><?PHP _e('Email Setup', 'freebiesub') ?></h3>
                                <div class="section">
                                    <div class="element">
                                        <p class="tc-p-first"><?PHP _e('Email From Name', 'freebiesub') ?></p>
                                        <input class="textfield" name="freebiesub-email-name" type="text" id="freebiesub-email-name" value="<?php echo get_option('freebiesub-email-name'); ?>" />
                                        <p><?PHP _e('Email From Address', 'freebiesub') ?></p>
                                        <input class="textfield" name="freebiesub-email-from" type="text" id="freebiesub-email-from" value="<?php echo get_option('freebiesub-email-from'); ?>" />
                                        <p><?PHP _e('Email Subject', 'freebiesub') ?></p>
                                        <input class="textfield" name="freebiesub-email-subject" type="text" id="freebiesub-email-subject" value="<?php echo get_option('freebiesub-email-subject'); ?>" />
                                        <p><?PHP _e('Email Message', 'freebiesub') ?></p>
                                        <textarea class="textfield" name="freebiesub-email-message" cols="" rows="5" id="freebiesub-email-message"><?php echo get_option('freebiesub-email-message'); ?></textarea>
                                    </div>
                                    <div class="description"><?PHP _e('Here you can setup the email that gets sent to verify the user. The from name and email address control who the email will appear to be sent from, and you can also edit the subject line of the email. You can also edit the message that gets sent in the email, the verification / download link will be added at the end of the message.', 'freebiesub') ?></div>
                                </div>
                            </div>
    
                        </div><!-- END TEMPLATE OPTIONS -->
                        
                        
                        <div id="stats_options" class="options_page <?PHP if(!isset($_GET['pagespan'])){ ?>hide<?PHP } ?>">
                        
                            <?PHP
                            
                            // Get Analytics
                            require_once('tcf_stats.php');
                            $freebiesub_stats = new freebiesubStats();
                            $followStats = $freebiesub_stats->subscriber_stats();
                            
                            // Set Page For Links
                            $adminLink = admin_url( 'options-general.php?page=wp-freebie-subscribe/inc/tcf_settings_page.php' );
                            if( isset( $_GET['pagespan'] ) ){
                                $spanClass = $_GET['pagespan'];
                            } else {
                                $spanClass = 'all';
                            }
							
							// Get Total Stats Ratio
							if( $freebiesub_stats->total == '0' ){
								$conversion_percent = '0';
							} else {
								$conversion_percent = ($freebiesub_stats->total/$freebiesub_stats->total_impressions*100);
							}
                            
                            ?>                    
              
              				<div class="tc-chart-block">
                            
                                <div class="tc-chart-wrapper">
                                    <div class="chart" data-percent="100"><span class="percent"><?PHP echo number_format($freebiesub_stats->total, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Subscriptions', 'freebiesub') ?></span>
                                </div>
        
                                <div class="tc-chart-wrapper">
                                    <div class="chart" data-percent="100"><span class="percent"><?PHP echo number_format($freebiesub_stats->total_impressions, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Impressions', 'freebiesub') ?></span>
                                </div>
        
                                <div class="tc-chart-wrapper last">
                                    <div class="chart" data-percent="<?PHP echo round($conversion_percent); ?>"><span class="percent"><?PHP echo round($conversion_percent); ?>%</span></div>
                                    <span class="chart-label"><?PHP _e('Conversion', 'freebiesub') ?></span>
                                </div>
                                
                                <br class="tcclear" />
                            
                            </div>

              				<div class="tc-chart-block alt">
                            
                            	<span class="tc-chart-block-title"><?PHP _e('Subscriber Stats', 'freebiesub') ?></span>
                                
                                <?PHP 
									// Get current month stats percentages
									// Today
									if( $freebiesub_stats->today == '0' ){
										$day_percent = '0';
									} else {
										$day_percent = ($freebiesub_stats->today/$freebiesub_stats->this_year*100);
									}
									// Week
									if( $freebiesub_stats->this_week == '0' ){
										$week_percent = '0';
									} else {
										$week_percent = ($freebiesub_stats->this_week/$freebiesub_stats->this_year*100);
									}
									// Month
									if( $freebiesub_stats->this_month == '0' ){
										$month_percent = '0';
									} else {
										$month_percent = ($freebiesub_stats->this_month/$freebiesub_stats->this_year*100);
									}
								?>
                            
                                <div class="tc-chart-wrapper">
                                    <div class="chart-row2" data-percent="<?PHP echo $day_percent; ?>"><span class="percent"><?PHP echo number_format($freebiesub_stats->today, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Day', 'freebiesub') ?></span>
                                </div>
        
                                <div class="tc-chart-wrapper">
                                    <div class="chart-row2" data-percent="<?PHP echo $week_percent; ?>"><span class="percent"><?PHP echo number_format($freebiesub_stats->this_week, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Week', 'freebiesub') ?></span>
                                </div>
        
                                <div class="tc-chart-wrapper">
                                    <div class="chart-row2" data-percent="<?PHP echo $month_percent; ?>"><span class="percent"><?PHP echo number_format($freebiesub_stats->this_month, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Month', 'freebiesub') ?></span>
                                </div>

                                <div class="tc-chart-wrapper last">
                                    <div class="chart-row2" data-percent="100"><span class="percent"><?PHP echo number_format($freebiesub_stats->this_year, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Year', 'freebiesub') ?></span>
                                </div>
                                
                                <br class="tcclear" />
                                
                                <div class="break-heading"><span><?PHP _e('Previous', 'freebiesub') ?></span></div>
                                
                                <?PHP 
									// Get current month stats percentages
									// Today
									if( $freebiesub_stats->yesterday == '0' ){
										$last_day_percent = '0';
									} else {
										$last_day_percent = ($freebiesub_stats->yesterday/$freebiesub_stats->last_year*100);
									}
									// Week
									if( $freebiesub_stats->this_week == '0' ){
										$last_week_percent = '0';
									} else {
										$last_week_percent = ($freebiesub_stats->last_week/$freebiesub_stats->last_year*100);
									}
									// Month
									if( $freebiesub_stats->this_month == '0' ){
										$last_month_percent = '0';
									} else {
										$last_month_percent = ($freebiesub_stats->last_month/$freebiesub_stats->last_year*100);
									}
								?>

                                <div class="tc-chart-wrapper">
                                    <div class="chart-row2" data-percent="<?PHP echo $last_day_percent; ?>"><span class="percent"><?PHP echo number_format($freebiesub_stats->yesterday, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Day', 'freebiesub') ?></span>
                                </div>
        
                                <div class="tc-chart-wrapper">
                                    <div class="chart-row2" data-percent="<?PHP echo $last_week_percent; ?>"><span class="percent"><?PHP echo number_format($freebiesub_stats->last_week, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Week', 'freebiesub') ?></span>
                                </div>
        
                                <div class="tc-chart-wrapper">
                                    <div class="chart-row2" data-percent="<?PHP echo $last_month_percent; ?>"><span class="percent"><?PHP echo number_format($freebiesub_stats->last_month, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Month', 'freebiesub') ?></span>
                                </div>

                                <div class="tc-chart-wrapper last">
                                    <div class="chart-row2" data-percent="100"><span class="percent"><?PHP echo number_format($freebiesub_stats->last_year, 0); ?></span></div>
                                    <span class="chart-label"><?PHP _e('Year', 'freebiesub') ?></span>
                                </div>
                                
                                <br class="tcclear" />
                                
                                <div id="tcyt-jump"></div>
                            
                            </div>

                      </div>  
                                                                   
                        <br class="clear" />
                        
                </div>
                                
            </form>
            
        </div>
    
    </div>
    
</div>

<?PHP } ?>
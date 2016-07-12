<?PHP

/*-----------------------------------------------------------------------------------*/
/*	Start Form Submit
/*-----------------------------------------------------------------------------------*/

header("Content-type: application/json");

// Boostrap WP
$wp_include = "../wp-load.php";
$i = 0;
while (!file_exists($wp_include) && $i++ < 10) {
  $wp_include = "../$wp_include";
}

// let's load WordPress
require($wp_include);

// Setup WPDB
global $wpdb;
global $tcfreebiesub_options;	

// Vars
$user_email = $wpdb->escape($_POST['email']);
if( isset( $_POST['first_name'] ) ){ $user_fname = $wpdb->escape($_POST['first_name']); }
if( isset( $_POST['last_name'] ) ){ $user_lname = $wpdb->escape($_POST['last_name']); }
if( isset( $_POST['phone'] ) ){ $user_phone = $wpdb->escape($_POST['phone']); }
$agree = $wpdb->escape($_POST['agree']);
$download = $wpdb->escape(base64_decode($_POST['id']));
$ip = $wpdb->escape($_SERVER['REMOTE_ADDR']);
$page_id = $wpdb->escape($_POST['page_id']);
$page_url = $wpdb->escape($_POST['page_url']);
$time = date( 'Y-m-d H:i:s' );
$return_error = 'false';

// Check for empty fields

$empties = 'false';
if( $tcfreebiesub_options['show-first-name'] == 'true' && $user_fname == '' ){ $empties = 'true'; }
if( $tcfreebiesub_options['show-last-name'] == 'true' && $user_lname == '' ){ $empties = 'true'; }
if( $tcfreebiesub_options['show-phone'] == 'true' && $user_phone == '' ){ $empties = 'true'; }

// Display error for empty fields
if( $empties == 'false' ){

	// Check for valid email
	if( is_email($user_email) ){
	
		if( $agree == '1' ){
	
			// Get Options
			$freebiesub_bitly_enabled = get_option('freebiesub-bitly-enabled');
			$freebiesub_bitly_api_key = get_option('freebiesub-bitly-api-key');
			$freebiesub_bitly_domain = get_option('freebiesub-bitly-domain');
			$freebiesub_penalty = get_option('freebiesub-penalty');
			$freebiesub_subscribe_heading = get_option('freebiesub-subscribe-heading');
			$freebiesub_subscribe_message = get_option('freebiesub-subscribe-message');
			$freebiesub_optin_message = get_option('freebiesub-optin-message');
			$freebiesub_download_message = get_option('freebiesub-download-message');
			$freebiesub_email_message = get_option('freebiesub-email-message');
			$freebiesub_email_subject = get_option('freebiesub-email-subject');
			$freebiesub_email_name = get_option('freebiesub-email-name');
			$freebiesub_email_from = get_option('freebiesub-email-from');
			$freebiesub_thankyou_message = get_option('freebiesub-thankyou-message');
					
			// Check if user already in DB
			$check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".$wpdb->prefix."freebie_subscriber WHERE email = %s", $user_email));
					
			// Only insert if not already in db
			if($check == '0'){
				
				// Set Unused Fields to N/A
				if( $tcfreebiesub_options['show-first-name'] == 'false' ){ $user_fname = "N/A"; }
				if( $tcfreebiesub_options['show-last-name'] == 'false' ){ $user_lname = "N/A"; }
				if( $tcfreebiesub_options['show-phone'] == 'false' ){ $user_phone = "N/A"; }
				
				$tablename = $wpdb->prefix . "freebie_subscriber";
				$wpdb->insert( 
					$tablename, 
					array( 
						'email' => $user_email, 
						'first_name' => $user_fname, 
						'last_name' => $user_lname, 
						'phone' => $user_phone, 
						'ip' => $ip, 
						'time' => $time, 
						'post_id' => $page_id, 
						'page_url' => $page_url,
						'download_url' => $download,
						'status' => 'verify',
						'event_type' => 'subscribe' 
					), 
					array( 
						'%s', 
						'%s', 
						'%s', 
						'%s', 
						'%s', 
						'%s', 
						'%s', 
						'%s', 
						'%s', 
						'%s', 
						'%s'
					) 
				);
				$uid = $wpdb->insert_id;
				
			} else { // otherwise return row already in table
			
				// Update Download URL
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."freebie_subscriber SET download_url=%s WHERE email=%s", $download, $user_email));
			
				//Get ID for URL
				$user_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."freebie_subscriber WHERE email = %s", $user_email));
				$uid = $user_row->id;		
			
			} // end if email in list
			
			// Setup URL
			$baseUrl = FREEBIESUB_LOCATION . '/inc/download.php?1='.$uid;
			if($freebiesub_bitly_enabled == 'true' && $freebiesub_bitly_api_key != ''){
				$sendUrl = freebiesub_bitly($baseUrl, $freebiesub_bitly_api_key, $freebiesub_bitly_domain);
			} else {
				$sendUrl = $baseUrl;
			}
			
			if( $sendUrl == '' || !isset($sendUrl) ){
				$sendUrl = $baseUrl;
			}
							
			// Sendmail
			$headers = 'From: '.$freebiesub_email_name.' <'.$freebiesub_email_from.'>' . "\r\n";
			$message = nl2br($freebiesub_email_message).' - '.$sendUrl;
			mail($user_email, $freebiesub_email_subject, $message, $headers);
			//wp_mail($user_email, $freebiesub_email_subject, $message, $headers);
			
			// Set Waiting Cookie With Penalty Time
			$wait = time() + ( $freebiesub_penalty * 60 );
			setcookie("wpfsubdl", 'waiting', $wait, "/");	
							
			// Return Success
			$return_msg = '<span class="freebiesub-success">'.$freebiesub_thankyou_message.'</span>';
			
		} else {
			
			// Return no opt-in error
			$return_msg = '<span class="freebiesub-error">'.__('You must check that you agree to receive our newsletter before downloading.', 'tcsubdl').'</span>';
			$return_error = 'true';
			
		}
	
	} else {
		
		// return bad email error
		$return_msg = '<span class="freebiesub-error">'.__('Whoa There! That email address is not valid.', 'tcsubdl').'</span>';
		$return_error = 'true';
		
	} // end if email valid
	
} else { // Form has empties!

	// return bad email error
	$return_msg = '<span class="freebiesub-error">'.__('Whoa There! Please make sure you have filled out all form fields.', 'tcsubdl').'</span>';
	$return_error = 'true';

}

/*-----------------------------------------------------------------------------------*/
/*	Return to Frontend
/*-----------------------------------------------------------------------------------*/

$return = array('message' => $return_msg, 'error' => $return_error);
echo json_encode($return);

/*-----------------------------------------------------------------------------------*/
/*	Make URL Short
/*-----------------------------------------------------------------------------------*/

function freebiesub_bitly($long_url, $access_token, $domain = NULL){
  $url = 'https://api-ssl.bitly.com/v3/shorten?access_token='.$access_token.'&longUrl='.urlencode($long_url).'&domain='.$domain;
  try {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $output = json_decode(curl_exec($ch));
  } catch (Exception $e) {
  }
  if(isset($output)){return $output->data->url;}
}
?>
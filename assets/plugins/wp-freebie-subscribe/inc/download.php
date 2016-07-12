<?PHP

// Boostrap WP
$wp_include = "../wp-load.php";
$i = 0;
while (!file_exists($wp_include) && $i++ < 10) {
  $wp_include = "../$wp_include";
}

// let's load WordPress
require($wp_include);

// Start DB
global $wpdb;
$wpdb->show_errors();

// Get entry ID from URL
$download = $wpdb->escape( $_GET['1'] );

// Quick Local Clean of IP
if($ip == '::1'){$ip = '127.0.0.1';}

// Check if user already in DB
$check = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."freebie_subscriber WHERE id = %d", $download));

// Only insert if not already in db
if($check == ''){
	
	'Error - This is not a valid token!';
	
} else {
	
	// Get Download URL
	$dl_url = $check->download_url;
	
	// Update Table with valid ID
	$time = date( 'Y-m-d H:i:s' );
	$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."freebie_subscriber SET status='confirmed', time='%s' WHERE id=%d", $time, $download));

	// Create cookie for user          
	setcookie("wpfsubdl", 'true', time()+31536000, "/");
	
	// redirect to download
	header("Location: $dl_url");
			
}

?>
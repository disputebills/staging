<?PHP

require_once("../../../../wp-config.php");
global $wpdb;
$rows = array();

$objects = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."freebie_subscriber",OBJECT_K);

foreach ($objects as $lead) {
	$columns = array(); // used to clear the array on each pass
	$columns['email'] = $lead->email;
	$columns['first_name'] = $lead->first_name;
	$columns['last_name'] = $lead->last_name;
	$columns['phone'] = $lead->phone;
	$rows[] = join(',',$columns); // fields are doing with a comma between them.
}

$file = join("\n",$rows); // rows are joined with a line-break between them.

if($_GET['type'] == 'csv'){
	
	$end = 'csv';
	
} elseif($_GET['type'] == 'text'){
	
	$end = 'txt';
	
} else {
	
	$end = 'csv';
	
}

header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename="export-file.'.$end.'"');
echo $file;

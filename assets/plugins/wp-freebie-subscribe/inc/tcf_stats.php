<?PHP

/*-----------------------------------------------------------------------------------*/
/*	Analytics Class
	- This file is used to generate the numbers / date reporting used in the
	analytics page of the plugin. These strings and queries are very sensitive
	and should not be modified.
/*-----------------------------------------------------------------------------------*/

class freebiesubStats {
	
	function subscriber_stats(){

		global $wpdb;
		$tc_table = $wpdb->prefix."freebie_subscriber";
		
		// Get Total
		// Subs
		$total = $wpdb->get_results("SELECT * FROM $tc_table WHERE `event_type` = 'subscribe' AND `status` = 'confirmed'");
		$this->total = $wpdb->num_rows;
		// Imps.
		$total_imp = $wpdb->get_results("SELECT * FROM $tc_table WHERE `event_type` = 'impression'");
		$this->total_impressions = $wpdb->num_rows;
	
		// Get 'This 'Week'
		$sunday = date(('Y-m-d H:i:s'), strtotime('last sunday'));
		$next_sunday = date(('Y-m-d H:i:s'), strtotime('next sunday'));
		$week = $wpdb->get_results("SELECT * FROM $tc_table WHERE `time` >= '$sunday' AND `time` <= '$next_sunday' AND`event_type` = 'subscribe' AND `status` = 'confirmed'");
		$this->this_week = $wpdb->num_rows;

		// Get 'Last 'Week'
		$start = date(('Y-m-d H:i:s'), strtotime('-2 week'));
		$end = date(('Y-m-d H:i:s'), strtotime('-1 week'));
		$last_week = $wpdb->get_results("SELECT * FROM $tc_table WHERE `time` >= '$start' AND `time` <= '$end' AND `event_type` = 'subscribe' AND `status` = 'confirmed'");
		$this->last_week = $wpdb->num_rows;

		// Get 'Last 'Month'
		$month = $wpdb->get_results("SELECT * FROM $tc_table WHERE YEAR(time) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND MONTH(time) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");
		$this->last_month = $wpdb->num_rows;

		// Get 'This 'Month'
		$month_timespan = date('Y-m-').'01 00:00:00';
		$month_end = date("Y-m-t 23:59:59", strtotime($month_timespan));
		$month = $wpdb->get_results("SELECT * FROM $tc_table WHERE `time` >= '$month_timespan' AND `time` <= '$month_end' AND `event_type` = 'subscribe' AND `status` = 'confirmed'");
		$this->this_month = $wpdb->num_rows;
	
		// Get 'Last 'Year'
		$this_year_num = idate('Y');
		$last_year_num = ($this_year_num - 1);
		$last_year = $wpdb->get_results("SELECT * FROM $tc_table WHERE `time` >= '$last_year_num-01-01 00:00:00' AND `time` <= '$this_year_num-01-01 00:00:00' AND `event_type` = 'subscribe' AND `status` = 'confirmed'");
		$this->last_year = $wpdb->num_rows;

		// Get 'This 'Year'
		$year_timespan = date('Y-').'-01-01 00:00:00';
		$year = $wpdb->get_results("SELECT * FROM $tc_table WHERE `time` >= '$year_timespan' AND `time` <= '".date('Y-m-d').' 23:59:59'."' AND `event_type` = 'subscribe' AND `status` = 'confirmed'");
		$this->this_year = $wpdb->num_rows;
	
		// Get 'Today'
		$today_timespan = date('Y-m-d').' 00:00:00';
		$today = $wpdb->get_results("SELECT * FROM $tc_table WHERE `time` >= '$today_timespan' AND `time` <= '".date('Y-m-d')." 23:59:59' AND `event_type` = 'subscribe' AND `status` = 'confirmed'");
		$this->today = $wpdb->num_rows;
	
		// Get 'Yesterday'
		$yesterday_timespan = date('Y-m-d', strtotime('-1 day'));
		$yesterday = $wpdb->get_results("SELECT * FROM $tc_table WHERE `time` >= '$yesterday_timespan 00:00:00' AND `time` < '$yesterday_timespan 23:59:59' AND `event_type` = 'subscribe' AND `status` = 'confirmed'");
		$this->yesterday = $wpdb->num_rows;
														
	}
	
}

?>
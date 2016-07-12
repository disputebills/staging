<?php

class live_edit {
	
	var $settings;
	
	
	/*
	*  __construct
	*
	*  description
	*
	*  @type	function
	*  @date	3/04/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->settings = array(
			
			// basic
			'name'			=> __('Live Edit', 'live-edit'),
			'version'		=> '2.1.4',
						
			// urls
			'basename'		=> plugin_basename( __FILE__ ),
			'path'			=> plugin_dir_path( __FILE__ ),
			'dir'			=> plugin_dir_url( __FILE__ ),
			
			// options
			'panel_width'	=> get_option('live_edit_panel_width', 600)
		);
		
		
		// set text domain
		load_plugin_textdomain('live-edit', false, basename(dirname(__FILE__)).'/lang' );
		
		
		// actions
		add_action('init', array($this,'_init'));
	}
	
	
	/*
	*  wp_init
	*
	*  description
	*
	*  @type	function
	*  @date	3/04/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function _init() {
		
		// must be logged in
		if( !is_user_logged_in() ) {
			
			return;
			
		}
		
		
		// scripst
		wp_register_script( 'live-edit-admin', $this->settings['dir'] . '/js/functions.admin.js', false, $this->settings['version'] );
		wp_register_script( 'live-edit-front', $this->settings['dir'] . '/js/functions.front.js', false, $this->settings['version'] );
		wp_register_style( 'live-edit-admin', $this->settings['dir'] . '/css/style.admin.css', false, $this->settings['version'] );
		wp_register_style( 'live-edit-front', $this->settings['dir'] . '/css/style.front.css', false, $this->settings['version'] );
		
		
		// actions (admin)
		add_action('admin_head', array($this,'admin_head'));
		add_action('admin_menu', array($this,'admin_menu'));
		
		
		// actions (front)
		add_action('wp_enqueue_scripts', array($this,'wp_enqueue_scripts'));
		//add_action('wp_head', array($this,'wp_head'));
		add_action('wp_footer', array($this,'wp_footer'));
		
		
		// actions (ajax)
		add_action('wp_ajax_live_edit_update_width', array($this, 'ajax_update_width'));
	
		
	}
		
	
	/*
	*  admin_head
	*
	*  description
	*
	*  @type	function
	*  @date	3/04/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function admin_head()
	{
		echo '<style type="text/css">#menu-settings a[href="options-general.php?page=live-edit-panel"] { display:none; }</style>';
	}
	
	
	/*
	*  admin_menu
	*
	*  description
	*
	*  @type	function
	*  @date	3/04/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function admin_menu() {
		
		$slug = add_options_page(__("Live Edit Panel",'live-edit'), __("Live Edit Panel",'live-edit'), 'edit_posts', 'live-edit-panel', array($this, 'panel_view'));
		
		// actions
		add_action("load-{$slug}", array($this,'panel_load'));
				
	}
	
	
	/*
	*  admin_load
	*
	*  description
	*
	*  @type	function
	*  @date	3/04/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function panel_load() {
		
		// save
		if( isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'live-edit') )
		{
			$this->save_post();
		}
		
		
		do_action('acf/input/admin_enqueue_scripts');
		
		
		// enqueue scripts
		wp_enqueue_script('live-edit-admin');
		wp_enqueue_style('live-edit-admin');
		
		
		add_action('admin_head', array($this, 'panel_admin_head'));
		
	}
	
	
	/*
	*  save_post
	*
	*  {description}
	*
	*  @since: 4.0.3
	*  @created: 16/05/13
	*/
	
	function save_post()
	{
		// validate
		if( !isset($_POST['post_id']) )
		{
			return;
		}
		
		
		// vars
		$post_id = $_POST['post_id'];
		$post_data = array();
		
		
		foreach( array('post_title', 'post_content', 'post_excerpt') as $v )
		{
			if( isset($_POST['fields'][ $v ]) )
			{
				$post_data[ $v ] = $_POST['fields'][ $v ];
				
				unset( $_POST['fields'][ $v ] );	
			}
		}
		
		
		// update post
		if( !empty($post_data) )
		{
			$post_data['ID'] = $post_id;
			wp_update_post( $post_data );
		}
		
		
		// save custom fields
		do_action('acf/save_post', $post_id);
		
		
		// set var
		$this->data['save_post'] = true;
		
	}
	
	
	/*
	*  page_admin_head
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 17/03/13
	*/
	
	function panel_admin_head() {	
	
		do_action('acf/input/admin_head');
		
	}
	
	
	/*
	*  wp_enqueue_scripts
	*
	*  @description:
	*  @since 1.0.0
	*  @created: 25/07/12
	*/
	
	function wp_enqueue_scripts() {
		
		wp_enqueue_script(array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-widget',
			'jquery-ui-mouse',
			'jquery-ui-resizable',
			'live-edit-front'
		));
		
		wp_enqueue_style('live-edit-front');
		
	}
	
	
	/*
	*  wp_footer
	*
	*  description
	*
	*  @type	function
	*  @date	3/04/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function wp_footer() {
		
		// vars
		$o = array(
			'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
			'panel_url'		=> admin_url( 'options-general.php?page=live-edit-panel' ),
			'panel_width'	=> $this->settings['panel_width'],
			'nonce'			=> wp_create_nonce('live_edit_nonce')
		);
		
		
		?>
		<script type="text/javascript">
		(function($) {
		
			live_edit.o = <?php echo json_encode( $o ); ?>;
		
		})(jQuery);	
		</script>
		<div id="live-edit-panel">
			<div id="live-edit-iframe-cover"></div>
			<iframe id="live-edit-iframe"></iframe>
		</div>
		<?php
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	ajax_update_width
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function ajax_update_width() {
		
		// vars
		$options = wp_parse_args($_POST, array(
			'width' 	=> 600,
			'nonce'		=> '',
		));
				
		
		// validate
		if( ! wp_verify_nonce($options['nonce'], 'live_edit_nonce') ) {
		
			wp_send_json_error();
			
		}
		
		
		// update option
		update_option( 'live_edit_panel_width', $options['width'] );
		
		
		// success
		wp_send_json_success();
		
	}
	
	
	/*
	*  panel_view
	*
	*  description
	*
	*  @type	function
	*  @date	3/04/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function panel_view() {
		
		global $acf;
		
		// vars
		$options = wp_parse_args($_GET, array(
			'fields'	=> '',
			'post_id'	=> 0,
			'updated'	=> 0,
			'nonce'		=> '',
		));
		
		// validate
		if( !$options['post_id'] )
		{
			wp_die( "Error: No post_id parameter found" );
		}
		
		if( !$options['fields'] )
		{
			wp_die( "Error: No fields parameter found" );
		}
		
		if( !wp_verify_nonce($options['nonce'], 'live_edit_nonce') ) {
		
			wp_die( "Error: Access Denied" );
			
		}
		
		
		// loop through and load all fields as objects
		$fields = explode(',',$options['fields']);

		if( $fields )
		{
			foreach( $fields as $k => $field_name )
			{
				$field = null;
				
				
				if( $field_name == "post_title" ) // post_title
				{
					$field = array(
						'key' => 'post_title',
						'label' => 'Post Title',
						'name' => 'post_title',
						'value' => get_post_field('post_title', $options['post_id']),
						'type'	=>	'text',
						'required' => 1
					);
				}
				elseif( $field_name == "post_content" ) // post_content
				{
					$field = array(
						'key' => 'post_content',
						'label' => 'Post Content',
						'name' => 'post_content',
						'value' => get_post_field('post_content', $options['post_id']),
						'type'	=>	'wysiwyg',
					);
				}
				elseif( $field_name == "post_excerpt" ) // post_excerpt
				{
					$field = array(
						'key' => 'post_excerpt',
						'label' => 'Post Excerpt',
						'name' => 'post_excerpt',
						'value' => get_post_field('post_excerpt', $options['post_id']),
						'type'	=>	'textarea',
					);
				}
				else // acf field
				{
					$field = get_field_object( $field_name, $options['post_id'], array( 'load_value' => false, 'format_value' => false ));
				}
				
				
				// load defualts (for post_title, etc)
				$field = apply_filters('acf/load_field_defaults', $field);
				
				$fields[ $k ] = $field;
			}
		}
	
		// render fields
?>

	<?php if( isset($this->data['save_post']) ): ?>
		<div class="updated" id="message"><p><?php _e("Post updated", 'live-edit'); ?></p></div>
	<?php endif; ?>
	
	<form id="post" method="post" name="post" class="acf-form">
		
		<?php echo '<div class="form-title"><h2>' . __('Live Edit', 'live-edit') . '</h2><ul class="hl"><li><a href="#" class="button button-close">' . __('Close Panel', 'live-edit') . '</a></li><li><span class="spinner"></span><input type="submit" value="' . __('Update', 'live-edit') . '" class="button button-primary"></li></ul></div>'; ?>
		
	
		<div style="display:none;">
			<input type="hidden" name="post_id" value="<?php echo $options['post_id']; ?>" />
			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'live-edit' ); ?>" />
		</div>
		
		<div class="metabox-holder" id="poststuff">
				
			<!-- Main -->
			<div id="post-body">
			<div id="post-body-content">
				<div class="acf_postbox acf-form-fields">
				
					<?php
					
					do_action('acf/create_fields', $fields, $options['post_id']);
					
					?>
					
					<?php echo '<p class="credits">' . __('Powered by', 'live-edit') . ' <a href="http://wordpress.org/plugins/live-edit/" target="_blank">' . __('Live Edit', 'live-edit') . '</a></p>'; ?>
					
				</div>
			</div>
			</div>
		
		</div>
	</form>
	
	<?php if( isset($this->data['save_post']) ): ?>
		<script type="text/javascript">
		(function($){
		
		// validate parent
		if( !parent || !parent.live_edit ) {
		
			return;
			
		}
		
		// update the div
		parent.live_edit.sync();
		
		})(jQuery);
		</script>
	<?php endif; ?>

<?php

	}
	
}

new live_edit();


/*
*   live_edit 
*
*  description
*
*  @type	function
*  @date	3/04/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function live_edit( $fields = false, $post_id = false ) {
	
	// validate fields
	if( !$fields ) {
	
		return false;
		
	}
	
	
	// filter post_id
	$post_id = acf_filter_post_id( $post_id );
	
	
	// turn array into string
	if( is_array($fields) )
	{
		$fields = implode(',', $fields);
	}
	
	
	// remove any white spaces from $fields
	$fields = str_replace(' ', '', $fields);

	
	// build atts
	echo 'data-live-edit-id="' . $post_id . '-' . str_replace(',', '-', $fields) . '" data-live-edit-fields="' . $fields . '" data-live-edit-post_id="' . $post_id . '"';
	
	
}

?>
<?php

class acf_field_row extends acf_field
{

	var $settings;


	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/

	function __construct()
	{
		// vars
		$this->name = 'row';
		$this->label = __("Row",'acf-row');
		$this->category = __("Layout",'acf-row');
		$this->defaults = array(
			'col_num'  =>	2,
			'row_type' =>	'row_open',
		);

		// do not delete!
    	parent::__construct();


    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

	}

	function input_admin_enqueue_scripts()
	{
		// register acf scripts
		wp_register_style( 'acf-input-row', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] );
		wp_register_script( 'acf-input-row', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version'] );


		// scripts
		wp_enqueue_script(array(
			'acf-input-row',
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-row',
		));

	}


	function create_options( $field )
		{
			// defaults?

			$field = array_merge($this->defaults, $field);


			// key is needed in the field names to correctly save the data
			$key = $field['name'];


			// Create Field Options HTML
			?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Type",'acf-row'); ?></label>
			</td>
			<td>
				<?php

				do_action('acf/create_field', array(
					'type'		=>	'radio',
					'name'		=>	'fields['.$key.'][row_type]',
					'value'		=>	$field['row_type'],
					'layout'	=>	'horizontal',
					'choices'	=>	array(
						'row_open' => __('Row Open','acf-row'),
						'row_close' => __('Row Close','acf-row'),
					)
				));

				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Column",'acf-row'); ?></label>
			</td>
			<td>
				<?php

				do_action('acf/create_field', array(
					'type'		=>	'radio',
					'name'		=>	'fields['.$key.'][col_num]',
					'value'		=>	$field['col_num'],
					'layout'	=>	'horizontal',
					'choices'	=>	array(
						'2' => __('2'),
						'3' => __('3'),
						'4' => __('4'),
					)
				));

				?>
			</td>
		</tr>
			<?php

		}


		function create_field( $field )
		{

			echo '<div class="acf-row" data-row_type="'.$field['row_type'].'" data-col_num="'.$field['col_num'].'">' . $field['label'] . '</div>';

		}


}

new acf_field_row();

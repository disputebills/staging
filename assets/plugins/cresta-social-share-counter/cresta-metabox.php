<?php
/**
 * Cresta Social Share Counter Meta Box
 */
 
function cresta_add_meta_box() {
	$thePostType = get_option( 'cresta_social_shares_selected_page' );
	$screens = explode(",",$thePostType);

	foreach ( $screens as $screen ) {

		add_meta_box(
			'cresta_sectionid',
			esc_html__( 'Cresta Social Share Counter', 'cresta-social-share-counter' ),
			'cresta_meta_box_callback',
			$screen,
			'side',
			'low'
		);
	}
}
add_action( 'add_meta_boxes', 'cresta_add_meta_box' );

function cresta_meta_box_callback( $post ) {
	wp_nonce_field( 'cresta_meta_box', 'cresta_meta_box_nonce' );
	$crestaValue = get_post_meta( $post->ID, '_get_cresta_plugin', true );
	?>
	<label for="cresta_new_field">
        <input type="checkbox" name="cresta_new_field" id="cresta_new_field" value="1" <?php checked( $crestaValue, '1' ); ?> /><?php esc_html_e( 'Hide icons in this page?', 'cresta-social-share-counter' )?>
    </label>
	<?php
}

function cresta_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['cresta_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['cresta_meta_box_nonce'], 'cresta_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	if ( isset( $_POST['cresta_new_field'] ) ) {
		update_post_meta( $post_id, '_get_cresta_plugin', $_POST['cresta_new_field'] );
	} else {
		delete_post_meta( $post_id, '_get_cresta_plugin' );
	}
	
}
add_action( 'save_post', 'cresta_save_meta_box_data' );
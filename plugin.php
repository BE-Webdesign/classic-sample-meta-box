<?php
/*
 * Plugin Name: A classic meta block.
 * Version: 0.1.0
 * Author: Edwin Cromley
 * Author URI: https://edwincromley.com
 * License: GPL3+
 *
 * Description: Random meta box.
 */

add_action( 'add_meta_boxes', 'sample_add_metabox' );
add_action( 'save_post', 'sample_save_metabox', 10, 2 );

/**
 * Adds the meta box.
 */
function sample_add_metabox() {
	add_meta_box(
		'post-notes',
		__( 'Post Notes', 'textdomain' ),
		'sample_render_metabox',
		'post',
		'normal',
		'default'
	);
}

/**
 * Renders the meta box.
 */
function sample_render_metabox( $post ) {
	// Add nonce for security and authentication.
	wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );

	$notes = get_post_meta( $post->ID, 'notes', true );

	?>
	<textarea style="width: 100%; height: 200px;" name="notes"><?php echo esc_html( $notes ); ?></textarea>
	<?php
}

/**
 * Handles saving the meta box.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return null
 */
function sample_save_metabox( $post_id, $post ) {
	// Add nonce for security and authentication.
	$nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
	$nonce_action = 'custom_nonce_action';

	// Check if nonce is set.
	if ( ! isset( $nonce_name ) ) {
		return;
	}

	// Check if nonce is valid.
	if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
		return;
	}

	// Check if user has permissions to save data.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Check if not an autosave.
	if ( wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// Check if not a revision.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['notes'] ) ) {
		return;
	}

	update_post_meta( $post_id, 'notes', wp_unslash( sanitize_text_field( $_POST['notes'] ) ) );
}

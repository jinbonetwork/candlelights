<?php
function candlelights_fix_permalink( $post_id ){
	global $wpdb;
	$wpdb->update(
		$wpdb->posts,
		array( 'post_name' => $post_id ),
		array( 'ID' => $post_id ),
		array( '%s' ),
		array( '%s' )
	);
}
add_action( 'save_post', 'candlelights_fix_permalink' );

<?
if( !defined( 'SC_STARTER' ) ) die( 'Do not excute this file directly.' );

function log_to_meta( $postID, $key, $log ) {
	$old = get_post_meta( $postID, $key );
	$old = is_array( $old ) ? $old[0] : $old;
	$new = $old . "\n" . $log;
	update_post_meta( $postID, $key, $new );
}

?>

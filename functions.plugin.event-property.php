<?php
function candlelights_sync_event_properties( $post_id ){
	// excute once
	if( defined( 'CANDLELIGHTS_SAVE_POST' ) ){
		return;
	} else {
		define( 'CANDLELIGHTS_SAVE_POST', true );
	}

	// check type
	if( $_POST[post_type] != 'ai1ec_event' ){
		return;
	}

	// check permission
	if( !current_user_can( EDITOR_CAPABILITY ) ){
		return;
	}

	// skip in autosave
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	}

	// validate nonce
	if( isset( $_POST[event_property_nonce] ) && !wp_verify_nonce( $_POST[event_property_nonce], 'event_property' ) ){
		return;
	}

	// do job
	global $wpdb;
	$wpdb->update( $wpdb->posts, array( 'menu_order' => $_POST[menu_order] ), array( 'ID' => $post_id ), array( '%d' ), array( '%d' ) );
	$wpdb->update( $wpdb->events, array( 'event_priority' => $_POST[menu_order], 'event_parent' => $_POST[post_parent], ), array( 'post_id' => $post_id ), array( '%d', '%d', ), array( '%d' ) );

	_log( "#{$post_id} updated.". PHP_EOL );
}
add_action( 'save_post', 'candlelights_sync_event_properties' );

function candlelights_sync_event_status( $new, $old, $post ){
	//if( $new == $old ) return;

	global $wpdb;
	$wpdb->update( $wpdb->events, array( 'event_status' => $new ), array( 'post_id' => $post->ID ), array( '%s' ), array( '%d' ) );

	_log( "#{$post->ID} - {$old} to {$new}" . PHP_EOL ); 
}
add_action( 'transition_post_status', 'candlelights_sync_event_status', 10, 3 );

function candlelights_print_meta_box( $post ){
	wp_nonce_field( 'event_property', 'event_property_nonce' );
	$fields = array(
		'post_parent' => __( 'Parent event', 'candlelights' ),
		'menu_order' => __( 'Event priority', 'candlelights' ),
	);
	foreach( $fields as $field => $label ){
		$value = esc_attr( $post->$field );
		//$disabled = ( ( $post->post_parent != 0 && $field == 'menu_order' ) || !current_user_can( EDITOR_CAPABILITY ) ) ? ' disabled="disabled"' : '';
		echo <<<EOT
			<p>
				<label for="$field">$label</label>
				<input type="text" id="$field" name="$field" value="$value" size="25" $disabled/>
			</p>
EOT;
	}
}
function candlelights_add_meta_box(){
	if( !current_user_can( EDITOR_CAPABILITY ) ) return;
	add_meta_box( 'event_property', __( 'Event property', 'candlelights' ), 'candlelights_print_meta_box', 'ai1ec_event', 'side', 'core', null ); // $id, $title, $callback, $post_type, $context, $priority, $callback_args
}
add_action( 'add_meta_boxes', 'candlelights_add_meta_box' );

?>

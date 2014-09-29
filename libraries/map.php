<?php
if( !defined( 'ABSPATH' ) ) die( 'Do not excute this file directly.' );

// GET MAP INITIAL LOCATION
function get_average_geocode( $geocodes ) {
	if( !$geocodes ) return false;

	foreach( $geocodes as $geocode ) {
		list( $lat, $lng ) = explode( ',', $geocode );
		$lats[] = $lat;
		$lngs[] = $lng;
	}
	$average[] = array_sum( $lats ) / count( $lats );
	$average[] = array_sum( $lngs ) / count( $lngs );

	return implode( ',', $average );
}


// GET GEOCODE BY ADDRESS
function get_geocode_query( $address ) {
	$q = trim( $address );
	$q = rawurlencode( $q );
//	$q = str_replace(' ','%20', $q );
	$attr = array(
		'apikey' => DAUM_LOCAL_APIKEY,
		'q' => $q,
		'output' => 'json',
		'callback' => '',
	);

	$query_attr = array();
	foreach ( $attr as $key => $value ) {
		$query_attr[] = "{$key}={$value}";
	}
	$query = DAUM_LOCAL_ENDPOINT . '/addr2coord?' . implode( '&', $query_attr );
	return $query;
}
function get_geocode( $address, $mode = 'single' ) {
	if( !$address ) return false;
	
	$query = get_geocode_query( $address );
	$results = file_get_contents( $query );
	$json = json_decode( $results );

	if( !$json->channel->item ) return false;

	switch( $mode ) {
		case 'single' :
			foreach( $json->channel->item as $item ) {
				$output = "{$item->lat},{$item->lng}";
				break;
			}
		break;
		case 'object' :
			$output = $json;
			break;
		case 'json' :
		default :
			$output = json_encode( $json );
			break;
	}

	return $output;
}

// GET ADDRESS BY GEOCODE
function get_address_query( $geocode ) {
	list( $lat, $lng ) = explode( ',', $geocode );
	$attr = array(
		'apikey' => DAUM_LOCAL_APIKEY,
		'longitude' => trim( $lng ),
		'latitude' => trim( $lat ),
		'inputCoordSystem' => '', // WGS84(기본값), CONGNAMUL, WCONGNAMUL, WTM
		'format' => 'simple', // optional
		'output' => 'json',
		'callback' => '',
	);
		
	$q = array();
	foreach ( $attr as $key => $value ) {
		$q[] = "{$key}={$value}";
	}
	$query = DAUM_LOCAL_ENDPOINT . '/coord2addr?' . implode( '&', $q );
	return $query;
}
function get_address( $geocode, $mode = 'single' ) {
	if( !$geocode ) return false;
	$query = get_address_query( $geocode );
	$result = file_get_contents( $query );
	switch( $mode ) {
		case 'single' :
			$json = (array) json_decode( $result );
			ksort( $json );
			unset( $json['code'] );
			$address = implode( ' ', $json );
			$output = $address;
			break;
		case 'object' :	
			$output = $result;
			break;
		case 'json' :	
		default :
			$output = json_encode( $result );
			break;
	}
	return $output;
}

function update_location( $location, $geocode = '' ) {
	global $wpdb;
	$format = array( 'location', 'geocode' );
	print_log( "Processing location: {$location}" );

	$old_query = "select * from $wpdb->locations where location = '$location'"; 
	$old = $wpdb->get_row( $old_query );

	if( $geocode ) {
		print_log( "CASE: manual update via wordpress" );
		if( $old ) {
			$method = 'update';
			print_log( "{$location}: already exists, need to update..." );
		} else {
			$method = 'insert';
			print_log( "{$location}: Fresh! need to insert..." );
		}
		$new = $geocode;
	} else {
		print_log( "CASE: API call via update.php" );
		if ( $old ) {
			print_log( "{$location}: already exists, skip..." );
			return; // exit without saving
		} else {
			$method = 'insert';
			print_log( "{$location}: Fresh! need to insert..." );
			$new = get_geocode( $location );
			if( !$new ) {
				print_log( "Empty geocode: {$location} -- please update manually" );
				return;
			}
		}
	}
	if( $new ) {
		$geocode = $new;	
		$geocode = is_array( $geocode ) ? implode( '', $geocode ) : $geocode;
		$geocode_query = get_geocode_query( $location );
		print_log( "New geocode: {$location} -- {$geocode}", $geocode_query );

		$posts = $wpdb->get_results( "select ID from $wpdb->posts where post_excerpt = '$location'" );
		if( $posts ) {
			foreach( $posts as $post ) {
				$geocode_old = get_post_meta( $post->ID, 'geocode' );
				$geocode_old = is_array( $geocode_old ) ? implode( '', $geocode_old ) : $geocode_old;
				if( !$geocode_old || $geocode_old != $geocode ) {
					update_post_meta( $post->ID, 'geocode', $geocode );
				}
			}
		}

		$data = array( 'location' => esc_sql( $location ), 'geocode' => $geocode, );
		switch( $method ) {
			case 'insert' :
				$result = $wpdb->insert( $wpdb->locations, $data );
			break;
			case 'update' :
				$result = $wpdb->update( $wpdb->locations, $data, array( 'location' => $location ) );
			break;
		}
	}
	if( $result !== false ) {
		print_log( "Saved[{$result}]: {$location} {$geocode}" );
	} else {
		print_log( "Save failed: {$location} {$geocode}", $wpdb->last_query );
	}
}


// GET MARKERS BY PARENT
function map_marker( $marker ) {
	$marker->cid = CID_PREFIX . $marker->calendarID;
	$marker->mid = MID_PREFIX . $marker->eventID;
	$marker->kid = escape_marker_id( $marker->post_title );
	$category = get_term_by( 'name', $marker->calendarID, 'category' );
	$marker->cname = $category->description;
	list( $marker->lat, $marker->lng ) = explode( ',', $marker->geocode );
	return $marker;
}
function get_markers_by_parentID( $parentID = '', $status = 'publish', $queryonly = false ) {
	global $wpdb;
	$query = "SELECT i.*,p.*,l.* FROM sc_event_instances AS i LEFT JOIN sc_posts AS p ON i.postID = p.ID LEFT JOIN sc_locations AS l ON i.location = l.location WHERE i.recurringEventID = '$parentID' AND l.geocode is not NULL AND l.geocode <> ''" . ( $status ? " AND i.status='{$status}'" : '' ) . " ORDER BY p.post_title ASC";
	if( $queryonly ) return $query;
	$markers = $wpdb->get_results( $query );
	if( $markers ) {

		$results = array();
		foreach( $markers as $marker ) {
			$tmp_results[] = map_marker( $marker );
		}
		foreach( $tmp_results as $marker ) {
			$results[$marker->mid] = $marker;
		}
	}
	return $results;
}

// BUILD MARKER
function get_marker_query( $where = array() ) {
	$where = $where ? $where : array(

	);
	return $query;
}
function get_markers_by_date( $date = '', $status = 'publish' ) {
	$date = $date ? $date : date( DATE_FORMAT );
	global $wpdb;
	$query = "SELECT i.*,p.*,l.* FROM sc_event_instances AS i LEFT JOIN sc_posts AS p ON i.postID = p.ID LEFT JOIN sc_locations AS l ON i.location = l.location WHERE i.sDate <= '$date' AND i.eDate >= '$date' AND l.geocode is not NULL AND l.geocode <> ''" . ( $status ? " AND i.status='{$status}'" : '' ) . " ORDER BY p.post_title ASC";
	$markers = $wpdb->get_results( $query );
	if( $markers ) {
		$results = array();
		foreach( $markers as $marker ) {
			$tmp_results[] = map_marker( $marker );
		}
		foreach( $tmp_results as $marker ) {
			$results[$marker->kid] = $marker;
		}
	}
	return $results;
}

// BUILD TOOLTIP
function get_post_by_eventID( $eventID, $status = 'publish' ) {
	global $wpdb;
	$query = "SELECT i.*,p.*,l.* FROM sc_event_instances AS i LEFT JOIN sc_posts AS p ON i.postID = p.ID LEFT JOIN sc_locations AS l ON i.location = l.location WHERE i.eventID = '$eventID'" . ( $status ? " AND i.status = '{$status}'" : '' );
	$event = $wpdb->get_row( $query );
	if( $event ) {
		$result = map_marker( $event );
	}
	return $result;
}
function get_popup_by_eventID( $eventID ) {
	global $wpdb;
	$ampm_pattern = array(
		'am' => TIME_AM_FORMAT,
		'AM' => TIME_AM_FORMAT,
		'pm' => TIME_PM_FORMAT,
		'PM' => TIME_PM_FORMAT,
	);
	$only_numbers_pattern = array(
		'-' => '',
		' ' => '',
		':' => '',
	);
	$post = get_post_by_eventID( $eventID );
	$post->edit_link = current_user_can( 'delete_pages' ) ? ABS_URL.SHOWHIDE_ACTION_URL.'?eventID=' . $post->eventID : '';
	$post->edit_link = $post->edit_link ? apply_pattern_to_structure( $post, POPUP_EDIT_LINK_STRUCTURE ) : '';
	$post->post_content_filtered = wpautop( $post->post_content );
	list( $post->post_excerpt, $post->post_keyword ) = explode( '|', $post->post_excerpt );
	$post->post_excerpt = trim( $post->post_excerpt );
	$post->post_keyword = trim( $post->post_keyword );


	$post->rrule = get_post_meta( $post->postID, 'rrule' );
	$post->rrule = is_array( $post->rrule ) ? implode( '', $post->rrule ) : $post->rrule;
	$post->since = get_post_meta( $post->postID, 'since' );
	if( $post->since ) {
		$post->since = is_array( $post->since ) ? implode( '', $post->since ) : $post->since;
		$post->osDate = str_replace( array( '/' ), '-', $post->since );
	} else {
	    $post->osDate = $post->recurringEventID ? $wpdb->get_var( "select sDate from $wpdb->event_instances where eventID = '$post->recurringEventID'" ) : $post->sDate;
	}
    list( $post->osYearZ, $post->osMonthZ, $post->osDayZ ) = explode( '-', $post->osDate );
    list( $post->osYear, $post->osMonth, $post->osDay ) = explode( '-', str_replace( '-0', '-', $post->osDate ) );

    $post->oeDate = $post->recurringEventID ? $wpdb->get_var( "select eDate from $wpdb->event_instances where eventID = '$post->recurringEventID'" ) : $post->eDate;
    list( $post->oeYearZ, $post->oeMonthZ, $post->oeDayZ ) = explode( '-', $post->oeDate );
    list( $post->oeYear, $post->oeMonth, $post->oeDay ) = explode( '-', str_replace( '-0', '-', $post->oeDate ) );

    $post->csDateTime = $post->startDateTime != '0000-00-00 00:00:00' ? $post->startDateTime : $post->startDate;
    list( $post->csDate, $post->csTime ) = explode( ' ', $post->csDateTime );
    list( $post->csYearZ, $post->csMonthZ, $post->csDayZ ) = explode( '-', $post->csDate );
    list( $post->csYear, $post->csMonth, $post->csDay ) = explode( '-', str_replace( '-0', '-', $post->csDate ) );
    list( $post->csHourZ, $post->csMinuteZ, $post->csSecondZ ) = explode( ':', $post->csTime );
	list( $post->csHour, $post->csMinute, $post->csSecond ) = explode( ':', str_replace( ':0', ':', $post->csTime ) );

    $post->csDateTimeNumbers = str_replace( array_keys( $only_numbers_pattern ), array_values( $only_numbers_pattern ), $post->csDateTime );
    $post->csDateNumbers = str_replace( array_keys( $only_numbers_pattern ), array_values( $only_numbers_pattern ), $post->csDate );
    $post->csTimeNumbers = str_replace( array_keys( $only_numbers_pattern ), array_values( $only_numbers_pattern ), $post->csTime );
	$post->csAMPM = str_replace( array_keys( $ampm_pattern ), array_values( $ampm_pattern ), date( 'A', strtotime( $post->csTime ) ) ); 
	if( $post->csHourZ == '00' ) {
		$post->csTimeText = MESSAGE_ALL_DAY;
	} else {
		$post->csTimeText = str_replace( array_keys( $ampm_pattern ), array_values( $ampm_pattern ), date( TIME_TEXT_FORMAT, strtotime( $post->csTime ) ) );
	}

    $post->ceDateTime = $post->endDateTime != '0000-00-00 00:00:00' ? $post->endDateTime : $post->endDate;
    list( $post->ceDate, $post->ceTime ) = explode( ' ', $post->ceDateTime );
    list( $post->ceYearZ, $post->ceMonthZ, $post->ceDayZ ) = explode( '-', $post->ceDate );
    list( $post->ceYear, $post->ceMonth, $post->ceDay ) = explode( '-', str_replace( '-0', '-', $post->ceDate ) );
    list( $post->ceHourZ, $post->ceMinuteZ, $post->ceSecondZ ) = explode( ':', $post->ceTime );
    list( $post->ceHour, $post->ceMinute, $post->ceSecond ) = explode( ':', str_replace( ':0', ':', $post->ceTime ) );

	$post->ceDateTimeNumbers = str_replace( array_keys( $only_numbers_pattern ), array_values( $only_numbers_pattern ), $post->ceDateTime );
    $post->ceDateumbers = str_replace( array_keys( $only_numbers_pattern ), array_values( $only_numbers_pattern ), $post->ceDate );
    $post->ceTimeNumbers = str_replace( array_keys( $only_numbers_pattern ), array_values( $only_numbers_pattern ), $post->ceTime );
	$post->ceAMPM = str_replace( array_keys( $ampm_pattern ), array_values( $ampm_pattern ), date( 'A', strtotime( $post->ceTime ) ) ); 
	if( $post->ceHourZ == '00' ) {
		$post->ceTimeText = MESSAGE_ALL_DAY;
	} else {
		$post->ceTimeText = str_replace( array_keys( $ampm_pattern ), array_values( $ampm_pattern ), date( TIME_TEXT_FORMAT, strtotime( $post->ceTime) ) );
	}
	$post->phone = get_post_meta( $post->postID, 'phone' );
	$post->email = get_post_meta( $post->postID, 'email' );
	if($post->phone || $post->email) {
//		if($post->phone)
//			$post->post_contact_form = POPUP_SMS_STRUCTURE;
//		else if($post->email)
//			$post->post_contact_form = POPUP_MAIL_STRUCTURE;
		$post->post_contact = apply_pattern_to_structure( $post, POPUP_CONTACT_STRUCTURE );
	} else {
		$post->post_contact = '';
	}

	$post = $post ? apply_pattern_to_structure( $post, POPUP_STRUCTURE ) : '';
	return $post;
}
function delete_cache() {
	rrmdir( ABS_PATH.'/cache' );
}
?>

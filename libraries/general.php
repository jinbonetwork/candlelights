<?php
if( !defined( 'SC_STARTER' ) ) die( 'Do not excute this file directly.' );

function print_sample() {
	extract( $_GET );
	$geocode = $geocode ? $geocode : SAMPLE_GEOCODE;
	$address = $address ? $address : SAMPLE_LOCATION;
	$calendarID = $calendarID ? $calendarID : SAMPLE_CALENDAR;
	$eventID = $eventID ? $eventID : SAMPLE_EVENT;
	$sampler = array(
		'geocode' => array(
			'sample' => $address,
			'query' => get_geocode_query( $address ),
			'result' => get_geocode( $address, 'object' ),
		),
		'address' => array(
			'sample' => $geocode,
			'query' => get_address_query( $geocode ),
			'result' => get_address( $geocode, 'object' ),
		),
		'events' => array(
			'sample' => $calendarID,
			'query' => get_events_query( $calendarID ),
			'result' => json_decode( get_events( $calendarID ) ),
		),
		'instances' => array(
			'sample' => array(
				'calendar' => $calendarID,
				'event' => $eventID,
			),
		   	'query' => get_instances_query( $calendarID, $eventID ),
			'result' => json_decode( get_instances( $calendarID, $eventID ) ),
		),
	);
	header( 'Content-type: application/javascript; charset=utf-8' );
	/*
	print_log( 'API Test' );
	print_log( 'Geocode:' );
	print_log( print_r( $sampler['geocode'], true ) );
	print_log( 'Address' );
	print_log( print_r( $sampler['address'], true ) );
	print_log( 'Events' );
	print_log( print_r( $sampler['events'], true ) );
	pring_log( 'Instances' );
	print_log( print_r( $sampler['instances'], true ) );
	*/
	echo json_encode($sampler);

}
function rrmdir( $dir, $contentsonly = true ) {
	foreach( glob( $dir . '/*' ) as $file ) { 
		if( is_dir( $file ) ) {
			rrmdir( $file );
		} else {
			unlink( $file );
		}
	}
	if( !$contentsonly ) rmdir( $dir );
}
function array_unique_recursive( $array ) {
	$result = array_map( 'unserialize', array_unique( array_map( 'serialize', $array ) ) );
	foreach( $result as $key => $value ) {
		if( is_array( $value ) ) {
			$result[$key] = array_unique_recursive( $value );
		}
	}
	return $result;
}

function array_to_list( $array ) {
	$markup = '<ul>';
	$array = is_object( $array ) ? (array) $array : $array;
	foreach( $array as $key => $item ) {
		$markup .= "<li><span class='label'>{$key}</span>";
		if( is_array( $item ) || is_object( $item ) ) {
			$markup .= array_to_list( $item );
		} else {
			$markup .= "<span class='split'> : </span><span class='value'>{$item}</span>";
		}
		$markup .= '</li>';
	}
	$markup .= '</ul>';
	return $markup; 
}

function wp_text_replace( $text ) {
	$pattern = array(
		'&' => '&amp;',
	);
	$text = str_replace( array_keys( $pattern ), array_values( $pattern ), $text );
	return $text;
}
function make_needle( $needle ) {
	return "%{$needle}%";
}
function make_defined_needle( $needle ) {
	return "__{$needle}__";
}
function escape_calendar_id( $string ) {
	$pattern = array(
		'@' => '_',
		'.' => '_',
	);
	return str_replace( array_keys( $pattern ), array_values( $pattern ), $string );
}
function escape_marker_id( $string ) {
	//$string = esc_attr( $string );
	$string = KID_PREFIX . sha1( $string );
	return $string;
}
function make_exit( $string ) {
	if( is_array( $string ) || is_object( $string ) ) return $string;
	/*
	$pattern = array(
		"'" => "\'",
		'"' => '\"',
	);
	return str_replace( array_keys( $pattern ), array_values( $pattern ), $string );
	*/
	return esc_sql( $string );
}
function apply_pattern_to_structure( $pattern, $structure, $escape = array(), $key_filter = 'make_needle', $value_filter = 'make_escape' ) {
	if( !$pattern ) return $structure;
	if( is_object( $pattern ) ) $pattern = (array) $pattern;
	if( !is_array( $pattern ) ) return $pattern;

	$escape = $escape ? $escape : array(
		'post_title', 'post_excerpt', 'post_content', 'post_content_filtered',
	);

	$original = array();
	$escaped = array();
	foreach( $pattern as $key => $value ) {
		if( is_array( $value ) || is_object( $value ) ) continue;
		$key = call_user_func( $key_filter, $key );
		$value = $value;
		$original[$key] = $value;
		if( in_array( $key, $escape ) ) {
			$key = call_user_func( $key_filter, $key . '_escaped' );
			$value = call_user_func( $value_filter, $value );
			$escaped[$key] = $value;
		}
	}
	$pattern = array_merge( $original, $escaped );
	$result = str_replace( array_keys( $pattern ), array_values( $pattern ), $structure );
	return $result;
}

function apply_defined_pattern_to_structure( $structure, $escape = array(), $category = 'user' ) {
	$pattern = array();
	$all = get_defined_constants( true );
	if( $category == 'all' ) {
		foreach( $all as $categorized ) {
			$pattern = array_merge( $pattern, $categorized );
		}
	} else {
		$pattern = $all[$category];
	}
	$result = apply_pattern_to_structure( $pattern, $structure, $escape, 'make_defined_needle', 'make_escape' );
	return $result;
}

?>

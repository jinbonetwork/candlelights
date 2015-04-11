<?php

function _log( $message ){
	$fp = fopen( "/tmp/candlelights.log", "a+" );
	fputs( $fp, time() . ' - ' . $message );
	fclose( $fp );
}

function make_plaintext( $string ) {
	$text = strip_tags( $string );
	$text = str_replace( array( "\n", "\t", '&nbsp;' ), ' ', $text );
	$text = preg_replace( '/ +/', ' ', $text );
	$text = trim( $text );
	return $text;

}
function make_needle( $needle ) {
	return "%{$needle}%";
}

function make_defined_needle( $needle ) {
	return "__{$needle}__";
}

function make_escape( $string ) {
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

function filter_date( $format, $time = '' ){
	$time = $time?$time:time();
	$pattern = array(
		'am' => __( 'am', 'candlelights' ),
		'pm' => __( 'pm', 'candlelights' ),
		'AM' => __( 'AM', 'candlelights' ),
		'PM' => __( 'PM', 'candlelights' ),
	);
	setlocale( LC_ALL, WP_LANG );
	$timezone_backup = date_default_timezone_get();
	date_default_timezone_set( get_option( 'timezone_string' ) );
	$date = date( $format, $time );
	$date = str_replace( array_keys( $pattern ), array_values( $pattern ), $date );
	date_default_timezone_set($timezone_backup);
	return $date;
}

function filter_recurrence_rules( $event ){

	if( $event->recurrence_rules ) {
		$event->event_rules = array();
		$rules = explode( ';', $event->recurrence_rules );
		foreach( $rules as $rule ) {
			list( $key, $value ) = explode( '=', $rule );
			$key = trim( strtolower( $key ) );
			$value = trim( $value );
			$event->event_rules[$key] = $value;
		}
		$event->event_date_start = filter_date( get_option( 'date_format' ), $event->start );
		$event->event_date_end = !$event->instant_event&&$event->event_rules[until] ? filter_date( get_option( 'date_format' ), strtotime( str_replace( array( 'T', 'Z' ), ' ', $event->event_rules[until] ) ) ) : '';
		$event->event_date = sprintf( __( '%s ~ %s', 'candlelights' ), $event->event_date_start, $event->event_date_end );

		if( $event->event_rules[byday] ) {
			$event->event_byday_raw = explode( ',', $event->event_rules[byday] );
			$event->event_byday_array = null;
			foreach( $event->event_byday_raw as $day ) {
				$event->event_byday_array[] = __( trim($day), 'candlelights' );
			}
			$event->event_byday = implode( ',', $event->event_byday_array );
		}
		$event->event_rule = __(
				sprintf(
					__( 'Every %s %s', 'candlelights' ),
					( $event->event_rules[interval] && $event->event_rules[interval] > 1 ? $event->event_rules[interval] : '' ),
					( $event->event_rules[freq] ? __( trim( $event->event_rules[freq] ), 'candlelights' ) : '' )
				),
				'candlelights'
			);
		$event->event_rule .= ( $event->event_rules[byday] ? ' ' . __( $event->event_byday, 'candlelights' ) : '' );
		$event->event_rules = array_filter( $event->event_rules, 'trim' );
	} else {
		if(date('Ymd',$event->start)==date('Ymd',$event->end)||!$event->end){
			$current_year = date('Y', time());
			$event_year = date('Y', $event->start);
			$format = ( strpos( $current_year, $event_year ) !== false && defined('ALT_DATE_FORMAT') ) ? ALT_DATE_FORMAT : get_option( 'date_format' );
			$event->event_date = filter_date( $format, $event->start );
			$event->event_rule = '';
		}else{
			$event->event_date_start = filter_date( get_option( 'date_format' ), $event->start );
			$event->event_date_end = filter_date( get_option( 'date_format' ), $event->end );
			$event->event_date = sprintf( __( '%s ~ %s', 'candlelights' ), $event->event_date_start, $event->event_date_end );
		}
	}
	$event->event_time_start = filter_date( get_option( 'time_format' ), $event->start );
	$event->event_time_end = !$event->instant_event?filter_date( get_option( 'time_format' ), $event->end ):'';
	if( $event->allday ) {
		$event->event_time = __( 'All day', 'candlelights' );
	} else {
		$event->event_time = sprintf( __( '<span class="from">%s</span> <span class="split">~</span> <span class="to">%s</span>', 'candlelights' ), $event->event_time_start, $event->event_time_end );
	}
	return $event;
}

function get_event_posts( $ids ){
	global $wpdb;
	$ida = implode(',', $ids);
	$query = "SELECT * FROM {$wpdb->posts} WHERE ID IN ({$ida})";
	$results = $wpdb->get_results( $query );
	if( $results ){
		foreach( $results as $post ){
			$posts[$post->ID] = $post;
		}
		return $posts;
	} else {
		return false;
	}
}
function get_event_category($entry){
	global $wpdb,$table_prefix;
	$terms = get_the_terms($entry->ID,'events_categories');
	if(!empty($terms)){
		$category = $terms[0];
		$db = $table_prefix.'ai1ec_event_category_meta';
		$categoryMeta = $wpdb->get_row("SELECT * FROM {$db} WHERE term_id='{$category->term_id}'");
		$categoryExtended = array_merge((array)$category,(array)$categoryMeta);
	}else{
		$categoryExtended = array();
	}
	return (object)$categoryExtended;
}
function determine_icon($name){
	$icon = (object)array('basename' => 'c'.md5($name),);
	$icon->basename = $icon->basename?$icon->basename:ICON_SLUG_DEFAULT;
	$icon->slug = $icon->basename;
	$icon->normal = ICON_PREFIX.$icon->slug.ICON_SUFFIX_NORMAL.ICON_EXTENSION;
	$icon->hover = ICON_PREFIX.$icon->slug.ICON_SUFFIX_HOVER.ICON_EXTENSION;
	if(!file_exists(TEMPLATEPATH.'/images/'.$icon->normal)){
		$icon->slug = ICON_SLUG_DEFAULT;
		$icon->normal = ICON_PREFIX.ICON_SLUG_DEFAULT.ICON_SUFFIX_NORMAL.ICON_EXTENSION;
		$icon->hover = ICON_PREFIX.ICON_SLUG_DEFAULT.ICON_SUFFIX_HOVER.ICON_EXTENSION;
	}
	return $icon;
}
function filter_event( $entry ){
	$entry->event_category = get_event_category($entry);
	$entry->category = $entry->event_category->term_id;
	$entry->icon = determine_icon($entry->event_category->slug);
	$entry->has_contact = $entry->contact_phone || $entry->contact_email ? true : false;
	$entry->post_content_filtered = apply_filters( 'the_content', $entry->post_content );
	$entry = filter_recurrence_rules( $entry );

	$entry->event_classes = array(
		'event',
		'event-id-' . $entry->ID,
		'event-category-' . $entry->event_category->term_id,
		'event-category-' . $entry->icon->slug,
		'event-priority-' . abs( $entry->menu_order ),
		'event-' . ( $entry->menu_order == 0 ? 'normal' : 'important' ),
	);
	if( $entry->has_contact ){
		$entry->event_classes[] = 'event-has-contact';
	} else {
		$entry->event_classes[] = 'event-has-no-contact';
	}
	if( $entry->contact_phone ){
		$entry->event_classes[] = 'event-contact-phone';
	}
	if( $entry->contact_email ){
		$entry->event_classes[] = 'event-contact-email';
	}
	if( $entry->lat ){
		$entry->event_classes[] = 'event-has-lat';
	}
	if( $entry->lng ){
		$entry->event_classes[] = 'event-has-lng';
	}
	if( $entry->show_map ){
		$entry->event_classes[] = 'event-has-map';
	}
	if( $entry->recurrence_rules ){
		foreach( $entry->event_rules as $key => $value ){
			$key_dirified = sanitize_title( $key );
			$value_dirified = sanitize_title( $value );
			$entry->event_classes[] = "event-rule-{$key_dirified}-{$value_dirified}";
		}
	}
	$entry->event_class = implode( ' ', $entry->event_classes );
	global $address_filter;
	$entry->address = str_replace( array_keys( $address_filter->patterns ), array_values( $address_filter->patterns ), $entry->address );
	return $entry;
}
function get_event( $id ){
	global $wpdb;
	$post = $wpdb->get_row( "SELECT * FROM {$wpdb->posts} WHERE ID={$id}" );
	$event = $wpdb->get_row( "SELECT * FROM {$wpdb->events} WHERE post_id={$id}" );
	$category = $wpdb->get_row( "SELECT * FROM {$wpdb->term_relationships} WHERE object_id={$id}" );
	$entry = (object) array_merge( (array) $post, (array) $event, (array) $category );
	$entry = filter_event( $entry );

	return $entry;
}

function get_events_query( $options = array() ){
	$defaults = array(
		//'select' => 'SELECT p.*, e.*, i.*, c.* FROM wp_ai1ec_events AS e LEFT JOIN wp_ai1ec_event_instances AS i ON e.post_id = i.post_id LEFT JOIN wp_term_relationships AS c ON c.object_id = e.post_id LEFT JOIN wp_posts AS p ON p.ID = e.post_id WHERE',
		'select' => 'SELECT e.*, i.*, c.* FROM wp_ai1ec_events AS e LEFT JOIN wp_ai1ec_event_instances AS i ON e.post_id = i.post_id LEFT JOIN wp_term_relationships AS c ON c.object_id = e.post_id WHERE',
		'where' => '',
		'order' => 'GROUP BY e.post_id ORDER BY e.event_priority DESC, e.start ASC',
		'offset' => 0,
		'limit' => POSTS_PER_PAGE,
	);
	$options = array_intersect_key( array_merge( $defaults, $options ), $defaults );
	extract( $options );
	/*
	 * Build query
	 */
	if( !$where ) { // default is list query;
		$query_conditions = array(); 
		$query_conditions[] = 'e.event_status="publish"';
		//$query_conditions[] = 'e.event_parent = 0';
		if( CATEGORY ) {
			$query_conditions[] = 'c.term_taxonomy_id = __CATEGORY__';
		}
		if( SW_LAT || NE_LAT || SW_LNG || NE_LNG ) {
			$query_conditions[] = '( e.latitude >= __SW_LAT__ AND e.latitude <= __NE_LAT__ AND e.longitude >= __SW_LNG__ AND e.longitude <= __NE_LNG__ )';
		}
		$query_conditions[] = '( e.end >= __YMD_START_TIME__ OR i.end >= __YMD_START_TIME__ )'; // events not ended
		if( TODAY_ONLY || YMD < TODAY_YMD ) {
			$query_conditions[] = '( i.start <= __YMD_END_TIME__ )'; // events must be available
		}
		$where = implode( ' AND ', $query_conditions );
	}

	$query_structure = "{$select} {$where} {$order}";
	if($limit){
		$query_structure .= ' LIMIT '.($offset?$offset.',':'').$limit;
	}

	/*
	 * Complete query
	 */
	$query = apply_defined_pattern_to_structure( $query_structure );

	return $query;
}
function sort_events_by( $a, $b, $field = 'event_priority', $order = 'desc' ){
	$_a = $a->$field;
	$_b = $b->$field;
	$_o = strtolower( $order ) == 'asc' ? 1 : -1;
	
	if( $_a == $_b ){
		return 0;
	} else {
		return ( $_a > $_b ? 1 : -1 ) * $_o;
	}
}
?>

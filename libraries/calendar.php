<?php
if( !defined( 'SC_STARTER' ) ) die( 'Do not excute this file directly.' );

$timezone = new DateTimeZone( TIME_ZONE );
function date_to_mysql_date( $string, $gmt = false ) {
	if( !$string ) return false;
	$time = new DateTime( $string );
	if( $gmt ) {
		global $timezone;
		$time->setTimeZone( $timezone );
	}
	$date = $time->format( DATE_FORMAT );
	return $date;
}

function date_to_mysql_datetime( $string, $gmt = false ) {
	if( !$string ) return false;
	$time = new DateTime( $string );
	if( $gmt ) {
		global $timezone;
		$time->setTimeZone( $timezone );
	}
	$date = $time->format( TIME_FORMAT );
	return $date;
}

function get_calendar_query( $method ) {
	$query = GOOGLE_CALENDAR_ENDPOINT
		. $method
		. '?maxResults=' . GOOGLE_CALENDAR_LIMIT
		. ( GOOGLE_CALENDAR_TIME_MAX ? '&timeMax=' . rawurlencode( GOOGLE_CALENDAR_TIME_MAX ) : '' )
		. ( GOOGLE_CALENDAR_TIME_MIN ? '&timeMin=' . rawurlencode( GOOGLE_CALENDAR_TIME_MIN ) : '' )
		. '&key=' . GOOGLE_CALENDAR_APIKEY;
	return $query;
}

function get_events_query( $calendarID ) {
	$query = get_calendar_query( "/{$calendarID}/events" );
	return $query;
}

function get_events( $calendarID ) {
	$query = get_events_query( $calendarID );
	$results = file_get_contents( $query );
	return $results;
}

function get_event_query( $calendarID, $eventID ) {
	$query = get_calendar_query( "/{$calendarID}/events/{$eventID}" );
	return $query;
}

function get_event( $calendarID, $eventID ) {
	$query = get_event_query( $calendarID, $eventID );
	$results = file_get_contents( $query );
	return $results;
}

function get_instances_query( $calendarID, $eventID ) {
	$query = get_calendar_query( "/{$calendarID}/events/{$eventID}/instances" );
	return $query;
}

function get_instances( $calendarID, $eventID ) {
	$query = get_instances_query( $calendarID, $eventID );
	$results = file_get_contents( $query );
	return $results;
}

function wp_date_filter( $date ) {
	if ( !$date || $date == '0000-00-00 00:00:00' || $date == '0000-00-00' ) return;
	else return $date;
}

function update_calendar( $calendar ) {
	global $instance_que;
	$calendar->events = json_decode( get_events( $calendar->name ) );
	$calendar->events->query = get_events_query( $calendar->name );
	if( $calendar->events->items ) {
		print_log( "Calendar events loaded: {$calendar->description} {$calendar->name}", $calendar->events->query );

		if( wp_update_term( $calendar->term_id, 'category', array( 'description' => $calendar->events->summary ) ) ) {
			print_log( "Calendar updated: {$calendar->description} {$calendar->name}", $calendar->events->summary );
		} else {
			print_log( "Calendar failed to update: {$calendar->description} {$calendar->name}", '?' );
		}

		foreach( $calendar->events->items as $event ) {
			$event->calendar = $calendar;
			unset( $event->calendar->events->items ); // prevent recursive object
			$event = update_event( $event ); // get updated event object
			$event->calendarID = $event->calendar->name;

			update_instance( $event );

			// QUE RECURRING EVENT
			//if( $event->recurrence ) {
				print_log( "Instance que added: {$event->id}");
				$instance_que[] = $event; // object
			//}
		}
	} else {
		print_log( "Calendar events not found: {$calendar->description} {$calendar->name}", $calendar->events->query );
	}
}

function update_event( $event ) {
	// $event must contain $calendar object. see update.events.php

	$format = array( 'ID', 'post_type', 'post_status', 'guid', 'post_name', 'post_title', 'post_content', 'post_excerpt', 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt', 'post_category' );
	
	global $wpdb;

	print_log( "Processing event: {$event->summary} -- {$event->location} ({$event->id})" );
	$event->new = (object) array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'guid' => $event->htmlLink,
		'post_name' => $event->id,
		'post_title' => wp_text_replace( $event->summary ),
		'post_content' => wp_text_replace( $event->description ),
		'post_excerpt' => wp_text_replace( $event->location ),
		//'post_date' => date_to_mysql_datetime( $event->created ),
		//'post_date_gmt' => date_to_mysql_datetime( $event->created, true ),
		//'post_modified' => date_to_mysql_datetime( $event->updated ),
		//'post_modified_gmt' => date_to_mysql_datetime( $event->updated, true ),
		'post_category' => array( $event->calendar->term_id ),
	);

	$old_query = "select * from {$wpdb->posts} where post_name='{$event->new->post_name}' and post_type='post' and post_status in ('publish','future','draft','protected')"; 
	$event->old = $wpdb->get_row( $old_query );
	$event->old->query = $old_query;
	print_log( "Checking duplicate", $event->old->query );
	if ( $event->old->ID ) {
		if( has_category( NOTICE_CATEGORY_ID, $event->old->ID ) ) {
			$event->new->post_category = array_merge( $event->new->post_category, array( NOTICE_CATEGORY_ID ) );
		}
		print_log( "Duplicate found" );
		if (
			wp_text_replace( $event->old->post_title ) != $event->new->post_title
			|| wp_text_replace( $event->old->post_content ) != $event->new->post_content
			|| wp_text_replace( $event->old->post_excerpt ) != $event->new->post_excerpt
			//|| date_to_mysql_datetime($event->old->post_date) != date_to_mysql_datetime($event->new->post_date)
			//|| date_to_mysql_datetime($event->old->post_date_gmt) != date_to_mysql_datetime($event->new->post_date_gmt)
			//|| date_to_mysql_datetime($event->old->post_modified) != date_to_mysql_datetime($event->new->post_modified)
			//|| date_to_mysql_datetime($event->old->post_modified_gmt) != date_to_mysql_datetime($event->new->post_modified_gmt)
		) {
			print_log( "Need to update..." );
			$event->que = (object) array_merge( (array) $event->old, (array) $event->new );
			$event->new->method = 'update';
		} else {
			print_log( "Nothing to update..." );
			$event->que = false;
			$event->new->method = false;
		}
	} else {
		print_log( "Fresh!" );
		$event->que = $event->new;
		$event->new->method = 'insert';
	}

	if( USE_EVENT_SAMPLER || $event->new->method == 'update' ) {
		print_log( 'Event:' );
		print_log( print_r( $event, true ) );
	}
	if( $event->que ) {
		$event->que = array_intersect_key( (array) $event->que, array_flip( $format ) );
		$event->new->result = wp_insert_post( $event->que );

		if( $event->new->result !== false ) {
			print_log( "Post updated" );
		} else {
			print_log( "Post failed to update" . print_r( $event->que, true ) );
		}
	}
	return $event;
}

function update_instance( $instance ) {

	$format = array( 'postID', 'eventID', 'calendarID', 'location', 'recurringEventID', 'sDate', 'startDate', 'startDateTime', 'eDate', 'endDate', 'endDateTime' );

	// $instance must contain $post object -- see function update_event()
	global $wpdb;

	print_log( "Processing instance: {$instance->id}" );

	if( $instance->recurringEventId ) {
		$instance->postID = $wpdb->get_var( "select ID from $wpdb->posts where post_name = '$instance->recurringEventId'" );
	} else {
		$instance->postID = $wpdb->get_var( "select ID from $wpdb->posts where post_name = '$instance->id'" );
	}

	$instance->new = (object) array(
		'postID' => $instance->postID,
		'eventID' => $instance->id,
		'calendarID' => $instance->calendarID,
		'location' => $instance->location,
		'recurringEventID' => $instance->recurringEventId,
		'sDate' => date_to_mysql_date( $instance->start->date ? $instance->start->date : $instance->start->dateTime ),
		'startDate' => date_to_mysql_datetime( $instance->start->date ),
		'startDateTime' => date_to_mysql_datetime( $instance->start->dateTime ),
		'eDate' => date_to_mysql_date( $instance->end->date ? $instance->end->date : $instance->end->dateTime ),
		'endDate' => date_to_mysql_datetime( $instance->end->date ),
		'endDateTime' => date_to_mysql_datetime( $instance->end->dateTime ),
	);

	$parent = get_post( $instance->postID );

	$old_query = "select * from {$wpdb->event_instances} where eventID = '{$instance->new->eventID}'";
	$instance->old = $wpdb->get_row( $old_query );
	$instance->old->query = $old_query;
	$instance->old->startDate = wp_date_filter( $instance->old->startDate );
	$instance->old->startDateTime = wp_date_filter( $instance->old->startDateTime );
	$instance->old->endDate = wp_date_filter( $instance->old->endDate );
	$instance->old->endDateTime = wp_date_filter( $instance->old->endDateTime );
	print_log( "Checking duplicate: {$instance->old->query}" );

	if( !$instance->old->eventID ) {
		print_log( "Fresh!" );
		$instance->que = $instance->new;
		$instance->new->method = 'insert';
	} else {
		print_log( "Duplicate found" );
		if (
			$instance->old->postID != $instance->new->postID
			|| $instance->old->eventID != $instance->new->eventID
			|| $instance->old->calendarID != $instance->new->calendarID
			|| $instance->old->recurringEventID != $instance->new->recurringEventID
			|| $instance->old->location != $instance->new->location
			|| date_to_mysql_date($instance->old->sDate) != date_to_mysql_date($instance->new->sDate)
			|| date_to_mysql_datetime($instance->old->startDate) != date_to_mysql_datetime($instance->new->startDate)
			|| date_to_mysql_datetime($instance->old->startDateTime) != date_to_mysql_datetime($instance->new->startDateTime)
			|| date_to_mysql_date($instance->old->eDate) != date_to_mysql_date($instance->new->eDate)
			|| date_to_mysql_datetime($instance->old->endDate) != date_to_mysql_datetime($instance->new->endDate)
			|| date_to_mysql_datetime($instance->old->endDateTime) != date_to_mysql_datetime($instance->new->endDateTime)
		) {
			print_log( "Need to update" );
			$instance->que = (object) array_merge( (array) $instance->old, (array) $instance->new );
			$instance->new->method = 'update';
		} else {
			print_log( "Nothing to update" );
			$instance->que = false;
			$instance->new->method = false;
		}
	}
	if( USE_INSTANCE_SAMPLER || $instance->new->method == 'update' ) {
		print_log( 'Instance:' );
		print_log( print_r( $instance, true ) );
	}
	if( $instance->que ) {
		$instance->que = array_intersect_key( (array) $instance->que, array_flip( $format ) );
		$instance->que['location'] = esc_sql( $instance->que['location'] );
		switch( $instance->new->method ) {
			case 'insert' :
				$instance->new->result = $wpdb->insert( $wpdb->event_instances, $instance->que );
			break;
			case 'update' :
				$instance->new->result = $wpdb->update( $wpdb->event_instances, $instance->que, array( 'eventID' => $instance->que['eventID'] ) );
			break;
		}
		$instance->new->query = $wpdb->last_query;
		if( $instance->new->result !== false ) {
			print_log( "Instance saved[{$instance->new->result}]", $instance->new->query );
		} else {
			print_log( "SQL querying failed", $instance->new->query );
		}
	}
	return $instance;
}

?>

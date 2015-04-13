<?php
define( 'WP_USE_THEMES', false );
require_once dirname(__FILE__).'/../../../wp-blog-header.php';
header( 'Content-type: text/html' );

function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

/*
 * Get query results
 */
global $wpdb;
$page = $_GET[page];
$page = $page ? $page : 1;
$offset = ( $page - 1 ) * POSTS_PER_PAGE;
$offset = $offset < 1 ? 0 : $offset;
$query = get_events_query( array( 'offset' => $offset ) );

if( current_user_can( EDITOR_CAPABILITY ) ){
	$query_escaped = esc_attr( $query );
	$time_start = microtime_float();
}

$events = $wpdb->get_results( $query );

if( current_user_can( DEVEL_CAPABILITY ) ){
	$time_end = microtime_float();
	$time_elapsed = ( $time_end - $time_start );
}

if( $events ){
	//usort( $events, 'sort_events_by' );

	$ids = array();
	foreach( $events as $event ){
		$ids[] = $event->post_id;
	}
	$posts = get_event_posts( $ids );
	if( current_user_can( DEVEL_CAPABILITY ) ){
		$time_end2 = microtime_float();
		$time_elapsed2 = ( $time_end2 - $time_end );
	}
	foreach( $events as $event ) {
		if( $event->event_parent && $events[$event->event_parent] ) continue;
		$event = filter_event( (object) array_merge( (array) $posts[$event->post_id], (array) $event ) );
		$entry = $event;
		require dirname(__FILE__).'/ajax-list.skin.php';
	}
	if( POSTS_PER_PAGE ){
		$more_label = __( 'Load more events', 'candlelights' );
		echo "<div class='event-more page-{$page}'><a href='javascript://' data-page='{$page}'>{$more_label}</a></div><script> page = {$page}; </script>" . PHP_EOL;
	}
} else {
	if( POSTS_PER_PAGE ){
		echo "<script> page = 0; </script>" . PHP_EOL;
		if( $page == 1 ) {
			$error = __( 'No events found in this area.', 'candlelights' );
			echo "<p>{$error}</p><!--{$query}-->" . PHP_EOL;
		}
	} else {
		$error = __( 'No events found in this area.', 'candlelights' );
		echo "<p>{$error}</p><!--{$query}-->" . PHP_EOL;
	}
} 
if( current_user_can( DEVEL_CAPABILITY ) ){
	$ymd_now = YMD_NOW.' ('.YMD_NOW_TIME.')';
	$today_now = TODAY_NOW.' ('.TODAY_NOW_TIME.')';
	$count = count($events);
	$message = <<<CONSOLE_MESSAGE
		<ul style="font-size: 11px;">
			<li>YMD_NOW: <code>{$ymd_now}</code></li>
			<li>TODAY_NOW: <code>{$today_now}</code></li>
			<li>Query: <code>{$query_escaped}</code></li>
			<li>Excution time:
				<ul>
					<li><code>{$time_elapsed}</code>/<code>{$time_elapsed2}</code>sec</li>
					<li><code>{$time_start} ~ {$time_end} ~ {$time_end2}</code></li>
				</ul>
			</li>
			<li><code>{$count}</code> entries loaded.</li>
		</ul>
CONSOLE_MESSAGE;
	$message = str_replace("\n",'',$message);
	echo <<<EOT
	<script>
		jQuery('#main-console').html('{$message}');
	</script>
EOT;
}

?>

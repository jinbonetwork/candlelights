<?php
define( 'WP_USE_THEMES', false );
define( 'POPUP_MODE', 'ajax' );
require_once dirname(__FILE__).'/../../../wp-blog-header.php';

global $wpdb;

/* get current event */
$entry = get_event( $_GET[id] );

if( $entry->event_status != 'publish' && !current_user_can( EDITOR_CAPABILITY ) ){
	exit;
}

/* get family events */
$parent = $entry->post_parent ? $entry->post_parent : $entry->ID;
$family = $wpdb->get_results( "SELECT ID FROM {$wpdb->posts} WHERE ( ID='{$parent}' OR post_parent='{$parent}' ) AND post_type='ai1ec_event'" );

/* get instances */
if( $family ) {
	$events = array();
	$instances = array();

	$unit = 60*60*24;
	$start = $entry->start - ($entry->start%$unit);
	$end = $entry->end - ($entry->end%$unit);

	if(!$entry->recurrence_rules&&$start!=$end){
		$events[$entry->ID] = $entry;
		for($i=$start;$i<=$end;$i=$i+$unit){
			$idx = $entry->ID.'-'.$i;
			$instances[$idx] = (object)array_merge((array)$entry,array(
				'start'=>$i
			));
			//echo "{$idx}: {$i} => ".date('Y-m-d',$i).'<br>';
		}
	}else{
		foreach( $family as $member ){
			$events[$member->ID] = $member->ID == $entry->ID ? $entry : get_event( $member->ID );
			$children = $wpdb->get_results( get_events_query( array( 'where' => "i.post_id = {$member->ID}", 'order' => false, 'limit' => false) ) );
			if( $children ){
				foreach( $children as $instance ){
					$instances[$instance->id] = $instance;
				}
			}
		}
	}
}

get_header();
require_once dirname(__FILE__).'/ajax-detail.skin.php';
if( current_user_can( DEVEL_CAPABILITY ) ){
	ob_start();
	print_r($entry);
	$debug_helper = ob_get_contents();
	ob_end_clean();
	$debug_helper = htmlspecialchars( $debug_helper );
	echo "<pre class='devel event-object'><code>{$debug_helper}</code></pre>";
}
get_footer();
?>

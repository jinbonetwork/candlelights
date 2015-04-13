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

	foreach( $family as $member ){
		$current = $member->ID == $entry->ID?$entry:get_event($member->ID);
		$start = filter_date('Ymd',$current->start);
		$end = filter_date('Ymd',$current->end);
		$events[$member->ID] = $current;

		if(!$current->recurrence_rules&&$start!=$end){
			for($i=$start;$i<=$end;$i++){
				$j = preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})/','$1-$2-$3 00:00:00',$i);
				$idx = $current->ID.'-'.$i;
				$instances[$idx] = (object)array_merge((array)$current,array(
					'instance_id' => $idx,
					'instance_start' => filter_time($j),
				));
				//echo "{$idx}: {$i} => {$j}<br>";
			}
		}else{
			$query = get_events_query(array('where'=>"e.post_id={$member->ID}",'order'=>false,'limit'=>false));
			$children = $wpdb->get_results($query);
			if(!empty($children)){
				foreach($children as $instance){
					$instances[$instance->instance_id] = $instance;
					//echo "{$instance->instance_id}: {$instance->instance_start} => ".filter_date('Y-m-d',$instance->instance_start)."<br>";
				}
			}
		}
		//echo 'Total '.count($instances).' instances';
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

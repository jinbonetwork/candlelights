<?php
if( !defined( 'ABSPATH' ) ) die( 'do not access this file directly' );

?>

<div id="event-<?php echo $entry->ID; ?>" class="event-list page-<?php echo $page; ?> <?php echo $entry->event_class; ?>" data-category="<?php echo $entry->category; ?>" data-icon="<?php echo $entry->icon; ?>" data-latitude="<?php echo $entry->latitude; ?>" data-longitude="<?php echo $entry->longitude; ?>" data-permalink="<?php echo get_permalink( $entry->post_id ); ?>" data-detail="<?php echo DETAILURL . '?id=' . $entry->post_id; ?>">
	<div class="event-header">
		<div class="event-info">
			<span class="event-venue"><?php echo $entry->venue; ?></span><span class="event-location"><?php echo $entry->address; ?></span>
		</div><!--/.event-info-->
		<div class="event-meta">
<?php
	echo $entry->event_date ? '<div class="event-date">' . $entry->event_date . '</div>' . PHP_EOL : '';
	echo $entry->event_rule ? '<div class="event-rule">' . $entry->event_rule . '</div>' . PHP_EOL : '';
	echo $entry->event_time ? '<div class="event-time">' . $entry->event_time . '</div>' . PHP_EOL : '';
?>
		</div><!--/.event-meta-->
		<div class="event-title-wrap">
			<h3 class="event-title"><i></i><span><a href="<?php echo get_permalink( $entry->ID ); ?>"><?php echo ( current_user_can( EDITOR_CAPABILITY ) ? "({$entry->event_priority})" : '' ) . $entry->post_title; ?></a></span></h3>
		</div>
	</div><!--/.event-header-->
	<!--div class="event-body">
		<div class="event-content-feature"><?/*php the_post_thumbnail( 'full' ); */ ?></div><!--/.event-content-feature-->
		<div class="event-content"><?/*php echo $entry->post_content_filtered; */ ?></div><!--/.event-content-->
	</div--><!--/.event-body-->
</div><!--/.event#event-<?php echo $entry->ID; ?>-->


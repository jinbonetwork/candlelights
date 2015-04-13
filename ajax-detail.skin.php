<?php
$_code = array();
$_code_output = '';
$_tooltip = array();
$_tooltip_output = '';
if( $instances ) {
	foreach( $instances as $instance ) {
		$_date = esc_attr( filter_date( 'Y-m-d', $instance->start ) );
		$_title = esc_attr( $instance->post_id == $entry->ID ? $entry->post_title : $child[$instance->post_id]->post_title );
		$_description = esc_attr( make_plaintext( wpautop( $instance->post_id == $entry->ID ? $entry->post_content : $child[$instance->post_id]->post_content ) ) );
		$_url = esc_attr( get_permalink( $entry->ID ) );
		$_code[] = "{ date: '$_date', title: '$_title', description: '$_description', url: '$_url' }";
		$_tooltip[] = "'$_date': { title: '$_title', description: '$_description' }";
	}
	$_code_output = implode( ',' . PHP_EOL, $_code ) . PHP_EOL;
	$_tooltip_output = implode( ',' . PHP_EOL, $_tooltip ) . PHP_EOL;
}
$share_url = get_permalink( $entry->ID );
$share_text = $entry->post_title;
?>
<div id="event-detail-<?php echo $entry->ID; ?>" class="event-detail <?php echo $entry->event_class; ?>">
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
<?php
	echo "<div class='event-category'>".PHP_EOL;
	if($entry->event_category->term_image){
		echo "<div class='event-category-image' style='background-image:url(\"{$entry->event_category->term_image}\")'></div><!--/.event-category-image-->".PHP_EOL;
	}
	if($entry->event_category->term_color){
		$entry->event_category->term_color_style = "color:{$entry->event_category->term_color}";
	}
	echo "<div class='event-category-label'><span style='{$entry->event_category->term_color_style}'>{$entry->event_category->name}</span></div><!--/.event-category-label-->".PHP_EOL;
	echo "</div><!--/.event-category-->".PHP_EOL;
?>
		<div class="event-title-wrap">
			<h3 class="event-title"><i></i><span><?php echo $entry->post_title; ?></span></h3>
		</div>
		<div class="event-console">
			<a href="javascript://" class="button to-contact"><span class="label"><?php _e( 'Contact us', 'candlelights' ); ?></span></a>
		</div><!--/.event-console-->
		<div class="event-feature">
			<div class="event-category"><?php echo $entry->event_category->name; ?></div>
			<div class="share event-share">
				<h4><?php _e( 'Share this page', 'candlelights' ); ?></h4>
				<ul>
					<li class="share twitter"><a href="https://twitter.com/share?text=<?php echo urlencode( $share_text ); ?>&amp;url=<?php echo urlencode( $share_url ); ?>"><span><?php _e( 'Share on Twitter', 'candlelights' ); ?></span></a></li>
					<li class="share facebook"><a href="https://facebook.com/sharer.php?u=<?php echo urlencode( $share_url ); ?>"><span><?php _e( 'Share on Facebook', 'candlelights' ); ?></span></a></li>
					<li class="share googleplus"><a href="https://plus.google.com/share?url=<?php echo urlencode( $share_url ); ?>"><span><?php _e( 'Share on Google+', 'candlelights' ); ?></span></a></li>
				</ul>
			</div><!--/.event-share-->
		</div><!--/.event-feature-->
	</div><!--/.event-header-->
	<div class="event-body">
		<?php edit_post_link( 'Edit', '<div class="event-edit">', '</div>', $entry->ID ); ?>
		<div class="event-content-feature"><?php the_post_thumbnail( 'full' ); ?></div><!--/.event-content-feature-->
		<div class="event-content block block-narrow"><?php echo $entry->post_content_filtered; ?></div><!--/.event-content-->
<?php
if(($entry->show_map||FORCE_SHOW_MAP)&&($entry->latitude&&$entry->longitude)) {
	echo <<<EOT
		<div id="event-map-box" class="event-map-box"><div id="event-map" class="event-map"></div></div>
EOT;
}
?>
		<div class="event-annotation block block-wide">
			<h2 class="title"><?php
				printf(
					__('<span class="today-is">Today is</span> <span class="date"><span class="year">%s</span> <span class="month">%s</span> <span class="day">%s</span><span class="split">;</span></span> <span class="today-description"><span class="counter">%s days</span> <span class="pre-phrase">have passed</span> <span class="post-phrase">since the Sewol Ferry Tragedy.</span></span>','candlelights'),
					TODAY_YEAR,
					TODAY_MONTH,
					TODAY_DAY,
					DAY_COUNTER
			   	);
			?></h2><!--/.title-->
			<div class="description"><?php
				printf(
					__('To join the petition, please visit %s.','candlelights'),
					"<a class='go-petition' href='http://petition.sewolho416.org'>petition.sewolho416.org</a>"
				);
			?></div><!--/.description-->
		</div><!--/#annotation-->
		<div id="calendar" class="event-calendar block block-wide">
			<script id="mini-clndr-template" type="text/template">
				<div class="controls">
					<div class="clndr-previous-button">&lsaquo;</div><div class="month"><%= month %></div><div class="clndr-next-button">&rsaquo;</div>
				</div>
				<div class="days-container">
					<div class="days">
						<div class="headers">
							<% _.each(daysOfTheWeek, function(day) { %><div class="day-header"><%= day %></div><% }); %>
						</div>
						<% _.each(days, function(day) { %><div class="<%= day.classes %>" id="<%= day.id %>"><span><%= day.day %></span></div><% }); %>
					</div>
				</div>
				<div class="legends">
					<span class="today"><span class="mark"></span> <span class="label"><?php _e('Today', 'candlelights'); ?></span></span>
					<span class="event"><span class="mark"></span> <span class="label"><?php _e('Event', 'candlelights'); ?></span></span>
				</div>
			</script>
		</div>
		<div id="contact" class="event-contact">
			<div class="event-contact-form-box">
<?php
if( !$entry->has_contact ) {
	echo __( 'No contact informations', 'candlelights' );
} else {
	$form = (object) array();
	$form->type = ( $entry->contact_phone ? 'sms' : 'email' );
	$form->title = __( 'Contact us', 'candlelights' );
	$form->help = __( 'Submit your contact and event manager will response.', 'candlelights' );
	$form->action = get_stylesheet_directory_uri() . '/ajax-contact.php';
	$form->id = $entry->ID;
	$form->sender_label = ( $form->type == 'sms' ? __( 'Phone number', 'candlelights' ) : __( 'E-mail address', 'candlelights' ) );
	$form->sender_pattern = ( $form->type == 'sms' ? CONTACT_PHONE_PATTERN : CONTACT_EMAIL_PATTERN );
	$form->sender_placeholder = esc_attr( ( $form->type == 'sms' ? '01012341234' : 'me@example.com' ) );
	$form->sender_help = ( $form->type == 'sms' ? __( 'Numbers only.', 'candlelights' ) : '' );
	$form->message_label = __( 'Message', 'candlelights' );
	$form->message_placeholder = esc_attr( '' );
	$form->message_help = ( $form->type == 'sms' ? sprintf( __( 'It limits %s bytes.', 'candlelights' ) . ' (%s/%s)', SMS_CAPACITY, '<span class="counter">0</span>', SMS_CAPACITY) : '' );
	$form->submit_label = __( 'Submit', 'candlelights' );
	$form->cancel_label = __( 'Cancel', 'candlelights' );
	echo <<<EOT
				<div class="event-contact-form block block-narrow">
					<h4 class="legend"><span>$form->title</span></h4>
					<p class="help">$form->help</p>
					<form id="event-contact" action="$form->action" target="event-contact-frame" method="post">
						<iframe name="event-contact-frame" id="event-contact-frame" style="display: none;"></iframe>
						<input type="hidden" name="id" value="$form->id">
						<input type="hidden" name="type" value="$form->type">
						<div class="field sender">
							<div class="wrap">
								<label for="sender">$form->sender_label</label>
								<input type="$form->sender_type" id="sender" name="sender" placeholder="$form->sender_placeholder" pattern="$form->sender_pattern" required>
								<p class="help">$form->sender_help</p>
							</div>
						</div>
						<div class="field message">
							<div class="wrap">
								<label for="message">$form->message_label</label>
								<textarea id="message" name="message" placeholder="$form->message_placeholder" required></textarea>
								<p class="help">$form->message_help</p>
							</div>
						</div>
						<div class="field buttons">
							<div class="wrap">
								<button class="button submit" type="submit"><span class="label">$form->submit_label</span></button>
								<button class="button cancel" type="button"><span class="label">$form->cancel_label</span></button>
							</div>
						</div>
					</form>
				</div>
EOT;
}
?>
			</div>
		</div><!--/.event#event-popup-<?php echo $entry->ID; ?>-->
	</div><!--/.event-body-->
<script>
meta.page = {
	url: '<?php echo esc_attr( get_permalink( $entry->ID ) ); ?>',
	title: '<?php echo esc_attr( $entry->post_title ); ?>',
	description: '<?php echo esc_attr( make_plaintext( apply_filters( 'the_content', $entry->post_content ) ) ); ?>',
	image: '<?php echo esc_attr( get_post_thumbnail_id( $entry->ID ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $entry->ID ), 'full' ) : '' ); ?>'
	//author: '<?php echo esc_attr( get_user_meta( $entry->post_author, 'display_name', true ) ); ?>',
	//author_twitter_account: ''
};
_change_social_meta( meta.page );


var smap;
function update_static_map(target,lat,lng){
	smap = {};
	jQuery('#event-map').each(function(index){
		smap = new daum.maps.StaticMap(
			document.getElementById(jQuery(this).attr('id')),
			{
				center: new daum.maps.LatLng( <?php echo "$entry->latitude, $entry->longitude"; ?> ),
				level: 3,
				mapTypeId: 'HYBRID',
				marker: {
					position: new daum.maps.LatLng( <?php echo "$entry->latitude, $entry->longitude"; ?> ),
					text: meta.page.title
				}
			}
		);
	});
}
jQuery(window).on('resize',function(e){
	update_static_map();
});
update_static_map();

_events = [<?php echo $_code_output; ?>];
_tooltips = {<?php echo $_tooltip_output; ?>};
var calendar = jQuery('#calendar').clndr({
	template: jQuery('#mini-clndr-template').html(),
	events: _events,
	clickEvents: {
		click: function(target){
			var $_date = target.date._i;
			var $_tooltip = _tooltips[$_date];
			console.log($_tooltip);
		}
	},
	doneRendering: function(){
		//console.log('clndr rendering complete.');
	}

});
jQuery('.event-detail .share a').on('click',function(e){
	e.preventDefault();
	var $this = jQuery(this);
	_open_external_popup( $this.attr('href') );
});
jQuery('.event-detail .button.to-contact').on('click',function(e){
	var container = jQuery('.fancybox-inner');
	var destination = jQuery('.event-contact');
	//destination.height( container.height() );
	_scroll_contact( destination.offset().top );
});
jQuery('.event-content img').each(function(index){
	var $this = jQuery(this);
	if( $this.width() > 300 ) {
		$this.appendTo( $this.closest('.event').find('.event-content-feature') );
		return true;
	}
});
jQuery('#message').on('keyup keypress keydown change',function(e){
	var $this = jQuery(this);
	jQuery('.message .counter').each(function(index){
		var $counter = get_capacity( $this.val() );
		jQuery(this).text( $counter );
		if( $counter > sms_capacity ){
			$this.addClass('error over-capacity');
		} else {
			$this.removeClass('error over-capacity');
		}
	});
});	
jQuery('#event-contact').on('submit',function(e){
	$this = jQuery(this);
	if( $this.find('.error').length ){
		e.preventDefault();
	} else if( $this.data('submitted') ) {
		e.preventDefault();
	} else {
		$this.data('submitted', true);
		jQuery('#event-contact .button.submit span.label').addClass('loading');
		$this.submit();
	}
});
jQuery('#event-contact .button.cancel').on('click',function(e){
	jQuery('#event-contact')[0].reset();
	_scroll_contact( 0 );
});
</script>

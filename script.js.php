<?php
define( 'WP_USE_THEMES', false );
require( dirname(__FILE__).'/../../../wp-blog-header.php' );
ob_start();
?>
/*
 * Plugin configurations
 */
jQuery.fn.datepicker.dates['ko_KR'] = {
	yearUnit: '<?php echo __( 'yearUnit', 'candlelights' ); ?>',
	months: [
		'<?php echo __( 'January', 'candlelights' ); ?>',
		'<?php echo __( 'Fabruary', 'candlelights' ); ?>',
		'<?php echo __( 'March', 'candlelights' ); ?>',
		'<?php echo __( 'April', 'candlelights' ); ?>',
		'<?php echo __( 'May', 'candlelights' ); ?>',
		'<?php echo __( 'June', 'candlelights' ); ?>',
		'<?php echo __( 'July', 'candlelights' ); ?>',
		'<?php echo __( 'August', 'candlelights' ); ?>',
		'<?php echo __( 'September', 'candlelights' ); ?>',
		'<?php echo __( 'October', 'candlelights' ); ?>',
		'<?php echo __( 'November', 'candlelights' ); ?>',
		'<?php echo __( 'December', 'candlelights' ); ?>'
	],
	monthsShort: [
		'<?php echo __( 'Jan', 'candlelights' ); ?>',
		'<?php echo __( 'Fab', 'candlelights' ); ?>',
		'<?php echo __( 'Mar', 'candlelights' ); ?>',
		'<?php echo __( 'Apr', 'candlelights' ); ?>',
		'<?php echo __( 'May', 'candlelights' ); ?>',
		'<?php echo __( 'Jun', 'candlelights' ); ?>',
		'<?php echo __( 'Jul', 'candlelights' ); ?>',
		'<?php echo __( 'Aug', 'candlelights' ); ?>',
		'<?php echo __( 'Sep', 'candlelights' ); ?>',
		'<?php echo __( 'Oct', 'candlelights' ); ?>',
		'<?php echo __( 'Nov', 'candlelights' ); ?>',
		'<?php echo __( 'Dec', 'candlelights' ); ?>'
	],
	days: [
		'<?php echo __( 'Sunday', 'candlelights' ); ?>',
		'<?php echo __( 'Monday', 'candlelights' ); ?>',
		'<?php echo __( 'Tuesday', 'candlelights' ); ?>',
		'<?php echo __( 'Wednesday', 'candlelights' ); ?>',
		'<?php echo __( 'Thursday', 'candlelights' ); ?>',
		'<?php echo __( 'Friday', 'candlelights' ); ?>',
		'<?php echo __( 'Saturday', 'candlelights' ); ?>'
	],
	daysShort: [
		'<?php echo __( 'Sun', 'candlelights' ); ?>',
		'<?php echo __( 'Mon', 'candlelights' ); ?>',
		'<?php echo __( 'Tue', 'candlelights' ); ?>',
		'<?php echo __( 'Wed', 'candlelights' ); ?>',
		'<?php echo __( 'Thu', 'candlelights' ); ?>',
		'<?php echo __( 'Fri', 'candlelights' ); ?>',
		'<?php echo __( 'Sat', 'candlelights' ); ?>'
	],
	daysMin: [
		'<?php echo __( 'Su', 'candlelights' ); ?>',
		'<?php echo __( 'Mo', 'candlelights' ); ?>',
		'<?php echo __( 'Tu', 'candlelights' ); ?>',
		'<?php echo __( 'We', 'candlelights' ); ?>',
		'<?php echo __( 'Th', 'candlelights' ); ?>',
		'<?php echo __( 'Fr', 'candlelights' ); ?>',
		'<?php echo __( 'Sa', 'candlelights' ); ?>'
	],
	today: '<?php echo __( 'Today', 'candlelights' ); ?>',
	clear: '<?php echo __( 'Clear', 'candlelights' ); ?>'
};
moment.lang('ko_KR',{
	months: [
		'<?php echo __( 'January', 'candlelights' ); ?>',
		'<?php echo __( 'Fabruary', 'candlelights' ); ?>',
		'<?php echo __( 'March', 'candlelights' ); ?>',
		'<?php echo __( 'April', 'candlelights' ); ?>',
		'<?php echo __( 'May', 'candlelights' ); ?>',
		'<?php echo __( 'June', 'candlelights' ); ?>',
		'<?php echo __( 'July', 'candlelights' ); ?>',
		'<?php echo __( 'August', 'candlelights' ); ?>',
		'<?php echo __( 'September', 'candlelights' ); ?>',
		'<?php echo __( 'October', 'candlelights' ); ?>',
		'<?php echo __( 'November', 'candlelights' ); ?>',
		'<?php echo __( 'December', 'candlelights' ); ?>'
	],
	monthsShort: [
		'<?php echo __( 'Jan', 'candlelights' ); ?>',
		'<?php echo __( 'Fab', 'candlelights' ); ?>',
		'<?php echo __( 'Mar', 'candlelights' ); ?>',
		'<?php echo __( 'Apr', 'candlelights' ); ?>',
		'<?php echo __( 'May', 'candlelights' ); ?>',
		'<?php echo __( 'Jun', 'candlelights' ); ?>',
		'<?php echo __( 'Jul', 'candlelights' ); ?>',
		'<?php echo __( 'Aug', 'candlelights' ); ?>',
		'<?php echo __( 'Sep', 'candlelights' ); ?>',
		'<?php echo __( 'Oct', 'candlelights' ); ?>',
		'<?php echo __( 'Nov', 'candlelights' ); ?>',
		'<?php echo __( 'Dec', 'candlelights' ); ?>'
	],
	weekdays: [
		'<?php echo __( 'Sunday', 'candlelights' ); ?>',
		'<?php echo __( 'Monday', 'candlelights' ); ?>',
		'<?php echo __( 'Tuesday', 'candlelights' ); ?>',
		'<?php echo __( 'Wednesday', 'candlelights' ); ?>',
		'<?php echo __( 'Thursday', 'candlelights' ); ?>',
		'<?php echo __( 'Friday', 'candlelights' ); ?>',
		'<?php echo __( 'Saturday', 'candlelights' ); ?>'
	],
	weekdaysShort: [
		'<?php echo __( 'Sun', 'candlelights' ); ?>',
		'<?php echo __( 'Mon', 'candlelights' ); ?>',
		'<?php echo __( 'Tue', 'candlelights' ); ?>',
		'<?php echo __( 'Wed', 'candlelights' ); ?>',
		'<?php echo __( 'Thu', 'candlelights' ); ?>',
		'<?php echo __( 'Fri', 'candlelights' ); ?>',
		'<?php echo __( 'Sat', 'candlelights' ); ?>'
	],
	weekdaysMin: [
		'<?php echo __( 'Su', 'candlelights' ); ?>',
		'<?php echo __( 'Mo', 'candlelights' ); ?>',
		'<?php echo __( 'Tu', 'candlelights' ); ?>',
		'<?php echo __( 'We', 'candlelights' ); ?>',
		'<?php echo __( 'Th', 'candlelights' ); ?>',
		'<?php echo __( 'Fr', 'candlelights' ); ?>',
		'<?php echo __( 'Sa', 'candlelights' ); ?>'
	],
	longDateFormat: {
		LT: '<?php echo get_option( 'time_format' ); ?>',
		L: '<?php echo get_option( 'date_format' ); ?>',
		l: '<?php echo get_option( 'date_format' ); ?>',
		LL: '<?php echo get_option( 'date_format' ); ?>',
		ll: '<?php echo get_option( 'date_format' ); ?>',
		LLL: '<?php echo get_option( 'date_format' ); ?>',
		lll: '<?php echo get_option( 'date_format' ); ?>',
		LLLL: '<?php echo get_option( 'date_format' ); ?>',
		llll: '<?php echo get_option( 'date_format' ); ?>'
	},
	meridiem: {
		am: '<?php echo __( 'am', 'candlelights' ); ?>',
		AM: '<?php echo __( 'AM', 'candlelights' ); ?>',
		pm: '<?php echo __( 'pm', 'candlelights' ); ?>',
		PM: '<?php echo __( 'PM', 'candlelights' ); ?>'
	},
	relativeTime: {
		future: '<?php echo __( 'in %s', 'candlelights' ); ?>',
		past: '<?php echo __( '%s ago', 'candlelights' ); ?>',
		s: '<?php echo __( 'seconds', 'candlelights' ); ?>',
		m: '<?php echo __( 'a minute', 'candlelights' ); ?>',
		mm: '<?php echo __( '%d minutes', 'candlelights' ); ?>',
		h: '<?php echo __( 'an hour', 'candlelights' ); ?>',
		hh: '<?php echo __( '%d hours', 'candlelights' ); ?>',
		d: '<?php echo __( 'a day', 'candlelights' ); ?>',
		dd: '<?php echo __( '%d days', 'candlelights' ); ?>',
		M: '<?php echo __( 'a month', 'candlelights' ); ?>',
		MM: '<?php echo __( '%d months', 'candlelights' ); ?>',
		y: '<?php echo __( 'a year', 'candlelights' ); ?>',
		yy: '<?php echo __( '%d years', 'candlelights' ); ?>'
	},
	calendar: {
		lastDay: '<?php echo __( '[Yesterday at] LT', 'candlelights' ); ?>',
		sameDay: '<?php echo __( '[Today at] LT', 'candlelights' ); ?>',
		nextDay: '<?php echo __( '[Tomorrow at] LT', 'candlelights' ); ?>',
		lastWeek: '<?php echo __( '[Last] dddd [at] LT', 'candlelights' ); ?>',
		nextWeek: '<?php echo __( 'dddd [at] LT', 'candlelights' ); ?>',
		sameElse: 'L'
	},
	ordinal : function (number, token) {
		var b = number % 10;
		var output = (~~ (number % 100 / 10) === 1) ? 'th' :
			(b === 1) ? 'st' :
			(b === 2) ? 'nd' :
			(b === 3) ? 'rd' : 'th';
		return number + output;
	},
	week : {
		dow : 0, // Sunday(0) is the first day of the week.
		doy : 1  // The week that contains Jan 1st is the first week of the year.
	}
});
moment.lang('__LANGUAGE__');
var iconOptions = {
	prefix: '__ICON_PREFIX__',
	suffix: {
		normal: '__ICON_SUFFIX_NORMAL__',
		hover: '__ICON_SUFFIX_HOVER__'
	},
	extension: '__ICON_EXTENSION__',
	entry: {
<?php
	foreach( get_terms( 'events_categories', array( 'hide_empty' => false ) ) as $category ){
		$icon = determine_icon($category->slug);
		ob_start();
		echo <<<EOT
		{$icon->basename}:{
			slug: '{$icon->slug}',
			width: 38,
			height: 53
		}
EOT;
		$entry[] = ob_get_contents();
		ob_end_clean();
	}
	echo implode( ','.PHP_EOL, $entry );
?>
	}
}
var icon = [];
var sms_capacity = __SMS_CAPACITY__;
<?php
$structure = ob_get_contents();
ob_end_clean();
$structure .= file_get_contents( dirname( __FILE__).'/script.js' );
$script = apply_defined_pattern_to_structure( $structure );
header( 'Content-type: text/javascript' );
echo $script;
?>

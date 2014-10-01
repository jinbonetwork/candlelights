<?php
if( !defined( 'ABSPATH' ) ) die( 'do not access this file directly' );

setlocale( LC_ALL, WP_LANG );
$timezone_backup = date_default_timezone_get();
date_default_timezone_set( get_option( 'timezone_string' ) );

$_defaults = array(
	'init' => 1,
	'init_lat' => 36.6935247,
	'init_lng' => 127.6301252,
	'init_level' => 12,
	'sw_lat' => 29.282442238789592,
	'sw_lng' => 125.19125734240878,
	'ne_lat' => 38.126448380480554,
	'ne_lng' => 136.66307174360804,
	'level' => 12,
	'ymd' => date('Y-m-d'),
	'today_only' => 0,
	'category' => 0, // 0: all
	'keyword' => '',
	'auto_search' => 1,
	'page' => 1,
);
$_attributes = array_intersect_key( array_merge( $_defaults, $_GET ), $_defaults );
$_attributes[init] = $_GET[sw_lat] ? 0 : $_attributes[init];
foreach( $_attributes as $key => $value ){
	define( strtoupper( $key ), urldecode( $value ) );
}

$today = mktime(0, 0, 0, date("m")  , date("d"), date("Y")); 
$theday = mktime(0, 0, 0, 4, 16, 2014); 
$counter = ($today - $theday) / (60 * 60 * 24);

define( 'YMD_TODAY', date('Y-m-d') );
define( 'DAY_COUNTER', $counter);
define( 'TODAY_YEAR', date('Y') );
define( 'TODAY_MONTH', date('n') );
define( 'TODAY_DAY', date('j') );
define( 'TODAY_WEEKDAY', date('D') );

define( 'SITE_URL', get_option('home') );
define( 'BASEURL', SITE_URL.'/' );

define( 'TEMPLATEDIR', get_stylesheet_directory_uri() );
define( 'LISTURL', TEMPLATEDIR.'/ajax-list.php' );
define( 'DETAILURL', TEMPLATEDIR.'/ajax-detail.php' );

define( 'SITE_TITLE', get_option('blogname') );
define( 'SITE_DESCRIPTION', get_option('blogdescription') );
define( 'SITE_IMAGE', TEMPLATEDIR.'/images/title.svg' );
define( 'SITE_CATEGORY', '정치' );
define( 'SITE_AUTHOR', get_user_meta( 1, 'display_name', true ) );
define( 'SITE_AUTHOR_TWITTER_ACCOUNT', '' );

define( 'SEARCH_LEVEL', 5 );
define( 'POPUP_LEVEL', 5 );
define( 'ANIMATION_MODE_DURATION', 0.2 ); // in seconds
define( 'ANIMATION_SCROLL_DURATION', 0 ); // in seconds

define( 'ICON_SLUG_DEFAULT', 'default' );
define( 'ICON_PREFIX', 'pin_' );
define( 'ICON_SUFFIX_NORMAL', '_normal' );
define( 'ICON_SUFFIX_HOVER', '_hover' );
define( 'ICON_EXTENSION', '.png' );

define( 'FANCYBOX_WIDTH', 800 );
define( 'FANCYBOX_HEIGHT', 1200 );
define( 'MOBILE_WIDTH_POINT', 950 );
define( 'MOBILE_HEIGHT_POINT', 50 );

define( 'DAUM_MAP_APIKEY', '' ); // YOUR DAUM MAP API KEY
define( 'DAUM_MAP_ENDPOINT', 'http://apis.daum.net/maps/maps3.js' );
define( 'DAUM_LOCAL_APIKEY', '' ); // YOUR DAUM LOCAL API KEY
define( 'DAUM_LOCAL_ENDPOINT', 'http://apis.daum.net/local/geo' );

define( 'MAIL_OUTBOUND_SMTP_AUTH', true );
define( 'MAIL_OUTBOUND_SMTP_SECURE', 'tls' );
define( 'MAIL_OUTBOUND_HOST', 'smtp.gmail.com' );
define( 'MAIL_OUTBOUND_PORT', '587' );
define( 'MAIL_OUTBOUND_ID', '' ); // YOUR GOOGLE MAIL ADDRESS
define( 'MAIL_OUTBOUND_PASSWORD', '' ); // YOUR GOOGLE MAIL PASSWORD

define( 'SMS_OUTBOUND_HOST', 'http://www.nesolution.com/service/sms.aspx' ); // YOUR NESOLUTION SMS SERVICE ENDPOINT
define( 'SMS_OUTBOUND_ID', '' ); // YOUR NESOLUTION SMS SERVICE NUMERIC ID
define( 'SMS_OUTBOUND_PASSWORD', '' ); // YOUR NESOLUTION SMS SERVICE PASSWORD
define( 'SMS_CAPACITY', 80 ); // cannot change

define( 'POSTS_PER_PAGE', 100 ); // false: all, numeric: limit
define( 'DEVEL_CAPABILITY', 'edit_theme_options' ); // for debu
define( 'EDITOR_CAPABILITY', 'edit_others_posts' ); // for permission check
define( 'CONTACT_EMAIL_PATTERN', '[^ @]+@[^ @]+' );
define( 'CONTACT_PHONE_PATTERN', '[0-9 \-]+' );

define( 'LANGUAGE', WPLANG ? WPLANG : 'en_US' );
define( 'ALT_DATE_FORMAT', 'n월 j일' );

if( file_exists( TEMPLATEPATH.'/address_filter.txt' ) ){
	global $address_filter;
	$address_filter = (object) array();
	$address_filter->txt = file_get_contents( TEMPLATEPATH.'/address_filter.txt' );
	$address_filter->lines = array_filter(explode( "\n", $address_filter->txt ), 'trim');
	foreach( $address_filter->lines as $line ){
		list( $pattern, $replace ) = explode('=',$line);
		$address_filter->patterns[$pattern] = $replace;
	}
}

date_default_timezone_set($timezone_backup);
?>

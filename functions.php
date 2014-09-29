<?php
if( !defined( 'ABSPATH' ) ) die( 'do not access this file directly' );

require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/functions.utilities.php';
require_once dirname(__FILE__).'/libraries/map.php';
require_once dirname(__FILE__).'/functions.plugin.event-property.php';
require_once dirname(__FILE__).'/functions.plugin.fix-permalink.php';

function candlelights_init(){
	// add new tables
	global $wpdb, $table_prefix;
	$wpdb->events = $table_prefix.'ai1ec_events';
	$wpdb->instances = $table_prefix.'ai1ec_event_instances';

	// hide frontend adminbar
	show_admin_bar( false );

	// enable two menus
	register_nav_menus(array(
		'menu-navigation' => __( 'Header menu.', 'candlelights' ),
		'menu-notices' => __( 'Notice links next to the list title.', 'candlelights' ),
	));
}
add_action( 'init', 'candlelights_init' );

function candlelights_after_setup_theme(){
	load_theme_textdomain( 'candlelights', get_template_directory().'/languages' );
}
add_action( 'after_setup_theme', 'candlelights_after_setup_theme' );

function candlelights_login_enqueue_scripts(){
	wp_enqueue_style( 'candlelights-admin-style', get_stylesheet_directory_uri().'/style.login.css' );
}
add_action( 'login_enqueue_scripts', 'candlelights_login_enqueue_scripts' );

function candlelights_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'candlelights_login_logo_url' );

function candlelights_login_logo_url_title() {
    return get_option('title');
}
add_filter( 'login_headertitle', 'candlelights_login_logo_url_title' );

function candlelights_admin_enqueue_scripts(){
	if( !current_user_can( EDITOR_CAPABILITY ) ) {
		wp_enqueue_style( 'candlelights-admin-style-hide-ui', get_stylesheet_directory_uri().'/style.admin.hide-ui.css' );
		//wp_enqueue_script( 'candlelights-admin-script-disable-dragging', get_stylesheet_directory_uri().'/script.admin.disable-dragging.js', array('jquery') );
		wp_enqueue_script( 'candlelights-admin-script-force-closing', get_stylesheet_directory_uri().'/script.admin.force-closing.js', array('jquery') );
	}
	wp_enqueue_style( 'candlelights-admin-style', get_stylesheet_directory_uri().'/style.admin.css' );
	//wp_enqueue_script( 'candlelights-admin-script', get_stylesheet_directory_uri().'/script.admin.js', array('jquery') );
}
add_action( 'admin_enqueue_scripts', 'candlelights_admin_enqueue_scripts' );

function candlelights_enqueue_libraries(){
	wp_enqueue_style( 'bootstrap-style', get_stylesheet_directory_uri().'/contrib/bootstrap/css/bootstrap.min.css' );
	wp_enqueue_style( 'datepicker-style', get_stylesheet_directory_uri().'/contrib/bootstrap-datepicker/css/datepicker.css' );
	wp_enqueue_style( 'fancybox-style', get_stylesheet_directory_uri().'/contrib/fancybox/source/jquery.fancybox.css' );
	wp_enqueue_style( 'foundation-icons-style', get_stylesheet_directory_uri().'/contrib/foundation-icons/foundation-icons.css' );
	wp_enqueue_style( 'social-icons-style', get_stylesheet_directory_uri().'/resources/social.css' );

	wp_enqueue_style( 'candlelights-style', get_stylesheet_directory_uri().'/style.css.php' );

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'bootstrap-script', get_stylesheet_directory_uri().'/contrib/bootstrap/js/bootstrap.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'datepicker-script', get_stylesheet_directory_uri().'/contrib/bootstrap-datepicker/js/bootstrap-datepicker.js', array( 'bootstrap-script' ) );
	wp_enqueue_script( 'fancybox-script', get_stylesheet_directory_uri().'/contrib/fancybox/source/jquery.fancybox.pack.js', array( 'jquery' ) );
	wp_enqueue_script( 'moment-script', get_stylesheet_directory_uri().'/contrib/moment/moment.js' );
	wp_enqueue_script( 'underscore-script', get_stylesheet_directory_uri().'/contrib/underscore/underscore-min.js' );
	wp_enqueue_script( 'clndr-script', get_stylesheet_directory_uri().'/contrib/clndr/clndr.min.js', array( 'jquery', 'moment-script', 'underscore-script' ) );
	//wp_enqueue_script( 'isloading-script', get_stylesheet_directory_uri().'/contrib/isloading/jquery.isloading.min.js', array('jquery') );

	wp_enqueue_script( 'daum-map-script', DAUM_MAP_ENDPOINT . '?apikey='.DAUM_MAP_APIKEY );

	wp_enqueue_script( 'candlelights-script', get_stylesheet_directory_uri().'/script.js.php', array( 'jquery' ) );

}
add_action( 'wp_enqueue_scripts', 'candlelights_enqueue_libraries' );

/*
function candlelights_admin_header() {
	require_once( dirname( __FILE__ ) . '/header.php' );
}
add_action( 'admin_head', 'candlelights_header' );

function candlelights_footer() {
	require_once( dirname( __FILE__ ) . '/footer.php' );
}
add_action( 'admin_footer', 'candlelights_footer' );
*/

function candlelights_head(){
	ob_start();
?>
<script>
/*
* prepare default setting object
*/
var query = {
	init: __INIT__,
	init_lat: __INIT_LAT__,
	init_lng: __INIT_LNG__,
	init_level: __INIT_LEVEL__,

	sw_lat: __SW_LAT__,
	sw_lng: __SW_LNG__,
	ne_lat: __NE_LAT__,
	ne_lng: __NE_LNG__,
	level: __LEVEL__,

	ymd: '__YMD__',
	today_only: __TODAY_ONLY__,

	category: __CATEGORY__,
	keyword: '__KEYWORD__',

	search_level: __SEARCH_LEVEL__,
	auto_search: __AUTO_SEARCH__,
	baseurl: '__BASEURL__',
	listurl: '__LISTURL__',
	detailurl: '__DETAILURL__',

	popup_detail: '__POPUP_DETAIL__',
	popup_address: '__POPUP_ADDRESS__',
	popup_level: __POPUP_LEVEL__,
	popup_lat: __POPUP_LAT__,
	popup_lng: __POPUP_LNG__
};
var meta = {
	site: {
		url: '<?php echo esc_attr( SITE_URL ); ?>',
		title: '<?php echo esc_attr( SITE_TITLE ); ?>',
		description: '<?php echo esc_attr( SITE_DESCRIPTION ); ?>',
		image: '<?php echo esc_attr( SITE_IMAGE ); ?>',
		author: '<?php echo esc_attr( SITE_AUTHOR ); ?>',
		author_twitter_account: '<?php esc_attr( SITE_AUTHOR_TWITTER_ACCOUNT ); ?>'
	}
};
var address_changable = __ADDRESS_CHANGABLE__;
</script>
<?php
	$structure = ob_get_contents();
	ob_end_clean();
	$script = apply_defined_pattern_to_structure( $structure );
	echo $script;
}
add_action( 'wp_head', 'candlelights_head', 1 );



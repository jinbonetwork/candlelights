<?php
define( 'WP_USE_THEMES', false );
require( dirname( __FILE__ ) . '/../../../wp-blog-header.php' );
header('Content-Type: application/javascript');

$keyword = $_GET[keyword];
$ajax = get_geocode( $keyword, 'object' );
if( $ajax ) {
	echo json_encode($ajax);
}

?>

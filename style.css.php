<?php
define( 'WP_USE_THEMES', false );
require( dirname(__FILE__).'/../../../wp-blog-header.php' );
$structure .= file_get_contents( dirname( __FILE__).'/style.css' );
$script = apply_defined_pattern_to_structure( $structure );
header( 'Content-type: text/css' );
echo $script;
?>

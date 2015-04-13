<?php
if( is_singular() ) {
	global $post;
	$event = get_event( $post->ID );
	define( 'POPUP_DETAIL', DETAILURL . '?id=' . $event->ID );
	define( 'POPUP_ADDRESS', get_permalink( $event->ID ) );
	if( $event->latitude ) define( 'POPUP_LAT', $event->latitude );
	if( $event->longitude ) define( 'POPUP_LNG', $event->longitude );
	define( 'ADDRESS_CHANGABLE', 0 );

	define( 'META_TITLE', $event->post_title );
	define( 'META_DESCRIPTION', make_plaintext(wptexturize($event->post_content)));
	define( 'META_URL', get_permalink( $event->ID ) );
	define( 'META_IMAGE', get_post_thumbnail_id( $event->ID ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $event->ID ), 'full' ) : '' );
	//define( 'META_AUTHOR', get_user_meta( $event->post_author, 'display_name', true ) );
	//define( 'META_AUTHOR_TWITTER_ACCOUNT', SITE_AUTHOR_TWITTER_ACCOUNT );
}	
@define( 'POPUP_MODE', 'iframe' );
@define( 'POPUP_DETAIL', '' );
@define( 'POPUP_ADDRESS', '' );
@define( 'POPUP_LAT', 0 );
@define( 'POPUP_LNG', 0 );
@define( 'ADDRESS_CHANGABLE', 1 );

@define( 'META_CATEGORY', SITE_CATEGORY );
@define( 'META_TITLE', SITE_TITLE );
@define( 'META_DESCRIPTION', SITE_DESCRIPTION );
@define( 'META_URL', SITE_URL );
@define( 'META_IMAGE', SITE_IMAGE );
@define( 'META_AUTHOR', SITE_AUTHOR );
@define( 'META_AUTHOR_TWITTER_ACCOUNT', SITE_AUTHOR_TWITTER_ACCOUNT );

if( POPUP_MODE != 'ajax' ) {
?><!DOCTYPE html>
<html>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width,user-scalable=0,initial-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black"><!-- default, black, black-translucent -->
	<link rel="apple-touch-icon-precomposed" href="">
	<link rel="apple-touch-startup-image" href="">
	<title><?php echo esc_attr( META_TITLE ); ?></title>
	<meta name="description" content="<?php echo esc_attr( META_DESCRIPTION ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( META_TITLE ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( META_TITLE ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( META_DESCRIPTION ); ?>">
	<meta property="og:url" content="<?php echo esc_attr( META_URL ); ?>">
	<meta property="og:image" content="<?php echo META_IMAGE; ?>">
	<meta property="og:author" content="<?php echo META_AUTHOR; ?>">
	<meta property="og:type" content="Article">
	<meta property="og:section" content="<?php echo esc_attr( META_CATEGORY ); ?>">
	<meta name="twitter:title" content="<?php echo esc_attr( META_TITLE ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( META_DESCRIPTION ); ?>">
	<meta name="twitter:domain" content="<?php echo esc_attr( META_URL ); ?>">
	<meta name="twitter:image:src" content="<?php echo META_IMAGE; ?>">
	<meta name="twitter:creator" content="<?php echo META_AUTHOR; ?>">
	<meta name="twitter:site" content="<?php echo META_AUTHOR_TWITTER_ACCOUNT ? '@' . META_AUTHOR_TWITTER_ACCOUNT : ''; ?>">
	<meta name="twitter:card" content="summary">
<?php wp_head(); ?>
<body <?php body_class(); ?>>
<?php
}
?>

<?php
if( in_array( get_post_type(), array( 'post', 'page' ) ) ){
	require_once dirname(__FILE__).'/iframe-post.php';
} else {
	require_once dirname(__FILE__).'/index.php';
}
?>

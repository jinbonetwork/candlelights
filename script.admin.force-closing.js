jQuery(document).ready(function(e){
	var $ref = document.referrer;
	if( $ref.search(/wp\-admin/i) != -1 && $ref.search(/(edit|post)\.php/i) == -1 ) {
		parent.jQuery.fancybox.close( true );
	}
});

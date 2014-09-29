<?php
get_header();
?>
<div id="frame">

	<div id="header">
		<div id="banner" role="banner">
			<h1 class="site-title"><a href="<?php echo esc_url( SITE_URL ); ?>" rel="home"><?php echo SITE_TITLE; ?></a></h1>
			<p class="site-description"><?php echo SITE_DESCRIPTION; ?></p>
		</div><!--/#banner-->
		<div id="navigation" role="navigation">
<?php
	
	$menu_navigation = wp_nav_menu( array( 'echo' => false, 'theme_location' => 'menu-navigation', 'container' => false, 'menu_id' => 'menu-navigation', 'menu_class' => 'menu', 'fallback_cb' => false ) );
	$menu_navigation = str_replace( '<a ', '<a class="overlay" ', $menu_navigation );
	echo $menu_navigation;
	echo '<ul id="menu-user" class="menu">' . PHP_EOL;
	echo '<li class="contribute"><a class="overlay" href="' . admin_url( 'post-new.php?post_type=ai1ec_event' ) . '">' . __( 'Add new event', 'candlelights' ) . '</a></li>' . PHP_EOL;
	if( is_user_logged_in() ) {
		echo '<li class="cabinet"><a class="overlay" href="' . admin_url( 'edit.php?post_type=ai1ec_event' ) . '">' . __( 'Manage events', 'candlelights' ) . '</a></li>' . PHP_EOL;
		echo '<li class="profile"><a class="overlay" href="' . admin_url( 'profile.php' ) . '">' . __( 'Edit profile', 'candlelights' ) . '</a></li>' . PHP_EOL;
		echo '<li class="logout"><a href="' . wp_logout_url( '/' ) . '">' . __( 'Logout', 'candlelights' ) . '</a></li>' . PHP_EOL;
	} else {
		echo '<li class="login"><a href="' . wp_login_url( '/' ) . '">' . __( 'Login', 'candlelights' ) . '</a></li>' . PHP_EOL;
	}
	echo '</ul>' . PHP_EOL;
?>
		</div><!--/#navigation-->
	</div><!--/#header-->

	<div id="feature">
		<div id="map" role="feature"></div><!--/#map-->
		<div id="zoom-box" class="map-control">
			<ul>
				<li><a class="button zoom in" href="javascript://"><span><?php _e( 'Zoom in', 'candlelights' ); ?></span></a></li>
				<li><a class="button zoom out" href="javascript://"><span><?php _e( 'Zoom out', 'candlelights' ); ?></span></a></li>
			</ul>
		</div><!--/#zoom-box-->
		<div id="locate-here" class="map-control"><a class="button locate-here" href="javascript://"><span><?php _e( 'Locate here', 'candlelights' ); ?></span></a></div><!--/#locate-here-->
		<div id="search-box" class="map-control">
			<form id="search">
				<label for="keyword"><?php _e( 'Search locations', 'candlelights' ); ?></label>
				<input type="search" name="keyword" id="keyword" placeholder="<?php _e( 'Search street names', 'candlelights' ); ?>" value="<?php echo esc_attr( urldecode( $_GET[keyword] ) ); ?>" autocomplete="off">
				<button type="submit"><?php _e( 'Submit', 'candlelights' ); ?></button>
			</form>
			<div id="search-results"></div><!--/#search-results-->
			<div id="auto-search">
				<input type="checkbox" name="auto_search" id="auto_search" value="1">
				<label for="auto_search"><?php _e( 'Auto search while map navigation', 'candlelights' ); ?></label>
			</div>
		</div><!--/#search-box-->
	</div><!--/#feature-->

	<div id="body">
		<div id="mode-toggler"><a class="mode-toggler" href="javascript://"><span class="mode-map"><?php _e( 'View event list', 'candlelights' ); ?></span><span class="mode-list"><?php _e( 'View map', 'candlelights' ); ?></span></a></div>
		<div id="console" role="navigation">
			<div id="category-selector" class="console-item">
				<label for="category"><?php _e('All categories','candlelights'); ?></label>
				<select id="category" name="category">
<?php
	$default_category = (object) array( 'term_id' => 0, 'name' => __( 'All categories', 'candlelights' ) );
	$categories = array_merge( array( $default_category ), (array) get_terms('events_categories',array('hide_empty'=>false) ) );
	foreach($categories as $category ){
		$checked = ($_GET[category]==$category->term_id)?'checked="checked"':'';
		echo '<option value="' . $category->term_id . '"' . $checked . '>' . $category->name . '</option>' . PHP_EOL;
	}
?>
				</select>
			</div>
			<div id="ymd-picker" class="console-item">
				<input type="text" name="ymd" id="ymd" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true" data-date-clear-btn="false">
			</div>
			<div id="today-only-switch" class="console-item">
				<input type="checkbox" name="today_only" id="today_only">		
				<label for="today_only"><?php _e( 'Today only', 'candlelights' ); ?></label>
			</div>
			<div id="share-this-site" class="share console-item">
				<h3><?php _e( 'Share this site', 'candlelights' ); ?></h3>
				<ul>
					<li class="share twitter"><a href="https://twitter.com/share?text=<?php echo urlencode( SITE_TITLE ); ?>&amp;url=<?php echo urlencode( SITE_URL ); ?>"><span><?php _e( 'Share on Twitter', 'candlelights' ); ?></span></a></li>
					<li class="share facebook"><a href="https://facebook.com/sharer.php?u=<?php echo urlencode( SITE_URL ); ?>"><span><?php _e( 'Share on Facebook', 'candlelights' ); ?></span></a></li>
					<li class="share googleplus"><a href="https://plus.google.com/share?url=<?php echo urlencode( SITE_URL ); ?>"><span><?php _e( 'Share on Google+', 'candlelights' ); ?></span></a></li>
				</ul>
			</div>
		</div><!--/#console-->
		<div id="main-header">
			<h2><?php _e( 'Event search results', 'candlelights' ); ?></h2>
<?php
	$menu_notices = wp_nav_menu( array( 'echo' => false, 'theme_location' => 'menu-notices', 'container' => false, 'menu_id' => 'menu-notices', 'menu_class' => 'menu', 'fallback_cb' => false ) );
	$menu_notices = str_replace( '<a ', '<a class="overlay" ', $menu_notices );
	echo $menu_notices;
?>
		</div>
		<div id="main-console"></div>
		<div id="main" role="main">
			<!-- AJAX contents -->
		</div><!--/#main-->
<?php
	$share_url = SITE_URL;
	$share_text = SITE_TITLE . ' - ' . SITE_DESCRIPTION;
?>
	</div><!--/#body-->

	<div id="footer" role="contentinfo">
		<div class="cornerstone">
			<span class="powered">Powered by <a href="http://jinbo.net/support/">JINBO.NET</a></span>
			<span class="since">2014</span>
			<span class="join"><?php printf( __('IT volunteers will be welcomed %s.','candlelights'), '<a href="http://github.com/jinbonet">' . __('here','candlelights') . '</a>' ); ?></span>
		</div><!--/.site-info-->
	</div><!--/#footer -->
</div><!--/#frame-->
<div id="loading-events"><span class="loading"><?php _e( 'Loading events...', 'candlelights' ); ?></span></div>
<?php
	get_footer();
?>

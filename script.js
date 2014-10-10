function get_capacity(str){
	var result=0;
	if(str==null){
		return 0;
	}
	for(var i=0;i<str.length;i++){
		var c=escape(str.charAt(i));
		if(c.length==1){
			result++;
		}else if(c.indexOf("%u")!=-1){
			result+=2;
		}else if(c.indexOf("%")!=-1){
			result+=c.length/3;
		}
	}
	return result;
}
function _open_external_popup( url, target, options ){
	target = target || 'popupWindow';
	options = options || 'width=500,height=250,scrollbars=no';
	window.open( url, target, options );
}
function _change_social_meta( attr ){
	for( key in attr ){
		switch( key ) {
			case 'title':
				jQuery('title').text(attr[key]);
				jQuery('meta[property="og:site_name"]').attr('content',attr[key]);
				jQuery('meta[property="og:title"]').attr('content',attr[key]);
				jQuery('meta[name="twitter:title"]').attr('content',attr[key]);
				break;
			case 'description':
				jQuery('meta[name="description"]').attr('content',attr[key]);
				jQuery('meta[property="og:description"]').attr('content',attr[key]);
				jQuery('meta[name="twitter:description"]').attr('content',attr[key]);
				break;
			case 'url':
				jQuery('meta[property="og:url"]').attr('content',attr[key]);
				jQuery('meta[name="twitter:domain"]').attr('content',attr[key]);
				break;
			case 'image':
				jQuery('meta[property="og:image"]').attr('content',attr[key]);
				jQuery('meta[name="twitter:image:src"]').attr('content',attr[key]);
				break;
		}
	}
}
function _change_address( new_address ){
	if( address_changable ) {
		new_address = new_address || query._address();
		history.pushState( null, null, new_address );
	}
}
function _scroll_contact( destination ){
	var target = jQuery('.fancybox-inner');
	destination = destination || 0;
	target.animate({
		scrollTop: destination
	},__ANIMATION_SCROLL_DURATION__ * 1000);
}
function _check_contact( result_raw, result_i18n ){
	result_i18n = result_i18n || result_raw;
	alert( result_i18n );
	jQuery('#event-contact').data('submitted',false);
	switch( result_raw ){
		case 'ID is missing.':
		break;
		case 'Host contact is missing.':
		break;
		case 'Sender contact is required.':
			jQuery('#sender').focus();
		break;
		case 'Sender contact is invalid.':
			jQuery('#sender').focus();
		break;
		case 'Message is required.':
			jQuery('#message').focus();
		break;
		case 'Sending complete.':
			jQuery('#event-contact')[0].reset();
			_scroll_contact( 0 );
		break;
		default:
		break;
	}
	jQuery('#event-contact .button.submit span.label').removeClass('loading');
}

var page = 0;
var mode = 'refresh';
var marker = {};
var tooltip = [];
var master = true;
var thread = 0;

jQuery(document).ready(function(e){
	var $window = jQuery(window);
	var $body = jQuery('body');
	var $header = jQuery('#header');
	function _redraw(){
		var _width = $window.width();
		var _height = $window.height();
		if($window.width()>__MOBILE_WIDTH_POINT__) {
			jQuery('body').removeClass('mode-list');
			jQuery('body').removeClass('mobile');
			jQuery('#feature,#map').css({
				width: ( _width / 2 ) + 'px',
				height: ( _height - $header.height() ) + 'px'
			});
			jQuery('#body').css({
				marginTop: $header.height() + 'px',
				marginLeft: ( _width / 2 ) + 'px'
			});
			jQuery('#console').css({
				left: ( _width / 2 ) + 'px'
			});
		} else {
			jQuery('body').addClass('mobile');
			jQuery('#feature,#map').css({
				width: _width + 'px',
				height: ( _height - $header.height() - __MOBILE_HEIGHT_POINT__ ) + 'px'
			});
			if(!$body.hasClass('mode-list')) {
				jQuery(document).scrollTop(0);
				jQuery('#body').css({
					marginTop: ( _height - __MOBILE_HEIGHT_POINT__ ) + 'px',
					marginLeft: '0px'
				});
			}
			jQuery('#console').css({
				left: '0px'
			});
		}
		jQuery('#body').css({
			minHeight: _height
		});
	}

	function _resize(){ /* refresh size of map and frame */
		_redraw();
		map.relayout(); // send resize signal to the map object
	}

	function _mode(){
		if( !jQuery('body').hasClass('mobile') ){
			return;
		}
		var _width = $window.width();
		var _height = $window.height();
		jQuery('body').scrollTop(0);
		jQuery('body').toggleClass('mode-list');
		if( jQuery('body').hasClass('mode-list') ){
			jQuery('#body').css({
				marginTop: $header.height()
			});
		} else {
			jQuery('#body').css({
				marginTop: ( _height - __MOBILE_HEIGHT_POINT__ )
			});
		}
	}

	function _move(){ /* refresh map */
		//map.setCenter( new daum.maps.LatLng( query.lat, query.lng ) );
		map.setLevel( query.level );
		map.setBounds( new daum.maps.LatLngBounds(
			new daum.maps.LatLng( query.sw_lat, query.sw_lng ),
			new daum.maps.LatLng( query.ne_lat, query.ne_lng )
		));
	}

	function _panTo( lat, lng, address, level ) {
		var address = address || '';
		var level = level || __SEARCH_LEVEL__;
		map.panTo( new daum.maps.LatLng( lat, lng ) );
		map.setLevel( level );
		map.relayout();
	}

	function _check(){ /* check map bounds */
		if( query.init ){
			query.init = false;
		} else {
			query.popup_lat = false;
			query.popup_lng = false;
		}
		query.sw_lat = map.getBounds().getSouthWest().getLat();
		query.sw_lng = map.getBounds().getSouthWest().getLng();
		query.ne_lat = map.getBounds().getNorthEast().getLat();
		query.ne_lng = map.getBounds().getNorthEast().getLng();
		query.level = map.getLevel();
	}

	function _load(){ /* access database, refresh entries and markers */
		thread++;
		if(thread>1) {
			thread--;
			return;
		}
		$body = jQuery('body');

		$body.addClass('loading');
		var q = query._url();
		page = page < 1 ? 1 : page;
		q += 'page=' + page;

		jQuery.ajax( q )
			.done(function(data){
				jQuery('body').removeClass('loading');
			})
			.error(function(data){
				jQuery('#main-console .error').html(data);
			})
			.complete(function(data){
				var markup = data.responseText;

				// 0. apply new markup
				if( mode == 'refresh' ){
					jQuery('#main').html(markup);
				} else if( mode == 'append' ){
					jQuery('.event-more').remove();
					jQuery('#main').append(markup);
					mode = 'refresh';
				}

				// 1. update address bar
				_change_address();

				// 2. clean markers
				for( eid in marker ) {
					marker[eid].setMap(null);
				}
				marker = [];

				// 3. clean tooltips
				for( eid in tooltip ) {
					tooltip[eid].setMap(null);
				}
				tooltip = [];

				// 4. bind listeners to events
				jQuery('.event').each(function(index){
					var $this = jQuery(this);
					$this.eid = $this.attr('id');
					$this.lat = $this.attr('data-latitude');
					$this.lng = $this.attr('data-longitude');
					$this.link = $this.attr('data-permalink');
					$this.detail = $this.attr('data-detail');
					$this.category = $this.attr('data-category');
					$this.icon = {
						normal: icon[$this.attr('data-icon') + icons.suffix.normal],
						hover: icon[$this.attr('data-icon') + icons.suffix.hover],
					};

					marker[$this.eid] = new daum.maps.Marker({ position: new daum.maps.LatLng( $this.lat, $this.lng ), image: $this.icon.normal });
					marker[$this.eid].setMap(map);
					marker[$this.eid].eid = $this.eid;
					marker[$this.eid].lat = $this.lat;
					marker[$this.eid].lng = $this.lng;
					marker[$this.eid].link = $this.link;
					marker[$this.eid].detail = $this.detail;

					daum.maps.event.addListener(marker[$this.eid],'click',function(){
						_open_overlay( 'ajax', this.detail, this.link );
					});
					daum.maps.event.addListener(marker[$this.eid],'mouseover',function(){
						marker[$this.eid].setImage( $this.icon.hover );
						marker[$this.eid].setZIndex( 999999999 );
						$this.addClass('hover');
						jQuery('body').not('.mobile').animate({
							scrollTop:$this.offset().top - jQuery('#header').height()
						},__ANIMATION_SCROLL_DURATION__ * 1000);
					});
					daum.maps.event.addListener(marker[$this.eid],'mouseout',function(){
						marker[$this.eid].setImage( $this.icon.normal );
						marker[$this.eid].setZIndex( 1 );
						$this.removeClass('hover');
					});
					if( $this.hasClass('page-'+page) ){
						$this.hover(
							function(e){
								marker[$this.eid].setImage( $this.icon.hover );
								marker[$this.eid].setZIndex( 999999999 );
								$this.addClass('hover');
							},
							function(e){
								marker[$this.eid].setImage( $this.icon.normal );
								marker[$this.eid].setZIndex( 1 );
								$this.removeClass('hover');
							}
						);
						$this.find('.event-title a').on('click',function(e){
							e.preventDefault();
						});
						$this.on('click',function(e){
							e.preventDefault();
							_open_overlay( 'ajax', $this.detail, jQuery(this).find('.event-title a').attr('href') );
						});
						/*
						$this.on('touchstart',function(e){
							e.preventDefault();
							_open_overlay( 'ajax', $this.detail, jQuery(this).attr('href') );
						});
						*/
						$this.find('.event-content img').each(function(index){
							if( jQuery(this).width() > 300 ) {
								jQuery(this).appendTo( $this.find('.event-content-feature') );
								return true;
							}
						});
					}
					jQuery('.event-more a').on('click',function(e){
						if( page == jQuery(this).attr('data-page') ) {
							//jQuery(this).addClass('loading');
							page += 1;
							mode = 'append';
							_update();
						}
					});
				});
			});
		thread--;
	}
	function _update(){
		_check();
		master = true;
		if($body.hasClass('mode-list')||$body.hasClass('has-fancybox')||$body.hasClass('form-active')) {
			master = false;
		}
		if( query.auto_search && master == true ) {
			_load();
		}
	}
	function _check_fancybox( trigger ){
		var box = jQuery('body');
		if( trigger ){
			box.addClass('has-fancybox');
		} else {
			box.removeClass('has-fancybox');
		}
	}
	function _open_overlay( _type, _detail, _address ){
		_type = _type || 'iframe';
		_address = _address || false;

		if( query.popup_lat ) {
			map.setLevel( query.popup_level );
			map.setCenter( new daum.maps.LatLng( query.popup_lat, query.popup_lng ) );
		}
		jQuery.fancybox.open({
			href: _detail,
			type: _type,
			width: __FANCYBOX_WIDTH__,
			height: __FANCYBOX_HEIGHT__,
			autoSize: false,
			autoResize: true,
			autoCenter: true,
			fitToView: true,
			margin: 0,
			padding: 0,
			closeBtn: true,
			wrapCSS: 'event-detail-box',
			afterLoad: function(){
				_check_fancybox( true );
				if( _address ) {
					_change_address( _address );
				}
			},
			afterClose: function(){
				_check_fancybox( false );
				address_changable = 1;
				if( _address ) {
					_change_address( query._address() );
					_change_social_meta( meta.site );
				}
				if( this.type == 'iframe' ) {
					_load();
				}
			}
		});
	}

	query._attributes = function(){
		var a = '';
		for( k in query ){
			if(
				k != 'listurl' && k != 'detailurl' && k != 'baseurl'
				&& k != '_address' && k != '_attributes' && k != '_url'
				&& k != 'lat' && k != 'lng' && k != 'search_level'
				&& k != 'popup_detail' && k != 'popup_address' && k != 'popup_lat' && k != 'popup_lng' && k != 'popup_level'
				&& k != 'init' && k != 'init_lat' && k != 'init_lng' && k != 'init_level'
				&& k != 'page'
			) {
				a += k + '=' + encodeURIComponent( query[k] ) + '&';
			}
		}
		return '?' + a;
	};
	query._url = function(){ return query.listurl + query._attributes(); };
	query._address = function(){ return query.baseurl + query._attributes(); };

	var ui = {
		keyword: jQuery('#keyword'),
		auto_search: jQuery('#auto_search'),
		category: jQuery('#category'),
		ymd: jQuery('#ymd'),
		today_only: jQuery('#today_only')
	};
	if( query.keyword ) ui.keyword.attr('value',decodeURIComponent(query.keyword));
	if( query.auto_search ) ui.auto_search.attr('checked','checked');
	if( query.category ) ui.category.find('option[value='+query.category+']').attr('selected','selected');
	if( query.ymd ) ui.ymd.attr('value',decodeURIComponent(query.ymd));
	if( query.today_only ) ui.today_only.attr('checked','checked');

	_redraw();
	if( query.init ) {
		query.lat = query.init_lat;
		query.lng = query.init_lng;
		query.level = query.init_level;
	} else {
		query.lat = ( query.sw_lat + query.ne_lat ) / 2;
		query.lng = ( query.sw_lng + query.ne_lng ) / 2;
	}
	for( i in icons.entry ){
		icon[i+icons.suffix.normal] = new daum.maps.MarkerImage( '__TEMPLATEDIR__/images/' + icons.prefix + icons.entry[i].slug + icons.suffix.normal + icons.extension, new daum.maps.Size( icons.entry[i].width, icons.entry[i].height ) );
		icon[i+icons.suffix.hover] = new daum.maps.MarkerImage( '__TEMPLATEDIR__/images/' + icons.prefix + icons.entry[i].slug + icons.suffix.hover + icons.extension, new daum.maps.Size( icons.entry[i].width, icons.entry[i].height ) );
	}
	var map = {};
	jQuery('#map').each(function(e){
		map = new daum.maps.Map(
			document.getElementById('map'),
			{
				center: new daum.maps.LatLng( query.lat, query.lng ),
				level: query.level,
				//mapTypeId: daum.maps.MapTypeId.HYBRID
				mapTypeId: daum.maps.MapTypeId.ROADMAP
			}
		);
		daum.maps.event.addListener(map,'idle',function(){_update();});
		//daum.maps.event.addListener(map,'bounds_changed',function(){_update();});
	});
	/*
	var control_type = new daum.maps.MapTypeControl();
	var control_zoom = new daum.maps.ZoomControl();
	map.addControl(control_type, daum.maps.ControlPosition.LEFT);
	map.addControl(control_zoom, daum.maps.ControlPosition.LEFT);
	*/
	jQuery('#zoom-box a').on('click',function(e){
		var $this = jQuery(this);
		$this.level = map.getLevel();
		if( $this.hasClass('in') ) {
			map.setLevel( $this.level - 1 );
		} else {
			map.setLevel( $this.level + 1 );
		}
	});
	jQuery('#locate-here a').on('click',function(e){
		navigator.geolocation.getCurrentPosition(
			function(pos){ /* success */
				var crd = pos.coords;
				console.log('Your current position is:');
				console.log('Latitude : ' + crd.latitude);
				console.log('Longitude: ' + crd.longitude);
				console.log('More or less ' + crd.accuracy + ' meters.');
				_panTo( crd.latitude, crd.longitude );
			},
			function(err){ /* error */
				console.warn('ERROR(' + err.code + '): ' + err.message);
			},
			{
				enableHighAccuracy: true,
				timeout: 5000,
				maximumAge: 0
			}
		);
	});
	jQuery('#category').each(function(e){
		var cat_dummy = '';
		jQuery(this).find('option').each(function(index){
			var $this = jQuery(this);
			cat_dummy += '<li data-value="'+$this.attr('value')+'">'+$this.text()+'</li>';
		});
		cat_dummy = '<ul>'+cat_dummy+'</ul>';
		jQuery(cat_dummy).appendTo(jQuery('#category-selector'));
	});
	jQuery('#category-selector label').on('click',function(e){
		e.preventDefault();
		jQuery(this).parent().toggleClass('click');
	});
	jQuery('#category-selector li').on('click',function(e){
		jQuery('#category-selector').removeClass('click');
		jQuery('#category-selector label').text(jQuery(this).text());
		jQuery('#category').val(jQuery(this).attr('data-value')).trigger('change');
	});
	jQuery('#ymd').datepicker({
		language: '__LANGUAGE__',
		format: 'yyyy-mm-dd'

	});
	jQuery('.share a').on('click',function(e){
		e.preventDefault();
		var $this = jQuery(this);
		_open_external_popup( $this.attr('href') );
	});
	ui.keyword.on('change',function(e){
		query.keyword = ui.keyword.val();
		_update();
	});
	ui.auto_search.on('click',function(e){
		query.auto_search = ui.auto_search.attr('checked') ? 1 : 0;
		_update();
	});
	ui.category.on('change',function(e){
		query.category = ui.category.val();
		_update();
	});
	ui.ymd.on('change',function(e){
		query.ymd = ui.ymd.val();
		_update();
	});
	ui.today_only.on('click',function(e){
		query.today_only = ui.today_only.attr('checked') ? 1: 0;
		_update();
	});
	jQuery('a.overlay').on('click',function(e){
		e.preventDefault();
		var $this = jQuery(this);
		var _type = $this.hasClass('ajax') ? 'ajax' : 'iframe';
		_open_overlay( _type, $this.attr('href') );
	});
	jQuery('a.mode-toggler').on('click',function(e){
		_mode();
	});
	function clear_search(){
		if( jQuery('#search').hasClass('done') ) {
			jQuery('#search').removeClass('done');
			jQuery('#search-results ul').remove();
		}
		if( jQuery('#keyword').hasClass('error') ) {
			jQuery('#keyword').removeClass('error');
		}
	}
	jQuery('#search').submit(function(e){
		e.preventDefault();
		var $this = jQuery(this);
		var keyword = jQuery('#keyword').val();
		var searchurl = query.listurl.replace( /ajax\-list\.php/, 'ajax-find.php' ) + '?keyword=' + encodeURIComponent(keyword);
		if( $this.hasClass('done') ) {
			clear_search();
		} else {
			jQuery.getJSON(searchurl,function(data){
				if(typeof data === 'undefined' || data == null) {
					jQuery('#keyword').addClass('error');
					jQuery('#keyword').attr('data-origin-keyword',keyword);
					return;
				} else {
					//console.log( data );
				}
				var total = data.channel.totalCount;
				if( total ) {
					jQuery('#keyword').removeClass('error');
					jQuery('#keyword').attr('data-origin-keyword',keyword);
					if( total == 1 ) {
						var lat = data.channel.item[0].lat;
						var lng = data.channel.item[0].lng;
						var address = data.channel.item[0].title;
						_panTo( lat, lng, address );
					} else {
						var searchUL = jQuery('<ul></ul>');
						for( i=0; i < data.channel.item.length; i++ ) {
							var lat = data.channel.item[i].lat;
							var lng = data.channel.item[i].lng;
							var label = data.channel.item[i].title;
							var searchLI = jQuery( '<li><a href="javascript://" data-lat="' + lat + '" data-lng="' + lng + '" data-address="' + label + '">' + label + '</a></li>' );
							searchLI.find('a').click(function(e){
								e.preventDefault();
								_panTo( jQuery(this).attr('data-lat'), jQuery(this).attr('data-lng'), jQuery(this).attr('data-address') );
							});
							searchUL.append( searchLI );
							$this.addClass('done');
							$this.find('button').bind('click.candle', function(e) {
								e.preventDefault();
								jQuery('#keyword').val('');
								clear_search();
								jQuery(this).unbind('click.candle');
							});
						}
						jQuery('#search-results').html( searchUL );
					}
				}
			});
		}
	});
	jQuery('#search-box')
		.mouseenter(function(e){
			jQuery('#search-results ul').show();
		})
		.mouseleave(function(e){
			jQuery('#search-results ul').hide();
		});
	jQuery('#keyword')
		.keydown(function(e) {
			$this = jQuery(this);
			if(typeof($this).attr('data-origin-keyword') !== 'undefined' && $this.attr('data-origin-keyword') != $this.val()) {
				clear_search();
				$this.parent().find('button').unbind('click.candle');
			}
		});
	jQuery('input,textarea,select,option')
		.on('focusin',function(e){
			jQuery('body').addClass('form-active');
		})
		.on('focusout',function(e){
			jQuery('body').removeClass('form-active');
		});

	/*
	 * initial actions
	 */
	jQuery('#map').each(function(e){
		jQuery(window).on('resize',function(e){
			_resize();
			_update();
		});
		_resize();
		_update();
		if( query.popup_detail != '' && query.popup_address != '' ){
			_open_overlay( 'ajax', query.popup_detail, query.popup_address );
		}
	});
	jQuery('#footer').appendTo('#body');
});

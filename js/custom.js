// Check whether current page is inside (visual) builder or not
var isBuilder = 'object' === typeof window.ET_Builder;

/*! ET custom.js */
(function($){
	window.et_calculating_scroll_position = false;
	window.et_side_nav_links_initialized  = false;

	var $et_pb_post_fullwidth = $( '.single.et_pb_pagebuilder_layout.et_full_width_page' ),
		et_is_mobile_device = navigator.userAgent.match( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/ ),
		et_is_ipad = navigator.userAgent.match( /iPad/ ),
		$et_container = $( '.container' ),
		et_container_width = $et_container.width(),
		et_is_fixed_nav = $( 'body' ).hasClass( 'et_fixed_nav' ) || $( 'body' ).hasClass( 'et_vertical_fixed' ),
		et_is_vertical_fixed_nav = $( 'body' ).hasClass( 'et_vertical_fixed' ),
		et_is_rtl = $( 'body' ).hasClass( 'rtl' ),
		et_hide_nav = $( 'body' ).hasClass( 'et_hide_nav' ),
		et_header_style_left = $( 'body' ).hasClass( 'et_header_style_left' ),
		$top_header = $('#top-header'),
		$main_header = $('#main-header'),
		$main_container_wrapper = $( '#page-container' ),
		$et_main_content_first_row = $( '#main-content .container:first-child' ),
		$et_main_content_first_row_meta_wrapper = $et_main_content_first_row.find('.et_post_meta_wrapper:first'),
		$et_main_content_first_row_meta_wrapper_title = $et_main_content_first_row_meta_wrapper.find( 'h1.entry-title' ),
		$et_main_content_first_row_content = $et_main_content_first_row.find('.entry-content:first'),
		$et_single_post = $( 'body.single' ),
		$et_window = $(window),
		etRecalculateOffset = false,
		et_header_height,
		et_header_modifier,
		et_header_offset,
		et_primary_header_top,
		$et_header_style_split = $('.et_header_style_split'),
		$et_top_navigation = $('#et-top-navigation'),
		$logo = $('#logo'),
		$et_pb_first_row = $( 'body.et_pb_pagebuilder_layout .et_pb_section:visible:first' ),
		et_is_touch_device = 'ontouchstart' in window || navigator.maxTouchPoints,
		$et_top_cart = $('#et-secondary-menu a.et-cart-info');

	function et_preload_image( src, callback ) {
		var img = new Image();
		img.onLoad = callback;
		img.onload = callback;
		img.src = src;
	}

	// We need to check first to see if we are on a woocommerce single product.
	if ( $(".woocommerce .woocommerce-product-gallery").length > 0 ) {
		// get the gallery container.
		var gal = $(".woocommerce-product-gallery")[0];

		// let's replace the data attribute since Salvatorre reconfigures
		// data-columns on the resize event.
		var newstr = gal.outerHTML.replace( 'data-columns', 'data-cols' );

		// finally we re-insert.
		gal.outerHTML = newstr;
	}

	// update the cart item on the secondary menu.
	if ( $et_top_cart.length > 0 && $('.shop_table.cart').length > 0 ) {
		$( document.body ).on( 'updated_wc_div', function(){
			var new_total = 0;
			var new_text;
			$('.shop_table.cart').find('.product-quantity input').each(function(){
				new_total = new_total + parseInt( $(this).val() );
			});

			if ( new_total === 1 ) {
				new_text  = DIVI.item_count;
			} else {
				new_text  = DIVI.items_count;
			}

			new_text = new_text.replace('%d', new_total);

			$et_top_cart.find('span').text(new_text);
		});
	}

	$(document).ready( function(){
		var $et_top_menu = $( 'ul.nav, ul.menu' ),
			$et_search_icon = $( '#et_search_icon' ),
			et_parent_menu_longpress_limit = 300,
			et_parent_menu_longpress_start,
			et_parent_menu_click = true,
			is_customize_preview = $('body').hasClass('et_is_customize_preview');

		window.et_pb_init_nav_menu( $et_top_menu );

		function et_header_menu_split(){
			var $logo_container = $( '#main-header > .container > .logo_container' ),
				$logo_container_splitted = $('.centered-inline-logo-wrap > .logo_container'),
				et_top_navigation_li_size = $et_top_navigation.children('nav').children('ul').children('li').size(),
				et_top_navigation_li_break_index = Math.round( et_top_navigation_li_size / 2 ) - 1,
				window_width = window.innerWidth || $et_window.width();

			if ( window_width > 980 && $logo_container.length && $('body').hasClass('et_header_style_split') ) {
				$('<li class="centered-inline-logo-wrap"></li>').insertAfter($et_top_navigation.find('nav > ul >li:nth('+et_top_navigation_li_break_index+')') );
				$logo_container.appendTo( $et_top_navigation.find('.centered-inline-logo-wrap') );
			}

			if ( window_width <= 980 && $logo_container_splitted.length ) {
				$logo_container_splitted.prependTo('#main-header > .container');
				$('#main-header .centered-inline-logo-wrap').remove();
			}
		}

		function et_set_right_vertical_menu() {
			var $body = $( 'body' );
			if ( $body.hasClass( 'et_boxed_layout' ) && $body.hasClass( 'et_vertical_fixed' ) && $body.hasClass( 'et_vertical_right' ) ) {
				var header_offset = parseFloat( $( '#page-container' ).css( 'margin-right' ) );
				header_offset += ( parseFloat( $( '#et-main-area' ).css( 'margin-right' ) ) - 225 );
				header_offset = 0 > header_offset ? 0 : header_offset;

				$( '#main-header' ).addClass( 'et_vertical_menu_set' ).css( { 'left': '', 'right': header_offset } );
			}
		}

		if ( ( $et_header_style_split.length && ! window.et_is_vertical_nav ) || is_customize_preview ) {
			et_header_menu_split();

			$(window).resize(function(){
				et_header_menu_split();
			});
		}

		if ( window.et_is_vertical_nav ) {
			if ( $( '#main-header' ).height() < $( '#et-top-navigation' ).height() ) {
				$( '#main-header' ).height( $( '#et-top-navigation' ).height() + $( '#logo' ).height() + 100 );
			}
			et_set_right_vertical_menu();
		}

		window.et_calculate_header_values = function() {
			var $top_header = $( '#top-header' ),
				secondary_nav_height = $top_header.length && $top_header.is( ':visible' ) ? parseInt( $top_header.innerHeight() ) : 0,
				admin_bar_height     = $( '#wpadminbar' ).length ? parseInt( $( '#wpadminbar' ).innerHeight() ) : 0,
				$slide_menu_container = $( '.et_header_style_slide .et_slide_in_menu_container' );

			et_header_height      = parseInt( $( '#main-header' ).innerHeight() ) + secondary_nav_height;
			et_header_modifier    = et_header_height <= 90 ? et_header_height - 29 : et_header_height - 56;
			et_header_offset      = et_header_modifier + admin_bar_height;

			et_primary_header_top = secondary_nav_height + admin_bar_height;

			if ( $slide_menu_container.length && ! $( 'body' ).hasClass( 'et_pb_slide_menu_active' ) ) {
				$slide_menu_container.css( { right: '-' + parseInt( $slide_menu_container.innerWidth() ) + 'px', 'display' : 'none' } );

				if ( $( 'body' ).hasClass( 'et_boxed_layout' ) ) {
					var page_container_margin = $main_container_wrapper.css( 'margin-left' );
					$main_header.css( { left : page_container_margin } );
				}
			}
		};

		var $comment_form = $('#commentform');

		et_pb_form_placeholders_init( $comment_form );

		$comment_form.submit(function(){
			et_pb_remove_placeholder_text( $comment_form );
		});

		et_duplicate_menu( $('#et-top-navigation ul.nav'), $('#et-top-navigation .mobile_nav'), 'mobile_menu', 'et_mobile_menu' );
		et_duplicate_menu( '', $('.et_pb_fullscreen_nav_container'), 'mobile_menu_slide', 'et_mobile_menu', 'no_click_event' );

		// Handle `Disable top tier dropdown menu links` Theme Option.
		if ($('ul.et_disable_top_tier').length) {
			$disbaled_top_tier_links = $("ul.et_disable_top_tier > li > ul").prev('a');

			$disbaled_top_tier_links.attr('href', '#');
			$disbaled_top_tier_links.on('click', function(e) {
				e.preventDefault();
			});

			// Handle top tier links in cloned mobile menu
			$disbaled_top_tier_links_mobile = $("ul#mobile_menu > li > ul").prev('a');

			$disbaled_top_tier_links_mobile.attr('href', '#');
			$disbaled_top_tier_links_mobile.on('click', function(e) {
				e.preventDefault();
			});
		}

		if ( $( '#et-secondary-nav' ).length ) {
			$('#et-top-navigation #mobile_menu').append( $( '#et-secondary-nav' ).clone().html() );
		}

		// adding arrows for the slide/fullscreen menus
		if ( $( '.et_slide_in_menu_container' ).length ) {
			var $item_with_sub = $( '.et_slide_in_menu_container' ).find( '.menu-item-has-children > a' );
			// add arrows for each menu item which has submenu
			if ( $item_with_sub.length ) {
				$item_with_sub.append( '<span class="et_mobile_menu_arrow"></span>' );
			}
		}

		function et_change_primary_nav_position( delay ) {
			setTimeout( function() {
				var $body = $('body'),
					$wpadminbar = isBuilder ? window.top.jQuery('#wpadminbar') : $('#wpadminbar'),
					$top_header = $( '#top-header' ),
					et_primary_header_top = 0;

				if ( $wpadminbar.length ) {
					var adminbarHeight = $wpadminbar.innerHeight();

					// Adjust admin bar height for builder's preview mode zoom since admin bar is rendered on top window
					if (isBuilder && window.top.jQuery('html').is('.et-fb-preview--zoom:not(.et-fb-preview--desktop)')) {
						adminbarHeight = adminbarHeight * 2;
					}

					et_primary_header_top += adminbarHeight;
				}

				if ( $top_header.length && $top_header.is(':visible') ) {
					et_primary_header_top += $top_header.innerHeight();
				}

				if ( ! window.et_is_vertical_nav && ( $body.hasClass( 'et_fixed_nav' ) ) ) {
					$('#main-header').css( 'top', et_primary_header_top );
				}
			}, delay );
		}

		window.et_change_primary_nav_position = et_change_primary_nav_position;

		function et_hide_nav_transform( ) {
			var $body = $( 'body' ),
				$body_height = $( document ).height(),
				$viewport_height = $( window ).height() + et_header_height + 200;

			// Do nothing when Vertical Navigation is Enabled
			if ($body.hasClass('et_vertical_nav')) {
				return;
			}

			if ($body.hasClass('et_hide_nav') || $body.hasClass('et_hide_nav_disabled') && ($body.hasClass('et_fixed_nav'))) {
				if ($body_height > $viewport_height) {
					if ($body.hasClass('et_hide_nav_disabled')) {
						$body.addClass('et_hide_nav');
						$body.removeClass('et_hide_nav_disabled');
					}
					$('#main-header').css('transform', 'translateY(-' + et_header_height + 'px)');
					$('#top-header').css('transform', 'translateY(-' + et_header_height + 'px)');
				} else {
					$('#main-header').css({ 'transform': 'translateY(0)', 'opacity': '1' });
					$('#top-header').css({ 'transform': 'translateY(0)', 'opacity': '1' });
					$body.removeClass('et_hide_nav');
					$body.addClass('et_hide_nav_disabled');
				}

				// Run fix page container again, needed when body height is not tall enough and
				// adjustment has been aded
				et_fix_page_container_position();
			}
		}

		// Saving current styling for the next resize cycle
		function et_save_initial_page_container_style( $selector, property ) {
			var styling = {};

			styling[property] = $selector.css( property );

			$selector.attr({
				'data-fix-page-container' : 'on'
			}).data({
				'fix_page_container_style' : styling
			});
		}

		function et_page_load_scroll_to_anchor() {
			var location_hash = window.et_location_hash.replace(/(\|)/g, "\\$1");

			if ($(location_hash).length === 0) {
				return;
			}

			var $map_container = $(location_hash + ' .et_pb_map_container');
			var $map           = $map_container.children('.et_pb_map');
			var $target        = $(location_hash);

			// Make the target element visible again
			$target.css('display', window.et_location_hash_style);

			var distance = ('undefined' !== typeof($target.offset().top)) ? $target.offset().top : 0;
			var speed    = (distance > 4000) ? 1600 : 800;

			if ($map_container.length) {
				google.maps.event.trigger($map[0], 'resize');
			}

			// Allow the header sizing functions enough time to finish before scrolling the page
			setTimeout(function() {
				et_pb_smooth_scroll($target, false, speed, 'swing');

				// During the page scroll animation, the header's height might change.
				// Do the scroll animation again to ensure its accuracy.
				setTimeout(function() {
					et_pb_smooth_scroll($target, false, 150, 'linear');
				}, speed + 25);

			}, 700);
		}

		// Retrieving padding/margin value based on formatted saved padding/margin strings
		function et_get_saved_padding_margin_value( saved_value, order ) {
			if ( typeof saved_value === 'undefined' ) {
				return false;
			}

			var values = saved_value.split('|');

			return typeof values[order] !== 'undefined' ? values[order] : false;
		}

		function et_fix_page_container_position(){
			var et_window_width     = parseInt( $et_window.width() ),
				$top_header          = $( '#top-header' ),
				$et_pb_first_row     = $( 'body.et_pb_pagebuilder_layout .et_pb_section:visible:first' ),
				secondary_nav_height = $top_header.length && $top_header.is( ':visible' ) ? parseInt( $top_header.innerHeight() ) : 0,
				main_header_fixed_height = 0,
				header_height,
				et_pb_first_row_padding_top;

				var $mainHeaderClone = $main_header
					.clone()
					.addClass('et-disabled-animations main-header-clone')
					.css({
						opacity: 0,
						position: 'fixed',
						top: 'auto',
						right: 0,
						bottom: 0,
						left: 0,
					})
					.appendTo($('body'));

			// Replace previous resize cycle's adjustment
			if (!$('body').hasClass('et-bfb')) {
				$('*[data-fix-page-container="on"]').each(function(){
					var $adjusted_element = $(this),
						styling = $adjusted_element.data();

					if (styling && styling.fix_page_container_style) {
						// Reapply previous styling
						$adjusted_element.css( styling.fix_page_container_style );
					}

				});
			}

			// Set data-height-onload for header if the page is loaded on large screen
			// If the page is loaded from small screen, rely on data-height-onload printed on the markup,
			// prevent window resizing issue from small to large
			// ignore data-height-loaded in VB to make sure it calculated correctly.
			if (et_window_width > 980 && (! $main_header.attr('data-height-loaded') || $('body').is('.et-fb'))) {
				var mainHeaderHeight = 0;
				if ($main_header.hasClass('et-fixed-header')) {
					$mainHeaderClone.removeClass('et-fixed-header');
					mainHeaderHeight = $mainHeaderClone.height();
					$mainHeaderClone.addClass('et-fixed-header');
				} else {
					mainHeaderHeight = $main_header.height();
				}
				$main_header.attr({ 'data-height-onload' : parseInt(mainHeaderHeight), 'data-height-loaded' : true });
			}

			// Use on page load calculation for large screen. Use on the fly calculation for small screen (980px below)
			if ( et_window_width <= 980 ) {
				header_height = parseInt( $main_header.innerHeight() ) + secondary_nav_height - ($('body').hasClass('et-fb') ? 0 : 1);

				// If transparent is detected, #main-content .container's padding-top needs to be added to header_height
				// And NOT a pagebuilder page
				if ( window.et_is_transparent_nav && ! $et_pb_first_row.length ) {
					header_height += 58;
				}
			} else {

				// Get header height from header attribute
				header_height = parseInt( $main_header.attr( 'data-height-onload' ) ) + secondary_nav_height;

				// Non page builder page needs to be added by #main-content .container's fixed height
				if ( window.et_is_transparent_nav && ! window.et_is_vertical_nav && $et_main_content_first_row.length ) {
					header_height += 58;
				}

				// Calculate fixed header height by cloning, emulating, and calculating its height
				main_header_fixed_height = $mainHeaderClone.height();
			}

			// Saved fixed main header height calculation
			$main_header.attr({
				'data-fixed-height-onload': main_header_fixed_height
			});

			// Specific adjustment required for transparent nav + not vertical nav
			if (window.et_is_transparent_nav && !window.et_is_vertical_nav) {

				if (!$('body').hasClass('et-bfb')) {
					// Add class for first row for custom section padding purpose
					$et_pb_first_row.addClass( 'et_pb_section_first' );
				}

				// List of conditionals
				var is_pb                            = $et_pb_first_row.length,
					is_post_pb                       = is_pb && $et_single_post.length,
					is_post_pb_full_layout_has_title = $et_pb_post_fullwidth.length && $et_main_content_first_row_meta_wrapper_title.length,
					is_post_pb_full_layout_no_title  = $et_pb_post_fullwidth.length && 0 === $et_main_content_first_row_meta_wrapper_title.length,
					is_pb_fullwidth_section_first    = $et_pb_first_row.is( '.et_pb_fullwidth_section' ),
					is_no_pb_mobile                  = et_window_width <= 980 && $et_main_content_first_row.length,
					isProject                        = $( 'body' ).hasClass( 'single-project' );

				if ( is_post_pb && ! ( is_post_pb_full_layout_no_title && is_pb_fullwidth_section_first ) && !isProject ) {

					/* Desktop / Mobile + Single Post */

					/*
					 * EXCEPT for fullwidth layout + fullwidth section ( at the first row ).
					 * It is basically the same as page + fullwidth section with few quirk.
					 * Instead of duplicating the conditional for each module, it'll be simpler to negate
					 * fullwidth layout + fullwidth section in is_post_pb and rely it to is_pb_fullwidth_section_first
					 */

					// Remove main content's inline padding to styling to prevent looping padding-top calculation
					$et_main_content_first_row.css({ 'paddingTop' : '' });

					if ( et_window_width < 980 ) {
						header_height += 40;
					}

					if ( is_pb_fullwidth_section_first ) {
						// If the first section is fullwidth, restore the padding-top modified area at first section
						$et_pb_first_row.css({
							'paddingTop' : '0'
						});
					}

					if ( is_post_pb_full_layout_has_title ) {

						// Add header height to post meta wrapper as padding top
						$et_main_content_first_row_meta_wrapper.css({
							'paddingTop' : header_height
						});

					} else if ( is_post_pb_full_layout_no_title ) {

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row,
							'paddingTop'
						);

						// Reset any inline padding-top.
						$et_pb_first_row.css({
							paddingTop: ''
						});

						$et_pb_first_row.css({
							// Ignore the extra 58px added to header height previously.
							'paddingTop' : 'calc(' + (header_height - 58) + 'px + ' + $et_pb_first_row.css('paddingTop') + ')'
						});

					} else {

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_main_content_first_row,
							'paddingTop'
						);

						// Add header height to first row content as padding top
						$et_main_content_first_row.css({
							'paddingTop' : header_height
						});

					}

				} else if ( is_pb_fullwidth_section_first ){

					/* Desktop / Mobile + Pagebuilder + Fullwidth Section */

					var $et_pb_first_row_first_module = $et_pb_first_row.children( '.et_pb_module:visible:first' );

					// Quirks: If this is post with fullwidth layout + no title + fullwidth section at first row,
					// Remove the added height at line 2656
					if ( is_post_pb_full_layout_no_title && is_pb_fullwidth_section_first && et_window_width > 980 ) {
						header_height = header_height - 58;
					}

					if ( $et_pb_first_row_first_module.is( '.et_pb_slider' ) ) {

						/* Desktop / Mobile + Pagebuilder + Fullwidth slider */

						var $et_pb_first_row_first_module_slide_image 		= $et_pb_first_row_first_module.find( '.et_pb_slide_image' ),
							$et_pb_first_row_first_module_slide 				= $et_pb_first_row_first_module.find( '.et_pb_slide' ),
							$et_pb_first_row_first_module_slide_container 	= $et_pb_first_row_first_module.find( '.et_pb_slide .et_pb_container' ),
							et_pb_slide_image_margin_top 		= 0 - ( parseInt( $et_pb_first_row_first_module_slide_image.height() ) / 2 ),
							et_pb_slide_container_height 		= 0,
							$et_pb_first_row_first_module_slider_arrow 		= $et_pb_first_row_first_module.find( '.et-pb-slider-arrows a'),
							et_pb_first_row_slider_arrow_height = $et_pb_first_row_first_module_slider_arrow.height();

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module_slide,
							'paddingTop'
						);

						// Adding padding top to each slide so the transparency become useful
						$et_pb_first_row_first_module_slide.css({
							'paddingTop' : header_height
						});

						// delete container's min-height
						$et_pb_first_row_first_module_slide_container.css({
							'min-height' : ''
						});


						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module_slide_image,
							'marginTop'
						);

						// Adjusting slider's image, considering additional top padding of slideshow
						$et_pb_first_row_first_module_slide_image.css({
							'marginTop' : et_pb_slide_image_margin_top
						});

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module_slider_arrow,
							'marginTop'
						);

						// Adjusting slider's arrow, considering additional top padding of slideshow
						$et_pb_first_row_first_module_slider_arrow.css({
							'marginTop' : ( ( header_height / 2 ) - ( et_pb_first_row_slider_arrow_height / 2 ) )
						});

						// Looping the slide and get the highest height of slide
						var et_pb_first_row_slide_container_height_new = 0;

						$et_pb_first_row_first_module.find( '.et_pb_slide' ).each( function(){
							var $et_pb_first_row_first_module_slide_item = $(this),
								$et_pb_first_row_first_module_slide_container = $et_pb_first_row_first_module_slide_item.find( '.et_pb_container' );

							// Make sure that the slide is visible to calculate correct height
							$et_pb_first_row_first_module_slide_item.show();

							// Remove existing inline css to make sure that it calculates the height
							$et_pb_first_row_first_module_slide_container.css({ 'min-height' : '' });

							var et_pb_first_row_slide_container_height = $et_pb_first_row_first_module_slide_container.innerHeight();

							if ( et_pb_first_row_slide_container_height_new < et_pb_first_row_slide_container_height ){
								et_pb_first_row_slide_container_height_new = et_pb_first_row_slide_container_height;
							}

							// Hide the slide back if it isn't active slide
							if ( $et_pb_first_row_first_module_slide_item.is( ':not(".et-pb-active-slide")' ) ){
								$et_pb_first_row_first_module_slide_item.hide();
							}
						});

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module_slide_container,
							'min-height'
						);

						// Setting appropriate min-height, considering additional top padding of slideshow
						$et_pb_first_row_first_module_slide_container.css({
							'min-height' : et_pb_first_row_slide_container_height_new
						});

					} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_header' ) ) {

						/* Desktop / Mobile + Pagebuilder + Fullwidth header */

						// Remove existing inline stylesheet to prevent looping padding
						$et_pb_first_row_first_module.removeAttr( 'style' );

						// Get paddingTop from stylesheet
						var et_pb_first_row_first_module_fullwidth_header_padding_top = parseInt( $et_pb_first_row_first_module.css( 'paddingTop' ) );

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module,
							'paddingTop'
						);

						// Implement stylesheet's padding-top + header_height
						$et_pb_first_row_first_module.css({
							'paddingTop' : ( header_height + et_pb_first_row_first_module_fullwidth_header_padding_top )
						} );

					} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_portfolio' ) ) {

						/* Desktop / Mobile + Pagebuilder + Fullwidth Portfolio */

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module,
							'paddingTop'
						);

						$et_pb_first_row_first_module.css({ 'paddingTop' : header_height });

					} else if ( $et_pb_first_row_first_module.is( '.et_pb_map_container' ) ) {

						/* Desktop / Mobile + Pagebuilder + Fullwidth Map */

						var $et_pb_first_row_map = $et_pb_first_row_first_module.find( '.et_pb_map' );

						// Remove existing inline height to prevent looping height calculation
						$et_pb_first_row_map.css({ 'height' : '' });

						// Implement map height + header height
						$et_pb_first_row_first_module.find('.et_pb_map').css({
							'height' : header_height + parseInt( $et_pb_first_row_map.css( 'height' ) )
						});

						// Adding specific class to mark the map as first row section element
						$et_pb_first_row_first_module.addClass( 'et_beneath_transparent_nav' );

					} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_menu' ) ) {

						/* Desktop / Mobile + Pagebuilder + Fullwidth Menu */

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module,
							'marginTop'
						);

						$et_pb_first_row_first_module.css({ 'marginTop' : header_height });

					} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_code' ) ) {

						/* Desktop / Mobile + Pagebuilder + Fullwidth code */

						var $et_pb_first_row_first_module_code = $et_pb_first_row_first_module;

						$et_pb_first_row_first_module_code.css({ 'paddingTop' : '' });

						var et_pb_first_row_first_module_code_padding_top = parseInt( $et_pb_first_row_first_module_code.css( 'paddingTop' ) );

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module_code,
							'paddingTop'
						);

						$et_pb_first_row_first_module_code.css({
							'paddingTop' : header_height + et_pb_first_row_first_module_code_padding_top
						});

					} else if ( $et_pb_first_row_first_module.is( '.et_pb_post_title' ) ) {

						/* Desktop / Mobile + Pagebuilder + Fullwidth Post Title */
						var $et_pb_first_row_first_module_title = $et_pb_first_row_first_module;

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row_first_module_title,
							'paddingTop'
						);

						$et_pb_first_row_first_module.css({
							'paddingTop' : header_height + 50
						});
					} else if ( ! $et_pb_first_row_first_module.length ) {
						// Get current padding top
						et_pb_first_row_padding_top = parseFloat($et_pb_first_row.css('paddingTop'));

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row,
							'paddingTop'
						);

						// Keep the state of previous cycle. The padding top is reset to the first
						// cycle by default (padding-top: 0px) so if previous cycle didn't hide the
						// nav, automatically add the additional padding top
						if ( ! $et_pb_first_row.data( 'is_hide_nav' ) ) {
							$et_pb_first_row.css({
								'paddingTop' : et_pb_first_row_padding_top + header_height
							});
						}

						// Use timeout to avoid flickering padding top when window is resized vertically
						// and hidden nav is transitioned to visible nav, vice versa.
						clearTimeout(window.et_fallback_transparent_adjustment_timeout);
						window.et_fallback_transparent_adjustment_timeout = setTimeout(function() {
							// Hidden nav can be decided by the existance of et_hide_nav class AND
							// the css transform attribute value because the visibility of nav is
							// modified by CSS transition
							var is_hide_nav = $('body').hasClass('et_hide_nav') && $('#main-header').css('transform') !== 'matrix(1, 0, 0, 1, 0, 0)';

							// Add / remove additional top padding accordingly
							if ( is_hide_nav ) {
								$et_pb_first_row.css({
									'paddingTop' : ''
								});
							} else {
								$et_pb_first_row.css({
									'paddingTop' : et_pb_first_row_padding_top + header_height
								});
							}

							// Save current nav state for next cycle assessment
							$et_pb_first_row.data( 'is_hide_nav', is_hide_nav );
						}, 300)
					}

				} else if ( is_pb ) {

					/* Desktop / Mobile + Pagebuilder + Regular section */

					// Remove first row's inline padding top styling to prevent looping padding-top calculation
					$et_pb_first_row.css({ 'paddingTop' : '' });

					// Get saved custom padding from data-* attributes. Builder automatically adds
					// saved custom paddings to data-* attributes on first section
					var saved_custom_padding            = $et_pb_first_row.attr('data-padding'),
						saved_custom_padding_top        = et_get_saved_padding_margin_value( saved_custom_padding, 0 ),
						saved_custom_padding_tablet     = $et_pb_first_row.attr('data-padding-tablet'),
						saved_custom_padding_tablet_top = et_get_saved_padding_margin_value( saved_custom_padding_tablet, 0 ),
						saved_custom_padding_phone      = $et_pb_first_row.attr('data-padding-phone'),
						saved_custom_padding_phone_top  = et_get_saved_padding_margin_value( saved_custom_padding_phone, 0 ),
						applied_saved_custom_padding;

					if ( saved_custom_padding_top || saved_custom_padding_tablet_top || saved_custom_padding_phone_top ) {
						// Applies padding top to first section to automatically convert saved unit into px
						if ( et_window_width > 980 && saved_custom_padding_top ) {
							$et_pb_first_row.css({
								paddingTop: saved_custom_padding_top
							});
						} else if ( et_window_width > 767 && saved_custom_padding_tablet_top ) {
							$et_pb_first_row.css({
								paddingTop: saved_custom_padding_tablet_top
							});
						} else if ( saved_custom_padding_phone_top ) {
							$et_pb_first_row.css({
								paddingTop: saved_custom_padding_phone_top
							});
						}

						// Get converted custom padding top value
						applied_saved_custom_padding = parseInt( $et_pb_first_row.css( 'paddingTop' ) );

						// Implemented saved & converted padding top + header height
						$et_pb_first_row.css({
							paddingTop: ( header_height + applied_saved_custom_padding )
						});
					} else {
						// Pagebuilder ignores #main-content .container's fixed height and uses its row's padding
						// Anticipate the use of custom section padding.
						et_pb_first_row_padding_top = header_height + parseInt( $et_pb_first_row.css( 'paddingTop' ) );

						// Save current styling for the next resize cycle
						et_save_initial_page_container_style(
							$et_pb_first_row,
							'paddingTop'
						);

						// Implementing padding-top + header_height
						$et_pb_first_row.css({
							'paddingTop' : et_pb_first_row_padding_top
						});
					}
				} else if ( is_no_pb_mobile ) {

					// Mobile + not pagebuilder
					$et_main_content_first_row.css({
						'paddingTop' : header_height
					});

				} else {

					$('#main-content .container:first-child').css({
						'paddingTop' : header_height
					});

				}

				// Set #page-container's padding-top to zero after inline styling first row's content has been added
				if ( ! $('#et_fix_page_container_position').length ){
					$( '<style />', {
						'id' : 'et_fix_page_container_position',
						'text' : '#page-container{ padding-top: 0 !important;}'
					}).appendTo('head');
				}

				// If the first visible (visibility is significant for for cached split test) section/row/module has
				// parallax background, trigger parallax height resize so the parallax location is correctly rendered
				// due to addition of first section/row/module margin-top/padding-top which is needed for transparent
				// primary nav
				var $firstSection = $('.et_pb_section:visible:first');
				var $firstRow = $firstSection.find('.et_pb_row:visible:first');
				var $firstModule = $firstSection.find('.et_pb_module:visible:first');

				var firstSectionHasParallax = $firstSection.hasClass('et_pb_section_parallax');
				var firstRowHasParallax = $firstRow.hasClass('et_pb_section_parallax');
				var firstModuleHasParallax = $firstModule.hasClass('et_pb_section_parallax');

				if (firstSectionHasParallax || firstRowHasParallax || firstModuleHasParallax) {
					$(window).trigger('resize.etTrueParallaxBackground');
				}

			} else if( et_is_fixed_nav ) {

				$main_container_wrapper.css( 'paddingTop', header_height );

			}

			$mainHeaderClone.remove();
			et_change_primary_nav_position( 0 );
			$(document).trigger('et-pb-header-height-calculated');
		}
		window.et_fix_page_container_position = et_fix_page_container_position;

		// Save container width on page load for reference
		$et_container.data( 'previous-width', $et_container.width() );

		$( window ).resize( function(){
			var window_width                = parseInt( $et_window.width() ),
				et_container_previous_width = parseInt( $et_container.data('previous-width') ),
				et_container_css_width      = $et_container.css( 'width' ),
				et_container_width_in_pixel = ( typeof et_container_css_width !== 'undefined' ) ? et_container_css_width.substr( -1, 1 ) !== '%' : '',
				et_container_actual_width   = ( et_container_width_in_pixel ) ? parseInt( $et_container.width() ) : ( ( parseInt( $et_container.width() ) / 100 ) * window_width ), // $et_container.width() doesn't recognize pixel or percentage unit. It's our duty to understand what it returns and convert it properly
				containerWidthChanged       = et_container_previous_width !== et_container_actual_width,
				$slide_menu_container       = $( '.et_slide_in_menu_container' ),
				$adminbar                   = isBuilder ? window.top.jQuery('#wpadminbar') : $('#wpadminbar'),
				page_container_margin;

			if ( et_is_fixed_nav && containerWidthChanged ) {
				if ( typeof update_page_container_position != 'undefined' ){
					clearTimeout( update_page_container_position );
				}

				var update_page_container_position = setTimeout( function() {
					et_fix_page_container_position();
					if ( typeof et_fix_fullscreen_section === 'function' ) {
						et_fix_fullscreen_section();
					}
				}, 200 );

				// Update container width data for future resizing reference
				$et_container.data('previous-width', et_container_actual_width );
			}

			if ( et_hide_nav ) {
				et_hide_nav_transform();
			}

			// Update header and primary adjustment when transitioning across breakpoints or inside visual builder
			if (($adminbar.length && et_is_fixed_nav && window_width >= 740 && window_width <= 782) || isBuilder) {
				et_calculate_header_values();

				et_change_primary_nav_position( 0 );
			}

			et_set_search_form_css();

			if ( $slide_menu_container.length && ! $( 'body' ).hasClass( 'et_pb_slide_menu_active' ) ) {
				$slide_menu_container.css( { right: '-' + parseInt( $slide_menu_container.innerWidth() ) + 'px' } );

				if ( $( 'body' ).hasClass( 'et_boxed_layout' ) && et_is_fixed_nav ) {
					page_container_margin = $main_container_wrapper.css( 'margin-left' );
					$main_header.css( { left : page_container_margin } );
				}
			}

			if ( $slide_menu_container.length && $( 'body' ).hasClass( 'et_pb_slide_menu_active' ) ) {
				if ( $( 'body' ).hasClass( 'et_boxed_layout' ) ) {
					var left_position;
					page_container_margin = parseFloat( $main_container_wrapper.css( 'margin-left' ) );

					$main_container_wrapper.css( { left: '-' + ( parseInt( $slide_menu_container.innerWidth() ) - page_container_margin ) + 'px' } );

					if ( et_is_fixed_nav ) {
						left_position = 0 > parseInt( $slide_menu_container.innerWidth() ) - ( page_container_margin * 2 ) ? Math.abs( $slide_menu_container.innerWidth() - ( page_container_margin * 2 ) ) : '-' + ( $slide_menu_container.innerWidth() - ( page_container_margin * 2 ) );

						if ( left_position < parseInt( $slide_menu_container.innerWidth() ) ) {
							$main_header.css( { left: left_position + 'px' } );
						}
					}
				} else {
					$( '#page-container, .et_fixed_nav #main-header' ).css( { left: '-' + parseInt( $slide_menu_container.innerWidth() ) + 'px' } );
				}
			}

			// adjust the padding in fullscreen menu
			if ( $slide_menu_container.length && $( 'body' ).hasClass( 'et_header_style_fullscreen' ) ) {
				var top_bar_height = parseInt( $slide_menu_container.find( '.et_slide_menu_top' ).innerHeight() );

				$slide_menu_container.css( { 'padding-top': top_bar_height + 20 } );
			}

			et_set_right_vertical_menu();
		} );

		if (isBuilder && jQuery('.et_header_style_fullscreen .et_slide_in_menu_container').length > 0) {
			jQuery(window).resize(et_pb_resize_fullscreen_menu);
		}

		$( window ).ready( function(){
			if ( $.fn.fitVids ) {
				$( '#main-content' ).fitVids( { customSelector: "iframe[src^='http://www.hulu.com'], iframe[src^='http://www.dailymotion.com'], iframe[src^='http://www.funnyordie.com'], iframe[src^='https://embed-ssl.ted.com'], iframe[src^='http://embed.revision3.com'], iframe[src^='https://flickr.com'], iframe[src^='http://blip.tv'], iframe[src^='http://www.collegehumor.com']"} );
			}
		} );

		function et_all_elements_loaded() {
			if ( et_is_fixed_nav ) {
				et_calculate_header_values();
			}

			// Run container position calculation with 0 timeout to make sure all elements are ready for proper calculation.
			setTimeout(function() {
				et_fix_page_container_position();
			}, 0);

			// Minified JS is ordered differently to avoid jquery-migrate to cause js error.
			// This might cause hiccup on some specific configuration (ie. parallax of first module on transparent nav)
			// Triggerring resize, in most case, re-calculate the UI correctly
			if ( window.et_is_minified_js && window.et_is_transparent_nav && ! window.et_is_vertical_nav ){
				$( window ).trigger( 'resize' );
			}

			if ( window.hasOwnProperty( 'et_location_hash' ) && '' !== window.et_location_hash ) {
				// Handle the page scroll that we prevented earlier in the <head>
				et_page_load_scroll_to_anchor();
			}

			if ( et_header_style_left && !window.et_is_vertical_nav) {
				$logo_width = parseInt( $( '#logo' ).width() );
				if ( et_is_rtl ) {
					$et_top_navigation.css( 'padding-right', $logo_width + 30 );
				} else {
					$et_top_navigation.css( 'padding-left', $logo_width + 30 );
				}
			}

			if ( $('p.demo_store').length ) {
				$('#footer-bottom').css('margin-bottom' , $('p.demo_store').innerHeight());
			}

			if ( $.fn.waypoint ) {
				var $waypoint_selector;
				if ( et_is_vertical_fixed_nav ) {
					$waypoint_selector = $('#main-content');

					$waypoint_selector.waypoint( {
						handler : function( direction ) {
							et_fix_logo_transition();

							if ( direction === 'down' ) {
								$('#main-header').addClass( 'et-fixed-header' );
							} else {
								$('#main-header').removeClass( 'et-fixed-header' );
							}
						}
					} );
				}

				if ( et_is_fixed_nav ) {

					// We don't want product pages with divi-builder to trigger fixed navigation
					// based on builder row/module position
					if (window.et_is_transparent_nav && ! (window.et_is_vertical_nav || $('body.woocommerce.single-product').length) && $et_pb_first_row.length) {

						// Fullscreen section at the first row requires specific adjustment
						if ($et_pb_first_row.is('.et_pb_fullwidth_section')) {
							$waypoint_selector = $et_pb_first_row.children('.et_pb_module:visible:first');
						} else {
							$waypoint_selector = $et_pb_first_row.find('.et_pb_row:visible:first');
						}

						// Fallback for a less likely but possible scenario: a) fullwidth section
						// has no module OR b) other section has no row. When this happened,
						// the safest option is look for the first visible module and use it
						// as waypoint selector
						if (! $waypoint_selector.length) {
							$waypoint_selector = $('body.et_pb_pagebuilder_layout .et_pb_module:visible:first');
						}
					} else if (window.et_is_transparent_nav && ! window.et_is_vertical_nav && $et_main_content_first_row.length) {
						$waypoint_selector = $('#content-area');
					} else {
						$waypoint_selector = $('#main-content');
					}

					// Disabled section/row/module can cause waypoint to trigger 'down' event during its setup even if
					// no scrolling happened, which would result in 'et-fixed-header' class being prematurely added.
					// Since this only happens when page is loaded, we add an extra check that is no longer needed
					// as soon as waypoint initialization is finished.
					var checkIfScrolled = true;
					setTimeout(function() { checkIfScrolled = false; }, 0);

					$waypoint_selector.waypoint( {
						offset: function() {
							if ( etRecalculateOffset ) {
								setTimeout( function() {
									et_calculate_header_values();
								}, 200 );

								etRecalculateOffset = false;
							}
							if ( et_hide_nav ) {
								return et_header_offset - et_header_height - 200;
							} else {

								// Transparent nav modification: #page-container's offset is set to 0. Modify et_header_offset's according to header height
								var waypoint_selector_offset = $waypoint_selector.offset();

								if ( waypoint_selector_offset.top < et_header_offset ) {
									et_header_offset = 0 - ( et_header_offset - waypoint_selector_offset.top );
								}

								return et_header_offset;
							}
						},
						handler : function( direction ) {
							et_fix_logo_transition();

							if ( direction === 'down' ) {
								if (checkIfScrolled && $et_window.scrollTop() === 0) {
									return;
								}
								$main_header.addClass( 'et-fixed-header' );
								$main_container_wrapper.addClass ( 'et-animated-content' );
								$top_header.addClass( 'et-fixed-header' );

								if ( ! et_hide_nav && ! window.et_is_transparent_nav && ! $( '.mobile_menu_bar_toggle' ).is(':visible') ) {
									var secondary_nav_height = $top_header.length ? parseInt( $top_header.height() ) : 0,
										$clone_header,
										clone_header_height,
										fix_padding;

									$clone_header = $main_header.clone().addClass( 'et-fixed-header, et_header_clone' ).css( { 'transition': 'none', 'display' : 'none' } );

									clone_header_height = parseInt( $clone_header.prependTo( 'body' ).height() );

									// Vertical nav doesn't need #page-container margin-top adjustment
									if ( ! window.et_is_vertical_nav ) {
										fix_padding = parseInt( $main_container_wrapper.css( 'padding-top' ) ) - clone_header_height - secondary_nav_height + 1 ;

										$main_container_wrapper.css( 'margin-top', -fix_padding );
									}

									$( '.et_header_clone' ).remove();
								}

							} else {
								$main_header.removeClass( 'et-fixed-header' );
								$top_header.removeClass( 'et-fixed-header' );
								$main_container_wrapper.css( 'margin-top', '-1px' );
							}
							setTimeout( function() {
								et_set_search_form_css();
							}, 400 );
						}
					} );
				}

				if ( et_hide_nav ) {
					et_hide_nav_transform();
				}
			}
		}

		$( 'a[href*="#"]:not([href="#"])' ).click( function() {
			var $this_link = $( this ),
				has_closest_smooth_scroll_disabled = $this_link.closest( '.et_smooth_scroll_disabled' ).length,
				has_closest_woocommerce_tabs = ( $this_link.closest( '.woocommerce-tabs' ).length && $this_link.closest( '.tabs' ).length ),
				has_closest_timetable_tab = $this_link.closest( '.tt_tabs_navigation' ).length,
				has_closest_eab_cal_link = $this_link.closest( '.eab-shortcode_calendar-navigation-link' ).length,
				has_closest_ee_cart_link = $this_link.closest( '.view-cart-lnk' ).length,
				has_acomment_reply = $this_link.hasClass( 'acomment-reply' ),
				is_woocommerce_review_link = $this_link.hasClass( 'woocommerce-review-link' ),
				disable_scroll = has_closest_smooth_scroll_disabled || has_closest_ee_cart_link || has_closest_woocommerce_tabs || has_closest_eab_cal_link || has_acomment_reply || is_woocommerce_review_link || has_closest_timetable_tab;

			if ( ( location.pathname.replace( /^\//,'' ) == this.pathname.replace( /^\//,'' ) && location.hostname == this.hostname ) && ! disable_scroll ) {
				var target = $( this.hash );
				target = target.length ? target : $( '[name=' + this.hash.slice(1) +']' );
				if ( target.length ) {

					// automatically close fullscreen menu if clicked from there
					if ( $this_link.closest( '.et_pb_fullscreen_menu_opened' ).length > 0 ) {
						et_pb_toggle_fullscreen_menu();
					}

					et_pb_smooth_scroll( target, false, 800 );

					if ( ! $( '#main-header' ).hasClass( 'et-fixed-header' ) && $( 'body' ).hasClass( 'et_fixed_nav' ) && $( window ).width() > 980 ) {
						setTimeout(function(){
							et_pb_smooth_scroll( target, false, 40, 'linear' );
						}, 780 );
					}

					return false;
				}
			}
		});

		window.et_pb_window_side_nav_scroll_init = function() {
			if ( true === window.et_calculating_scroll_position || false === window.et_side_nav_links_initialized ) {
				return;
			}

			window.et_calculating_scroll_position = true;

			var add_offset = $( 'body' ).hasClass( 'et_fixed_nav' ) ? 20 : -90;
			var top_header_height = $( '#top-header' ).length > 0 ? parseInt( $( '#top-header' ).height() ) : 0;
			var main_header_height = $( '#main-header' ).length > 0 ? parseInt( $( '#main-header' ).height() ) : 0;
			var side_offset;

			if ( $( '#wpadminbar' ).length > 0 && parseInt( $( window ).width() ) > 600 ) {
				add_offset += parseInt( $( '#wpadminbar' ).outerHeight() );
			}

			if ( window.et_is_vertical_nav ) {
				side_offset = top_header_height + add_offset + 60;
			} else {
				side_offset = top_header_height + main_header_height + add_offset;
			}

			var window_height = parseInt( $( window ).height() );
			var scroll_position = parseInt( $( window ).scrollTop() );
			var document_height = parseInt( $( document ).height() );
			var at_bottom_of_page = ( window_height + scroll_position ) === document_height;
			var total_links = $( '.side_nav_item a' ).length - 1;

			for ( var link = 0; link <= total_links; link++ ) {
				var $target_section = $( '.et_pb_section:visible:not(.et_pb_section div)' ).eq( link );
				var at_top_of_page = 'undefined' === typeof $target_section.offset();
				var current_active = $( '.side_nav_item a.active' ).parent().index();
				var next_active = null;
				var target_offset = ( false === at_top_of_page ) ? $target_section.offset().top - side_offset : 0;

				if ( at_top_of_page ) {
					next_active = 0;
				} else if ( at_bottom_of_page ) {
					next_active = total_links;
				} else if ( scroll_position >= target_offset ) {
					next_active = link;
				}

				if ( null !== next_active && next_active !== current_active ) {
					$( '.side_nav_item a' ).removeClass( 'active' );
					$( 'a#side_nav_item_id_' + next_active ).addClass( 'active' );
				}
			}

			window.et_calculating_scroll_position = false;
		};

		window.et_pb_side_nav_page_init = function() {
			var $sections = $( '.et_pb_section:visible:not(.et_pb_section div)' );
			var total_sections = $sections.length;
			var side_nav_offset = parseInt( ( total_sections * 20 + 40 ) / 2 );

			window.et_side_nav_links_initialized = false;

			window.et_calculating_scroll_position = false;

			if ( total_sections > 1 && $( '.et_pb_side_nav_page' ).length ) {

				$( '#main-content' ).append( '<ul class="et_pb_side_nav"></ul>' );

				$sections.each( function( index, element ) {
					var active_class = ( 0 === index ) ? 'active' : '';
					$( '.et_pb_side_nav' ).append( '<li class="side_nav_item"><a href="#" id="side_nav_item_id_' + index + '" class= "' + active_class + '">' + index + '</a></li>' );

					if ( total_sections - 1 === index ) {
						window.et_side_nav_links_initialized = true;
					}
				});

				$( 'ul.et_pb_side_nav' ).css( 'marginTop', '-' + side_nav_offset + 'px' );
				$( '.et_pb_side_nav' ).addClass( 'et-visible' );

				$( '.et_pb_side_nav a' ).click( function() {
					// We use the index position of the sections to locate them instead of custom classes so
					// that we have the same implementation for the frontend website and the Visual Builder.
					var index = parseInt( $( this ).text() );
					var $target = $( '.et_pb_section:visible:not(.et_pb_section div)' ).eq( index );
					var top_section = $(this).text() == "0";

					et_pb_smooth_scroll( $target, top_section, 800 );

					if ( ! $( '#main-header' ).hasClass( 'et-fixed-header' ) && $( 'body' ).hasClass( 'et_fixed_nav' ) && parseInt( $( window ).width() ) > 980 ) {
						setTimeout( function() {
							et_pb_smooth_scroll( $target, top_section, 200 );
						}, 500 );
					}

					return false;
				});

				$( window ).on( 'scroll', et_pb_window_side_nav_scroll_init );
			}
		};

		if ($('body').is('.et-fb, .et-bfb')) {
			var _ = window._ || isBuilder && window.top._;
			if (_) {
				// Debounce slow function
				window.et_pb_side_nav_page_init = _.debounce(window.et_pb_side_nav_page_init, 200);
			}
		}

		et_pb_side_nav_page_init();

		if ( $('.et_pb_scroll_top').length ) {
			$(window).scroll(function(){
				if ($(this).scrollTop() > 800) {
					$('.et_pb_scroll_top').show().removeClass( 'et-hidden' ).addClass( 'et-visible' );
				} else {
					$('.et_pb_scroll_top').removeClass( 'et-visible' ).addClass( 'et-hidden' );
				}
			});

			//Click event to scroll to top
			$('.et_pb_scroll_top').click(function(){
				$('html, body').animate({scrollTop : 0},800);
			});
		}

		if ( $( '.comment-reply-link' ).length ) {
			$( '.comment-reply-link' ).addClass( 'et_pb_button' );
		}

		$( '#et_top_search' ).click( function() {
			var $search_container = $( '.et_search_form_container' );

			if ( $search_container.hasClass('et_pb_is_animating') ) {
				return;
			}

			$( '.et_menu_container' ).removeClass( 'et_pb_menu_visible et_pb_no_animation' ).addClass('et_pb_menu_hidden');
			$search_container.removeClass( 'et_pb_search_form_hidden et_pb_no_animation' ).addClass('et_pb_search_visible et_pb_is_animating');
			setTimeout( function() {
				$( '.et_menu_container' ).addClass( 'et_pb_no_animation' );
				$search_container.addClass( 'et_pb_no_animation' ).removeClass('et_pb_is_animating');
			}, 1000);
			$search_container.find( 'input' ).focus();

			et_set_search_form_css();
		});

		function et_hide_search() {
			if ( $( '.et_search_form_container' ).hasClass('et_pb_is_animating') ) {
				return;
			}

			$( '.et_menu_container' ).removeClass( 'et_pb_menu_hidden et_pb_no_animation' ).addClass( 'et_pb_menu_visible' );
			$( '.et_search_form_container' ).removeClass('et_pb_search_visible et_pb_no_animation' ).addClass( 'et_pb_search_form_hidden et_pb_is_animating' );
			setTimeout( function() {
				$( '.et_menu_container' ).addClass( 'et_pb_no_animation' );
				$( '.et_search_form_container' ).addClass( 'et_pb_no_animation' ).removeClass('et_pb_is_animating');
			}, 1000);
		}

		function et_set_search_form_css() {
			var $search_container = $( '.et_search_form_container' );
			var $body = $( 'body' );
			if ( $search_container.hasClass( 'et_pb_search_visible' ) ) {
				var header_height = $( '#main-header' ).innerHeight(),
					menu_width = $( '#top-menu' ).width(),
					font_size = $( '#top-menu li a' ).css( 'font-size' );
				$search_container.css( { 'height' : header_height + 'px' } );
				$search_container.find( 'input' ).css( 'font-size', font_size );
				if ( ! $body.hasClass( 'et_header_style_left' ) ) {
					$search_container.css( 'max-width', menu_width + 60 );
				} else {
					$search_container.find( 'form' ).css( 'max-width', menu_width + 60 );
				}
			}
		}

		$( '.et_close_search_field' ).click( function() {
			et_hide_search();
		});

		$( document ).mouseup( function(e) {
			var $header = $( '#main-header' );
			if ( $( '.et_menu_container' ).hasClass('et_pb_menu_hidden') ) {
				if ( ! $header.is( e.target ) && $header.has( e.target ).length === 0 )	{
					et_hide_search();
				}
			}
		});

		// Detect actual logo dimension, used for tricky fixed navigation transition
		function et_define_logo_dimension() {
			var $logo = $('#logo'),
				logo_src = $logo.attr( 'src' ),
				is_svg = logo_src.substr( -3, 3 ) === 'svg' ? true : false,
				$logo_wrap,
				logo_width,
				logo_height;

			// Append invisible wrapper at the bottom of the page
			$('body').append( $('<div />', {
				'id' : 'et-define-logo-wrap',
				'style' : 'position: fixed; bottom: 0; opacity: 0;'
			}));

			// Define logo wrap
			$logo_wrap = $('#et-define-logo-wrap');

			if( is_svg ) {
				$logo_wrap.addClass( 'svg-logo' );
			}

			// Clone logo to invisible wrapper
			$logo_wrap.html( $logo.clone().css({ 'display' : 'block' }).removeAttr( 'id' ) );

			// Get dimension
			logo_width = $logo_wrap.find('img').width();
			logo_height = $logo_wrap.find('img').height();

			// Add data attribute to $logo
			$logo.attr({
				'data-actual-width' : logo_width,
				'data-actual-height' : logo_height
			});

			// Destroy invisible wrapper
			$logo_wrap.remove();

			// Init logo transition onload
			et_fix_logo_transition( true );
		}

		if ( $('#logo').length ) {
			// Wait until logo is loaded before performing logo dimension fix
			// This comes handy when the page is heavy due to the use of images or other assets
			et_preload_image( $('#logo').attr('src'), et_define_logo_dimension );
		}

		// Set width for adsense in footer widget
		$('.footer-widget').each(function(){
			var $footer_widget = $(this),
				footer_widget_width = $footer_widget.width(),
				$adsense_ins = $footer_widget.find( '.widget_adsensewidget ins' );

			if ( $adsense_ins.length ) {
				$adsense_ins.width( footer_widget_width );
			}
		});

		/**
		 * Visual Builder adjustment
		 */
		function et_fb_side_nav_page_init() {
			$( window ).off( 'scroll', window.et_pb_window_side_nav_scroll_init );
			$( '#main-content .et_pb_side_nav' ).off( 'click', '.et_pb_side_nav a' );
			$( '#main-content .et_pb_side_nav' ).remove();
			et_pb_side_nav_page_init();
		}
		if ( $('body').is('.et-fb') ) {
			$(window).on( 'et_fb_root_did_mount', function() {
				et_fb_side_nav_page_init();

				et_all_elements_loaded();
			} );

			$(window).on( 'et_fb_section_content_change', et_fb_side_nav_page_init );
		} else {
			$( window ).load( et_all_elements_loaded );
		}
	} );

	// Fixing logo size transition in tricky header style
	function et_fix_logo_transition( is_onload ) {
		var $body                = $( 'body' ),
			$logo                = $( '#logo' ),
			logo_actual_width    = parseInt( $logo.attr( 'data-actual-width' ) ),
			logo_actual_height   = parseInt( $logo.attr( 'data-actual-height' ) ),
			logo_height_percentage = parseInt( $logo.attr( 'data-height-percentage' ) ),
			$top_nav             = $( '#et-top-navigation' ),
			top_nav_height       = parseInt( $top_nav.attr( 'data-height' ) ),
			top_nav_fixed_height = parseInt( $top_nav.attr( 'data-fixed-height' ) ),
			$main_header          = $('#main-header'),
			is_header_split      = $body.hasClass( 'et_header_style_split' ),
			is_fixed_nav         = $main_header.hasClass( 'et-fixed-header' ),
			is_hide_primary_logo = $body.hasClass( 'et_hide_primary_logo' ),
			is_hide_fixed_logo   = $body.hasClass( 'et_hide_fixed_logo' ),
			logo_height_base     = is_fixed_nav ? top_nav_height : top_nav_fixed_height,
			logo_wrapper_width,
			logo_wrapper_height;

		is_onload = typeof is_onload === 'undefined' ? false : is_onload;

		// Fix for inline centered logo in horizontal nav
		if ( is_header_split && ! window.et_is_vertical_nav ) {
			// On page load, logo_height_base should be top_nav_height
			if ( is_onload ) {
				logo_height_base = top_nav_height;
			}

			// Calculate logo wrapper height
			logo_wrapper_height = ( logo_height_base * ( logo_height_percentage / 100 ) + 22 );

			// Calculate logo wrapper width
			logo_wrapper_width = logo_actual_width * (  logo_wrapper_height / logo_actual_height  );

			// Override logo wrapper width to 0 if it is hidden
			if ( is_hide_primary_logo && ( is_fixed_nav || is_onload ) ) {
				logo_wrapper_width = 0;
			}

			if ( is_hide_fixed_logo && ! is_fixed_nav && ! is_onload ) {
				logo_wrapper_width = 0;
			}

 			// Set fixed width for logo wrapper to force correct dimension
			$( '.et_header_style_split .centered-inline-logo-wrap' ).css( { 'width' : logo_wrapper_width } );
		}
	}

	function et_toggle_slide_menu( force_state ) {
		var $slide_menu_container = $( '.et_header_style_slide .et_slide_in_menu_container' ),
			$page_container = $( '.et_header_style_slide #page-container, .et_header_style_slide.et_fixed_nav #main-header' ),
			$header_container = $( '.et_header_style_slide #main-header' ),
			is_menu_opened = $slide_menu_container.hasClass( 'et_pb_slide_menu_opened' ),
			set_to = typeof force_state !== 'undefined' ? force_state : 'auto',
			is_boxed_layout = $( 'body' ).hasClass( 'et_boxed_layout' ),
			page_container_margin = is_boxed_layout ? parseFloat( $( '#page-container' ).css( 'margin-left' ) ) : 0,
			slide_container_width = $slide_menu_container.innerWidth();

		if ( 'auto' !== set_to && ( ( is_menu_opened && 'open' === set_to ) || ( ! is_menu_opened && 'close' === set_to ) ) ) {
			return;
		}

		if ( is_menu_opened ) {
			$slide_menu_container.css( { right: '-' + slide_container_width + 'px' } );
			$page_container.css( { left: '0' } );

			if ( is_boxed_layout && et_is_fixed_nav ) {
				$header_container.css( { left : page_container_margin + 'px' } );
			}

			// hide the menu after animation completed
			setTimeout( function() {
				$slide_menu_container.css( { 'display' : 'none' } );
			}, 700 );
		} else {
			$slide_menu_container.css( { 'display' : 'block' } );
			// add some delay to make sure css animation applied correctly
			setTimeout( function() {
				$slide_menu_container.css( { right: '0' } );
				$page_container.css( { left: '-' + ( slide_container_width - page_container_margin ) + 'px' } );

				if ( is_boxed_layout && et_is_fixed_nav ) {
					var left_position = 0 > slide_container_width - ( page_container_margin * 2 ) ? Math.abs( slide_container_width - ( page_container_margin * 2 ) ) : '-' + ( slide_container_width - ( page_container_margin * 2 ) );

					if ( left_position < slide_container_width ) {
						$header_container.css( { left: left_position + 'px' } );
					}
				}
			}, 50 );
		}

		$( 'body' ).toggleClass( 'et_pb_slide_menu_active' );
		$slide_menu_container.toggleClass( 'et_pb_slide_menu_opened' );
	}

	// Scrolling to the correct place on page if Fixed Nav enabled
	function et_adjust_woocommerce_checkout_scroll() {
		if ( ! et_is_fixed_nav ) {
			return;
		}

		var window_width = parseInt( $et_window.width() );

		if ( 980 >= window_width ) {
			return;
		}

		var headerHeight = parseInt( $( '#main-header' ).innerHeight() );

		// scroll to the top of checkout form taking into account fixed header height
		$( 'html, body' ).animate({
			scrollTop: ( $( 'form.checkout' ).offset().top - 100 - headerHeight )
		}, 1000 );
	}

	$( '#main-header' ).on( 'click', '.et_toggle_slide_menu', function() {
		et_toggle_slide_menu();
	});

	if ( et_is_touch_device ) {
		// open slide menu on swipe left
		$et_window.on( 'swipeleft', function( event ) {
			var window_width = parseInt( $et_window.width() ),
				swipe_start = parseInt( event.swipestart.coords[0] ); // horizontal coordinates of the swipe start

			// if swipe started from the right edge of screen then open slide menu
			if ( 30 >= window_width - swipe_start ) {
				et_toggle_slide_menu( 'open' );
			}
		} );

		// close slide menu on swipe right
		$et_window.on( 'swiperight', function( event ){
			if ( $( 'body' ).hasClass( 'et_pb_slide_menu_active' ) ) {
				et_toggle_slide_menu( 'close' );
			}
		});
	}

	$( '#page-container' ).on( 'click', '.et_toggle_fullscreen_menu', function() {
		et_pb_toggle_fullscreen_menu();
	});

	function et_pb_toggle_fullscreen_menu() {
		var $menu_container = $( '.et_header_style_fullscreen .et_slide_in_menu_container' ),
			top_bar_height = $menu_container.find( '.et_slide_menu_top' ).innerHeight();

		$menu_container.toggleClass( 'et_pb_fullscreen_menu_opened' );
		$( 'body' ).toggleClass( 'et_pb_fullscreen_menu_active' );
		et_pb_resize_fullscreen_menu();

		if ( $menu_container.hasClass( 'et_pb_fullscreen_menu_opened' ) ) {
			$menu_container.addClass( 'et_pb_fullscreen_menu_animated' );

			// adjust the padding in fullscreen menu
			$menu_container.css( { 'padding-top': top_bar_height + 20 } );
		} else {
			setTimeout( function() {
				$menu_container.removeClass( 'et_pb_fullscreen_menu_animated' );
			}, 1000 );
		}
	}

	function et_pb_resize_fullscreen_menu(e) {
		if (isBuilder) {
			var $menu = jQuery('.et_header_style_fullscreen .et_slide_in_menu_container.et_pb_fullscreen_menu_opened');
			if ($menu.length > 0) {
				var height = jQuery(window.top).height();
				// Account for padding
				height -= parseInt($menu.css('padding-top'), 10);
				// and AdminBar
				if ($menu.closest('.admin-bar').length > 0) {
					height -= 32;
				}
				$menu.find('.et_pb_fullscreen_nav_container').css('max-height', height);
			}
		}
	}

	$( window ).unload( function () {
		/**
		 * Fix the issue with Fullscreen menu, that remains open,
		 * when back button is clicked in Firefox
		 */
		if ( $('body').hasClass('et_pb_fullscreen_menu_active') ) {
			$('.et_toggle_fullscreen_menu').trigger('click');
		}
	} );

	$( '.et_pb_fullscreen_nav_container' ).on( 'click', 'li.menu-item-has-children > a', function() {
		var $this_parent = $( this ).closest( 'li' ),
			$this_arrow = $this_parent.find( '>a .et_mobile_menu_arrow' ),
			$closest_submenu =  $this_parent.find( '>ul' ),
			is_opened_submenu = $this_arrow.hasClass( 'et_pb_submenu_opened' ),
			sub_menu_max_height;

		$this_arrow.toggleClass( 'et_pb_submenu_opened' );

		if ( is_opened_submenu ) {
			$closest_submenu.removeClass( 'et_pb_slide_dropdown_opened' );
			$closest_submenu.slideToggle( 700, 'easeInOutCubic' );
		} else {
			$closest_submenu.slideToggle( 700, 'easeInOutCubic' );
			$closest_submenu.addClass( 'et_pb_slide_dropdown_opened' );
		}

		return false;
	} );

	// define initial padding-top for fullscreen menu container
	if ( $( 'body' ).hasClass( 'et_header_style_fullscreen' ) ) {
		var $menu_container = $( '.et_header_style_fullscreen .et_slide_in_menu_container' );

		if ( $menu_container.length ) {
			var top_bar_height = $menu_container.find( '.et_slide_menu_top' ).innerHeight();
			$menu_container.css( { 'padding-top': top_bar_height + 20 } );
		}
	}

	// adjust the scrolling position on Woocommerce checkout page in case of error
	$( document.body ).on( 'checkout_error', function() {
		et_adjust_woocommerce_checkout_scroll();
	} );

	$( document.body ).on( 'updated_checkout', function( data ) {
		if ( 'failure' !== data.result ) {
			return;
		}

		et_adjust_woocommerce_checkout_scroll();
	} );

})(jQuery);

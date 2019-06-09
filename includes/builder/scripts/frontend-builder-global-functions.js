/*! ET frontend-builder-global-functions.js */
(function($){
	window.et_pb_smooth_scroll = function( $target, $top_section, speed, easing ) {
		var $window_width = $( window ).width();

		if ( $( 'body' ).hasClass( 'et_fixed_nav' ) && $window_width > 980 ) {
			$menu_offset = $( '#top-header' ).outerHeight() + $( '#main-header' ).outerHeight() - 1;
		} else {
			$menu_offset = -1;
		}

		if ( $ ('#wpadminbar').length && $window_width > 600 ) {
			$menu_offset += $( '#wpadminbar' ).outerHeight();
		}

		//fix sidenav scroll to top
		if ( $top_section ) {
			$scroll_position = 0;
		} else {
			$scroll_position = $target.offset().top - $menu_offset;
		}

		// set swing (animate's scrollTop default) as default value
		if( typeof easing === 'undefined' ){
			easing = 'swing';
		}

		$( 'html, body' ).animate( { scrollTop :  $scroll_position }, speed, easing );
	};

	window.et_pb_form_placeholders_init = function( $form ) {
		$form.find('input:text, input[type="email"], input[type="url"], textarea').each(function(index,domEle){
			var $et_current_input = jQuery(domEle),
				$et_comment_label = $et_current_input.siblings('label'),
				et_comment_label_value = $et_current_input.siblings('label').text();
			if ( $et_comment_label.length ) {
				$et_comment_label.hide();
				if ( $et_current_input.siblings('span.required') ) {
					et_comment_label_value += $et_current_input.siblings('span.required').text();
					$et_current_input.siblings('span.required').hide();
				}
				$et_current_input.val(et_comment_label_value);
			}
		}).bind('focus',function(){
			var et_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === et_label_text) jQuery(this).val("");
		}).bind('blur',function(){
			var et_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === "") jQuery(this).val( et_label_text );
		});
	};

	window.et_duplicate_menu = function( menu, append_to, menu_id, menu_class, menu_click_event ){
		append_to.each( function() {
			var $this_menu = $(this),
				$cloned_nav;

			// make this function work with existing menus, without cloning
			if ( '' !== menu ) {
				menu.clone().attr('id',menu_id).removeClass().attr('class',menu_class).appendTo( $this_menu );
			}

			$cloned_nav = $this_menu.find('> ul');
			$cloned_nav.find('.menu_slide').remove();
			$cloned_nav.find('li:first').addClass('et_first_mobile_item');

			$cloned_nav.find( 'a' ).on( 'click', function(){
				$(this).parents( '.et_mobile_menu' ).siblings( '.mobile_menu_bar' ).trigger( 'click' );
			} );

			if ( 'no_click_event' !== menu_click_event ) {
				$this_menu.on( 'click', '.mobile_menu_bar', function(){
					if ( $this_menu.hasClass('closed') ){
						$this_menu.removeClass( 'closed' ).addClass( 'opened' );
						$cloned_nav.stop().slideDown( 500 );
					} else {
						$this_menu.removeClass( 'opened' ).addClass( 'closed' );
						$cloned_nav.stop().slideUp( 500 );
					}
					return false;
				} );
			}
		} );

		$('#mobile_menu .centered-inline-logo-wrap').remove();
	};

	// remove placeholder text before form submission
	window.et_pb_remove_placeholder_text = function( $form ) {
		$form.find('input:text, textarea').each(function(index,domEle){
			var $et_current_input = jQuery(domEle),
				$et_label = $et_current_input.siblings('label'),
				et_label_value = $et_current_input.siblings('label').text();

			if ( $et_label.length && $et_label.is(':hidden') ) {
				if ( $et_label.text() == $et_current_input.val() )
					$et_current_input.val( '' );
			}
		});
	};

	window.et_fix_fullscreen_section = function() {
		var $et_window = $(window);

		$( 'section.et_pb_fullscreen' ).each( function(){
			var $this_section = $( this );

			$.proxy( et_calc_fullscreen_section, $this_section )();

			$et_window.on( 'resize', $.proxy( et_calc_fullscreen_section, $this_section ) );
		});
	};

	window.et_bar_counters_init = function( $bar_item ) {
		if ( ! $bar_item.length ) {
			return;
		}

		$bar_item.css({
			'width' : parseFloat( $bar_item.attr( 'data-width' ) ) + '%'
		});
	};

	window.et_fix_pricing_currency_position = function( $pricing_table ) {
		var $all_pricing_tables = typeof $pricing_table !== 'undefined' ? $pricing_table : $( '.et_pb_pricing_table' );

		if ( ! $all_pricing_tables.length ) {
			return;
		}

		$all_pricing_tables.each( function() {
			var $this_table = $( this ),
				$price_container = $this_table.find( '.et_pb_et_price' ),
				$currency = $price_container.length ? $price_container.find( '.et_pb_dollar_sign' ) : false,
				$price = $price_container.length ? $price_container.find( '.et_pb_sum' ) : false;

			if ( ! $currency || ! $price ) {
				return;
			}

			// adjust the margin of currency sign to make sure it doesn't overflow the price
			$currency.css( { 'marginLeft' : - $currency.width() + 'px' } );
		});
	};

	window.et_pb_set_responsive_grid = function( $grid_items_container, single_item_selector ) {
		setTimeout(function () {
			var container_width = $grid_items_container.innerWidth();
			var $grid_items = $grid_items_container.find(single_item_selector);
			var item_width = $grid_items.outerWidth(true);
			var last_item_margin = item_width - $grid_items.outerWidth();
			var columns_count = Math.round((container_width + last_item_margin) / item_width);
			var counter = 1;
			var first_in_row = 1;
			var $first_in_last_row = $();

			$grid_items.removeClass('last_in_row first_in_row on_last_row');
			$grid_items.filter(':visible').each(function () {
				var $this_element = $(this);

				if (!$this_element.hasClass('inactive')) {
					if (first_in_row === counter) {
						$this_element.addClass('first_in_row');
						$first_in_last_row = $this_element;
					} else if (0 === counter % columns_count) {
						$this_element.addClass('last_in_row');
						first_in_row = counter + 1;
					}
					counter++;
				}
			});
			if ($first_in_last_row.length) {
				var $module = $first_in_last_row.parents('.et_pb_module');
				//set margin bottom to 0 if the gallery is the last module on the column
				if ($module.is(':last-child')) {
					var column = $first_in_last_row.parents('.et_pb_column')[0];
					$(column).find('.et_pb_grid_item').removeClass('on_last_row');
					// keep gutter margin if gallery has pagination
					var pagination = $module.find('.et_pb_gallery_pagination');
					if (0 === pagination.length) {
						pagination = $module.find('.et_pb_portofolio_pagination');
					}
					if (0 === pagination.length || (pagination.length > 0 && !pagination.is(':visible'))) {
						$first_in_last_row.addClass('on_last_row');
						$first_in_last_row.nextAll().addClass('on_last_row');
					}
				}
			}
		}, 1); // need this timeout to make sure all the css applied before calculating sizes
	};

	window.et_pb_set_tabs_height = function( $tabs_module ) {
		if ( typeof $tabs_module === 'undefined' ) {
			$tabs_module = $( '.et_pb_tabs' );
		}

		if ( ! $tabs_module.length ) {
			return;
		}

		$tabs_module.each( function() {
			var $tab_controls = $( this ).find( '.et_pb_tabs_controls' );
			var $all_tabs = $tab_controls.find( 'li' );
			var max_height = 0;
			var small_columns      = '.et_pb_column_1_3, .et_pb_column_1_4, .et_pb_column_3_8';
			var in_small_column    = $( this ).parents( small_columns ).length > 0;
			var on_small_screen    = parseFloat( $( window ).width() ) < 768;
			var vertically_stacked = in_small_column || on_small_screen;

			if ( vertically_stacked ) {
				$( this ).addClass( 'et_pb_tabs_vertically_stacked' );
			}

			// determine the height of the tallest tab
			if ( $all_tabs.length ) {
				// remove the height attribute if it was added to calculate the height correctly
				$tab_controls.children('li').removeAttr('style');

				$all_tabs.each( function() {
					var tab_height = $( this ).outerHeight();

					if ( vertically_stacked ) {
						return;
					}

					if ( tab_height > max_height ) {
						max_height = tab_height;
					}
				});
			}

			if ( 0 !== max_height ) {
				// set the height of tabs container based on the height of the tallest tab
				$tab_controls.children('li').css('height', max_height);
			}
		});
	};

	window.et_pb_box_shadow_apply_overlay = function (el) {
		var pointerEventsSupport = document.body.style.pointerEvents !== undefined
			&&
			//For some reasons IE 10 tells that supports pointer-events, but it doesn't
			(document.documentMode === undefined || document.documentMode >= 11);

		if (pointerEventsSupport) {
			$(el).each(function () {
				if (! $(this).children('.box-shadow-overlay').length) {
					$(this)
						.addClass('has-box-shadow-overlay')
						.prepend('<div class="box-shadow-overlay"></div>');
				}
			});
		} else {
			$(el).addClass('.et-box-shadow-no-overlay');
		}
	};

	window.et_pb_init_nav_menu = function($et_menus) {
		$et_menus.each(function() {
			var $et_menu = $(this);

			// don't attach event handlers several times to the same menu
			if ( $et_menu.data('et-is-menu-ready') ) {
				return;
			}

			$et_menu.find('li').hover(function() {
				window.et_pb_toggle_nav_menu($(this), 'open');
			}, function() {
				window.et_pb_toggle_nav_menu($(this), 'close');
			} );

			// close all opened menus on touch outside the menu
			$('body').on('touchend', function(event){
				if ($(event.target).closest('ul.nav, ul.menu').length < 1 && $('.et-hover').length > 0) {
					window.et_pb_toggle_nav_menu($('.et-hover'), 'close');
				}
			});

			// Dropdown menu adjustment for touch screen
			$et_menu.find('li.menu-item-has-children').on('touchend', function(event) {
				var $closest_li = $(event.target).closest('.menu-item');

				// no need special processing if parent li doesn't have hidden child elements
				if (! $closest_li.hasClass('menu-item-has-children')) {
					return;
				}

				var $this_el = $(this);
				var is_mega_menu_opened = $closest_li.closest('.mega-menu-parent.et-touch-hover').length > 0;

				// open submenu on 1st tap
				// open link on second tap
				if ($this_el.hasClass('et-touch-hover') || is_mega_menu_opened) {
					var href = $this_el.find('>a').attr('href');

					if (typeof href !== 'undefined') {//if parent link is not empty then open the link
						window.location = $this_el.find('>a').attr('href');
					}
				} else {
					var $opened_menu = $(event.target);
					var $already_opened_menus = $opened_menu.closest('.menu-item').siblings('.et-touch-hover');
					// close the menu before opening new one
					if ($opened_menu.closest('.et-touch-hover').length < 1) {
						window.et_pb_toggle_nav_menu($('.et-hover'), 'close', 0);
					}

					$this_el.addClass('et-touch-hover');

					if ($already_opened_menus.length > 0) {
						var $submenus_in_already_opened = $already_opened_menus.find('.et-touch-hover');
						// close already opened submenus to avoid overlaps
						window.et_pb_toggle_nav_menu($already_opened_menus, 'close');
						window.et_pb_toggle_nav_menu($submenus_in_already_opened, 'close');
					}
					// open new submenu
					window.et_pb_toggle_nav_menu($this_el, 'open');
				}

				event.preventDefault();
				event.stopPropagation();
			} );

			$et_menu.find( 'li.mega-menu' ).each(function(){
				var $li_mega_menu           = $(this),
					$li_mega_menu_item      = $li_mega_menu.children( 'ul' ).children( 'li' ),
					li_mega_menu_item_count = $li_mega_menu_item.length;

				if ( li_mega_menu_item_count < 4 ) {
					$li_mega_menu.addClass( 'mega-menu-parent mega-menu-parent-' + li_mega_menu_item_count );
				}
			});

			// mark the menu as ready
			$et_menu.data('et-is-menu-ready', 'ready');
		});
	};

	window.et_pb_toggle_nav_menu = function($element, state, delay) {
		if ( 'open' === state ) {
			if ( ! $element.closest( 'li.mega-menu' ).length || $element.hasClass( 'mega-menu' ) ) {
				$element.addClass( 'et-show-dropdown' );
				$element.removeClass( 'et-hover' ).addClass( 'et-hover' );
			}
		} else {
			var closeDelay = typeof delay !== 'undefined' ? delay : 200;
			$element.removeClass( 'et-show-dropdown' );
			$element.removeClass( 'et-touch-hover' );

			setTimeout( function() {
				if ( ! $element.hasClass( 'et-show-dropdown' ) ) {
					$element.removeClass( 'et-hover' );
				}
			}, closeDelay );
		}
	};

	window.et_pb_apply_sticky_image_effect = function($sticky_image_el) {
		var $row                = $sticky_image_el.closest('.et_pb_row');
		var $section            = $row.closest('.et_pb_section');
		var $column             = $sticky_image_el.closest('.et_pb_column');
		var sticky_class        = 'et_pb_section_sticky';
		var sticky_mobile_class = 'et_pb_section_sticky_mobile';
		var $lastRowInSection   = $section.children('.et_pb_row').last();
		var $lastColumnInRow    = $row.children('.et_pb_column').last();
		var $lastModuleInColumn = $column.children('.et_pb_module').last();

		// If it is not in the last row, continue
		if (! $row.is($lastRowInSection)) {
			return true;
		}

		$lastRowInSection.addClass('et-last-child');

		// Make sure sticky image is the last element in the column
		if (! $sticky_image_el.is($lastModuleInColumn)) {
			return true;
		}

		// If it is in the last row, find the parent section and attach new class to it
		if (! $section.hasClass(sticky_class)) {
			$section.addClass(sticky_class);
		}

		$column.addClass('et_pb_row_sticky');

		if (! $section.hasClass(sticky_mobile_class) && $column.is($lastColumnInRow)) {
			$section.addClass(sticky_mobile_class);
		}
	};
})(jQuery);

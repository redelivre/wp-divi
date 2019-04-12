(function($){
	// Create BFB event.
	var BFB_event = document.createEvent('Event');
	var enable_bfb_attempts = 0;

	// set the BFB event name.
	BFB_event.initEvent('et_fb_before_disabling_bfb', true, true);

	// Disable builder metabox drag until builder is loaded
	var $metabox = $('#et_pb_layout').addClass('et-drag-disabled');
	$(window).on('et_fb_init_app_after', function() {
		$metabox.removeClass('et-drag-disabled');
	});

	$(window).on('load', function() {
		setTimeout(function() {
			var $et_toggle_builder_button     = $('#et_pb_toggle_builder');
			var $et_pb_fb_cta                 = $('#et_pb_fb_cta');
			var $et_pb_toggle_builder_wrapper = $('.et_pb_toggle_builder_wrapper');

			$et_pb_toggle_builder_wrapper.css('opacity', '');

			$et_toggle_builder_button.addClass('et_pb_ready');

			if ($et_toggle_builder_button.hasClass('et_pb_builder_is_used')) {
				$et_pb_fb_cta.addClass('et_pb_ready');
			}
		}, 250);

		// Callback for metabox sortable change and screen option toggle
		var updateFirstVisibleState = function() {
			$(this).find('.postbox').removeClass('first-visible');

			if ($(this).is('#normal-sortables')) {
				$(this).find('.postbox:visible:first').addClass('first-visible');
			}
		};

		// Update first-visible classname on meta box being moved
		$('.meta-box-sortables').sortable('option', 'update', updateFirstVisibleState);

		// Update first-visible classname on screen option being toggled
		$('#screen-options-wrap').on('change', '.hide-postbox-tog', function() {
			$('.meta-box-sortables').each(updateFirstVisibleState);
		});

		// Add classname on body which marks that meta box is currently dragged
		$('.meta-box-sortables').on('sortstart', function() {
			$('body').addClass('et-bfb--metabox-dragged');
		});

		// Remove classname on body which marks that meta box is currently dragged
		$('.meta-box-sortables').on('sortstop', function() {
			$('body').removeClass('et-bfb--metabox-dragged');
		});
	} );

	$(window).on('et_fb_disabling_bfb_confirmed', function() {
		var $et_pb_old_content = $('#et_pb_old_content');
		var $use_builder_custom_field = $('#et_pb_use_builder');
		var $save_button = $('#minor-publishing-actions #save-post').length > 0 ? $('#minor-publishing-actions #save-post') : $('#publishing-action #publish');

		et_set_wp_editor_content($et_pb_old_content.val());

		$et_pb_old_content.val('');
		$use_builder_custom_field.val('off');

		// emulate post save to reload page with new settings
		et_maybe_toggle_bfb($save_button);
	});

	// Adjust page settings bar on window resize, after builder is initialized and after toolbar setting is changed
	var pageSettingsBarAdjustmentTimeout;

	function pageSettingsBarAdjustment() {
		// Clear untriggered callback because new callback is already called
		if (pageSettingsBarAdjustmentTimeout) {
			clearTimeout(pageSettingsBarAdjustmentTimeout);
		}

		pageSettingsBarAdjustmentTimeout = setTimeout(function() {
			// Selectors
			var $layout = $('#et_pb_layout');
			var $app = $('#et-fb-app');
			var $toggleBuilderWrapper = $('.et_pb_toggle_builder_wrapper.et_pb_builder_is_used');
			var $toggleBuilder = $('#et_pb_toggle_builder');
			var $toggleVisualBuilder = $('#et_pb_fb_cta');
			var $buttonGroupResponsiveMode = $('.et-fb-button-group--responsive-mode');
			var $buttonGroupBuilderMode = $('.et-fb-button-group--builder-mode');
			var $settingsBarColumnRight = $('.et-fb-page-settings-bar__column--right');

			// Calculating width
			var toggleBuilderWrapperWidth = $toggleBuilderWrapper.outerWidth();
			var builderToggleWidth = parseFloat($toggleBuilder.outerWidth()) + parseFloat($toggleVisualBuilder.outerWidth()) + parseFloat($toggleVisualBuilder.css('marginLeft'));
			var buttonGroupResponsiveModeWidth = $buttonGroupResponsiveMode.length && $buttonGroupResponsiveMode.is(':visible') ? $buttonGroupResponsiveMode.outerWidth() : 0;
			var buttonGroupBuilderModeWidth = $buttonGroupBuilderMode.length && $buttonGroupBuilderMode.is(':visible') ? ($buttonGroupBuilderMode.outerWidth() + 10) : 0;
			var settingsBarColumnRightWidth = $settingsBarColumnRight.length ? $settingsBarColumnRight.outerWidth() : 0;
			var buttonGroupTopWidth = buttonGroupResponsiveModeWidth + buttonGroupBuilderModeWidth + settingsBarColumnRightWidth;

			// If there's no room for preview and builder mode button, add necessary classname which will adjust
			// the position of preview and builder mode buttons accordingly
			if ((toggleBuilderWrapperWidth - (builderToggleWidth + buttonGroupTopWidth)) <= 30) {
				$layout.addClass('et_pb_layout--compact');
				$app.addClass('et-fb-app--compact');
			} else {
				$layout.removeClass('et_pb_layout--compact');
				$app.removeClass('et-fb-app--compact');
			}
		}, 50);
	}

	$(window).on('et_fb_init_app_after resize et_fb_toolbar_change', pageSettingsBarAdjustment);
	$(window.top).on('et-preview-animation-complete et-bfb-modal-snapped', pageSettingsBarAdjustment);

	$('#et_pb_toggle_builder').click(function(event){
		event.preventDefault();

		var $clicked_button = $(this);
		var $use_builder_custom_field = $('#et_pb_use_builder');
		var content = et_get_wp_editor_content();
		var $save_button = $('#minor-publishing-actions #save-post').length > 0 ? $('#minor-publishing-actions #save-post') : $('#publishing-action #publish');
		var $et_pb_old_content = $('#et_pb_old_content');
		var post_title = $('#titlediv #title').length > 0 ? $('#titlediv #title').val() : '';

		// disable/enable BFB
		if ($clicked_button.hasClass('et_pb_builder_is_used')) {
			// trigger an event so confirmation modal will be opened from BFB
			window.dispatchEvent(BFB_event);
			return;
		} else {
			$use_builder_custom_field.val('on');

			if ('' !== content) {
				// Save content as old content on post meta
				$et_pb_old_content.val(content);

				if (content.indexOf('[et_pb_section') < 0) {
					// Reformat content as text module if it's not a builder shortcode
					content = '[et_pb_section][et_pb_row][et_pb_column type="' + et_bfb_options.default_initial_column_type + '"][' + et_bfb_options.default_initial_text_module + ']' + content + '[/' + et_bfb_options.default_initial_text_module + '][/et_pb_column][/et_pb_row][/et_pb_section]';
				}

				// Re-set content on editor
				et_set_wp_editor_content(content);
			}

			// Update Post meta directly via ajax request if post has no title or content.
			// Because in this case clicking `Save` button won't update the post meta.
			if ('' === content && '' === post_title) {
				var post_id = $('#post_ID').length > 0 ? $('#post_ID').val() : 0;
				$.ajax({
					type: "POST",
					url: et_bfb_options.ajaxurl,
					data: {
						action : 'et_builder_activate_bfb_auto_draft',
						et_enable_bfb_nonce : et_bfb_options.et_enable_bfb_nonce,
						et_post_id : post_id
					}
				});
			}

			$('body').append('<div class="et-bfb-page-preloading"></div>');
		}

		et_maybe_toggle_bfb($save_button);
	});

	function et_maybe_toggle_bfb($save_button) {
		// Some plugins set title to required which prevents saving the page when the field is empty
		if ($('#title').prop('required')) {
			$('#title').removeProp('required');
		}

		// make sure save button clickable
		if ($save_button.hasClass('disabled')) {
			// wait for 1 second and try again
			// limit is 20 seconds
			if (enable_bfb_attempts <= 20) {
				enable_bfb_attempts++;
				setTimeout(function(){
					et_maybe_toggle_bfb($save_button);
				}, 1000);
			} else {
				$('.et-bfb-page-preloading').remove();
			}
		} else {
			// emulate post save to reload page with new settings
			$save_button.trigger('click');
		}
	}

	function et_get_wp_editor_content() {
		var content;

		if ('undefined' !== typeof window.tinyMCE && window.tinyMCE.get('content') && ! window.tinyMCE.get('content').isHidden()) {
			content = window.tinyMCE.get('content').getContent();
		} else {
			content = $('#content').val();
		}

		return content.trim();
	}

	function et_set_wp_editor_content(new_content) {
		if ('undefined' !== typeof window.tinyMCE && window.tinyMCE.get('content') && ! window.tinyMCE.get('content').isHidden()) {
			var editor = window.tinyMCE.get('content');
			editor.setContent(new_content, { format : 'html' });
		} else {
			$('#content').val(new_content);
		}
	}
})(jQuery)

/* <![CDATA[ */
	var clearpath = ePanelSettings.clearpath;

	jQuery(document).ready(function($){
		$('#epanel-content,#epanel-content > div').tabs({
			fx: {
				opacity: 'toggle',
				duration:'fast'
			},
			selected: 0,
			activate: function( event, ui ) {
				$epanel = $('#epanel');

				if ( $epanel.hasClass('onload') ) {
					$epanel.removeClass('onload');
				}
			}
		});

		$(".box-description").click(function(){
			var descheading = $(this).parent('.epanel-box').find(".box-title h3").html();
			var desctext = $(this).parent('.epanel-box').find(".box-title .box-descr").html();

			$('body').append("<div id='custom-lbox'><div class='box-desc'><div class='box-desc-top'>"+ ePanelSettings.help_label +"</div><div class='box-desc-content'><h3>"+descheading+"</h3>"+desctext+"<div class='lightboxclose'></div> </div> <div class='box-desc-bottom'></div>	</div></div>");

			$( '.lightboxclose' ).click( function() {
				et_pb_close_modal( $( '#custom-lbox' ) );
			});
		});

		$(".defaults-button").click(function(e) {
			e.preventDefault();
			$(".reset-popup-overlay, .defaults-hover").addClass('active');
		});

		$( '.no' ).click( function() {
			et_pb_close_modal( $( '.reset-popup-overlay' ), 'no_remove' );

			//clean the modal classes when animation complete
			setTimeout( function() {
				$( '.reset-popup-overlay, .defaults-hover' ).removeClass( 'active et_pb_modal_closing' );
			}, 600 );
		});

		// ":not([safari])" is desirable but not necessary selector
		// ":not([safari])" is desirable but not necessary selector
		$('input:checkbox:not([safari]):not(.yes_no_button)').checkbox();
		$('input[safari]:checkbox:not(.yes_no_button)').checkbox({cls:'jquery-safari-checkbox'});
		$('input:radio:not(.yes_no_button)').checkbox();

		// Yes - No button UI
		$('.yes_no_button').each(function() {
			$checkbox = $(this),
			value     = $checkbox.is(':checked'),
			state     = value ? 'et_pb_on_state' : 'et_pb_off_state',
			$template = $($('#epanel-yes-no-button-template').html()).find('.et_pb_yes_no_button').addClass(state);

			$checkbox.hide().after($template);
		});

		$('.box-content').on( 'click', '.et_pb_yes_no_button', function(e){
			e.preventDefault();

			var $click_area = $(this),
				$box_content = $click_area.parents('.box-content'),
				$checkbox    = $box_content.find('input[type="checkbox"]'),
				$state       = $box_content.find('.et_pb_yes_no_button');

			$state.toggleClass('et_pb_on_state et_pb_off_state');

			if ( $checkbox.is(':checked' ) ) {
				$checkbox.prop('checked', false);
			} else {
				$checkbox.prop('checked', true);
			}

		});

		var $save_message = $("#epanel-ajax-saving");

		$('#epanel-save-top').click(function(e){
			e.preventDefault();

			$('#epanel-save').trigger('click');
		})

		$('#epanel-save').click(function(){
			var options_fromform = $('#main_options_form').formSerialize(),
				add_nonce = '&_ajax_nonce='+ePanelSettings.epanel_nonce;

			options_fromform += add_nonce;

			var save_button=$(this);
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: options_fromform,
				beforeSend: function ( xhr ){
					$save_message.removeAttr('class').fadeIn('fast');
				},
				success: function(response){
					$save_message.addClass('success-animation');

					setTimeout(function(){
						$save_message.fadeOut();
					},500);
				}
			});

			return false;
		});

		function et_pb_close_modal( $overlay, no_overlay_remove ) {
			var $modal_container = $overlay;

			// add class to apply the closing animation to modal
			$modal_container.addClass( 'et_pb_modal_closing' );

			//remove the modal with overlay when animation complete
			setTimeout( function() {
				if ( 'no_remove' !== no_overlay_remove ) {
					$modal_container.remove();
				}
			}, 600 );
		}
	});
/* ]]> */
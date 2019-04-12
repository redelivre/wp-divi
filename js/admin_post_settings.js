(function($){
	function init_post_format_js() {
		var post_format_option_type = $('.editor-post-format select').length > 0 ? 'select' : 'check';
		var $post_format            = 'select' === post_format_option_type ? $('.editor-post-format select') : $('input[name="post_format"]');
		var $settings               = $('.et_divi_format_setting');
		var $use_bg_color_setting   = $('#et_post_use_bg_color');

		$('.color-picker-hex').wpColorPicker();
		
		$post_format.change( function() {
			var $this = $(this);

			$settings.hide();

			$('.et_divi_format_setting' + '.et_divi_' + $this.val() + '_settings').show();

			$use_bg_color_setting.trigger('change');
		} );

		$use_bg_color_setting.change(function() {
			var $this = $(this);
			
			if ($this.is(':visible')) {
				$('.et_post_bg_color_setting').toggle($this.is(':checked'));
			}
		});

		if ('select' === post_format_option_type) {
			$post_format.trigger('change');
		} else {
			$post_format.filter(':checked').trigger('change');
		}
	}
	$(document).ready(function() {
		init_post_format_js();
	});
	
	// Init when Gutenberg interface is ready
	$(document).on('ETGBReady', event => {
		setTimeout(function() {
			init_post_format_js();
		}, 100);
	});
})(jQuery);

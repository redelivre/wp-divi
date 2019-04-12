(function ($) {
  var $toggleBuilderWrapper = $('.et_pb_toggle_builder_wrapper');
  var $pageSettings         = $('.et_pb_page_setting');
  var $layoutSettings       = $('.et_pb_page_layout_settings');
  var $postFormat           = $('#formatdiv');
  var options               = window.et_pb_options;

  function hide() {
    var $pageLayoutSettings = $layoutSettings.closest('#et_settings_meta_box').find('.et_pb_page_layout_settings');

    if ($pageSettings.filter(':visible').length > 1) {
      $pageLayoutSettings.hide();
      $layoutSettings.find('.et_pb_side_nav_settings').show();
    } else {
      if (options.post_type !== 'post' && options.is_third_party_post_type === 'no') {
        $pageLayoutSettings.hide();
      }

      $layoutSettings.closest('#et_settings_meta_box').find('.et_pb_side_nav_settings').show();
      $layoutSettings.closest('#et_settings_meta_box').find('.et_pb_single_title').show();
    }

    if ($pageLayoutSettings.length > 0) {
      var $fullwidth = $pageLayoutSettings.find('option[value="et_full_width_page"]');

      if ($fullwidth.length > 0) {
        $fullwidth.show();
      }
    }

    // On post, hide post format UI and layout settings if pagebuilder is activated
    if ($postFormat.length) {
      var active = $postFormat.find('input[type="radio"]:checked').val();

      $postFormat.hide();
      $('.et_divi_format_setting.et_divi_' + active + '_settings').hide();
    }

    // Show project navigation option when builder enabled
    if (options.post_type === 'project') {
      $layoutSettings.closest('#et_settings_meta_box').find('.et_pb_project_nav').show();
    }
  }

  if ($toggleBuilderWrapper.hasClass('et_pb_builder_is_used')) {
    hide();
  }
}(jQuery));

(function($){
    /**
     * Find all Divi Modules outside the main content area
     * which are not wrapped with `#et-boc` and wrap it to make sure styles applied correcly
     *
     * Applied on CPT only to support 3rd party plugins which adds Divi Modules to various places like Header, Footer, etc.
     *
     */

    $divi_modules_outside_main_content = $('.et_pb_module:not(#et-boc .et_pb_module, .et_pb_section .et_pb_module), .et_pb_row:not(#et-boc .et_pb_row, .et_pb_section .et_pb_row), .et_pb_section:not(#et-boc .et_pb_section)');

    if ($divi_modules_outside_main_content.length > 0) {
        $divi_modules_outside_main_content.each(function() {
            var $module = $(this);

            // Make sure we didn't wrap this content area yet
            if ($module.closest('#et-boc').length === 0) {
                $module.wrap('<div id="et-boc"></div>');
            }
        })
    }
})(jQuery);
(function () {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEyecixHandler = function ($scope, $) {
        console.log($scope);
    };

    // Make sure you run this code under Elementor.
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/awesomesauce.default', WidgetEyecixHandler);
    });

})(jQuery);
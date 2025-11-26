jQuery(window).on('elementor/frontend/init', function () {

    elementorFrontend.hooks.addAction('frontend/element_ready/itinerary_widget.default', function ($scope, $) {

        // Find the specific elements within this widget instance
        var $accordionTitles = $scope.find('.elementor-tab-title');

        $accordionTitles.on('click', function () {
            var $this = $(this),
                $accordionItem = $this.closest('.elementor-accordion-item'),
                $accordionContent = $this.next('.elementor-tab-content'),
                isActive = $this.hasClass('elementor-active');

            // 1. Reset all other items (if you want "Accordion" behavior, not "Toggle")
            // Remove this block if you want multiple items open at once
            $scope.find('.elementor-tab-title').removeClass('elementor-active');
            $scope.find('.elementor-tab-content').slideUp().removeClass('elementor-active');
            $scope.find('.elementor-tab-title').attr('aria-expanded', 'false');

            // 2. Toggle the clicked item
            if (!isActive) {
                $this.addClass('elementor-active');
                $accordionContent.slideDown().addClass('elementor-active');
                $this.attr('aria-expanded', 'true');
            }
        });
    });
});
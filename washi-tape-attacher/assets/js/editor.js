(function($) {
    'use strict';

    /**
     * This script handles the live preview updates in the Elementor editor.
     */
    var WashiTapeEditorHandler = function($scope, $) {

        // A function to update the tape based on the current settings
        var updateTapePreview = function() {
            var elementId = $scope.data('id');
            var model = elementor.elements.models[elementId];

            if (!model) {
                return;
            }

            var settings = model.get('settings').attributes;
            var $tape = $scope.find('.wta-tape-instance-preview');

            // If tape is disabled or no tape is selected, remove it and exit
            if (settings.wta_enable !== 'yes' || !settings.wta_tape_id || settings.wta_tape_id === '0') {
                if ($tape.length) {
                    $tape.remove();
                }
                return;
            }

            // If the tape div doesn't exist, create it
            if (!$tape.length) {
                $tape = $('<div class="wta-tape-instance-preview"></div>');
                $scope.append($tape);
            }

            // --- Update SVG Content ---
            var currentTapeId = $tape.attr('data-tape-id');
            if (currentTapeId !== settings.wta_tape_id) {
                $tape.attr('data-tape-id', settings.wta_tape_id);
                // Fetch new SVG via AJAX
                $.ajax({
                    url: wtaEditor.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'wta_get_tape_svg',
                        nonce: wtaEditor.nonce,
                        tape_id: settings.wta_tape_id
                    },
                    success: function(response) {
                        if (response.success) {
                            $tape.html(response.data.svg);
                        } else {
                            $tape.html(''); // Clear on error
                        }
                    }
                });
            }
            
            // --- Update Styles ---
            // Get responsive settings
            var device = elementor.channels.deviceMode.request('currentMode');
            var width = settings.wta_width_tablet && device === 'tablet' ? settings.wta_width_tablet : (settings.wta_width_mobile && device === 'mobile' ? settings.wta_width_mobile : settings.wta_width);
            var height = settings.wta_height_tablet && device === 'tablet' ? settings.wta_height_tablet : (settings.wta_height_mobile && device === 'mobile' ? settings.wta_height_mobile : settings.wta_height);

            $tape.css({
                width: width.size + width.unit,
                height: height.size + height.unit,
                top: settings.wta_vertical_offset.size + settings.wta_vertical_offset.unit,
                left: settings.wta_horizontal_offset.size + settings.wta_horizontal_offset.unit,
                transform: 'rotate(' + settings.wta_rotation.size + 'deg)',
                zIndex: settings.wta_z_index
            });
        };

        // Listen for changes on the specific controls
        var controls = [
            'wta_enable', 'wta_tape_id', 'wta_width', 'wta_height', 
            'wta_horizontal_offset', 'wta_vertical_offset', 'wta_rotation', 'wta_z_index'
        ];

        $.each(controls, function(index, control_name) {
            elementor.channels.editor.on('change:' + control_name, function(controlView, editor, newValues) {
                // Check if the change happened in the current element
                if (controlView.model.get('id') === $scope.data('id')) {
                    updateTapePreview();
                }
            });
        });
        
        // Also run on device mode change to update responsive controls
        elementor.channels.deviceMode.on('change', updateTapePreview);

        // Initial update on load
        updateTapePreview();
    };

    // Register the handler for all element types
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', WashiTapeEditorHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/section', WashiTapeEditorHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/column', WashiTapeEditorHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/container', WashiTapeEditorHandler);
    });

})(jQuery);

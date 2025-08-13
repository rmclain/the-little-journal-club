/**
 * TLJC Posts Editor Script
 */
(function ($) {
    'use strict';

    var TLJCPostsEditor = {
        init: function () {
            this.initWashiTapePreview();
        },

        initWashiTapePreview: function () {
            // Initialize washi tape preview when element is ready
            elementor.hooks.addAction('panel/open_editor/widget/tljc_posts', this.onPanelOpen);
            elementor.hooks.addAction('panel/open_editor/widget/tljc_posts', this.onPanelChange);
        },

        onPanelOpen: function (panel, model, view) {
            // Initialize preview when panel opens
            TLJCPostsEditor.updateWashiTapePreview(panel);
        },

        onPanelChange: function (panel, model, view) {
            // Update preview when settings change
            panel.$el.on('change', 'select, input', function () {
                TLJCPostsEditor.updateWashiTapePreview(panel);
            });
        },

        updateWashiTapePreview: function (panel) {
            var $element = panel.$el.closest('.elementor-element');
            var $cards = $element.find('.tljc-post-card');
            var settings = panel.getSettings();

            $cards.each(function () {
                var $card = $(this);
                $card.find('.tljc-washi-tape-element').remove();
                if (settings.show_washi_tape === 'yes' && settings.washi_tape_style) {
                    var $tape = $('<div class="tljc-washi-tape-element"></div>')
                        .attr('data-tape-id', settings.washi_tape_style)
                        .attr('data-position', settings.washi_tape_position)
                        .attr('data-rotation', settings.washi_tape_rotation && settings.washi_tape_rotation.size ? settings.washi_tape_rotation.size : 0)
                        .attr('data-width', settings.washi_tape_width && settings.washi_tape_width.size ? settings.washi_tape_width.size : 180)
                        .attr('data-height', settings.washi_tape_height && settings.washi_tape_height.size ? settings.washi_tape_height.size : 45);
                    $card.prepend($tape);
                }
            });

            if (typeof initWashiTapes === 'function') {
                initWashiTapes();
                // Fallback: if SVG is not present, try again after a short delay
                setTimeout(function () {
                    $cards.find('.tljc-washi-tape-element').each(function () {
                        if (!$(this).find('svg').length) {
                            initWashiTapes();
                        }
                    });
                }, 300);
            }
        }
    };

    // Initialize when Elementor is ready
    $(window).on('elementor/frontend/init', function () {
        elementor.hooks.addAction('frontend/element_ready/tljc_posts.default', function () {
            TLJCPostsEditor.init();
        });
    });

})(jQuery); 
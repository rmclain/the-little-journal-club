(function($) {
    'use strict';

    // Handler for Washi Tape controls
    var WashiTapeHandler = elementorModules.frontend.handlers.Base.extend({
        getDefaultSettings: function() {
            return {
                selectors: {
                    wrapper: '.ljc-washi-tape-wrapper',
                    tape: '.ljc-tape'
                }
            };
        },

        getDefaultElements: function() {
            var selectors = this.getSettings('selectors');
            return {
                $wrapper: this.$element.find(selectors.wrapper),
                $tape: this.$element.find(selectors.tape)
            };
        },

        bindEvents: function() {
            // Bind to control changes
            this.addControlsEventListeners();
        },

        addControlsEventListeners: function() {
            var self = this;

            // Listen for any control changes in the washi tape section
            self.controls = {
                tape_style: self.getElementSettings('tape_style'),
                tape_position: self.getElementSettings('tape_position'),
                enable_washi_tape: self.getElementSettings('enable_washi_tape'),
                tape_width: self.getElementSettings('tape_width'),
                tape_height: self.getElementSettings('tape_height'),
                tape_rotation: self.getElementSettings('tape_rotation'),
                tape_vertical_offset: self.getElementSettings('tape_vertical_offset'),
                tape_custom_image: self.getElementSettings('tape_custom_image'),
                tape_image_size: self.getElementSettings('tape_image_size'),
                tape_image_opacity: self.getElementSettings('tape_image_opacity')
            };

            // Update on any control change
            Object.keys(self.controls).forEach(function(controlName) {
                self.addControlListener(controlName, function(controlValue) {
                    self.updateTape(controlName, controlValue);
                });
            });
        },

        updateTape: function(controlName, value) {
            var self = this;
            var $wrapper = self.elements.$wrapper;

            // Update the control value in our stored settings
            self.controls[controlName] = value;

            // If wrapper doesn't exist, create it
            if (!$wrapper.length) {
                $wrapper = $('<div>', {
                    class: 'ljc-washi-tape-wrapper',
                    'data-tape-enabled': 'true'
                });
                self.$element.prepend($wrapper);
                self.elements.$wrapper = $wrapper;
            }

            // Handle enable/disable
            if (controlName === 'enable_washi_tape') {
                $wrapper.toggle(value === 'yes');
                return;
            }

            // Handle style changes
            if (controlName === 'tape_style') {
                self.elements.$tape.removeClass(function(index, className) {
                    return (className.match(/(^|\s)ljc-tape-\S+/g) || []).join(' ');
                }).addClass('ljc-tape ljc-tape-' + self.controls.tape_position + ' ljc-tape-' + value);
            }

            // Handle position changes
            if (controlName === 'tape_position') {
                self.updateTapePosition(value);
            }

            // Handle dimension changes
            if (['tape_width', 'tape_height', 'tape_vertical_offset'].includes(controlName)) {
                var size = value.size + value.unit;
                if (controlName === 'tape_width') {
                    self.elements.$tape.css('width', size);
                } else if (controlName === 'tape_height') {
                    self.elements.$tape.css('height', size);
                } else if (controlName === 'tape_vertical_offset') {
                    self.elements.$tape.css('margin-top', size);
                }
            }

            // Handle custom image
            if (controlName === 'tape_custom_image' && self.controls.tape_style === 'custom-image') {
                if (value.url) {
                    self.elements.$tape.css('background-image', 'url(' + value.url + ')');
                }
            }

            // Ensure the wrapper remains visible
            $wrapper.css('display', 'block');
        },

        updateTapePosition: function(position) {
            var self = this;
            var $wrapper = self.elements.$wrapper;
            var $existingTapes = $wrapper.find('.ljc-tape');

            // Remove existing tapes
            $existingTapes.remove();

            // Create new tape(s) based on position
            if (position === 'both' || position === 'left') {
                self.createTape('left');
            }
            if (position === 'both' || position === 'right') {
                self.createTape('right');
            }
            if (position === 'center') {
                self.createTape('center');
            }
        },

        createTape: function(position) {
            var self = this;
            var $wrapper = self.elements.$wrapper;
            var style = self.controls.tape_style;
            
            var $tape = $('<div>', {
                class: 'ljc-tape ljc-tape-' + position + ' ljc-tape-' + style
            });

            if (style === 'custom-image' && self.controls.tape_custom_image.url) {
                $tape.css('background-image', 'url(' + self.controls.tape_custom_image.url + ')');
            }

            $wrapper.prepend($tape);
        },

        onElementChange: function(propertyName) {
            // This ensures we catch all control changes
            if (propertyName.startsWith('tape_')) {
                this.updateTape(propertyName, this.getElementSettings(propertyName));
            }
        },

        forceRefresh: function() {
            var self = this;
            if (self.elements.$wrapper.length) {
                self.elements.$wrapper.hide().show(0);
            }
        }
    });

    // Add the handler to Elementor's frontend
    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
        elementorFrontend.elementsHandler.addHandler(WashiTapeHandler, {
            $element: $scope
        });
    });

})(jQuery); 
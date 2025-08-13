/**
 * Washi Tape Generator - Elementor Editor Integration
 * This script handles the integration with Elementor editor
 */
(function($) {
    'use strict';

    // Track editor state
    let isEditorInitialized = false;
    let editorWashiTapes = {};

    /**
     * Initialize Washi Tape in Elementor editor
     */
    function initWashiTapeEditor() {
        if (isEditorInitialized) {
            return;
        }
        
        isEditorInitialized = true;
        
        // Create container for washi tapes if not exists
        if ($('#elementor-washi-tapes-container').length === 0) {
            $('body').append('<div id="elementor-washi-tapes-container" style="display:none;"></div>');
        }

        // Listen for panel changes
        elementor.channels.editor.on('section:activated', handleSectionActivated);
        
        // Listen for element settings changes
        elementor.channels.editor.on('change', handleSettingsChanged);
        
        // Listen for preview related events
        elementor.settings.page.model.on('change', handlePageSettingsChange);
        
        // Add custom CSS class to the editor
        $('body').addClass('washi-tape-editor-active');
        
        console.log('Washi Tape Generator: Elementor editor integration initialized');
        
        // Scan for existing washi tapes
        scanExistingWashiTapes();
    }

    /**
     * Handle section activated in panel
     */
    function handleSectionActivated(sectionName, editor) {
        if (sectionName === 'section_washi_tape_controls') {
            const model = editor.getOption('editedElementView').getContainer().model;
            const settings = model.get('settings').attributes;
            
            // If washi tape is enabled, refresh it
            if (settings.enable_washi_tape === 'yes' && settings.washi_tape_id !== '0') {
                refreshWashiTape(model.get('id'), settings);
            }
        }
    }

    /**
     * Handle settings changed in panel
     */
    function handleSettingsChanged(view) {
        const settingsModel = view.container.settings;
        const settings = settingsModel.attributes;
        const elementId = view.container.model.get('id');
        
        // Check if this change affects washi tape settings
        if (settings.hasOwnProperty('enable_washi_tape') || 
            settings.hasOwnProperty('washi_tape_id') || 
            settings.hasOwnProperty('washi_tape_position') || 
            settings.hasOwnProperty('washi_tape_rotation') || 
            settings.hasOwnProperty('washi_tape_z_index')) {
            
            // If washi tape is enabled and has an ID
            if (settings.enable_washi_tape === 'yes' && settings.washi_tape_id !== '0') {
                refreshWashiTape(elementId, settings);
            } else {
                // Remove washi tape
                removeWashiTape(elementId);
            }
        }
    }

    /**
     * Handle page settings change
     */
    function handlePageSettingsChange() {
        // This is a good place to handle global settings that might affect all washi tapes
        // For now, we'll just log that settings changed
        console.log('Page settings changed - Washi Tape Generator is aware');
    }

    /**
     * Scan for existing washi tapes in the editor
     */
    function scanExistingWashiTapes() {
        // Get all elements with washi tape
        elementor.getPreviewView().$el.find('.has-washi-tape').each(function() {
            const $element = $(this);
            const elementId = $element.data('model-cid');
            
            if (elementId) {
                const container = elementor.getContainer(elementId);
                if (container) {
                    const settings = container.settings.attributes;
                    if (settings.enable_washi_tape === 'yes' && settings.washi_tape_id !== '0') {
                        refreshWashiTape(elementId, settings);
                    }
                }
            }
        });
    }

    /**
     * Refresh a washi tape on an element
     */
    function refreshWashiTape(elementId, settings) {
        // Get the element in preview
        const $element = elementor.getPreviewView().$el.find(`[data-model-cid="${elementId}"]`);
        
        if (!$element.length) {
            return;
        }
        
        // Mark the element
        $element.addClass('has-washi-tape');
        $element.attr('data-washi-tape-id', `washi-tape-${settings.washi_tape_id}`);
        $element.attr('data-washi-tape-position', settings.washi_tape_position || 'top-left');
        
        // Check if we already have this washi tape loaded
        if (editorWashiTapes[settings.washi_tape_id]) {
            insertWashiTape($element, settings);
        } else {
            // Fetch the washi tape
            loadWashiTape(settings.washi_tape_id, function() {
                insertWashiTape($element, settings);
            });
        }
    }

    /**
     * Load a washi tape from the server
     */
    function loadWashiTape(tapeId, callback) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_washi_tape_svg',
                nonce: washiTapePreviewParams.nonce,
                id: tapeId
            },
            success: function(response) {
                if (response.success && response.data.svg) {
                    // Store in our cache
                    editorWashiTapes[tapeId] = response.data.svg;
                    
                    // Make sure we have the container
                    let $container = $('#elementor-washi-tapes-container');
                    if (!$container.length) {
                        $container = $('<div id="elementor-washi-tapes-container" style="display:none;"></div>');
                        $('body').append($container);
                    }
                    
                    // Add or update the SVG in the container
                    const tapeElementId = `washi-tape-${tapeId}`;
                    let $tapeElement = $(`#${tapeElementId}`);
                    
                    if ($tapeElement.length) {
                        $tapeElement.html(response.data.svg);
                    } else {
                        $container.append(`<div id="${tapeElementId}" class="elementor-washi-tape">${response.data.svg}</div>`);
                    }
                    
                    if (typeof callback === 'function') {
                        callback();
                    }
                } else {
                    console.error('Failed to load washi tape:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load washi tape:', error);
            }
        });
    }

    /**
     * Insert a washi tape onto an element
     */
    function insertWashiTape($element, settings) {
        // Remove any existing washi tape first
        $element.find('.elementor-washi-tape').remove();
        
        // Get the tape from our container
        const $sourceTape = $(`#washi-tape-${settings.washi_tape_id}`);
        
        if (!$sourceTape.length) {
            console.error('Washi tape not found:', settings.washi_tape_id);
            return;
        }
        
        // Clone the tape
        const $tapeCopy = $sourceTape.clone();
        $tapeCopy.removeAttr('id');
        
        // Position the tape
        positionTape($tapeCopy, settings);
        
        // Add to the element
        $element.append($tapeCopy);
        
        // Make sure element is positioned relatively
        if ($element.css('position') === 'static') {
            $element.css('position', 'relative');
        }
    }

    /**
     * Position a tape based on settings
     */
    function positionTape($tape, settings) {
        // Make tape visible
        $tape.css({
            'display': 'block',
            'position': 'absolute',
            'z-index': settings.washi_tape_z_index || 1
        });
        
        // Set position
        const position = settings.washi_tape_position || 'top-left';
        
        // Reset all positions first
        $tape.css({
            'top': '',
            'right': '',
            'bottom': '',
            'left': ''
        });
        
        if (position === 'top-left') {
            $tape.css({
                'top': '-20px',
                'left': '-20px'
            });
        } else if (position === 'top-right') {
            $tape.css({
                'top': '-20px',
                'right': '-20px'
            });
        } else if (position === 'bottom-left') {
            $tape.css({
                'bottom': '-20px',
                'left': '-20px'
            });
        } else if (position === 'bottom-right') {
            $tape.css({
                'bottom': '-20px',
                'right': '-20px'
            });
        }
        
        // Apply rotation if set
        if (settings.washi_tape_rotation) {
            const degrees = settings.washi_tape_rotation.size || 0;
            $tape.css('transform', `rotate(${degrees}deg)`);
        }
    }

    /**
     * Remove a washi tape from an element
     */
    function removeWashiTape(elementId) {
        const $element = elementor.getPreviewView().$el.find(`[data-model-cid="${elementId}"]`);
        
        if (!$element.length) {
            return;
        }
        
        $element.removeClass('has-washi-tape');
        $element.removeAttr('data-washi-tape-id');
        $element.removeAttr('data-washi-tape-position');
        $element.find('.elementor-washi-tape').remove();
    }

    // Initialize when Elementor is ready
    $(window).on('elementor/frontend/init', function() {
        elementor.on('preview:loaded', initWashiTapeEditor);
    });

})(jQuery);
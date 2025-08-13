/**
 * Washi Tape Generator Elementor Preview Script
 * This script is only loaded in the Elementor editor
 */
(function($) {
    'use strict';

    /**
     * Initialize the washi tape preview functionality
     */
    function initWashiTapePreview() {
        // Listen for changes to washi tape controls
        elementor.channels.editor.on('change', function(view) {
            const element = view.container.view.$el;
            const model = view.container.model;
            const settings = model.get('settings');
            
            // Check if washi tape is enabled
            if (settings.attributes.enable_washi_tape === 'yes' && settings.attributes.washi_tape_id !== '0') {
                updateWashiTape(element, settings.attributes);
            } else {
                removeWashiTape(element);
            }
        });
        
        // Handle section panel change
        elementor.channels.editor.on('section:activated', function(sectionName, editor) {
            if (sectionName === 'section_washi_tape_controls') {
                refreshWashiTapePreview(editor.model.cid);
            }
        });
    }
    
    /**
     * Update washi tape on an element
     */
    function updateWashiTape(element, settings) {
        // Add washi tape class and attributes
        element.addClass('has-washi-tape');
        element.attr('data-washi-tape-id', 'washi-tape-' + settings.washi_tape_id);
        element.attr('data-washi-tape-position', settings.washi_tape_position);
        
        // Create or update washi tape in editor
        createEditorWashiTape(settings.washi_tape_id, function(tapeElement) {
            if (!tapeElement) return;
            
            // Remove existing tape
            element.find('.elementor-washi-tape').remove();
            
            // Clone and add the new tape
            const tapeClone = $(tapeElement).clone();
            tapeClone.css({
                'display': 'block',
                'position': 'absolute',
                'z-index': settings.washi_tape_z_index || 1
            });
            
            // Position the tape
            positionTape(tapeClone, settings.washi_tape_position);
            
            // Apply rotation
            if (settings.washi_tape_rotation) {
                const degrees = settings.washi_tape_rotation.size || 0;
                tapeClone.css('transform', 'rotate(' + degrees + 'deg)');
            }
            
            element.append(tapeClone);
        });
    }
    
    /**
     * Position a tape based on position setting
     */
    function positionTape(tapeElement, position) {
        // Reset positioning
        tapeElement.css({
            'top': '',
            'right': '',
            'bottom': '',
            'left': ''
        });
        
        // Set position
        if (position === 'top-left') {
            tapeElement.css({
                'top': '-20px',
                'left': '-20px'
            });
        } else if (position === 'top-right') {
            tapeElement.css({
                'top': '-20px',
                'right': '-20px'
            });
        } else if (position === 'bottom-left') {
            tapeElement.css({
                'bottom': '-20px',
                'left': '-20px'
            });
        } else if (position === 'bottom-right') {
            tapeElement.css({
                'bottom': '-20px',
                'right': '-20px'
            });
        }
    }
    
    /**
     * Remove washi tape from an element
     */
    function removeWashiTape(element) {
        element.removeClass('has-washi-tape');
        element.removeAttr('data-washi-tape-id');
        element.removeAttr('data-washi-tape-position');
        element.find('.elementor-washi-tape').remove();
    }
    
    /**
     * Refresh washi tape preview when the section is activated
     */
    function refreshWashiTapePreview(cid) {
        const model = elementor.getContainer(cid).model;
        const settings = model.get('settings');
        const element = elementor.$preview.find('[data-model-cid="' + cid + '"]');
        
        if (settings.attributes.enable_washi_tape === 'yes' && settings.attributes.washi_tape_id !== '0') {
            updateWashiTape(element, settings.attributes);
        }
    }
    
    /**
     * Create a washi tape element in the editor
     */
    function createEditorWashiTape(tapeId, callback) {
        // Use AJAX to get the washi tape SVG
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
                    // Create element
                    const tapeElement = $('<div class="elementor-washi-tape">' + response.data.svg + '</div>');
                    
                    // Add to hidden container if not exists
                    let container = $('#elementor-washi-tapes-container');
                    if (container.length === 0) {
                        container = $('<div id="elementor-washi-tapes-container" style="display:none;"></div>');
                        $('body').append(container);
                    }
                    
                    // Add with ID
                    const tapeId = 'washi-tape-' + response.data.id;
                    let existingTape = $('#' + tapeId);
                    if (existingTape.length) {
                        existingTape.replaceWith(tapeElement.attr('id', tapeId));
                    } else {
                        container.append(tapeElement.attr('id', tapeId));
                    }
                    
                    callback(tapeElement[0]);
                } else {
                    console.error('Error loading washi tape:', response);
                    callback(null);
                }
            },
            error: function() {
                console.error('Error loading washi tape');
                callback(null);
            }
        });
    }

    // Initialize when Elementor is ready
    $(window).on('elementor/frontend/init', function() {
        elementor.on('preview:loaded', initWashiTapePreview);
    });

})(jQuery);

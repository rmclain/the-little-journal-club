/**
 * Washi Tape Generator Front-end Script
 */
(function () {
    'use strict';

    /* ---------- core ---------- */
    function initWashiTapes(retryCount = 0) {
        console.log('Initializing washi tapes...'); // Debug log
        const elements = document.querySelectorAll('.tljc-washi-tape-element');
        console.log('Found elements:', elements.length); // Debug log

        elements.forEach(function (element) {
            const tapeId = element.getAttribute('data-tape-id');
            console.log('Processing element with tape ID:', tapeId); // Debug log

            if (!tapeId) return;

            // Get the SVG from the hidden template
            const tapeTemplate = document.getElementById('washi-tape-' + tapeId);
            if (!tapeTemplate) {
                console.warn('Washi tape template not found for ID:', tapeId);
                // Retry up to 3 times if template is not found (in case of late rendering)
                if (retryCount < 3) {
                    setTimeout(function () { initWashiTapes(retryCount + 1); }, 200);
                }
                return;
            }

            // Get size and position attributes
            const width = element.getAttribute('data-width') || 180;
            const height = element.getAttribute('data-height') || 45;
            const position = element.getAttribute('data-position') || 'top-center';
            const rotation = element.getAttribute('data-rotation') || 0;
            const verticalOffset = element.getAttribute('data-vertical-offset') || 0;
            const horizontalOffset = element.getAttribute('data-horizontal-offset') || 0;

            // Clean up the SVG content
            let svgContent = tapeTemplate.innerHTML;
            svgContent = svgContent
                .replace(/\\&quot;/g, '"')
                .replace(/&quot;/g, '"')
                .replace(/\\"/g, '"')
                .replace(/\s+/g, ' ')
                .replace(/"\s+/g, '"')
                .replace(/\s+"/g, '"')
                .replace(/" >/g, '">')
                .replace(/> </g, '><')
                .replace(/style="[^"]*"/g, '')
                .replace(/\\"pattern-/g, 'pattern-')
                .replace(/\\"patternUnits=/g, 'patternUnits=')
                .replace(/\\"userSpaceOnUse\\"/g, 'userSpaceOnUse')
                .replace(/\\"width=/g, 'width=')
                .replace(/\\"height=/g, 'height=')
                .replace(/\\"(\d+)\\"/g, '"$1"')
                .replace(/\\"([^"\s]+)\\"/g, '"$1"');

            // Remove any existing SVG
            element.innerHTML = '';

            // Insert SVG markup directly (not as nested SVG)
            element.innerHTML = svgContent;
            const svgElement = element.querySelector('svg');
            if (!svgElement) {
                console.warn('SVG element not found after inserting tape content for ID:', tapeId);
                return;
            }
            svgElement.setAttribute('width', width);
            svgElement.setAttribute('height', height);
            svgElement.setAttribute('preserveAspectRatio', 'none');
            svgElement.style.width = width + 'px';
            svgElement.style.height = height + 'px';
            svgElement.style.left = '50%';
            svgElement.style.top = '-10px';
            svgElement.style.transform = `translateX(-50%) rotate(${rotation}deg)`;
            svgElement.style.position = 'absolute';
            svgElement.style.pointerEvents = 'none';
            svgElement.style.zIndex = '1000';
            // No parent width/height set
            console.log('Tape SVG appended to element:', tapeId); // Debug log
        });
    }

    /* ---------- bootstrap ---------- */
    // Initialize immediately if DOM is already loaded
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initWashiTapes();
    }

    // Plain front-end
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM Content Loaded - Initializing washi tapes'); // Debug log
        initWashiTapes();
    });

    // Elementor preview / editor
    function attachToElementor() {
        console.log('Attaching to Elementor'); // Debug log

        // Handle regular Elementor preview
        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/global',
            function ($scope) {
                setTimeout(initWashiTapes, 100);
            }
        );

        // Handle Elementor editor preview
        if (window.elementor && window.elementor.channels && window.elementor.channels.editor) {
            window.elementor.channels.editor.on('change', function () {
                setTimeout(initWashiTapes, 100);
            });
            window.elementor.channels.editor.on('section:activated', function () {
                setTimeout(initWashiTapes, 100);
            });
            window.elementor.channels.editor.on('panel:open', function () {
                setTimeout(initWashiTapes, 100);
            });
            window.elementor.channels.editor.on('panel:close', function () {
                setTimeout(initWashiTapes, 100);
            });
        }
    }

    // Initialize based on environment
    if (window.elementorFrontend && window.elementorFrontend.hooks) {
        console.log('Elementor already initialized - attaching now'); // Debug log
        attachToElementor(); // Elementor already initialised
    } else {
        console.log('Waiting for Elementor to initialize'); // Debug log
        // Wait until Elementor finishes booting
        jQuery(window).on('elementor/frontend/init', function () {
            console.log('Elementor initialized - attaching now'); // Debug log
            attachToElementor();
        });
    }

    // Re-initialize on dynamic content load
    jQuery(document).on('elementor/popup/hide', function () {
        console.log('Popup hidden - reinitializing washi tapes'); // Debug log
        initWashiTapes();
    });

    jQuery(document).on('elementor/popup/show', function () {
        console.log('Popup shown - reinitializing washi tapes'); // Debug log
        initWashiTapes();
    });

    // Also initialize on window load to catch any late-loading content
    window.addEventListener('load', function () {
        console.log('Window loaded - reinitializing washi tapes'); // Debug log
        initWashiTapes();
    });
})();
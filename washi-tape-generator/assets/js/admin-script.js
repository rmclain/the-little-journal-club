/**
 * Washi Tape Generator Admin Script
 */
(function ($) {
    'use strict';

    // Initialize the washi tape generator
    const WashiTapeGenerator = {
        init: function () {
            this.initVars();
            this.bindEvents();
            this.initMediaUploader();
            this.updatePreview();
            this.loadSavedWashiTapes();
        },

        initVars: function () {
            // Form elements
            this.colorInput = $('#color');
            this.patternSelect = $('#pattern');
            this.tornEdgesCheckbox = $('#torn-edges');
            this.widthInput = $('#width');
            this.heightInput = $('#height');
            this.positionSelect = $('#position');
            this.rotationInput = $('#rotation');
            this.patternSpacingInput = $('#pattern-spacing');
            this.lineThicknessInput = $('#line-thickness');
            this.lineColorInput = $('#line-color');
            this.dotDiameterInput = $('#dot-diameter');
            this.dotColorInput = $('#dot-color');
            this.patternRandomnessInput = $('#pattern-randomness');
            this.useImageOverlayCheckbox = $('#use-image-overlay');
            this.imageUrlInput = $('#image-url');
            this.imageOpacityInput = $('#image-opacity');

            // Output elements
            this.washiSvg = document.getElementById('washi-svg');
            this.savedWashiTapesContainer = $('#saved-washi-tapes');

            // Other variables
            this.svgDefs = null;
            this.currentImageData = null;
        },

        bindEvents: function () {
            // Update preview on input change, excluding the title input
            $('input:not(#tape-title), select').on('input change', this.updatePreview.bind(this));

            // Reset button
            $('#reset-washi-tape').on('click', this.resetForm.bind(this));

            // Save button
            $('#save-washi-tape').on('click', this.saveWashiTape.bind(this));

            // Image overlay toggle
            this.useImageOverlayCheckbox.on('change', function () {
                if (this.checked) {
                    $('#image-overlay-controls').show();
                } else {
                    $('#image-overlay-controls').hide();
                }
                WashiTapeGenerator.updatePreview();
            });

            // Pattern select change
            this.patternSelect.on('change', function () {
                const pattern = $(this).val();
                $('#line-options').toggle(['horizontal-lines', 'vertical-lines', 'grid', 'random-lines'].includes(pattern));
                $('#dot-options').toggle(pattern === 'dots');
                $('#pattern-spacing-group').toggle(pattern !== 'solid');
                $('#pattern-randomness-control').toggle(pattern === 'random-lines');
                WashiTapeGenerator.updatePreview();
            });

            // Range slider value display
            $('#roughness').on('input', function () {
                $('#roughness-value').text($(this).val() + '%');
            });

            $('#segments').on('input', function () {
                $('#segments-value').text($(this).val());
            });

            $('#opacity').on('input', function () {
                $('#opacity-value').text($(this).val() + '%');
            });

            $('#pattern-opacity').on('input', function () {
                $('#pattern-opacity-value').text($(this).val() + '%');
            });

            $('#pattern-randomness').on('input', function () {
                $('#pattern-randomness-value').text($(this).val() + '%');
            });

            $('#image-opacity').on('input', function () {
                $('#image-opacity-value').text($(this).val() + '%');
            });
        },

        initMediaUploader: function () {
            let mediaUploader;

            $('#image-upload-btn').on('click', function (e) {
                e.preventDefault();

                // If the uploader object has already been created, reopen the dialog
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                // Create the media uploader
                mediaUploader = wp.media({
                    title: 'Select Image for Washi Tape',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });

                // When an image is selected, run a callback
                mediaUploader.on('select', function () {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#image-url').val(attachment.url);
                    $('#image-preview').html('<img src="' + attachment.url + '" style="max-width: 100%; max-height: 100px;">');

                    // Convert image to base64 for SVG embedding
                    WashiTapeGenerator.convertImageToBase64(attachment.url);
                });

                // Open the uploader dialog
                mediaUploader.open();
            });
        },

        convertImageToBase64: function (imageUrl) {
            const img = new Image();
            img.crossOrigin = 'Anonymous';
            img.onload = function () {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                const dataURL = canvas.toDataURL('image/png');
                WashiTapeGenerator.currentImageData = dataURL;
                WashiTapeGenerator.updatePreview();
            };

            img.onerror = function () {
                console.error('Error loading image');
                WashiTapeGenerator.currentImageData = null;
            };

            img.src = imageUrl;
        },

        generateTornEdgePath: function (width, height) {
            // Generate a random torn edge path with torn left and right edges only
            const segments = parseInt($('#segments').val()) || 10;
            const segmentHeight = height / segments;
            const roughnessPercent = parseInt($('#roughness').val()) || 10;
            const roughnessLeft = width * (roughnessPercent / 100); // Roughness of the left torn edge
            const roughnessRight = width * (roughnessPercent / 100); // Roughness of the right torn edge

            // Start at top-left
            let path = `M0,0 `;

            // Top edge (straight)
            path += `L${width},0 `;

            // Right edge with random variations
            for (let i = 1; i < segments; i++) {
                const y = i * segmentHeight;
                const x = width - (Math.random() * roughnessRight);
                path += `L${x},${y} `;
            }
            path += `L${width},${height} `;

            // Bottom edge (straight)
            path += `L0,${height} `;

            // Left edge with random variations
            for (let i = segments - 1; i > 0; i--) {
                const y = i * segmentHeight;
                const x = (Math.random() * roughnessLeft);
                path += `L${x},${y} `;
            }

            // Close the path
            path += 'Z';

            return path;
        },

        createSvgPattern: function (patternType) {
            // Remove existing defs
            const existingDefs = this.washiSvg.querySelector('defs');
            if (existingDefs) {
                this.washiSvg.removeChild(existingDefs);
            }

            // Create new defs
            this.svgDefs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            const patternId = 'pattern-' + Date.now();
            const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');

            const spacing = parseInt(this.patternSpacingInput.val()) || 10;
            const patternSize = spacing;

            pattern.setAttribute('id', patternId);
            pattern.setAttribute('patternUnits', 'userSpaceOnUse');
            pattern.setAttribute('width', patternSize);
            pattern.setAttribute('height', patternSize);

            if (patternType === 'horizontal-lines') {
                const lineThickness = parseInt(this.lineThicknessInput.val()) || 1;
                const lineColor = this.lineColorInput.val();

                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', '0');
                line.setAttribute('y1', patternSize / 2);
                line.setAttribute('x2', patternSize);
                line.setAttribute('y2', patternSize / 2);
                line.setAttribute('stroke', lineColor);
                line.setAttribute('stroke-width', lineThickness);

                pattern.appendChild(line);
            }
            else if (patternType === 'vertical-lines') {
                const lineThickness = parseInt(this.lineThicknessInput.val()) || 1;
                const lineColor = this.lineColorInput.val();

                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', patternSize / 2);
                line.setAttribute('y1', '0');
                line.setAttribute('x2', patternSize / 2);
                line.setAttribute('y2', patternSize);
                line.setAttribute('stroke', lineColor);
                line.setAttribute('stroke-width', lineThickness);

                pattern.appendChild(line);
            }
            else if (patternType === 'grid') {
                const lineThickness = parseInt(this.lineThicknessInput.val()) || 1;
                const lineColor = this.lineColorInput.val();

                // Horizontal line
                const hLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                hLine.setAttribute('x1', '0');
                hLine.setAttribute('y1', patternSize / 2);
                hLine.setAttribute('x2', patternSize);
                hLine.setAttribute('y2', patternSize / 2);
                hLine.setAttribute('stroke', lineColor);
                hLine.setAttribute('stroke-width', lineThickness);

                // Vertical line
                const vLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                vLine.setAttribute('x1', patternSize / 2);
                vLine.setAttribute('y1', '0');
                vLine.setAttribute('x2', patternSize / 2);
                vLine.setAttribute('y2', patternSize);
                vLine.setAttribute('stroke', lineColor);
                vLine.setAttribute('stroke-width', lineThickness);

                pattern.appendChild(hLine);
                pattern.appendChild(vLine);
            }
            else if (patternType === 'random-lines') {
                const lineThickness = parseInt(this.lineThicknessInput.val()) || 1;
                const lineColor = this.lineColorInput.val();
                const randomness = parseInt(this.patternRandomnessInput.val()) || 0;

                // Add multiple random lines
                for (let i = 0; i < 4; i++) {
                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');

                    if (Math.random() > 0.5) {
                        // Horizontal-ish line
                        const y = Math.random() * patternSize;
                        const deviation = (randomness / 100) * patternSize;

                        line.setAttribute('x1', '0');
                        line.setAttribute('y1', y);
                        line.setAttribute('x2', patternSize);
                        line.setAttribute('y2', y + (Math.random() * deviation * 2 - deviation));
                    } else {
                        // Vertical-ish line
                        const x = Math.random() * patternSize;
                        const deviation = (randomness / 100) * patternSize;

                        line.setAttribute('x1', x);
                        line.setAttribute('y1', '0');
                        line.setAttribute('x2', x + (Math.random() * deviation * 2 - deviation));
                        line.setAttribute('y2', patternSize);
                    }

                    line.setAttribute('stroke', lineColor);
                    line.setAttribute('stroke-width', lineThickness);
                    pattern.appendChild(line);
                }
            }
            else if (patternType === 'dots') {
                const dotDiameter = parseInt(this.dotDiameterInput.val()) || 5;
                const dotColor = this.dotColorInput.val();

                const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                dot.setAttribute('cx', patternSize / 2);
                dot.setAttribute('cy', patternSize / 2);
                dot.setAttribute('r', dotDiameter / 2);
                dot.setAttribute('fill', dotColor);

                pattern.appendChild(dot);
            }

            this.svgDefs.appendChild(pattern);
            this.washiSvg.appendChild(this.svgDefs);

            return `url(#${patternId})`;
        },

        updatePreview: function () {
            // Get values from form
            const color = this.colorInput.val();
            const patternType = this.patternSelect.val();
            const tornEdges = this.tornEdgesCheckbox.prop('checked');
            const width = parseInt(this.widthInput.val()) || 180;
            const height = parseInt(this.heightInput.val()) || 45;
            const position = this.positionSelect.val();
            const rotation = parseInt(this.rotationInput.val()) || 0;
            const useImageOverlay = this.useImageOverlayCheckbox.prop('checked');
            const imageOpacity = parseInt(this.imageOpacityInput.val()) / 100 || 0.5;

            // Clear existing SVG content
            while (this.washiSvg.firstChild) {
                this.washiSvg.removeChild(this.washiSvg.firstChild);
            }

            // Set SVG dimensions
            this.washiSvg.setAttribute('width', width);
            this.washiSvg.setAttribute('height', height);

            // Create pattern if needed
            let fill = color;
            let patternFill = null;

            if (patternType !== 'solid') {
                patternFill = this.createSvgPattern(patternType);
            }

            // Create the tape shape
            const tapePath = document.createElementNS('http://www.w3.org/2000/svg', 'path');

            // Set path data based on torn edges setting
            if (tornEdges) {
                tapePath.setAttribute('d', this.generateTornEdgePath(width, height));
            } else {
                tapePath.setAttribute('d', `M0,0 L${width},0 L${width},${height} L0,${height} Z`);
            }

            // Apply fill color with opacity
            const baseOpacity = parseInt($('#opacity').val()) / 100 || 1;
            tapePath.setAttribute('fill', fill);
            tapePath.setAttribute('fill-opacity', baseOpacity);

            // Add the path to the SVG
            this.washiSvg.appendChild(tapePath);

            // Add pattern overlay if selected
            if (patternFill) {
                const patternOpacity = parseInt($('#pattern-opacity').val()) / 100 || 1;
                const patternOverlay = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                patternOverlay.setAttribute('d', tapePath.getAttribute('d'));
                patternOverlay.setAttribute('fill', patternFill);
                patternOverlay.setAttribute('fill-opacity', patternOpacity);
                this.washiSvg.appendChild(patternOverlay);
            }

            // Add image overlay if enabled
            if (useImageOverlay && this.currentImageData) {
                // Create clipPath for the image
                const clipPath = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
                const clipPathId = 'clip-' + Date.now();
                clipPath.setAttribute('id', clipPathId);

                // Clone the tape path for clipping
                const clipPathShape = tapePath.cloneNode(true);
                clipPath.appendChild(clipPathShape);

                // Add clipPath to defs
                if (!this.svgDefs) {
                    this.svgDefs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                }
                this.svgDefs.appendChild(clipPath);
                this.washiSvg.appendChild(this.svgDefs);

                const imageSize = $('#image-size').val();
                if (imageSize === 'repeat' || imageSize === 'repeat-x' || imageSize === 'repeat-y') {
                    // For repeating patterns, create a pattern element
                    const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
                    const patternId = 'pattern-' + Date.now();
                    pattern.setAttribute('id', patternId);
                    pattern.setAttribute('patternUnits', 'userSpaceOnUse');

                    // Load the image to get its dimensions
                    const tempImg = new Image();
                    tempImg.src = this.currentImageData;
                    tempImg.onload = () => {
                        const imgWidth = tempImg.width;
                        const imgHeight = tempImg.height;

                        // Set pattern size based on repeat type
                        if (imageSize === 'repeat' || imageSize === 'repeat-x') {
                            pattern.setAttribute('width', imgWidth);
                        } else {
                            pattern.setAttribute('width', width);
                        }
                        if (imageSize === 'repeat' || imageSize === 'repeat-y') {
                            pattern.setAttribute('height', imgHeight);
                        } else {
                            pattern.setAttribute('height', height);
                        }

                        // Add image to pattern
                        const patternImage = document.createElementNS('http://www.w3.org/2000/svg', 'image');
                        patternImage.setAttribute('href', this.currentImageData);
                        patternImage.setAttribute('width', imgWidth);
                        patternImage.setAttribute('height', imgHeight);
                        pattern.appendChild(patternImage);

                        // Add pattern to defs
                        this.svgDefs.appendChild(pattern);

                        // Create rect with pattern fill
                        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect.setAttribute('width', width);
                        rect.setAttribute('height', height);
                        rect.setAttribute('fill', `url(#${patternId})`);
                        rect.setAttribute('clip-path', `url(#${clipPathId})`);
                        rect.setAttribute('opacity', imageOpacity);
                        this.washiSvg.appendChild(rect);
                    };
                } else {
                    // For cover/contain, use regular image
                    const image = document.createElementNS('http://www.w3.org/2000/svg', 'image');
                    image.setAttribute('href', this.currentImageData);
                    image.setAttribute('width', width);
                    image.setAttribute('height', height);
                    image.setAttribute('preserveAspectRatio',
                        imageSize === 'cover' ? 'xMidYMid slice' : 'xMidYMid meet');
                    image.setAttribute('clip-path', `url(#${clipPathId})`);
                    image.setAttribute('opacity', imageOpacity);
                    this.washiSvg.appendChild(image);
                }
            }

            // Position the SVG in the preview
            const previewContainer = document.querySelector('.preview-container');
            const contentBox = document.querySelector('.content-box');

            // Add these lines to ensure the SVG is visible
            this.washiSvg.style.position = 'absolute';
            this.washiSvg.style.display = 'block';

            // Reset transform
            this.washiSvg.style.transform = '';

            if (position === 'left') {
                this.washiSvg.style.left = '-20px';
                this.washiSvg.style.top = '30px';
                this.washiSvg.style.transform = `rotate(${rotation - 10}deg)`;
            } else if (position === 'right') {
                this.washiSvg.style.right = '-20px';
                this.washiSvg.style.left = 'auto';
                this.washiSvg.style.top = '30px';
                this.washiSvg.style.transform = `rotate(${rotation + 10}deg)`;
            } else if (position === 'top') {
                this.washiSvg.style.left = '50%';
                this.washiSvg.style.top = '-20px';
                this.washiSvg.style.transform = `translateX(-50%) rotate(${rotation}deg)`;
            } else if (position === 'bottom') {
                this.washiSvg.style.left = '50%';
                this.washiSvg.style.bottom = '-20px';
                this.washiSvg.style.top = 'auto';
                this.washiSvg.style.transform = `translateX(-50%) rotate(${rotation}deg)`;
            }
        },

        resetForm: function () {
            // Reset form to default values
            $('#tape-title').val('');
            $('#tape-id').val('0');
            this.colorInput.val('#8a5cf7');
            this.patternSelect.val('vertical-lines').trigger('change');
            this.tornEdgesCheckbox.prop('checked', true);
            this.widthInput.val('180');
            this.heightInput.val('45');
            this.positionSelect.val('left');
            this.rotationInput.val('0');
            this.patternSpacingInput.val('10');
            this.lineThicknessInput.val('1');
            this.lineColorInput.val('#000000');
            this.dotDiameterInput.val('5');
            this.dotColorInput.val('#ffffff');
            this.patternRandomnessInput.val('0');
            $('#pattern-randomness-value').text('0%');
            this.useImageOverlayCheckbox.prop('checked', false).trigger('change');
            this.imageUrlInput.val('');
            this.imageOpacityInput.val('50');
            $('#image-opacity-value').text('50%');
            $('#image-preview').html('');
            this.currentImageData = null;

            // Reset range slider value displays
            $('#roughness').val('10');
            $('#roughness-value').text('10%');
            $('#segments').val('10');
            $('#segments-value').text('10');
            $('#opacity').val('100');
            $('#opacity-value').text('100%');
            $('#pattern-opacity').val('100');
            $('#pattern-opacity-value').text('100%');

            // Update preview
            this.updatePreview();
        },

        saveWashiTape: function () {
            // Get form values
            const title = $('#tape-title').val();
            const id = $('#tape-id').val();

            if (!title) {
                alert('Please enter a name for your washi tape.');
                return;
            }

            // Get SVG code and clean it up
            let svgCode = this.washiSvg.outerHTML;
            // Remove any escaped quotes and normalize the SVG
            svgCode = svgCode
                .replace(/&quot;/g, '"')
                .replace(/\\&quot;/g, '"')
                .replace(/\s+/g, ' ')
                .replace(/"\s+/g, '"')
                .replace(/\s+"/g, '"')
                .replace(/" >/g, '">')
                .replace(/> </g, '><')
                .replace(/\\"pattern-/g, 'pattern-')
                .replace(/\\"patternUnits=/g, 'patternUnits=')
                .replace(/\\"userSpaceOnUse\\"/g, 'userSpaceOnUse')
                .replace(/\\"width=/g, 'width=')
                .replace(/\\"height=/g, 'height=')
                .replace(/\\"(\d+)\\"/g, '"$1"')
                .replace(/\\"([^"]+)\\"/g, '"$1"')
                .replace(/absolute;=/g, '')
                .replace(/display:=/g, '')
                .replace(/left:=/g, '')
                .replace(/top:=/g, '')
                .replace(/transform:=/g, '')
                .replace(/rotate\(-10deg\);\\"/g, '')
                .replace(/\s+style="[^"]*"/g, '');

            // Ensure proper xmlns attribute
            if (!svgCode.includes('xmlns="http://www.w3.org/2000/svg"')) {
                svgCode = svgCode.replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"');
            }

            // Get all settings
            const settings = {
                color: this.colorInput.val(),
                pattern: this.patternSelect.val(),
                tornEdges: this.tornEdgesCheckbox.prop('checked'),
                width: this.widthInput.val(),
                height: this.heightInput.val(),
                position: this.positionSelect.val(),
                rotation: this.rotationInput.val(),
                patternSpacing: this.patternSpacingInput.val(),
                lineThickness: this.lineThicknessInput.val(),
                lineColor: this.lineColorInput.val(),
                dotDiameter: this.dotDiameterInput.val(),
                dotColor: this.dotColorInput.val(),
                patternRandomness: this.patternRandomnessInput.val(),
                roughness: $('#roughness').val(),
                segments: $('#segments').val(),
                opacity: $('#opacity').val(),
                patternOpacity: $('#pattern-opacity').val(),
                useImageOverlay: this.useImageOverlayCheckbox.prop('checked'),
                imageUrl: this.imageUrlInput.val(),
                imageOpacity: this.imageOpacityInput.val(),
                imageData: this.currentImageData,
                imageSize: $('#image-size').val()
            };

            // Save the washi tape
            $.ajax({
                url: washiTapeParams.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'save_washi_tape',
                    nonce: washiTapeParams.nonce,
                    id: id,
                    title: title,
                    svg: svgCode,
                    settings: JSON.stringify(settings)
                },
                success: function (response) {
                    if (response.success) {
                        // After successful save, reload the saved tapes
                        WashiTapeGenerator.loadSavedWashiTapes();

                        // Reset the form if this was a new tape
                        if (id === '0') {
                            WashiTapeGenerator.resetForm();
                        }

                        alert('Washi tape saved successfully!');
                    } else {
                        alert('Error saving washi tape.');
                    }
                },
                error: function () {
                    alert('Error saving washi tape.');
                }
            });
        },

        loadSavedWashiTapes: function () {
            $.ajax({
                url: washiTapeParams.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_washi_tapes',
                    nonce: washiTapeParams.nonce
                },
                beforeSend: function () {
                    WashiTapeGenerator.savedWashiTapesContainer.html('<div class="loading-indicator">Loading...</div>');
                },
                success: function (response) {
                    if (response.success && response.data.washi_tapes) {
                        if (response.data.washi_tapes.length === 0) {
                            WashiTapeGenerator.savedWashiTapesContainer.html('<p>No saved washi tapes yet.</p>');
                            return;
                        }

                        let html = '<div class="washi-tape-grid">';

                        response.data.washi_tapes.forEach(function (tape) {
                            // Clean up the SVG before inserting
                            const cleanSvg = tape.svg
                                .replace(/&quot;/g, '"')
                                .replace(/\\&quot;/g, '"')
                                .replace(/\s+/g, ' ')
                                .replace(/"\s+/g, '"')
                                .replace(/\s+"/g, '"')
                                .replace(/" >/g, '">')
                                .replace(/> </g, '><')
                                .replace(/absolute;/g, '')
                                .replace(/display:/g, '')
                                .replace(/left:/g, '')
                                .replace(/top:/g, '')
                                .replace(/transform:/g, '')
                                .replace(/rotate\(-10deg\);/g, '')
                                .replace(/style="[^"]*"/g, '')
                                .replace(/\\"(\d+)\\"/g, '"$1"')
                                .replace(/\\"([^"]+)\\"/g, '"$1"')
                                .replace(/\\"pattern-/g, 'pattern-')
                                .replace(/\\"patternUnits=/g, 'patternUnits=')
                                .replace(/\\"userSpaceOnUse\\"/g, 'userSpaceOnUse')
                                .replace(/\\"width=/g, 'width=')
                                .replace(/\\"height=/g, 'height=');

                            html += '<div class="washi-tape-item" data-id="' + tape.id + '" data-settings="' + encodeURIComponent(tape.settings) + '">';
                            html += '<div class="washi-tape-preview">';
                            html += '<div class="preview-background">';
                            html += cleanSvg;
                            html += '</div>';
                            html += '</div>';
                            html += '<div class="washi-tape-details">';
                            html += '<h3>' + tape.title + '</h3>';
                            html += '<div class="washi-tape-actions">';
                            html += '<button type="button" class="button edit-washi-tape">Edit</button>';
                            html += '<button type="button" class="button button-link-delete delete-washi-tape">Delete</button>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        });

                        html += '</div>';

                        WashiTapeGenerator.savedWashiTapesContainer.html(html);

                        // Add event listeners
                        $('.edit-washi-tape').on('click', WashiTapeGenerator.editWashiTape);
                        $('.delete-washi-tape').on('click', WashiTapeGenerator.deleteWashiTape);
                    } else {
                        WashiTapeGenerator.savedWashiTapesContainer.html('<p>Error loading washi tapes.</p>');
                    }
                },
                error: function () {
                    WashiTapeGenerator.savedWashiTapesContainer.html('<p>Error loading washi tapes.</p>');
                }
            });
        },

        editWashiTape: function () {
            const item = $(this).closest('.washi-tape-item');
            const id = item.data('id');
            const settingsJson = decodeURIComponent(item.data('settings'));
            const settings = JSON.parse(settingsJson);

            // Fill the form with the settings
            $('#tape-id').val(id);
            $('#tape-title').val(item.find('h3').text());

            // Set all form values
            WashiTapeGenerator.colorInput.val(settings.color);
            WashiTapeGenerator.patternSelect.val(settings.pattern).trigger('change');
            WashiTapeGenerator.tornEdgesCheckbox.prop('checked', settings.tornEdges);
            WashiTapeGenerator.widthInput.val(settings.width);
            WashiTapeGenerator.heightInput.val(settings.height);
            WashiTapeGenerator.positionSelect.val(settings.position);
            WashiTapeGenerator.rotationInput.val(settings.rotation);
            WashiTapeGenerator.patternSpacingInput.val(settings.patternSpacing);
            WashiTapeGenerator.lineThicknessInput.val(settings.lineThickness);
            WashiTapeGenerator.lineColorInput.val(settings.lineColor);
            WashiTapeGenerator.dotDiameterInput.val(settings.dotDiameter);
            WashiTapeGenerator.dotColorInput.val(settings.dotColor);
            WashiTapeGenerator.patternRandomnessInput.val(settings.patternRandomness || 0);
            $('#pattern-randomness-value').text((settings.patternRandomness || 0) + '%');
            $('#roughness').val(settings.roughness);
            $('#roughness-value').text(settings.roughness + '%');
            $('#segments').val(settings.segments);
            $('#segments-value').text(settings.segments);
            $('#opacity').val(settings.opacity);
            $('#opacity-value').text(settings.opacity + '%');
            $('#pattern-opacity').val(settings.patternOpacity);
            $('#pattern-opacity-value').text(settings.patternOpacity + '%');

            // Handle image overlay
            WashiTapeGenerator.useImageOverlayCheckbox.prop('checked', settings.useImageOverlay).trigger('change');
            if (settings.useImageOverlay && settings.imageUrl) {
                WashiTapeGenerator.imageUrlInput.val(settings.imageUrl);
                WashiTapeGenerator.imageOpacityInput.val(settings.imageOpacity);
                $('#image-opacity-value').text(settings.imageOpacity + '%');
                $('#image-size').val(settings.imageSize || 'cover');

                if (settings.imageData) {
                    WashiTapeGenerator.currentImageData = settings.imageData;
                    $('#image-preview').html('<img src="' + settings.imageData + '" style="max-width: 100%; max-height: 100px;">');
                } else if (settings.imageUrl) {
                    $('#image-preview').html('<img src="' + settings.imageUrl + '" style="max-width: 100%; max-height: 100px;">');
                    WashiTapeGenerator.convertImageToBase64(settings.imageUrl);
                }
            }

            // Scroll to top of form
            $('html, body').animate({
                scrollTop: $('.generator-panel').offset().top - 50
            }, 500);

            // Update preview
            WashiTapeGenerator.updatePreview();
        },

        deleteWashiTape: function () {
            if (!confirm('Are you sure you want to delete this washi tape?')) {
                return;
            }

            const item = $(this).closest('.washi-tape-item');
            const id = item.data('id');

            $.ajax({
                url: washiTapeParams.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'delete_washi_tape',
                    nonce: washiTapeParams.nonce,
                    id: id
                },
                success: function (response) {
                    if (response.success) {
                        alert(response.data.message);
                        // Reload saved washi tapes
                        WashiTapeGenerator.loadSavedWashiTapes();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function () {
                    alert('An error occurred while deleting the washi tape.');
                }
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function () {
        WashiTapeGenerator.init();
    });

})(jQuery);

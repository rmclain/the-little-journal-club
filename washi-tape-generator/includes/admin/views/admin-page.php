<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap washi-tape-admin">
    <h1><?php echo esc_html__('Washi Tape Generator', 'washi-tape-generator'); ?></h1>

    <div class="washi-tape-admin-grid">
        <!-- Generator Panel -->
        <div class="washi-tape-panel generator-panel">
            <div class="panel-header">
                <h2><?php echo esc_html__('Create Washi Tape', 'washi-tape-generator'); ?></h2>
            </div>
            <div class="panel-body">
                <form id="washi-form">
                    <div class="form-section">
                        <div class="form-section-title"><?php echo esc_html__('Tape Name', 'washi-tape-generator'); ?></div>
                        <div class="form-control">
                            <input type="text" id="tape-title" class="regular-text" placeholder="<?php echo esc_attr__('Enter a name for your washi tape', 'washi-tape-generator'); ?>" required>
                            <input type="hidden" id="tape-id" value="0">
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><?php echo esc_html__('Appearance', 'washi-tape-generator'); ?></div>
                        <div class="form-group">
                            <div class="form-control">
                                <label for="color"><?php echo esc_html__('Base Color', 'washi-tape-generator'); ?></label>
                                <input type="color" id="color" value="#8a5cf7">
                            </div>
                            <div class="form-control">
                                <label for="opacity"><?php echo esc_html__('Base Opacity', 'washi-tape-generator'); ?></label>
                                <input type="range" id="opacity" min="0" max="100" value="100" class="range-slider">
                                <output for="opacity" id="opacity-value">100%</output>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-control">
                                <label for="pattern"><?php echo esc_html__('Pattern', 'washi-tape-generator'); ?></label>
                                <select id="pattern">
                                    <option value="solid"><?php echo esc_html__('Solid', 'washi-tape-generator'); ?></option>
                                    <option value="horizontal-lines"><?php echo esc_html__('Horizontal Lines', 'washi-tape-generator'); ?></option>
                                    <option value="vertical-lines" selected><?php echo esc_html__('Vertical Lines', 'washi-tape-generator'); ?></option>
                                    <option value="grid"><?php echo esc_html__('Grid', 'washi-tape-generator'); ?></option>
                                    <option value="dots"><?php echo esc_html__('Dots', 'washi-tape-generator'); ?></option>
                                    <option value="random-lines"><?php echo esc_html__('Random Lines', 'washi-tape-generator'); ?></option>
                                </select>
                            </div>
                            <div class="form-control">
                                <label for="pattern-opacity"><?php echo esc_html__('Pattern Opacity', 'washi-tape-generator'); ?></label>
                                <input type="range" id="pattern-opacity" min="0" max="100" value="100" class="range-slider">
                                <output for="pattern-opacity" id="pattern-opacity-value">100%</output>
                            </div>
                        </div>

                        <div id="pattern-options">
                            <div class="form-group" id="pattern-spacing-group">
                                <div class="form-control">
                                    <label for="pattern-spacing"><?php echo esc_html__('Pattern Spacing (px)', 'washi-tape-generator'); ?></label>
                                    <input type="number" id="pattern-spacing" value="10" min="1">
                                </div>
                                <div class="form-control" id="pattern-randomness-control">
                                    <label for="pattern-randomness"><?php echo esc_html__('Pattern Randomness', 'washi-tape-generator'); ?></label>
                                    <input type="range" id="pattern-randomness" min="0" max="100" value="0" class="range-slider">
                                    <output for="pattern-randomness" id="pattern-randomness-value">0%</output>
                                </div>
                            </div>

                            <div class="form-group" id="line-options">
                                <div class="form-control">
                                    <label for="line-thickness"><?php echo esc_html__('Line Thickness (px)', 'washi-tape-generator'); ?></label>
                                    <input type="number" id="line-thickness" value="1" min="1">
                                </div>
                                <div class="form-control">
                                    <label for="line-color"><?php echo esc_html__('Line Color', 'washi-tape-generator'); ?></label>
                                    <input type="color" id="line-color" value="#000000">
                                </div>
                            </div>

                            <div class="form-group" id="dot-options" style="display: none;">
                                <div class="form-control">
                                    <label for="dot-diameter"><?php echo esc_html__('Dot Diameter (px)', 'washi-tape-generator'); ?></label>
                                    <input type="number" id="dot-diameter" value="5" min="1">
                                </div>
                                <div class="form-control">
                                    <label for="dot-color"><?php echo esc_html__('Dot Color', 'washi-tape-generator'); ?></label>
                                    <input type="color" id="dot-color" value="#ffffff">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><?php echo esc_html__('Image Overlay', 'washi-tape-generator'); ?></div>
                        <div class="form-control">
                            <div class="toggle-container">
                                <label class="toggle">
                                    <input type="checkbox" id="use-image-overlay">
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label"><?php echo esc_html__('Use Image Overlay', 'washi-tape-generator'); ?></span>
                            </div>
                        </div>

                        <div id="image-overlay-controls" style="display: none;">
                            <div class="form-control">
                                <label for="image-upload"><?php echo esc_html__('Upload Image', 'washi-tape-generator'); ?></label>
                                <div class="image-upload-container">
                                    <button type="button" id="image-upload-btn" class="button"><?php echo esc_html__('Select Image', 'washi-tape-generator'); ?></button>
                                    <div id="image-preview"></div>
                                    <input type="hidden" id="image-url" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-control">
                                    <label for="image-size"><?php echo esc_html__('Image Size', 'washi-tape-generator'); ?></label>
                                    <select id="image-size">
                                        <option value="cover"><?php echo esc_html__('Cover', 'washi-tape-generator'); ?></option>
                                        <option value="contain"><?php echo esc_html__('Contain', 'washi-tape-generator'); ?></option>
                                        <option value="repeat"><?php echo esc_html__('Repeat', 'washi-tape-generator'); ?></option>
                                        <option value="repeat-x"><?php echo esc_html__('Repeat X', 'washi-tape-generator'); ?></option>
                                        <option value="repeat-y"><?php echo esc_html__('Repeat Y', 'washi-tape-generator'); ?></option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label for="image-opacity"><?php echo esc_html__('Image Opacity', 'washi-tape-generator'); ?></label>
                                    <input type="range" id="image-opacity" min="0" max="100" value="50" class="range-slider">
                                    <output for="image-opacity" id="image-opacity-value">50%</output>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><?php echo esc_html__('Size & Position', 'washi-tape-generator'); ?></div>
                        <div class="form-group">
                            <div class="form-control">
                                <label for="width"><?php echo esc_html__('Width (px)', 'washi-tape-generator'); ?></label>
                                <input type="number" id="width" value="180" min="50">
                            </div>
                            <div class="form-control">
                                <label for="height"><?php echo esc_html__('Height (px)', 'washi-tape-generator'); ?></label>
                                <input type="number" id="height" value="45" min="20">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-control">
                                <label for="position"><?php echo esc_html__('Position', 'washi-tape-generator'); ?></label>
                                <select id="position">
                                    <option value="left"><?php echo esc_html__('Left', 'washi-tape-generator'); ?></option>
                                    <option value="right"><?php echo esc_html__('Right', 'washi-tape-generator'); ?></option>
                                    <option value="top"><?php echo esc_html__('Top', 'washi-tape-generator'); ?></option>
                                    <option value="bottom"><?php echo esc_html__('Bottom', 'washi-tape-generator'); ?></option>
                                </select>
                            </div>
                            <div class="form-control">
                                <label for="rotation"><?php echo esc_html__('Rotation (degrees)', 'washi-tape-generator'); ?></label>
                                <input type="number" id="rotation" value="0" step="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-control">
                                <label for="roughness"><?php echo esc_html__('Edge Roughness', 'washi-tape-generator'); ?></label>
                                <input type="range" id="roughness" min="1" max="20" value="10" class="range-slider">
                                <output for="roughness" id="roughness-value">10%</output>
                            </div>
                            <div class="form-control">
                                <label for="segments"><?php echo esc_html__('Edge Segments', 'washi-tape-generator'); ?></label>
                                <input type="range" id="segments" min="5" max="20" value="10" class="range-slider">
                                <output for="segments" id="segments-value">10</output>
                            </div>
                        </div>

                        <div class="form-control">
                            <div class="toggle-container">
                                <label class="toggle">
                                    <input type="checkbox" id="torn-edges" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label"><?php echo esc_html__('Torn Edges', 'washi-tape-generator'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" id="save-washi-tape" class="button button-primary"><?php echo esc_html__('Save Washi Tape', 'washi-tape-generator'); ?></button>
                        <button type="button" id="reset-washi-tape" class="button"><?php echo esc_html__('Reset', 'washi-tape-generator'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview & Saved Tapes Panel -->
        <div class="washi-tape-panel preview-panel">
            <div class="panel-header">
                <h2><?php echo esc_html__('Preview', 'washi-tape-generator'); ?></h2>
            </div>
            <div class="panel-body">
                <div class="preview-container">
                    <svg id="washi-svg" class="washi-svg" preserveAspectRatio="none"></svg>
                    <div class="content-box">
                        <h3><?php echo esc_html__('Washi Tape Preview', 'washi-tape-generator'); ?></h3>
                        <p><?php echo esc_html__('Customize your decorative tape using the controls', 'washi-tape-generator'); ?></p>
                    </div>
                </div>
            </div>

            <div class="panel-header saved-tapes-header">
                <h2><?php echo esc_html__('Saved Washi Tapes', 'washi-tape-generator'); ?></h2>
            </div>
            <div class="panel-body">
                <div id="saved-washi-tapes" class="saved-washi-tapes">
                    <div class="loading-indicator"><?php echo esc_html__('Loading...', 'washi-tape-generator'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
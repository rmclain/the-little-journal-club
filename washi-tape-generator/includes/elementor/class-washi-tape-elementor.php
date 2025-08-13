<?php

namespace Washi_Tape\Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add Washi Tape Controls to Elementor widgets
 * This is the SINGLE SOURCE OF TRUTH for washi tape controls
 */
class Washi_Tape_Controls
{
    /**
     * Track if controls have already been added to prevent duplicates
     */
    private static $controls_added = [];

    /**
     * Initialize the class
     */
    public function __construct()
    {
        // Frontend styles
        \add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
        \add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);

        // Editor styles
        \add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles']);
        \add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_preview_styles']);

        // Controls - Use a more specific hook to prevent conflicts
        \add_action('elementor/element/common/_section_style/after_section_end', [$this, 'add_washi_tape_controls'], 10, 1);
        \add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'add_washi_tape_controls'], 10, 1);
        \add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'add_washi_tape_controls'], 10, 1);
        \add_action('elementor/element/container/section_layout/after_section_end', [$this, 'add_washi_tape_controls'], 10, 1);

        // Content filter
        \add_filter('elementor/widget/render_content', [$this, 'apply_washi_tape'], 10, 2);

        // Add editor script loading
        \add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_preview_scripts']);

        // Register Washi Tape Controls
        \add_action('elementor/controls/register', [$this, 'register_washi_tape_control']);
    }

    /**
     * Add Washi Tape controls to Elementor widgets
     * This method prevents duplicate controls from being added
     */
    public function add_washi_tape_controls($element)
    {
        // Prevent duplicate controls
        $element_id = $element->get_id();
        if (isset(self::$controls_added[$element_id])) {
            return;
        }

        if (!\did_action('elementor/loaded') || !class_exists('\\Elementor\\Plugin')) {
            return;
        }

        if (!$element instanceof \Elementor\Element_Base) {
            return;
        }

        $allowed_types = ['widget', 'section', 'column', 'container'];
        if (!in_array($element->get_type(), $allowed_types, true)) {
            return;
        }

        try {
            // Log database interaction
            if (WP_DEBUG) {
                error_log('Washi Tape Generator: Attempting to fetch washi tapes from database');
            }

            // Get all washi tapes
            $db = new \Washi_Tape_DB();
            $washi_tapes = $db->get_all_washi_tapes();

            if (empty($washi_tapes) && WP_DEBUG) {
                error_log('Washi Tape Generator: No washi tapes found in database');
            }

            // Start adding controls
            $element->start_controls_section(
                'section_washi_tape',
                [
                    'label' => __('Washi Tape Maker', 'washi-tape-generator'),
                    'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
                ]
            );

            // Prepare options
            $options = array(
                '0' => __('None', 'washi-tape-generator'),
            );

            if (!empty($washi_tapes)) {
                foreach ($washi_tapes as $tape) {
                    $options[$tape->id] = $tape->title;
                }
            }

            $element->add_control(
                'enable_washi_tape',
                [
                    'label' => __('Enable Tape Decor', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'default' => '',
                    'label_on' => __('Yes', 'washi-tape-generator'),
                    'label_off' => __('No', 'washi-tape-generator'),
                    'return_value' => 'yes',
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'washi_tape_id',
                [
                    'label' => __('Select Washi Tape', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '0',
                    'options' => $options,
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'washi_tape_position',
                [
                    'label' => __('Tape Position', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'top-left',
                    'options' => [
                        'top-left' => __('Top Left', 'washi-tape-generator'),
                        'top-center' => __('Top Center', 'washi-tape-generator'),
                        'top-right' => __('Top Right', 'washi-tape-generator'),
                        'middle-left' => __('Middle Left', 'washi-tape-generator'),
                        'middle-center' => __('Middle Center', 'washi-tape-generator'),
                        'middle-right' => __('Middle Right', 'washi-tape-generator'),
                        'bottom-left' => __('Bottom Left', 'washi-tape-generator'),
                        'bottom-center' => __('Bottom Center', 'washi-tape-generator'),
                        'bottom-right' => __('Bottom Right', 'washi-tape-generator'),
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'washi_tape_rotation',
                [
                    'label' => __('Tape Rotation', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['deg'],
                    'range' => [
                        'deg' => [
                            'min' => -180,
                            'max' => 180,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'deg',
                        'size' => 0,
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'washi_tape_scale',
                [
                    'label' => __('Tape Scale', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['%'],
                    'range' => [
                        '%' => [
                            'min' => 10,
                            'max' => 200,
                            'step' => 5,
                        ],
                    ],
                    'default' => [
                        'unit' => '%',
                        'size' => 100,
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->add_control(
                'washi_tape_opacity',
                [
                    'label' => __('Tape Opacity', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['%'],
                    'range' => [
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => '%',
                        'size' => 100,
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                    'render_type' => 'template',
                ]
            );

            $element->end_controls_section();

            // Mark controls as added for this element
            self::$controls_added[$element_id] = true;

        } catch (\Exception $e) {
            if (WP_DEBUG) {
                error_log('Washi Tape Generator Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Enqueue styles
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            'washi-tape-elementor',
            WASHI_TAPE_URL . 'assets/css/elementor.css',
            [],
            WASHI_TAPE_VERSION
        );
    }

    /**
     * Enqueue editor styles
     */
    public function enqueue_editor_styles()
    {
        wp_enqueue_style(
            'washi-tape-elementor-editor',
            WASHI_TAPE_URL . 'assets/css/elementor-editor.css',
            [],
            WASHI_TAPE_VERSION
        );
    }

    /**
     * Enqueue preview styles
     */
    public function enqueue_preview_styles()
    {
        \wp_enqueue_style(
            'washi-tape-elementor-preview',
            WASHI_TAPE_URL . 'assets/css/elementor-preview.css',
            [],
            WASHI_TAPE_VERSION
        );
    }

    /**
     * Enqueue preview scripts
     */
    public function enqueue_preview_scripts()
    {
        \wp_enqueue_script(
            'washi-tape-elementor-preview',
            WASHI_TAPE_URL . 'assets/js/elementor-preview.js',
            ['jquery'],
            WASHI_TAPE_VERSION,
            true
        );
    }

    /**
     * Apply Washi Tape to widget content
     */
    public function apply_washi_tape($content, $widget)
    {
        try {
            if (WP_DEBUG) {
                error_log('Washi Tape Generator: Attempting to apply washi tape to widget: ' . $widget->get_name());
            }

            // Get settings
            $settings = $widget->get_settings_for_display();

            // Check if washi tape is enabled
            if (empty($settings['enable_washi_tape']) || $settings['enable_washi_tape'] !== 'yes') {
                return $content;
            }

            // Check if a tape is selected
            if (empty($settings['washi_tape_id']) || $settings['washi_tape_id'] === '0') {
                return $content;
            }

            // Get the washi tape data
            $db = new \Washi_Tape_DB();
            $tape = $db->get_washi_tape($settings['washi_tape_id']);

            if (!$tape) {
                if (WP_DEBUG) {
                    error_log('Washi Tape Generator: Tape not found with ID: ' . $settings['washi_tape_id']);
                }
                return $content;
            }

            // Get position
            $position = !empty($settings['washi_tape_position']) ? $settings['washi_tape_position'] : 'top-left';

            // Read controls
            $rotation = isset($settings['washi_tape_rotation']['size']) ? floatval($settings['washi_tape_rotation']['size']) : 0;
            $scale_percent = isset($settings['washi_tape_scale']['size']) ? floatval($settings['washi_tape_scale']['size']) : 100;
            $scale_factor = max(0.1, $scale_percent / 100);
            $opacity_percent = isset($settings['washi_tape_opacity']['size']) ? floatval($settings['washi_tape_opacity']['size']) : 100;
            $opacity = max(0.0, min(1.0, $opacity_percent / 100));

            // Sanitize SVG
            $svg_content = !empty($tape->svg) ? $tape->svg : '';
            if (empty($svg_content)) {
                return $content;
            }
            $allowed_tags = [
                'svg' => [
                    'xmlns' => [],
                    'viewBox' => [],
                    'width' => [],
                    'height' => [],
                    'preserveAspectRatio' => [],
                    'class' => [],
                ],
                'path' => [
                    'd' => [],
                    'fill' => [],
                    'stroke' => [],
                    'stroke-width' => [],
                    'opacity' => [],
                ],
                'g' => ['transform' => [], 'fill' => [], 'stroke' => []],
                'rect' => ['x' => [], 'y' => [], 'width' => [], 'height' => [], 'fill' => [], 'rx' => [], 'ry' => []],
                'circle' => ['cx' => [], 'cy' => [], 'r' => [], 'fill' => []],
                'polygon' => ['points' => [], 'fill' => []],
                'defs' => [],
                'pattern' => ['id' => [], 'patternUnits' => [], 'width' => [], 'height' => []],
                'line' => ['x1' => [], 'y1' => [], 'x2' => [], 'y2' => [], 'stroke' => [], 'stroke-width' => []],
            ];
            $sanitized_svg = \wp_kses($svg_content, $allowed_tags);

            // Build inline style
            $inline_style = \sprintf(
                'opacity:%s; transform: rotate(%sdeg) scale(%s); display:inline-block; pointer-events:none;',
                $opacity,
                $rotation,
                $scale_factor
            );

            // Create the washi tape HTML
            $tape_html = \sprintf(
                '<div class="elementor-washi-tape washi-tape-position-%s" style="%s" data-tape-id="%d">%s</div>',
                \esc_attr($position),
                \esc_attr($inline_style),
                (int) $tape->id,
                $sanitized_svg
            );

            if (WP_DEBUG) {
                error_log('Washi Tape Generator: Successfully applied washi tape to widget');
            }

            return $tape_html . $content;
        } catch (\Exception $e) {
            error_log(sprintf(
                'Washi Tape Generator: Error applying washi tape - Message: %s | File: %s | Line: %d | Trace: %s',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            ));
            return $content;
        }
    }

    /**
     * Register Washi Tape Control
     * 
     * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
     */
    public function register_washi_tape_control($controls_manager)
    {
        try {
            if (WP_DEBUG) {
                error_log('Washi Tape Generator: Starting control registration');
            }

            if (!$controls_manager instanceof \Elementor\Controls_Manager) {
                error_log('Washi Tape Generator: Invalid controls manager instance');
                return;
            }

            // Get all washi tapes from database
            $db = new \Washi_Tape_DB();
            $washi_tapes = $db->get_all_washi_tapes();

            if (empty($washi_tapes) && WP_DEBUG) {
                error_log('Washi Tape Generator: No washi tapes found during control registration');
            }

            // Prepare options for the dropdown
            $options = [
                '0' => __('None', 'washi-tape-generator'),
            ];

            if (!empty($washi_tapes)) {
                foreach ($washi_tapes as $tape) {
                    $options[$tape->id] = $tape->title;
                }
            }

            // Register a new control type
            $controls_manager->add_control(
                'washi_tape_selector',
                [
                    'label' => __('Washi Tape', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '0',
                    'options' => $options,
                    'label_block' => true,
                ]
            );

            if (WP_DEBUG) {
                error_log('Washi Tape Generator: Successfully registered washi tape control');
            }
        } catch (\Exception $e) {
            error_log(sprintf(
                'Washi Tape Generator: Error registering control - Message: %s | File: %s | Line: %d | Trace: %s',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            ));
        }
    }
}

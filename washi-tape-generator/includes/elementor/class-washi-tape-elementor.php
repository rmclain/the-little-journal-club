<?php

namespace Washi_Tape\Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add Washi Tape Controls to Elementor widgets
 */
class Washi_Tape_Controls
{
    /**
     * Initialize the class
     */
    public function __construct()
    {
        // Frontend styles
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);

        // Editor styles
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles']);
        add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_preview_styles']);

        // Controls
        add_action('elementor/element/after_section_end', [$this, 'add_washi_tape_controls'], 10, 3);

        // Content filter
        add_filter('elementor/widget/render_content', [$this, 'apply_washi_tape'], 10, 2);

        // Add editor script loading
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_preview_scripts']);

        // Register Washi Tape Controls
        add_action('elementor/controls/register', [$this, 'register_washi_tape_control']);
    }

    /**
     * Add Washi Tape controls to Elementor widgets
     */
    public function add_washi_tape_controls($element, $section_id, $args)
    {

        if ('section_advanced' !== $section_id || 'section_custom_css' !== $section_id) { // Try "section_advanced" instead
            return;
        }

        if (!did_action('elementor/loaded') || !class_exists('\Elementor\Plugin')) {
            return;
        }

        if (!$element instanceof \Elementor\Element_Base) {
            return;
        }

        $allowed_types = ['widget', 'section', 'column'];
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
                ]
            );

            $element->add_control(
                'washi_tape_id',
                [
                    'label' => __('Select Tape', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '0',
                    'options' => $options,
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                ]
            );

            $element->add_control(
                'washi_tape_position',
                [
                    'label' => __('Position', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'top-left',
                    'options' => [
                        'top-left' => __('Top Left', 'washi-tape-generator'),
                        'top-right' => __('Top Right', 'washi-tape-generator'),
                        'bottom-left' => __('Bottom Left', 'washi-tape-generator'),
                        'bottom-right' => __('Bottom Right', 'washi-tape-generator'),
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'washi_tape_id!' => '0',
                    ],
                ]
            );

            $element->add_control(
                'washi_tape_rotation',
                [
                    'label' => __('Rotation', 'washi-tape-generator'),
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
                        'washi_tape_id!' => '0',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-washi-tape' => 'transform: rotate({{SIZE}}{{UNIT}});',
                    ],
                ]
            );

            $element->add_control(
                'washi_tape_z_index',
                [
                    'label' => __('Z-Index', 'washi-tape-generator'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => -1000,
                    'max' => 1000,
                    'step' => 1,
                    'default' => 1,
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'washi_tape_id!' => '0',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-washi-tape' => 'z-index: {{VALUE}};',
                    ],
                ]
            );

            $element->end_controls_section();

            if (WP_DEBUG) {
                error_log('Washi Tape Generator: Successfully added all controls to element');
            }
        } catch (\Exception $e) {
            error_log(sprintf(
                'Washi Tape Generator: Error adding controls - Message: %s | File: %s | Line: %d | Trace: %s',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            ));
            return;
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
        wp_enqueue_style(
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
        wp_enqueue_script(
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

            // Create the washi tape HTML
            $tape_html = sprintf(
                '<div class="elementor-washi-tape washi-tape-position-%s" style="background-image: url(%s);" data-tape-id="%d"></div>',
                esc_attr($position),
                esc_url($tape->image_url),
                (int) $tape->id
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

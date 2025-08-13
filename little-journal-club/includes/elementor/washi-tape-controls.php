<?php
namespace LJC\Elementor;

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
    }

    /**
     * Add Washi Tape controls to Elementor widgets
     */
    public function add_washi_tape_controls($element, $section_id, $args)
    {
        if ('section_custom_css' !== $section_id) {
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
            $element->start_controls_section(
                'section_washi_tape',
                [
                    'label' => __('Washi Tape', 'little-journal-club'),
                    'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
                ]
            );

            $element->add_control(
                'enable_washi_tape',
                [
                    'label' => __('Enable Washi Tape', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'default' => '',
                    'label_on' => __('Yes', 'little-journal-club'),
                    'label_off' => __('No', 'little-journal-club'),
                    'return_value' => 'yes',
                ]
            );

            $element->add_control(
                'tape_position',
                [
                    'label' => __('Tape Position', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'both',
                    'options' => [
                        'both' => __('Both Sides', 'little-journal-club'),
                        'left' => __('Left Side Only', 'little-journal-club'),
                        'right' => __('Right Side Only', 'little-journal-club'),
                        'center' => __('Center', 'little-journal-club'),
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                ]
            );

            $element->add_control(
                'tape_style',
                [
                    'label' => __('Tape Style', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'pink',
                    'options' => [
                        'pink' => __('Pink', 'little-journal-club'),
                        'mint' => __('Mint', 'little-journal-club'),
                        'lavender' => __('Lavender', 'little-journal-club'),
                        'striped' => __('Striped', 'little-journal-club'),
                        'polkadot' => __('Polka Dot', 'little-journal-club'),
                        'floral' => __('Floral', 'little-journal-club'),
                        'grid' => __('Blue Grid', 'little-journal-club'),
                        'bw-grid' => __('Black & White Grid', 'little-journal-club'),
                        'rainbow' => __('Rainbow', 'little-journal-club'),
                        'custom' => __('Custom Color', 'little-journal-club'),
                        'custom-image' => __('Custom Image', 'little-journal-club'),
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                ]
            );

            $element->add_control(
                'tape_custom_color',
                [
                    'label' => __('Custom Tape Color', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#FFD166',
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'tape_style' => 'custom',
                    ],
                ]
            );

            $element->add_control(
                'tape_custom_image',
                [
                    'label' => __('Custom Tape Image', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'default' => [
                        'url' => '',
                    ],
                    'condition' => [
                        'tape_style' => 'custom-image',
                    ],
                ]
            );

            $element->add_control(
                'tape_width',
                [
                    'label' => __('Tape Width', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 25,
                            'max' => 300,
                            'step' => 5,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 125,
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                ]
            );

            $element->add_control(
                'tape_height',
                [
                    'label' => __('Tape Height', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 22,
                            'max' => 82,
                            'step' => 2,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 52,
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                ]
            );

            $element->add_control(
                'tape_rotation',
                [
                    'label' => __('Tape Rotation', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['deg'],
                    'range' => [
                        'deg' => [
                            'min' => 13,
                            'max' => 53,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'deg',
                        'size' => 33,
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'tape_position!' => 'center',
                    ],
                ]
            );

            $element->add_control(
                'tape_vertical_offset',
                [
                    'label' => __('Tape Vertical Position', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 25,
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ljc-tape' => 'margin-top: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->add_control(
                'tape_image_size',
                [
                    'label' => __('Image Size', 'little-journal-club'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'cover',
                    'options' => [
                        'cover' => __('Cover', 'little-journal-club'),
                        'contain' => __('Contain', 'little-journal-club'),
                        'repeat' => __('Repeat', 'little-journal-club'),
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'tape_style' => 'custom-image',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ljc-tape.ljc-tape-custom-image' => 'background-size: {{VALUE}};',
                    ],
                ]
            );

            $element->add_control(
                'tape_image_opacity',
                [
                    'label' => __('Image Opacity', 'little-journal-club'),
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
                        'size' => 80,
                    ],
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'tape_style' => 'custom-image',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ljc-tape.ljc-tape-custom-image' => 'opacity: {{SIZE}}%;',
                    ],
                ]
            );

            $element->end_controls_section();

        } catch (\Exception $e) {
            error_log('LJC Washi Tape: Error adding controls - ' . $e->getMessage());
            return;
        }
    }

    /**
     * Apply Washi Tape to widget content
     */
    public function apply_washi_tape($content, $widget)
    {
        $settings = $widget->get_settings_for_display();

        if (empty($settings['enable_washi_tape']) || $settings['enable_washi_tape'] !== 'yes') {
            return $content;
        }

        $tape_position = $settings['tape_position'];
        $tape_style = $settings['tape_style'];
        $tape_width = isset($settings['tape_width']['size']) ? $settings['tape_width']['size'] : 125;
        $tape_width .= isset($settings['tape_width']['unit']) ? $settings['tape_width']['unit'] : 'px';
        $tape_height = isset($settings['tape_height']['size']) ? $settings['tape_height']['size'] : 52;
        $tape_height .= 'px';
        $tape_rotation = isset($settings['tape_rotation']['size']) ? $settings['tape_rotation']['size'] : 33;
        $tape_rotation .= 'deg';
        $vertical_offset = isset($settings['tape_vertical_offset']['size']) ? $settings['tape_vertical_offset']['size'] : 25;
        $vertical_offset .= 'px';

        // Create unique ID for this instance
        $unique_id = 'ljc-washi-' . uniqid();

        // Handle custom image background
        $custom_bg_style = '';
        if ($tape_style === 'custom-image' && !empty($settings['tape_custom_image']['url'])) {
            $custom_bg_style = sprintf(
                'background-image: url(%s); background-size: cover; background-position: center; background-repeat: no-repeat;',
                esc_url($settings['tape_custom_image']['url'])
            );
        }

        // Start building the wrapper
        $output = '<div class="ljc-washi-tape-wrapper" id="' . esc_attr($unique_id) . '" data-tape-enabled="true">';
        
        // Base tape styles
        $tape_style_attr = sprintf(
            'width: %s; height: %s; margin-top: %s; %s',
            esc_attr($tape_width),
            esc_attr($tape_height),
            esc_attr($vertical_offset),
            $custom_bg_style
        );

        // Function to create tape element
        $create_tape = function($position) use ($tape_style, $tape_style_attr) {
            $output = sprintf(
                '<div class="ljc-tape ljc-tape-%s ljc-tape-%s" style="%s">',
                esc_attr($position),
                esc_attr($tape_style),
                $tape_style_attr
            );
            
            if ($tape_style === 'custom-image') {
                $output .= '<div class="ljc-tape-overlay"></div>';
            }
            
            $output .= '</div>';
            return $output;
        };

        // Add tape elements based on position
        if ($tape_position === 'both' || $tape_position === 'left') {
            $output .= $create_tape('left');
        }
        
        if ($tape_position === 'both' || $tape_position === 'right') {
            $output .= $create_tape('right');
        }
        
        if ($tape_position === 'center') {
            $output .= $create_tape('center');
        }

        // Add the content
        $output .= '<div class="ljc-washi-content">';
        $output .= $content;
        $output .= '</div>';
        
        $output .= '</div>';

        // Add custom CSS for this instance
        $custom_css = '<style>
            #' . $unique_id . ' {
                position: relative;
                z-index: 0;
            }
            #' . $unique_id . ' .ljc-tape {
                position: absolute;
                z-index: 2;
            }
            #' . $unique_id . ' .ljc-tape-left {
                transform: rotate(-' . $tape_rotation . ');
                left: -20px;
            }
            #' . $unique_id . ' .ljc-tape-right {
                transform: rotate(' . $tape_rotation . ');
                right: -20px;
            }
            #' . $unique_id . ' .ljc-tape-center {
                left: 50%;
                transform: translateX(-50%);
            }
            #' . $unique_id . ' .ljc-washi-content {
                position: relative;
                z-index: 1;
            }
        </style>';

        return $custom_css . $output;
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            'ljc-washi-tape',
            LJC_PLUGIN_URL . 'assets/css/washi.css',
            [],
            LJC_VERSION
        );
    }

    /**
     * Enqueue editor styles
     */
    public function enqueue_editor_styles()
    {
        wp_enqueue_style(
            'ljc-washi-tape-editor',
            LJC_PLUGIN_URL . 'assets/css/washi-editor.css',
            [],
            LJC_VERSION
        );
    }

    /**
     * Enqueue preview styles
     */
    public function enqueue_preview_styles()
    {
        wp_enqueue_style(
            'ljc-washi-tape-preview',
            LJC_PLUGIN_URL . 'assets/css/washi-preview.css',
            [],
            LJC_VERSION
        );
        
        // Also enqueue main styles to ensure they're available in preview
        wp_enqueue_style(
            'ljc-washi-tape',
            LJC_PLUGIN_URL . 'assets/css/washi.css',
            [],
            LJC_VERSION
        );
    }

    public function enqueue_preview_scripts()
    {
        wp_enqueue_script(
            'ljc-washi-tape-preview',
            LJC_PLUGIN_URL . 'assets/js/washi-preview.js',
            ['jquery', 'elementor-frontend'],
            LJC_VERSION,
            true
        );
    }
}
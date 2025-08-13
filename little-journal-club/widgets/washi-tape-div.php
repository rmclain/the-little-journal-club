<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Washi_Tape_Div_Widget extends \Elementor\Widget_Base
{
    /**
     * Retrieve saved washi tapes from the Generator plugin if available
     * @return array id => title
     */
    private function get_generator_washi_tape_options()
    {
        $options = ['0' => __('Select a Tape', 'little-journal-club')];

        // Check if the Washi_Tape_DB class exists and is available
        if (class_exists('Washi_Tape_DB')) {
            try {
                $db = new \Washi_Tape_DB();
                if (method_exists($db, 'get_all_washi_tapes')) {
                    $tapes = $db->get_all_washi_tapes();
                    if (!empty($tapes) && is_array($tapes)) {
                        foreach ($tapes as $tape) {
                            if (isset($tape->id) && isset($tape->title)) {
                                $options[(string) $tape->id] = $tape->title;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Silently ignore and fall back to default
                error_log('Washi Tape DB Error: ' . $e->getMessage());
            }
        }

        return $options;
    }

    /**
     * Fetch SVG for a saved washi tape from the Generator plugin
     */
    private function get_generator_washi_tape_svg($tape_id)
    {
        if (!class_exists('Washi_Tape_DB')) {
            return '';
        }

        try {
            $db = new \Washi_Tape_DB();
            if (method_exists($db, 'get_washi_tape')) {
                $record = $db->get_washi_tape(intval($tape_id));
                if ($record && !empty($record->svg)) {
                    return $record->svg;
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the widget
            error_log('Washi Tape SVG Error: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * Very basic SVG sanitization (aligns with the generator plugin approach)
     */
    private function sanitize_svg_content($svg_content)
    {
        $allowed_tags = [
            'svg' => [
                'xmlns' => [],
                'viewbox' => [],
                'width' => [],
                'height' => [],
                'preserveaspectratio' => [],
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

        if (function_exists('wp_kses_svg')) {
            return wp_kses($svg_content, wp_kses_svg());
        }
        return wp_kses($svg_content, $allowed_tags);
    }
    public function get_name()
    {
        return 'washi_tape_div';
    }

    public function get_title()
    {
        return __('Washi Tape Div', 'little-journal-club');
    }

    public function get_icon()
    {
        return 'eicon-image-box';
    }

    public function get_categories()
    {
        return ['little-journal-club'];
    }

    public function get_script_depends()
    {
        return [];
    }

    public function get_style_depends()
    {
        return ['ljc-washi-tape-style', 'ljc-widget-washi-tape-style'];
    }

    protected function _register_controls()
    {
        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'little-journal-club'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Your Title Here', 'little-journal-club'),
                'placeholder' => __('Enter your title', 'little-journal-club'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'content',
            [
                'label' => __('Content', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Add your content here', 'little-journal-club'),
            ]
        );

        $this->end_controls_section();

        // Washi Tape Settings (Legacy built-in styles)
        $this->start_controls_section(
            'section_washi_tape',
            [
                'label' => __('Washi Tape Settings (Legacy)', 'little-journal-club'),
            ]
        );

        $this->add_control(
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
            ]
        );

        $this->add_control(
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
            ]
        );

        $this->add_control(
            'tape_custom_color',
            [
                'label' => __('Custom Tape Color', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD166',
                'condition' => [
                    'tape_style' => 'custom',
                ],
            ]
        );

        $this->add_control(
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

        $this->add_control(
            'tape_image_size',
            [
                'label' => __('Image Pattern Size', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'condition' => [
                    'tape_style' => 'custom-image',
                ],
            ]
        );

        $this->add_control(
            'tape_width',
            [
                'label' => __('Tape Width', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 95,
                        'max' => 295,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 195,
                ],
                'condition' => [
                    'tape_position!' => '',
                ],
            ]
        );

        $this->add_control(
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
                    'tape_position!' => '',
                ],
            ]
        );

        $this->add_control(
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
                    'tape_position!' => 'center',
                ],
            ]
        );

        $this->add_responsive_control(
            'tape_vertical_position',
            [
                'label' => __('Tape Vertical Position', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'description' => __('Adjust the vertical position of all tape elements', 'little-journal-club'),
            ]
        );

        $this->add_control(
            'tape_left_offset',
            [
                'label' => __('Left Tape Offset', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 0,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -40,
                        'max' => 0,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -50,
                ],
                'condition' => [
                    'tape_position' => ['both', 'left'],
                ],
            ]
        );

        $this->add_control(
            'tape_right_offset',
            [
                'label' => __('Right Tape Offset', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 0,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -40,
                        'max' => 0,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -50,
                ],
                'condition' => [
                    'tape_position' => ['both', 'right'],
                ],
            ]
        );

        $this->add_control(
            'tape_center_offset',
            [
                'label' => __('Center Tape Offset', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -10,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'condition' => [
                    'tape_position' => 'center',
                ],
            ]
        );

        $this->add_control(
            'tape_shadow',
            [
                'label' => __('Tape Shadow', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'little-journal-club'),
                'label_off' => __('No', 'little-journal-club'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tape_shadow_intensity',
            [
                'label' => __('Shadow Intensity', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'condition' => [
                    'tape_shadow' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tape_shadow_color',
            [
                'label' => __('Shadow Color', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.2)',
                'condition' => [
                    'tape_shadow' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Advanced: Generator compatibility controls (applies to this widget)
        $this->start_controls_section(
            'section_washi_tape_generator',
            [
                'label' => __('Washi Tape Settings', 'little-journal-club'),
                'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
            ]
        );

        $this->add_control(
            'gen_enable_washi_tape',
            [
                'label' => __('Enable Washi Tape', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'little-journal-club'),
                'label_off' => __('No', 'little-journal-club'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'gen_washi_tape_mode',
            [
                'label' => __('Washi Tape Mode', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'specific',
                'options' => [
                    'specific' => __('Specific Tape', 'little-journal-club'),
                    'legacy' => __('Legacy Styles', 'little-journal-club'),
                ],
                'condition' => ['gen_enable_washi_tape' => 'yes'],
            ]
        );

        $this->add_control(
            'gen_washi_tape_id',
            [
                'label' => __('Select Washi Tape', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_generator_washi_tape_options(),
                'default' => '0',
                'condition' => [
                    'gen_enable_washi_tape' => 'yes',
                    'gen_washi_tape_mode' => 'specific',
                ],
            ]
        );

        $this->add_control(
            'gen_top_clearance',
            [
                'label' => __('Top Clearance (padding at top of card)', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 200, 'step' => 1]],
                'default' => ['unit' => 'px', 'size' => 25],
                'condition' => ['gen_enable_washi_tape' => 'yes'],
            ]
        );

        $this->add_control(
            'gen_horizontal_offset',
            [
                'label' => __('Horizontal Offset', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => -200, 'max' => 200, 'step' => 1]],
                'default' => ['unit' => 'px', 'size' => 0],
                'condition' => ['gen_enable_washi_tape' => 'yes'],
            ]
        );

        $this->add_control(
            'gen_vertical_offset',
            [
                'label' => __('Vertical Offset (Y)', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => -200, 'max' => 200, 'step' => 1]],
                'default' => ['unit' => 'px', 'size' => -64],
                'condition' => ['gen_enable_washi_tape' => 'yes'],
            ]
        );

        $this->add_control(
            'gen_randomize_angles',
            [
                'label' => __('Randomize Tape Angles', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'little-journal-club'),
                'label_off' => __('No', 'little-journal-club'),
                'return_value' => 'yes',
                'default' => '',
                'condition' => ['gen_enable_washi_tape' => 'yes'],
            ]
        );

        $this->add_control(
            'gen_tape_rotation',
            [
                'label' => __('Tape Rotation', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => ['deg' => ['min' => 0, 'max' => 90, 'step' => 1]],
                'default' => ['unit' => 'deg', 'size' => 10],
                'condition' => ['gen_enable_washi_tape' => 'yes'],
            ]
        );

        $this->end_controls_section();

        // Div Style Settings
        $this->start_controls_section(
            'section_div_style',
            [
                'label' => __('Div Style', 'little-journal-club'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'div_background',
            [
                'label' => __('Background Color', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'div_border',
                'label' => __('Border', 'little-journal-club'),
                'selector' => '{{WRAPPER}} .washi-tape-content',
            ]
        );

        $this->add_control(
            'div_border_radius',
            [
                'label' => __('Border Radius', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_responsive_control(
            'div_padding',
            [
                'label' => __('Padding', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 20,
                    'right' => 20,
                    'bottom' => 20,
                    'left' => 20,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'div_box_shadow',
                'label' => __('Box Shadow', 'little-journal-club'),
                'selector' => '{{WRAPPER}} .washi-tape-content',
            ]
        );

        $this->add_responsive_control(
            'div_min_height',
            [
                'label' => __('Minimum Height', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-content' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'div_width',
            [
                'label' => __('Width', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 10,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_min_height',
            [
                'label' => __('Container Minimum Height', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-container' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_top_position',
            [
                'label' => __('Content Top Position', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => -99,
                        'max' => 1,
                        'step' => 1,
                    ],
                    'vh' => [
                        'min' => -20,
                        'max' => 10,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -20,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -49,
                ],
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-content' => 'position: relative; top: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'text_align',
            [
                'label' => __('Text Alignment', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'little-journal-club'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'little-journal-club'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'little-journal-club'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Title Typography Section
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __('Title Style', 'little-journal-club'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#222222',
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'little-journal-club'),
                'selector' => '{{WRAPPER}} .washi-tape-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margin', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 15,
                    'left' => 0,
                    'unit' => 'px',
                    'isLinked' => false,
                ],
            ]
        );

        $this->end_controls_section();

        // Content Typography Section
        $this->start_controls_section(
            'section_typography',
            [
                'label' => __('Content Typography', 'little-journal-club'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => __('Typography', 'little-journal-club'),
                'selector' => '{{WRAPPER}} .washi-tape-content-text',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => __('Text Color', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .washi-tape-content-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Get tape settings with proper fallbacks
        $tape_position = isset($settings['tape_position']) ? $settings['tape_position'] : 'both';
        $tape_style = isset($settings['tape_style']) ? $settings['tape_style'] : 'pink';

        // Ensure width and height have proper defaults
        $tape_width = isset($settings['tape_width']['size']) ? $settings['tape_width']['size'] . $settings['tape_width']['unit'] : '195px';
        $tape_height = isset($settings['tape_height']['size']) ? $settings['tape_height']['size'] . 'px' : '52px';
        $tape_rotation = isset($settings['tape_rotation']['size']) ? $settings['tape_rotation']['size'] . 'deg' : '33deg';

        // Get offset values with updated defaults
        $left_offset = isset($settings['tape_left_offset']['size']) ? $settings['tape_left_offset']['size'] . $settings['tape_left_offset']['unit'] : '-50px';
        $right_offset = isset($settings['tape_right_offset']['size']) ? $settings['tape_right_offset']['size'] . $settings['tape_right_offset']['unit'] : '-50px';
        $center_offset = isset($settings['tape_center_offset']['size']) ? $settings['tape_center_offset']['size'] . $settings['tape_center_offset']['unit'] : '0';

        // Get tape vertical position
        $tape_vertical_offset = isset($settings['tape_vertical_position']['size']) ? $settings['tape_vertical_position']['size'] . $settings['tape_vertical_position']['unit'] : '0';

        // Calculate top position based on tape height and vertical offset
        $tape_height_value = isset($settings['tape_height']['size']) ? $settings['tape_height']['size'] : 52;
        $base_top = '-' . ($tape_height_value / 2) . 'px';
        $tape_top = 'calc(' . $base_top . ' + ' . $tape_vertical_offset . ')';

        // Custom style calculation
        $custom_style = '';
        if ($tape_style === 'custom' && !empty($settings['tape_custom_color'])) {
            $custom_style = 'background-color: ' . esc_attr($settings['tape_custom_color']) . ';';
        } elseif ($tape_style === 'custom-image' && !empty($settings['tape_custom_image']['url'])) {
            $image_size = isset($settings['tape_image_size']['size']) ? $settings['tape_image_size']['size'] : 50;
            $custom_style = 'background-image: url(' . esc_url($settings['tape_custom_image']['url']) . ');';
            $custom_style .= 'background-repeat: repeat;';
            $custom_style .= 'background-size: ' . $image_size . 'px;';
        }

        // Shadow effect if enabled
        $shadow_style = '';
        if (isset($settings['tape_shadow']) && $settings['tape_shadow'] === 'yes') {
            $shadow_intensity = isset($settings['tape_shadow_intensity']['size']) ? $settings['tape_shadow_intensity']['size'] : 10;
            $shadow_color = !empty($settings['tape_shadow_color']) ? $settings['tape_shadow_color'] : 'rgba(0, 0, 0, 0.2)';
            $shadow_style = 'filter: drop-shadow(0 0 ' . $shadow_intensity . 'px ' . $shadow_color . ');';
        }

        // Container class
        $container_class = 'widget-washi-tape-container';

        // Container style for min-height (if set directly in render)
        $container_style = '';
        if (isset($settings['container_min_height']['size']) && $settings['container_min_height']['size'] > 0) {
            $container_style .= 'min-height: ' . $settings['container_min_height']['size'] . $settings['container_min_height']['unit'] . ';';
        }

        // Content style for top position (if not using selectors)
        $content_style = '';
        if (isset($settings['content_top_position']['size'])) {
            $top_value = $settings['content_top_position']['size'] . $settings['content_top_position']['unit'];
            $content_style .= 'position: relative; top: ' . $top_value . ';';
        } else {
            $content_style .= 'position: relative; top: -49px;'; // Updated default value
        }

        // If generator-based tape is enabled and selected, render using generator SVG
        $use_generator = isset($settings['gen_enable_washi_tape']) && $settings['gen_enable_washi_tape'] === 'yes' &&
            isset($settings['gen_washi_tape_mode']) && $settings['gen_washi_tape_mode'] === 'specific' &&
            !empty($settings['gen_washi_tape_id']) && $settings['gen_washi_tape_id'] !== '0';

        // For generator controls
        $gen_top_clearance = isset($settings['gen_top_clearance']['size']) ? intval($settings['gen_top_clearance']['size']) : 25;
        $gen_h_offset = isset($settings['gen_horizontal_offset']['size']) ? intval($settings['gen_horizontal_offset']['size']) : 0;
        $gen_v_offset = isset($settings['gen_vertical_offset']['size']) ? intval($settings['gen_vertical_offset']['size']) : 0;
        $gen_rotation = isset($settings['gen_tape_rotation']['size']) ? intval($settings['gen_tape_rotation']['size']) : 10;
        $gen_randomize = !empty($settings['gen_randomize_angles']) && $settings['gen_randomize_angles'] === 'yes';

        // Adjust content top padding if generator enabled
        if ($use_generator) {
            $content_style .= 'padding-top: ' . $gen_top_clearance . 'px;';
        }

        // When using the generator, avoid legacy background classes so the SVG is the only visual
        $tape_visual_class = $use_generator ? 'tape-from-generator' : 'tape-pattern-' . $tape_style;

        // Ensure we have valid settings for rendering
        if (empty($tape_position)) {
            $tape_position = 'both';
        }

        ?>
        <div class="widget-washi-tape-container" <?php if (!empty($container_style))
            echo 'style="' . esc_attr($container_style) . '"'; ?>>
            <?php
            // Left tape
            if ($tape_position === 'both' || $tape_position === 'left'):
                // Position with offset
                $left_pos = 'calc(0px + ' . $left_offset . ')';
                if ($use_generator) {
                    $left_pos = 'calc(' . $left_pos . ' + ' . $gen_h_offset . 'px)';
                }
                // Top value (use generator vertical offset when enabled)
                $left_top = $tape_top;
                if ($use_generator) {
                    $left_top = 'calc(' . $base_top . ' + ' . $gen_v_offset . 'px)';
                }
                ?>
                <div class="widget-tape widget-tape-left <?php echo esc_attr($tape_visual_class); ?>" style="width: <?php echo esc_attr($tape_width); ?>; 
                            height: <?php echo esc_attr($tape_height); ?>; 
                            top: <?php echo esc_attr($left_top); ?>;
                            left: <?php echo esc_attr($left_pos); ?>;
                            <?php
                            if ($use_generator) {
                                $left_deg = $gen_randomize ? max(0, $gen_rotation - 5 + (rand(0, 10))) : $gen_rotation;
                                echo ' transform: rotate(-' . esc_attr($left_deg) . 'deg);';
                            } else {
                                echo ' transform: rotate(-' . esc_attr($tape_rotation) . ');';
                            }
                            ?> 
                            transform-origin: 0 50%;
                            <?php echo $custom_style; ?>
                            <?php echo $shadow_style; ?>">
                    <?php if ($use_generator) {
                        $svg = $this->get_generator_washi_tape_svg($settings['gen_washi_tape_id']);
                        if (!empty($svg)) {
                            echo $this->sanitize_svg_content($svg);
                        }
                    } ?>
                </div>
            <?php endif; ?>

            <?php
            // Right tape
            if ($tape_position === 'both' || $tape_position === 'right'):
                // Position with offset
                $right_pos = 'calc(0px + ' . $right_offset . ')';
                if ($use_generator) {
                    $right_pos = 'calc(' . $right_pos . ' + ' . $gen_h_offset . 'px)';
                }
                $right_top = $tape_top;
                if ($use_generator) {
                    $right_top = 'calc(' . $base_top . ' + ' . $gen_v_offset . 'px)';
                }
                ?>
                <div class="widget-tape widget-tape-right <?php echo esc_attr($tape_visual_class); ?>" style="width: <?php echo esc_attr($tape_width); ?>; 
                            height: <?php echo esc_attr($tape_height); ?>; 
                            top: <?php echo esc_attr($right_top); ?>;
                            right: <?php echo esc_attr($right_pos); ?>;
                            <?php
                            if ($use_generator) {
                                $right_deg = $gen_randomize ? max(0, $gen_rotation - 5 + (rand(0, 10))) : $gen_rotation;
                                echo ' transform: rotate(' . esc_attr($right_deg) . 'deg);';
                            } else {
                                echo ' transform: rotate(' . esc_attr($tape_rotation) . ');';
                            }
                            ?> 
                            transform-origin: 100% 50%;
                            <?php echo $custom_style; ?>
                            <?php echo $shadow_style; ?>">
                    <?php if ($use_generator) {
                        $svg = $this->get_generator_washi_tape_svg($settings['gen_washi_tape_id']);
                        if (!empty($svg)) {
                            echo $this->sanitize_svg_content($svg);
                        }
                    } ?>
                </div>
            <?php endif; ?>

            <?php
            // Center tape
            if ($tape_position === 'center'):
                // Position with offset
                $center_pos = 'calc(50% + ' . $center_offset . ')';
                if ($use_generator) {
                    $center_pos = 'calc(50% + ' . $center_offset . ' + ' . $gen_h_offset . 'px)';
                }
                $center_top = $tape_top;
                if ($use_generator) {
                    $center_top = 'calc(' . $base_top . ' + ' . $gen_v_offset . 'px)';
                }
                ?>
                <div class="widget-tape widget-tape-center <?php echo esc_attr($tape_visual_class); ?>" style="width: <?php echo esc_attr($tape_width); ?>; 
                            height: <?php echo esc_attr($tape_height); ?>; 
                            top: <?php echo esc_attr($center_top); ?>;
                            left: <?php echo esc_attr($center_pos); ?>;
                            <?php
                            if ($use_generator) {
                                echo ' transform: translateX(-50%) rotate(' . esc_attr($gen_rotation) . 'deg);';
                            } else {
                                echo ' transform: translateX(-50%);';
                            }
                            ?>
                            <?php echo $custom_style; ?>
                            <?php echo $shadow_style; ?>">
                    <?php if ($use_generator) {
                        $svg = $this->get_generator_washi_tape_svg($settings['gen_washi_tape_id']);
                        if (!empty($svg)) {
                            echo $this->sanitize_svg_content($svg);
                        }
                    } ?>
                </div>
            <?php endif; ?>

            <div class="widget-washi-tape-content" <?php if (!empty($content_style))
                echo 'style="' . esc_attr($content_style) . '"'; ?>>
                <?php if (!empty($settings['title'])): ?>
                    <h3 class="widget-washi-tape-title"><?php echo esc_html($settings['title']); ?></h3>
                <?php endif; ?>
                <div class="widget-washi-tape-content-text">
                    <?php echo wp_kses_post($settings['content']); ?>
                </div>
            </div>
        </div>
        <?php
    }
}

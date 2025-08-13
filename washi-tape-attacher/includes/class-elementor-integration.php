<?php
namespace Washi_Tape_Attacher;

use Elementor\Controls_Manager;
use Elementor\Element_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Handles the integration with Elementor.
 */
class Elementor_Integration {

    private static $_instance = null;
    private static $washi_tapes_cache = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        // Register the controls for various element types
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_controls'], 10, 1);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_controls'], 10, 1);
        add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'register_controls'], 10, 1);
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_controls'], 10, 1);

        // Render the tape on the frontend
        add_action('elementor/frontend/before_render', [$this, 'before_render'], 10, 1);
        add_action('elementor/frontend/after_render', [$this, 'after_render'], 10, 1);

        // Enqueue assets
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_frontend_styles']);
    }

    /**
     * Fetches all washi tapes from the database and caches the result.
     */
    private function get_washi_tapes() {
        if (self::$washi_tapes_cache === null) {
            self::$washi_tapes_cache = [];
            if (class_exists('Washi_Tape_DB')) {
                $db = new \Washi_Tape_DB();
                $tapes = $db->get_all_washi_tapes();
                if (!empty($tapes)) {
                    foreach ($tapes as $tape) {
                        self::$washi_tapes_cache[$tape->id] = esc_html($tape->title);
                    }
                }
            }
        }
        return ['0' => __('— Select Tape —', 'washi-tape-attacher')] + self::$washi_tapes_cache;
    }

    /**
     * Register the controls in the Elementor panel.
     */
    public function register_controls(Element_Base $element) {
        $element->start_controls_section(
            'section_washi_tape_attacher',
            [
                'label' => __('Washi Tape', 'washi-tape-attacher'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );

        $element->add_control(
            'wta_enable',
            [
                'label' => __('Enable Washi Tape', 'washi-tape-attacher'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'render_type' => 'template', // This is crucial for live preview
            ]
        );

        $element->add_control(
            'wta_tape_id',
            [
                'label' => __('Select Washi Tape', 'washi-tape-attacher'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_washi_tapes(),
                'default' => '0',
                'condition' => ['wta_enable' => 'yes'],
                'render_type' => 'template',
            ]
        );

        $element->add_control(
            'wta_position',
            [
                'label' => __('Tape Position', 'washi-tape-attacher'),
                'type' => Controls_Manager::SELECT,
                'default' => 'top-center',
                'options' => [
                    'top-center' => __('Top Center', 'washi-tape-attacher'),
                    'top-left' => __('Top Left', 'washi-tape-attacher'),
                    'top-right' => __('Top Right', 'washi-tape-attacher'),
                ],
                'condition' => ['wta_enable' => 'yes', 'wta_tape_id!' => '0'],
                'render_type' => 'template',
            ]
        );

        $element->add_responsive_control(
            'wta_width',
            [
                'label' => __('Tape Width', 'washi-tape-attacher'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => ['px' => ['min' => 20, 'max' => 500]],
                'default' => ['unit' => 'px', 'size' => 120],
                'condition' => ['wta_enable' => 'yes', 'wta_tape_id!' => '0'],
                'render_type' => 'template',
                 'selectors' => [
                    '{{WRAPPER}} .wta-tape-instance' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $element->add_responsive_control(
            'wta_height',
            [
                'label' => __('Tape Height', 'washi-tape-attacher'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 10, 'max' => 200]],
                'default' => ['unit' => 'px', 'size' => 45],
                'condition' => ['wta_enable' => 'yes', 'wta_tape_id!' => '0'],
                'render_type' => 'template',
                 'selectors' => [
                    '{{WRAPPER}} .wta-tape-instance' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $element->add_responsive_control(
            'wta_horizontal_offset',
            [
                'label' => __('Horizontal Offset', 'washi-tape-attacher'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => ['px' => ['min' => -200, 'max' => 200], '%' => ['min' => -100, 'max' => 100]],
                'default' => ['unit' => 'px', 'size' => 0],
                'condition' => ['wta_enable' => 'yes', 'wta_tape_id!' => '0'],
                'render_type' => 'template',
                 'selectors' => [
                    '{{WRAPPER}} .wta-tape-instance' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $element->add_responsive_control(
            'wta_vertical_offset',
            [
                'label' => __('Vertical Offset', 'washi-tape-attacher'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => ['px' => ['min' => -200, 'max' => 200], '%' => ['min' => -100, 'max' => 100]],
                'default' => ['unit' => 'px', 'size' => -20],
                'condition' => ['wta_enable' => 'yes', 'wta_tape_id!' => '0'],
                'render_type' => 'template',
                 'selectors' => [
                    '{{WRAPPER}} .wta-tape-instance' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $element->add_control(
            'wta_rotation',
            [
                'label' => __('Tape Rotation', 'washi-tape-attacher'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => ['deg' => ['min' => -180, 'max' => 180]],
                'default' => ['unit' => 'deg', 'size' => -5],
                'condition' => ['wta_enable' => 'yes', 'wta_tape_id!' => '0'],
                'render_type' => 'template',
                 'selectors' => [
                    '{{WRAPPER}} .wta-tape-instance' => 'transform: {{wta-position.value === \'top-center\' ? \'translateX(-50%)\' : \'\'}} rotate({{SIZE}}deg);',
                ],
            ]
        );

        $element->add_control(
            'wta_z_index',
            [
                'label' => __('Z-Index', 'washi-tape-attacher'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'condition' => ['wta_enable' => 'yes', 'wta_tape_id!' => '0'],
                'render_type' => 'template',
                 'selectors' => [
                    '{{WRAPPER}} .wta-tape-instance' => 'z-index: {{VALUE}};',
                ],
            ]
        );

        $element->end_controls_section();
    }

    /**
     * Before the element renders, add a class to its wrapper.
     */
    public function before_render(Element_Base $element) {
        $settings = $element->get_settings_for_display();
        if (isset($settings['wta_enable']) && $settings['wta_enable'] === 'yes' && !empty($settings['wta_tape_id']) && $settings['wta_tape_id'] !== '0') {
            $element->add_render_attribute('_wrapper', 'class', 'wta-element-wrapper');
        }
    }

    /**
     * After the element renders, output the washi tape HTML.
     */
    public function after_render(Element_Base $element) {
        $settings = $element->get_settings_for_display();
        if (isset($settings['wta_enable']) && $settings['wta_enable'] === 'yes' && !empty($settings['wta_tape_id']) && $settings['wta_tape_id'] !== '0') {
            
            $svg_content = '';
            if (class_exists('Washi_Tape_DB')) {
                $db = new \Washi_Tape_DB();
                $tape = $db->get_washi_tape($settings['wta_tape_id']);
                if ($tape && !empty($tape->svg)) {
                    $svg_content = stripslashes($tape->svg);
                }
            }

            if (empty($svg_content)) {
                return;
            }
            
            $position_class = 'wta-pos-' . esc_attr($settings['wta_position']);

            // Output the tape div. The CSS selectors will handle positioning.
            printf(
                '<div class="wta-tape-instance %s">%s</div>',
                $position_class,
                $svg_content
            );
        }
    }
    
    /**
     * Enqueue frontend styles.
     */
    public function enqueue_frontend_styles() {
        wp_enqueue_style(
            'washi-tape-attacher-frontend',
            WASHI_TAPE_ATTACHER_URL . 'assets/css/frontend.css',
            [],
            WASHI_TAPE_ATTACHER_VERSION
        );
    }
}

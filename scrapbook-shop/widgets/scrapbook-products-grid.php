<?php

/**
 * Scrapbook Products Archive Grid Widget
 * 
 * Creates the full archive layout with sorting and grid
 */

namespace Scrapbook_Shop\Widgets;

if (!defined('ABSPATH')) {
    exit;
}

// Bail out if Elementor base class is not loaded yet
if (!class_exists('Elementor\\Widget_Base')) {
    return;
}

class Scrapbook_Products_Grid extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'scrapbook_products_grid';
    }

    public function get_title()
    {
        return esc_html__('Scrapbook Products Grid', 'scrapbook-shop');
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['woocommerce-elements'];
    }

    protected function register_controls()
    {

        // Query Section
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'query_type',
            [
                'label' => esc_html__('Query Type', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => esc_html__('All Products', 'scrapbook-shop'),
                    'featured' => esc_html__('Featured Products', 'scrapbook-shop'),
                    'sale' => esc_html__('On Sale', 'scrapbook-shop'),
                    'best_selling' => esc_html__('Best Selling', 'scrapbook-shop'),
                    'top_rated' => esc_html__('Top Rated', 'scrapbook-shop'),
                    'category' => esc_html__('By Category', 'scrapbook-shop'),
                    'manual' => esc_html__('Manual Selection', 'scrapbook-shop'),
                ],
            ]
        );

        $this->add_control(
            'product_categories',
            [
                'label' => esc_html__('Categories', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_product_categories(),
                'condition' => [
                    'query_type' => 'category',
                ],
            ]
        );

        $this->add_control(
            'products_per_page',
            [
                'label' => esc_html__('Products Per Page', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'scrapbook-shop'),
                    'title' => esc_html__('Title', 'scrapbook-shop'),
                    'price' => esc_html__('Price', 'scrapbook-shop'),
                    'popularity' => esc_html__('Popularity', 'scrapbook-shop'),
                    'rating' => esc_html__('Rating', 'scrapbook-shop'),
                    'rand' => esc_html__('Random', 'scrapbook-shop'),
                    'menu_order' => esc_html__('Menu Order', 'scrapbook-shop'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => esc_html__('Ascending', 'scrapbook-shop'),
                    'DESC' => esc_html__('Descending', 'scrapbook-shop'),
                ],
            ]
        );

        $this->end_controls_section();

        // Layout Section
        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('Layout', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'show_page_title',
            [
                'label' => esc_html__('Show Page Title', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'page_title_text',
            [
                'label' => esc_html__('Custom Title', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Shop', 'scrapbook-shop'),
                'condition' => [
                    'show_page_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_sorting',
            [
                'label' => esc_html__('Show Sorting Dropdown', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_result_count',
            [
                'label' => esc_html__('Show Result Count', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-products-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_gap',
            [
                'label' => esc_html__('Grid Gap', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 25,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-products-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Row Gap', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 160,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-products-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__('Show Pagination', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Washi Tape Settings
        $this->start_controls_section(
            'section_washi_tape_settings',
            [
                'label' => esc_html__('Washi Tape Settings', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'enable_washi_tape',
            [
                'label' => esc_html__('Enable Washi Tape', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'washi_tape_mode',
            [
                'label' => esc_html__('Washi Tape Mode', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'random',
                'options' => [
                    'random' => esc_html__('Random from Plugin', 'scrapbook-shop'),
                    'specific' => esc_html__('Specific Tape', 'scrapbook-shop'),
                    'rotating' => esc_html__('Rotating Selection', 'scrapbook-shop'),
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        if (class_exists('Washi_Tape_DB')) {
            $this->add_control(
                'specific_washi_tape',
                [
                    'label' => esc_html__('Select Washi Tape', 'scrapbook-shop'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $this->get_washi_tapes_list(),
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'washi_tape_mode' => 'specific',
                    ],
                ]
            );

            $this->add_control(
                'rotating_washi_tapes',
                [
                    'label' => esc_html__('Select Tapes to Rotate', 'scrapbook-shop'),
                    'type' => \Elementor\Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => $this->get_washi_tapes_list(),
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'washi_tape_mode' => 'rotating',
                    ],
                ]
            );
        }

        // Tape size & position controls
        $this->add_control(
            'tape_width',
            [
                'label' => esc_html__('Tape Width', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 60,
                        'max' => 240,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 120,
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tape_height',
            [
                'label' => esc_html__('Tape Height', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 45,
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tape_vertical_offset',
            [
                'label' => esc_html__('Tape Y Position (relative to card)', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -80,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -20,
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tape_clearance',
            [
                'label' => esc_html__('Top Clearance (padding at top of card)', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 120,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card' => 'padding-top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tape_horizontal_offset',
            [
                'label' => esc_html__('Horizontal Offset', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -120,
                        'max' => 120,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        // Add vertical slider immediately after horizontal
        $this->add_control(
            'tape_vertical_offset_ui',
            [
                'label' => esc_html__('Vertical Offset (Y)', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -120,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -20,
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'randomize_tape_rotation',
            [
                'label' => esc_html__('Randomize Tape Angles', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tape_rotation_fixed',
            [
                'label' => esc_html__('Tape Rotation', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => -45,
                        'max' => 45,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => -10,
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                    'randomize_tape_rotation!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tape_rotation_range',
            [
                'label' => esc_html__('Rotation Range', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => 10,
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                    'randomize_tape_rotation' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Archive Header
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Style', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'page_title_typography',
                'label' => esc_html__('Page Title Typography', 'scrapbook-shop'),
                'selector' => '{{WRAPPER}} .scrapbook-archive-title',
                'condition' => [
                    'show_page_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'page_title_color',
            [
                'label' => esc_html__('Page Title Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2C2C2C',
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-archive-title' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_page_title' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Product Card
        $this->start_controls_section(
            'section_style_card',
            [
                'label' => esc_html__('Product Card', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background_color',
            [
                'label' => esc_html__('Card Background', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FAF2F1',
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_color',
            [
                'label' => esc_html__('Card Border Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#EFE5DA',
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => esc_html__('Card Border Radius', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .scrapbook-product-card',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'card_title_typography',
                'label' => esc_html__('Title Typography', 'scrapbook-shop'),
                'selector' => '{{WRAPPER}} .scrapbook-product-card .product-title',
            ]
        );

        $this->add_control(
            'card_title_color',
            [
                'label' => esc_html__('Title Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card .product-title, {{WRAPPER}} .scrapbook-product-card .product-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'card_price_typography',
                'label' => esc_html__('Price Typography', 'scrapbook-shop'),
                'selector' => '{{WRAPPER}} .scrapbook-product-card .price',
            ]
        );

        $this->add_control(
            'card_price_color',
            [
                'label' => esc_html__('Price Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card .price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Add to Cart Button
        $this->start_controls_section(
            'section_style_button',
            [
                'label' => esc_html__('Add To Cart Button', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .scrapbook-product-card .product-actions .button, {{WRAPPER}} .scrapbook-product-card .product-actions .add_to_cart_button',
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Text Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card .product-actions .button, {{WRAPPER}} .scrapbook-product-card .product-actions .add_to_cart_button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => esc_html__('Background Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card .product-actions .button, {{WRAPPER}} .scrapbook-product-card .product-actions .add_to_cart_button' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color_hover',
            [
                'label' => esc_html__('Background Hover', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card .product-actions .button:hover, {{WRAPPER}} .scrapbook-product-card .product-actions .add_to_cart_button:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label' => esc_html__('Text Hover', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card .product-actions .button:hover, {{WRAPPER}} .scrapbook-product-card .product-actions .add_to_cart_button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card .product-actions .button, {{WRAPPER}} .scrapbook-product-card .product-actions .add_to_cart_button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card .product-actions .button, {{WRAPPER}} .scrapbook-product-card .product-actions .add_to_cart_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Determine dynamic title on archive pages when default title is used
        $page_title_text = isset($settings['page_title_text']) ? $settings['page_title_text'] : '';
        $is_cat_archive = (is_tax('product_cat') || (function_exists('is_product_category') && is_product_category()));
        if ($settings['show_page_title'] === 'yes' && $is_cat_archive) {
            // Only override when user hasn't customized the title (i.e., it is still the default "Shop")
            $default_title = esc_html__('Shop', 'scrapbook-shop');
            if ($page_title_text === 'Shop' || $page_title_text === $default_title || $page_title_text === '') {
                $page_title_text = single_term_title('', false);
            }
        }

        // Setup query
        $query_args = $this->get_query_args($settings);
        $products = new \WP_Query($query_args);

?>
        <div class="scrapbook-products-archive">
            <?php if ($settings['show_page_title'] === 'yes' || $settings['show_sorting'] === 'yes'): ?>
                <div class="scrapbook-archive-header">
                    <?php if ($settings['show_page_title'] === 'yes'): ?>
                        <h1 class="scrapbook-archive-title">
                            <?php echo esc_html($page_title_text); ?>
                        </h1>
                    <?php endif; ?>

                    <?php if ($settings['show_sorting'] === 'yes'): ?>
                        <div class="scrapbook-sorting">
                            <?php $this->render_sorting_dropdown(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_result_count'] === 'yes'): ?>
                <div class="scrapbook-result-count">
                    <?php
                    $total = $products->found_posts;
                    $per_page = $settings['products_per_page'];
                    $current = max(1, get_query_var('paged'));
                    $showing_start = ($current - 1) * $per_page + 1;
                    $showing_end = min($current * $per_page, $total);

                    printf(
                        esc_html__('Showing %1$d–%2$d of %3$d results', 'scrapbook-shop'),
                        $showing_start,
                        $showing_end,
                        $total
                    );
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($products->have_posts()): ?>
                <div class="scrapbook-products-grid">
                    <?php
                    $index = 0;
                    while ($products->have_posts()):
                        $products->the_post();
                        global $product;

                        // Get washi tape for this product
                        $washi_tape_html = $this->get_washi_tape_for_product($settings, $index);
                        if (!empty($washi_tape_html)) {
                            $washi_tape_html = $this->sanitize_washi_svg($washi_tape_html);
                        }
                        $rotation = $this->get_rotation_for_product($settings, $index);
                        if (empty($settings['randomize_tape_rotation']) || $settings['randomize_tape_rotation'] !== 'yes') {
                            $rotation = isset($settings['tape_rotation_fixed']['size']) ? (int) $settings['tape_rotation_fixed']['size'] : 0;
                        }

                        list($svg_w, $svg_h) = $this->get_svg_dimensions($washi_tape_html);
                        $tape_width = isset($settings['tape_width']['size']) ? (int) $settings['tape_width']['size'] : ($svg_w ?: 120);
                        $tape_height = isset($settings['tape_height']['size']) ? (int) $settings['tape_height']['size'] : ($svg_h ?: 45);
                        $tape_top = isset($settings['tape_vertical_offset_ui']['size']) ? (int) $settings['tape_vertical_offset_ui']['size'] : (isset($settings['tape_vertical_offset']['size']) ? (int) $settings['tape_vertical_offset']['size'] : -20);
                        $tape_left_offset = isset($settings['tape_horizontal_offset']['size']) ? (int) $settings['tape_horizontal_offset']['size'] : 0;

                    ?>
                        <div class="scrapbook-grid-item">
                            <?php
                            // Always use our inline product card to ensure washi tape renders consistently
                            $this->render_product_card($product, $washi_tape_html, $rotation, $tape_width, $tape_height, $tape_top, $tape_left_offset);
                            ?>
                        </div>
                    <?php
                        $index++;
                    endwhile;
                    ?>
                </div>

                <?php if ($settings['show_pagination'] === 'yes'): ?>
                    <div class="scrapbook-pagination">
                        <?php
                        echo paginate_links([
                            'total' => $products->max_num_pages,
                            'current' => max(1, get_query_var('paged')),
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                        ]);
                        ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="scrapbook-no-products">
                    <?php esc_html_e('No products found.', 'scrapbook-shop'); ?>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </div>
    <?php
    }

    /**
     * Render individual product card
     */
    private function render_product_card($product, $washi_tape_html, $rotation, $tape_width, $tape_height, $tape_top, $tape_left_offset)
    {
    ?>
        <div class="scrapbook-product-card">
            <?php if (!empty($washi_tape_html)): ?>
                <div class="washi-tape-decoration" style="left: calc(50% + <?php echo esc_attr($tape_left_offset); ?>px); transform: translateX(-50%) rotate(<?php echo esc_attr($rotation); ?>deg); width: <?php echo esc_attr($tape_width); ?>px; height: <?php echo esc_attr($tape_height); ?>px; top: <?php echo esc_attr($tape_top); ?>px;">
                    <?php echo $washi_tape_html; ?>
                </div>
            <?php endif; ?>
            <div class="product-card-inner">
                <div class="product-image-wrapper">

                    <?php if ($product->is_on_sale()): ?>
                        <span class="onsale"><?php esc_html_e('Sale!', 'scrapbook-shop'); ?></span>
                    <?php endif; ?>

                    <div class="polaroid-frame">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                            <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                        </a>
                    </div>
                </div>

                <div class="product-details">
                    <h3 class="product-title">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                            <?php echo esc_html($product->get_name()); ?>
                        </a>
                    </h3>

                    <?php woocommerce_template_loop_rating(); ?>

                    <div class="price"><?php echo $product->get_price_html(); ?></div>
                </div>

                <div class="product-actions">
                    <?php woocommerce_template_loop_add_to_cart(); ?>
                </div>
            </div>
        </div>
    <?php
    }

    /**
     * Get query arguments
     */
    private function get_query_args($settings)
    {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => $settings['products_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'paged' => max(1, get_query_var('paged')),
        ];

        // Auto-scope to current product category archive when applicable
        // Works on URLs like /product-category/{slug}/ and Elementor archive templates
        $is_category_archive = (is_tax('product_cat') || (function_exists('is_product_category') && is_product_category()));
        if ($is_category_archive) {
            $explicit_category_filter = (
                isset($settings['query_type']) && $settings['query_type'] === 'category' &&
                !empty($settings['product_categories'])
            );
            // If user didn't explicitly pick categories, scope to the current archive term
            if (!$explicit_category_filter) {
                $queried = get_queried_object();
                $current_term_id = $queried && isset($queried->term_id) ? (int) $queried->term_id : 0;
                if ($current_term_id > 0) {
                    $args['tax_query'][] = [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => [$current_term_id],
                        'include_children' => true,
                    ];
                }
            }
        }

        // Handle different query types
        switch ($settings['query_type']) {
            case 'featured':
                $args['tax_query'][] = [
                    'taxonomy' => 'product_visibility',
                    'field' => 'name',
                    'terms' => 'featured',
                ];
                break;

            case 'sale':
                $args['post__in'] = wc_get_product_ids_on_sale();
                break;

            case 'best_selling':
                $args['meta_key'] = 'total_sales';
                $args['orderby'] = 'meta_value_num';
                break;

            case 'top_rated':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                break;

            case 'category':
                if (!empty($settings['product_categories'])) {
                    $args['tax_query'][] = [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $settings['product_categories'],
                    ];
                }
                break;
        }

        return $args;
    }

    /**
     * Get washi tape HTML for a product
     */
    private function get_washi_tape_for_product($settings, $index)
    {
        if ($settings['enable_washi_tape'] !== 'yes' || !class_exists('Washi_Tape_DB')) {
            return '';
        }

        $db = new \Washi_Tape_DB();
        $tape = null;

        switch ($settings['washi_tape_mode']) {
            case 'specific':
                if (!empty($settings['specific_washi_tape'])) {
                    $tape = $db->get_washi_tape($settings['specific_washi_tape']);
                }
                break;

            case 'rotating':
                if (!empty($settings['rotating_washi_tapes'])) {
                    $tape_ids = $settings['rotating_washi_tapes'];
                    $tape_id = $tape_ids[$index % count($tape_ids)];
                    $tape = $db->get_washi_tape($tape_id);
                }
                break;

            case 'random':
            default:
                $tapes = $db->get_all_washi_tapes();
                if (!empty($tapes)) {
                    $tape = $tapes[array_rand($tapes)];
                }
                break;
        }

        return ($tape && !empty($tape->svg)) ? $tape->svg : '';
    }

    /**
     * Get rotation for a product
     */
    private function get_rotation_for_product($settings, $index)
    {
        if ($settings['randomize_tape_rotation'] !== 'yes') {
            return 0;
        }

        $range = $settings['tape_rotation_range']['size'];
        return rand(-$range, $range);
    }

    /**
     * Clean up SVG markup stored in DB to ensure it renders
     */
    private function sanitize_washi_svg($svg)
    {
        if (!is_string($svg) || $svg === '') {
            return '';
        }

        // Normalize quotes and whitespace and strip inline styles that can conflict
        $clean = str_replace(['\\&quot;', '&quot;', '\\"'], '"', $svg);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = str_replace(['" >', '"  >'], '" >', $clean);
        $clean = str_replace(['> <', '>  <'], '><', $clean);
        // Remove inline style attributes to avoid absolute positioning leaking from saved preview
        $clean = preg_replace('/\sstyle="[^"]*"/i', '', $clean);

        // Ensure SVG has xmlns attribute
        if (strpos($clean, '<svg') !== false && strpos($clean, 'xmlns="http://www.w3.org/2000/svg"') === false) {
            $clean = preg_replace('/<svg\b/', '<svg xmlns="http://www.w3.org/2000/svg"', $clean, 1);
        }

        return $clean;
    }

    /**
     * Extract width/height from SVG markup if present
     *
     * @return array [width:int|null, height:int|null]
     */
    private function get_svg_dimensions($svg)
    {
        $width = null;
        $height = null;
        if (!is_string($svg)) {
            return [null, null];
        }
        if (preg_match('/\bwidth="(\d+(?:\.\d+)?)"/i', $svg, $m)) {
            $width = (int) round((float) $m[1]);
        }
        if (preg_match('/\bheight="(\d+(?:\.\d+)?)"/i', $svg, $m)) {
            $height = (int) round((float) $m[1]);
        }
        return [$width, $height];
    }

    /**
     * Render sorting dropdown
     */
    private function render_sorting_dropdown()
    {
    ?>
        <form class="scrapbook-ordering" method="get">
            <select name="orderby" class="orderby">
                <option value="menu_order"><?php esc_html_e('Default sorting', 'scrapbook-shop'); ?></option>
                <option value="popularity"><?php esc_html_e('Sort by popularity', 'scrapbook-shop'); ?></option>
                <option value="rating"><?php esc_html_e('Sort by average rating', 'scrapbook-shop'); ?></option>
                <option value="date"><?php esc_html_e('Sort by latest', 'scrapbook-shop'); ?></option>
                <option value="price"><?php esc_html_e('Sort by price: low to high', 'scrapbook-shop'); ?></option>
                <option value="price-desc"><?php esc_html_e('Sort by price: high to low', 'scrapbook-shop'); ?></option>
            </select>
            <?php wc_query_string_form_fields(null, ['orderby', 'submit']); ?>
        </form>
<?php
    }

    /**
     * Get product categories for selector
     */
    private function get_product_categories()
    {
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ]);

        $options = [];
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }

        return $options;
    }

    /**
     * Get washi tapes list
     */
    private function get_washi_tapes_list()
    {
        $options = ['' => esc_html__('Select Tape', 'scrapbook-shop')];

        if (class_exists('Washi_Tape_DB')) {
            $db = new \Washi_Tape_DB();
            $tapes = $db->get_all_washi_tapes();

            if (!empty($tapes)) {
                foreach ($tapes as $tape) {
                    // Support both object and array shapes and different field names
                    $id = null;
                    $title = null;

                    if (is_array($tape)) {
                        $id = $tape['id'] ?? ($tape['ID'] ?? ($tape['tape_id'] ?? ($tape['term_id'] ?? null)));
                        $title = $tape['title'] ?? ($tape['name'] ?? ($tape['post_title'] ?? ($tape['label'] ?? null)));
                    } elseif (is_object($tape)) {
                        $id = $tape->id ?? ($tape->ID ?? ($tape->tape_id ?? ($tape->term_id ?? null)));
                        $title = $tape->title ?? ($tape->name ?? ($tape->post_title ?? ($tape->label ?? null)));
                    }

                    if ($id !== null && $title) {
                        $options[$id] = $title;
                    }
                }
            }
        }

        return $options;
    }
}

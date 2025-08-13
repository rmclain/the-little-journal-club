<?php

/**
 * Scrapbook Product Card Widget
 * 
 * Custom Elementor widget that creates a scrapbook-style product card
 * with integrated washi tape support
 */

namespace Scrapbook_Shop\Widgets;

if (!defined('ABSPATH')) {
    exit;
}

// Bail out if Elementor base class is not loaded yet
if (!class_exists('Elementor\\Widget_Base')) {
    return;
}

class Scrapbook_Product_Card extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'scrapbook_product_card';
    }

    public function get_title()
    {
        return esc_html__('Scrapbook Product Card', 'scrapbook-shop');
    }

    public function get_icon()
    {
        return 'eicon-product-images';
    }

    public function get_categories()
    {
        return ['woocommerce-elements'];
    }

    public function get_keywords()
    {
        return ['product', 'woocommerce', 'scrapbook', 'polaroid', 'washi tape'];
    }

    protected function register_controls()
    {

        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Content', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'product_id',
            [
                'label' => esc_html__('Product', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_products_list(),
                'label_block' => true,
                'description' => esc_html__('Leave empty to use in loop', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => esc_html__('Show Price', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => esc_html__('Show Rating', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_add_to_cart',
            [
                'label' => esc_html__('Show Add to Cart', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_sale_badge',
            [
                'label' => esc_html__('Show Sale Badge', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Washi Tape Section
        $this->start_controls_section(
            'section_washi_tape',
            [
                'label' => esc_html__('Washi Tape', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'use_washi_tape',
            [
                'label' => esc_html__('Use Washi Tape', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'washi_tape_source',
            [
                'label' => esc_html__('Washi Tape Source', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'plugin',
                'options' => [
                    'plugin' => esc_html__('Washi Tape Plugin', 'scrapbook-shop'),
                    'image' => esc_html__('Custom Image', 'scrapbook-shop'),
                    'random' => esc_html__('Random from Plugin', 'scrapbook-shop'),
                ],
                'condition' => [
                    'use_washi_tape' => 'yes',
                ],
            ]
        );

        // If Washi Tape plugin is active, show tape selector
        if (class_exists('Washi_Tape_DB')) {
            $this->add_control(
                'washi_tape_id',
                [
                    'label' => esc_html__('Select Washi Tape', 'scrapbook-shop'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $this->get_washi_tapes_list(),
                    'condition' => [
                        'use_washi_tape' => 'yes',
                        'washi_tape_source' => 'plugin',
                    ],
                ]
            );
        }

        $this->add_control(
            'washi_tape_image',
            [
                'label' => esc_html__('Washi Tape Image', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'condition' => [
                    'use_washi_tape' => 'yes',
                    'washi_tape_source' => 'image',
                ],
            ]
        );

        $this->add_control(
            'washi_tape_rotation',
            [
                'label' => esc_html__('Tape Rotation', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => -15,
                        'max' => 15,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => 0,
                ],
                'condition' => [
                    'use_washi_tape' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Card
        $this->start_controls_section(
            'section_style_card',
            [
                'label' => esc_html__('Card Style', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Background Color', 'scrapbook-shop'),
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
                'label' => esc_html__('Border Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#EFE5DA',
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_rotate',
            [
                'label' => esc_html__('Hover Rotation', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => -5,
                        'max' => 5,
                        'step' => 0.5,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => -1.5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card:hover' => 'transform: translateY(-5px) rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_shadow',
                'selector' => '{{WRAPPER}} .scrapbook-product-card',
            ]
        );

        $this->end_controls_section();

        // Style Section - Polaroid Frame
        $this->start_controls_section(
            'section_style_polaroid',
            [
                'label' => esc_html__('Polaroid Frame', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'polaroid_background',
            [
                'label' => esc_html__('Frame Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .polaroid-frame' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'polaroid_padding',
            [
                'label' => esc_html__('Frame Padding', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 25,
                    'left' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .polaroid-frame' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Typography
        $this->start_controls_section(
            'section_typography',
            [
                'label' => esc_html__('Typography', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title', 'scrapbook-shop'),
                'selector' => '{{WRAPPER}} .product-title',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => esc_html__('Price', 'scrapbook-shop'),
                'selector' => '{{WRAPPER}} .price',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Get product
        $product = $this->get_product($settings);

        if (!$product) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="elementor-alert elementor-alert-info">' .
                    esc_html__('Please select a product or use this widget in a loop.', 'scrapbook-shop') .
                    '</div>';
            }
            return;
        }

        // Get washi tape
        $washi_tape_html = $this->get_washi_tape_html($settings);

?>
        <div class="scrapbook-product-card">
            <div class="product-card-inner">
                <div class="product-image-wrapper">
                    <?php if ($washi_tape_html): ?>
                        <div class="washi-tape-decoration" style="transform: rotate(<?php echo esc_attr($settings['washi_tape_rotation']['size']); ?>deg);">
                            <?php echo $washi_tape_html; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($product->is_on_sale() && $settings['show_sale_badge'] === 'yes'): ?>
                        <span class="onsale"><?php esc_html_e('Sale!', 'scrapbook-shop'); ?></span>
                    <?php endif; ?>

                    <div class="polaroid-frame">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                            <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                        </a>
                    </div>
                </div>

                <div class="product-details">
                    <?php if ($settings['show_title'] === 'yes'): ?>
                        <h3 class="product-title">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                        </h3>
                    <?php endif; ?>

                    <?php if ($settings['show_rating'] === 'yes'): ?>
                        <?php woocommerce_template_loop_rating(); ?>
                    <?php endif; ?>

                    <?php if ($settings['show_price'] === 'yes'): ?>
                        <div class="price"><?php echo $product->get_price_html(); ?></div>
                    <?php endif; ?>
                </div>

                <?php if ($settings['show_add_to_cart'] === 'yes'): ?>
                    <div class="product-actions">
                        <?php woocommerce_template_loop_add_to_cart(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php
    }

    /**
     * Get product for the widget
     */
    private function get_product($settings)
    {
        global $product;

        // If specific product is selected
        if (!empty($settings['product_id'])) {
            return wc_get_product($settings['product_id']);
        }

        // If in loop, use current product
        if ($product instanceof \WC_Product) {
            return $product;
        }

        // Try to get from post
        $post_id = get_the_ID();
        if ($post_id && get_post_type($post_id) === 'product') {
            return wc_get_product($post_id);
        }

        return false;
    }

    /**
     * Get washi tape HTML
     */
    private function get_washi_tape_html($settings)
    {
        if ($settings['use_washi_tape'] !== 'yes') {
            return '';
        }

        $html = '';

        switch ($settings['washi_tape_source']) {
            case 'plugin':
                if (class_exists('Washi_Tape_DB') && !empty($settings['washi_tape_id'])) {
                    $db = new \Washi_Tape_DB();
                    $tape = $db->get_washi_tape($settings['washi_tape_id']);
                    if ($tape && !empty($tape->svg)) {
                        $html = $tape->svg;
                    }
                }
                break;

            case 'random':
                if (class_exists('Washi_Tape_DB')) {
                    $db = new \Washi_Tape_DB();
                    $tapes = $db->get_all_washi_tapes();
                    if (!empty($tapes)) {
                        $random_tape = $tapes[array_rand($tapes)];
                        $html = $random_tape->svg;
                    }
                }
                break;

            case 'image':
                if (!empty($settings['washi_tape_image']['url'])) {
                    $html = '<img src="' . esc_url($settings['washi_tape_image']['url']) . '" alt="Washi Tape">';
                }
                break;
        }

        return $html;
    }

    /**
     * Get list of products for selector
     */
    private function get_products_list()
    {
        $products = wc_get_products([
            'limit' => -1,
            'orderby' => 'name',
            'order' => 'ASC',
            'return' => 'ids',
        ]);

        $options = ['' => esc_html__('None (Use in Loop)', 'scrapbook-shop')];

        foreach ($products as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                $options[$product_id] = $product->get_name();
            }
        }

        return $options;
    }

    /**
     * Get list of washi tapes from plugin
     */
    private function get_washi_tapes_list()
    {
        $options = ['' => esc_html__('Select Tape', 'scrapbook-shop')];

        if (class_exists('Washi_Tape_DB')) {
            $db = new \Washi_Tape_DB();
            $tapes = $db->get_all_washi_tapes();

            if (!empty($tapes)) {
                foreach ($tapes as $tape) {
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

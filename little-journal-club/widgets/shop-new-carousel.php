<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Shop_New_Carousel_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'shop_new_carousel';
    }

    public function get_title()
    {
        return __('Shop New Carousel', 'little-journal-club');
    }

    public function get_icon()
    {
        return 'eicon-products';
    }

    public function get_categories()
    {
        return ['little-journal-club'];
    }

    public function get_script_depends()
    {
        return ['ljc-shop-new-carousel-script'];
    }

    public function get_style_depends()
    {
        return ['ljc-shop-new-carousel-style'];
    }

    protected function _register_controls()
    {
        // Create a Repeater for multiple products.
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'product_id',
            [
                'label'       => __('Select Product', 'little-journal-club'),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'options'     => $this->get_woocommerce_products(),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'svg_mask',
            [
                'label'       => __('SVG Mask', 'little-journal-club'),
                'type'        => \Elementor\Controls_Manager::MEDIA,
                'media_types' => ['svg'],
                'label_block' => true,
                'description' => __('Upload or select an SVG file to mask the product image.', 'little-journal-club'),
            ]
        );

        $this->start_controls_section(
            'section_products',
            [
                'label' => __('Products', 'little-journal-club'),
            ]
        );

        $this->add_control(
            'products_list',
            [
                'label'       => __('Products List', 'little-journal-club'),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [],
                'title_field' => '{{{ product_id }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'little-journal-club'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'extend_background',
            [
                'label' => __('Extend Background Full Width', 'little-journal-club'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'little-journal-club'),
                'label_off' => __('No', 'little-journal-club'),
                'return_value' => 'yes',
                'default' => 'no',
                'prefix_class' => 'extend-background-',
            ]
        );

        $this->add_control(
            'banner_background',
            [
                'label'     => __('Banner Background Color', 'little-journal-club'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .shop-new-carousel-container' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.extend-background-yes .shop-new-carousel-container' => 'width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw;',
                ],
            ]
        );

        $this->add_control(
            'scale_percentage',
            [
                'label'      => __('Scale Percentage', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range'      => [
                    '%' => [
                        'min' => 50,
                        'max' => 150,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
            ]
        );

        $this->add_control(
            'font_size',
            [
                'label'      => __('Font Size', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range'      => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                    'rem' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .shop-new-item__title' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'font_color',
            [
                'label'     => __('Font Color', 'little-journal-club'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .shop-new-item__title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'typography',
                'label'    => __('Typography', 'little-journal-club'),
                'selector' => '{{WRAPPER}} .shop-new-item__title',
            ]
        );

        $this->add_control(
            'enable_scrolling',
            [
                'label'        => __('Enable Scrolling', 'little-journal-club'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'little-journal-club'),
                'label_off'    => __('No', 'little-journal-club'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'element_padding',
            [
                'label'      => __('Element Padding', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .shop-new-item' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_width',
            [
                'label'      => __('Image Width', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
            ]
        );

        $this->add_control(
            'image_height',
            [
                'label'      => __('Image Height', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'auto',
                    'size' => '',
                ],
            ]
        );

        $this->add_control(
            'widget_height',
            [
                'label'      => __('Widget Height', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'auto',
                    'size' => '',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings      = $this->get_settings_for_display();
        $products_list = $settings['products_list'];

        if (empty($products_list)) {
            return; // No products to display
        }

        // Convert scale to a factor (e.g. 100% becomes 1, 80% becomes 0.8, etc.)
        $scale = ! empty($settings['scale_percentage']['size'])
            ? (float) $settings['scale_percentage']['size'] / 100
            : 1;

        $widget_height = ! empty($settings['widget_height']['size'])
            ? $settings['widget_height']['size'] . $settings['widget_height']['unit']
            : 'auto';

        $image_width = ! empty($settings['image_width']['size'])
            ? $settings['image_width']['size'] . $settings['image_width']['unit']
            : '100%';

        $image_height = ! empty($settings['image_height']['size'])
            ? $settings['image_height']['size'] . $settings['image_height']['unit']
            : 'auto';
?>
        <div class="shop-new-carousel-container" style="overflow: visible; padding: 20px; text-align: center; height: <?php echo esc_attr($widget_height); ?>;">
            <div class="shop-new-carousel swiper-container" <?php if ($settings['enable_scrolling'] !== 'yes') echo 'style="overflow: hidden;"'; ?>>
                <div class="swiper-wrapper">
                    <?php foreach ($products_list as $item) : ?>
                        <?php
                        $product_id = $item['product_id'] ?? '';
                        if (! $product_id) {
                            continue;
                        }
                        $product = wc_get_product($product_id);
                        if (! $product) {
                            continue;
                        }
                        $title      = $product->get_name();
                        $price_html = $product->get_price_html();
                        $img_id     = $product->get_image_id();
                        $img_url    = $img_id ? wp_get_attachment_url($img_id) : \Elementor\Utils::get_placeholder_image_src();
                        $mask_url   = ! empty($item['svg_mask']['url']) ? $item['svg_mask']['url'] : '';
                        $product_url = get_permalink($product_id);
                        ?>
                        <div class="swiper-slide shop-new-item" style="padding: <?php echo esc_attr($settings['element_padding']['size'] . $settings['element_padding']['unit']); ?>;">
                            <div class="shop-new-item__wrapper" style="transform: scale(<?php echo esc_attr($scale); ?>); transform-origin: center center;">
                                <a href="<?php echo esc_url($product_url); ?>" class="shop-new-item__image" style="width: <?php echo esc_attr($image_width); ?>; height: <?php echo esc_attr($image_height); ?>; <?php if ($mask_url) : ?>
                                    mask-image: url('<?php echo esc_url($mask_url); ?>');
                                    -webkit-mask-image: url('<?php echo esc_url($mask_url); ?>');
                                    mask-size: contain;
                                    -webkit-mask-size: contain;
                                    mask-repeat: no-repeat;
                                    -webkit-mask-repeat: no-repeat;
                                    mask-position: center;
                                    -webkit-mask-position: center;
                                <?php endif; ?>">
                                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>">
                                </a>
                                <div class="shop-new-item__info">
                                    <h3 class="shop-new-item__title"><?php echo esc_html($title); ?></h3>
                                    <span class="shop-new-item__price"><?php echo wp_kses_post($price_html); ?></span>
                                </div>
                            </div> <!-- .shop-new-item__wrapper -->
                        </div>
                    <?php endforeach; ?>
                </div> <!-- .swiper-wrapper -->

                <?php if ($settings['enable_scrolling'] === 'yes') : ?>
                    <!-- Optional Swiper navigation -->
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                <?php endif; ?>
            </div> <!-- .shop-new-carousel -->
        </div> <!-- .shop-new-carousel-container -->
<?php
    }

    private function get_woocommerce_products()
    {
        if (! class_exists('WooCommerce')) {
            return [];
        }

        $args     = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];
        $products = get_posts($args);
        $options  = [];

        foreach ($products as $prod) {
            $options[$prod->ID] = $prod->post_title;
        }

        return $options;
    }
}

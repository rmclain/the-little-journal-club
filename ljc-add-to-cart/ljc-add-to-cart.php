<?php

/**
 * Plugin Name: LJC Add to Cart (Elementor)
 * Description: Cute, configurable Add to Cart widget for Elementor, designed for The Little Journal Club. Supports WooCommerce simple & variable products, quantity, a cute variations dropdown, button size control, label toggle, and more.
 * Version: 2.0.0
 * Author: The Little Journal Club
 * Text Domain: ljc-add-to-cart
 */

if (! defined('ABSPATH')) {
	exit;
}

add_action('plugins_loaded', function () {
	if (! did_action('elementor/loaded') || ! class_exists('WooCommerce')) {
		add_action('admin_notices', function () {
			if (! did_action('elementor/loaded')) {
				echo '<div class="notice notice-error"><p><strong>LJC Add to Cart</strong> requires Elementor to be installed and active.</p></div>';
			}
			if (! class_exists('WooCommerce')) {
				echo '<div class="notice notice-error"><p><strong>LJC Add to Cart</strong> requires WooCommerce to be installed and active.</p></div>';
			}
		});
		return;
	}

	add_action('elementor/elements/categories_registered', function ($elements_manager) {
		$elements_manager->add_category('the-little-journal-club', ['title' => __('The Little Journal Club', 'ljc-add-to-cart'), 'icon'  => 'fa fa-heart']);
	});

	add_action('elementor/widgets/register', function ($widgets_manager) {
		class LJC_Add_To_Cart_Widget extends \Elementor\Widget_Base
		{
			public function get_name()
			{
				return 'ljc-add-to-cart';
			}
			public function get_title()
			{
				return __('LJC – Add to Cart', 'ljc-add-to-cart');
			}
			public function get_icon()
			{
				return 'eicon-cart';
			}
			public function get_categories()
			{
				return ['the-little-journal-club'];
			}

			protected function register_controls()
			{
				// CONTENT SECTION
				$this->start_controls_section('section_content', ['label' => __('Content', 'ljc-add-to-cart')]);
				$this->add_control('product_source', ['label' => __('Product Source', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['current' => __('Current Product', 'ljc-add-to-cart'), 'by_id' => __('Choose Product by ID', 'ljc-add-to-cart')], 'default' => 'current']);
				$this->add_control('product_id', ['label' => __('Product ID', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::NUMBER, 'condition' => ['product_source' => 'by_id']]);
				$this->add_control('show_price', ['label' => __('Show Price', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
				$this->add_control('show_quantity', ['label' => __('Quantity Selector', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
				$this->add_control('button_text', ['label' => __('Button Text', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('Add to Cart', 'ljc-add-to-cart')]);
				$this->add_control('cute_emojis', ['label' => __('Cute Accents', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '😊 ✨ 📒']);
				$this->add_control('hide_variation_labels', ['label' => __('Hide Variation Labels', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('center_content', ['label' => __('Center All Items', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('align_items', [
					'label' => __('Align Items', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'left' => ['title' => __('Left', 'ljc-add-to-cart'), 'icon' => 'eicon-text-align-left'],
						'center' => ['title' => __('Center', 'ljc-add-to-cart'), 'icon' => 'eicon-text-align-center'],
						'right' => ['title' => __('Right', 'ljc-add-to-cart'), 'icon' => 'eicon-text-align-right'],
					],
					'toggle' => true,
					'default' => 'left',
					'prefix_class' => 'ljc-align-'
				]);
				$this->add_responsive_control('stack_gap', [
					'label' => __('Stack Spacing', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 0, 'max' => 60]],
					'size_units' => ['px'],
					'selectors' => [
						'{{WRAPPER}} .ljc-atc' => 'gap: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ljc-atc form.cart' => 'gap: {{SIZE}}{{UNIT}};'
					]
				]);
				$this->end_controls_section();

				// PRODUCT INFO SECTION
				$this->start_controls_section('section_product_info', ['label' => __('Product Information', 'ljc-add-to-cart')]);
				$this->add_control('show_sku', ['label' => __('Show SKU', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('show_stock', ['label' => __('Show Stock Status', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('show_categories', ['label' => __('Show Categories', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('show_tags', ['label' => __('Show Tags', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('show_short_desc', ['label' => __('Show Short Description', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('show_product_image', ['label' => __('Show Product Image', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('image_size', [
					'label' => __('Image Size', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'thumbnail' => __('Thumbnail', 'ljc-add-to-cart'),
						'medium' => __('Medium', 'ljc-add-to-cart'),
						'large' => __('Large', 'ljc-add-to-cart'),
						'woocommerce_thumbnail' => __('WooCommerce Thumbnail', 'ljc-add-to-cart'),
					],
					'default' => 'woocommerce_thumbnail',
					'condition' => ['show_product_image' => 'yes']
				]);
				$this->add_control('show_sale_badge', ['label' => __('Show Sale Badge', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('sale_badge_text', [
					'label' => __('Sale Badge Text', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __('SALE!', 'ljc-add-to-cart'),
					'condition' => ['show_sale_badge' => 'yes']
				]);
				$this->end_controls_section();

				// CART BEHAVIOR SECTION
				$this->start_controls_section('section_cart_behavior', ['label' => __('Cart Behavior', 'ljc-add-to-cart')]);
				$this->add_control('ajax_cart', ['label' => __('Enable AJAX Add to Cart', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
				$this->add_control('redirect_after_add', [
					'label' => __('Redirect After Add', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'no' => __('Stay on Page', 'ljc-add-to-cart'),
						'cart' => __('Go to Cart', 'ljc-add-to-cart'),
						'checkout' => __('Go to Checkout', 'ljc-add-to-cart'),
						'custom' => __('Custom URL', 'ljc-add-to-cart'),
					],
					'default' => 'no'
				]);
				$this->add_control('redirect_custom_url', [
					'label' => __('Custom Redirect URL', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::URL,
					'condition' => ['redirect_after_add' => 'custom']
				]);
				$this->add_control('success_message', [
					'label' => __('Success Message', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __('Product added to cart!', 'ljc-add-to-cart')
				]);
				$this->add_control('show_success_icon', ['label' => __('Show Success Icon', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
				$this->add_control('default_quantity', [
					'label' => __('Default Quantity', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 1,
					'min' => 1
				]);
				$this->add_control('min_quantity', [
					'label' => __('Minimum Quantity', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 1,
					'min' => 1
				]);
				$this->add_control('max_quantity', [
					'label' => __('Maximum Quantity (0 = unlimited)', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 0,
					'min' => 0
				]);
				$this->add_control('quantity_step', [
					'label' => __('Quantity Step', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 1,
					'min' => 1
				]);
				$this->end_controls_section();

				// VARIATIONS SECTION
				$this->start_controls_section('section_variations', ['label' => __('Variations Options', 'ljc-add-to-cart')]);
				$this->add_control('variation_display', [
					'label' => __('Variation Display Type', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'dropdown' => __('Dropdown', 'ljc-add-to-cart'),
						'radio' => __('Radio Buttons', 'ljc-add-to-cart'),
						'buttons' => __('Button Swatches', 'ljc-add-to-cart'),
					],
					'default' => 'dropdown'
				]);
				$this->add_control('show_clear_link', ['label' => __('Show Clear Selection Link', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
				$this->add_control('clear_text', [
					'label' => __('Clear Selection Text', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __('Clear', 'ljc-add-to-cart'),
					'condition' => ['show_clear_link' => 'yes']
				]);
				$this->add_control('show_variation_price', ['label' => __('Show Variation Price', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
				$this->add_control('show_variation_description', ['label' => __('Show Variation Description', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('show_variation_image', ['label' => __('Update Product Image on Selection', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
				$this->end_controls_section();

				// BUTTON EXTRAS SECTION
				$this->start_controls_section('section_button_extras', ['label' => __('Button Extras', 'ljc-add-to-cart')]);
				$this->add_control('button_icon', [
					'label' => __('Button Icon', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::ICONS,
					'default' => []
				]);
				$this->add_control('icon_position', [
					'label' => __('Icon Position', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'before' => __('Before Text', 'ljc-add-to-cart'),
						'after' => __('After Text', 'ljc-add-to-cart'),
					],
					'default' => 'before'
				]);
				$this->add_control('loading_text', [
					'label' => __('Loading Text', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __('Adding...', 'ljc-add-to-cart')
				]);
				$this->add_control('show_loading_spinner', ['label' => __('Show Loading Spinner', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
				$this->add_control('button_style_preset', [
					'label' => __('Button Style Preset', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'default' => __('Default', 'ljc-add-to-cart'),
						'gradient' => __('Gradient', 'ljc-add-to-cart'),
						'outline' => __('Outline', 'ljc-add-to-cart'),
						'3d' => __('3D Effect', 'ljc-add-to-cart'),
						'glow' => __('Glow Effect', 'ljc-add-to-cart'),
					],
					'default' => 'default',
					'prefix_class' => 'ljc-btn-style-'
				]);
				$this->end_controls_section();

				// ANIMATIONS SECTION
				$this->start_controls_section('section_animations', ['label' => __('Animations', 'ljc-add-to-cart')]);
				$this->add_control('entrance_animation', [
					'label' => __('Entrance Animation', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'none' => __('None', 'ljc-add-to-cart'),
						'fadeIn' => __('Fade In', 'ljc-add-to-cart'),
						'slideInUp' => __('Slide Up', 'ljc-add-to-cart'),
						'slideInDown' => __('Slide Down', 'ljc-add-to-cart'),
						'bounceIn' => __('Bounce In', 'ljc-add-to-cart'),
						'zoomIn' => __('Zoom In', 'ljc-add-to-cart'),
					],
					'default' => 'none'
				]);
				$this->add_control('hover_animation', [
					'label' => __('Button Hover Animation', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'none' => __('None', 'ljc-add-to-cart'),
						'pulse' => __('Pulse', 'ljc-add-to-cart'),
						'bounce' => __('Bounce', 'ljc-add-to-cart'),
						'shake' => __('Shake', 'ljc-add-to-cart'),
						'grow' => __('Grow', 'ljc-add-to-cart'),
					],
					'default' => 'none',
					'prefix_class' => 'ljc-hover-'
				]);
				$this->add_control('success_animation', [
					'label' => __('Success Animation', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'none' => __('None', 'ljc-add-to-cart'),
						'checkmark' => __('Checkmark', 'ljc-add-to-cart'),
						'confetti' => __('Confetti', 'ljc-add-to-cart'),
						'tada' => __('Tada', 'ljc-add-to-cart'),
					],
					'default' => 'checkmark'
				]);
				$this->end_controls_section();

				// ADVANCED SECTION
				$this->start_controls_section('section_advanced', ['label' => __('Advanced', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_ADVANCED]);
				$this->add_control('custom_attributes', [
					'label' => __('Custom Button Attributes', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::TEXTAREA,
					'placeholder' => 'data-attribute="value"',
					'description' => __('Add custom attributes to the button. One per line.', 'ljc-add-to-cart')
				]);
				$this->add_control('enable_ga_tracking', ['label' => __('Enable GA/GTM Tracking', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
				$this->add_control('ga_event_name', [
					'label' => __('GA Event Name', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => 'add_to_cart',
					'condition' => ['enable_ga_tracking' => 'yes']
				]);
				$this->add_control('custom_css', [
					'label' => __('Custom CSS', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::CODE,
					'language' => 'css',
					'description' => __('Add custom CSS here. Use {{WRAPPER}} to target this widget.', 'ljc-add-to-cart')
				]);
				$this->end_controls_section();

				// STYLE: Price typography & color (KEEP EXISTING)
				$this->start_controls_section('section_style_price', ['label' => __('Price', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('price_color', [
					'label' => __('Text Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-price' => 'color: {{VALUE}};']
				]);
				$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
					'name' => 'price_typography',
					'selector' => '{{WRAPPER}} .ljc-atc .ljc-price'
				]);
				$this->end_controls_section();

				// STYLE: Variation labels typography & color (KEEP EXISTING)
				$this->start_controls_section('section_style_labels', ['label' => __('Variation Labels', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('labels_color', [
					'label' => __('Text Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc table.variations .label, {{WRAPPER}} .ljc-atc table.variations .label label' => 'color: {{VALUE}};']
				]);
				$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
					'name' => 'labels_typography',
					'selector' => '{{WRAPPER}} .ljc-atc table.variations .label, {{WRAPPER}} .ljc-atc table.variations .label label'
				]);
				$this->end_controls_section();

				// STYLE: Cart Box styles (KEEP EXISTING)
				$this->start_controls_section('section_style_box', ['label' => __('Cart Box', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('box_bg', [
					'label' => __('Background Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#FFFFFF',
					'selectors' => ['{{WRAPPER}} .ljc-atc' => 'background-color: {{VALUE}};']
				]);
				$this->add_control('grid_paper', [
					'label' => __('Grid-paper Background', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __('On', 'ljc-add-to-cart'),
					'label_off' => __('Off', 'ljc-add-to-cart'),
					'return_value' => 'yes',
					'default' => 'yes',
					'prefix_class' => 'ljc-grid-'
				]);
				$this->add_control('box_border_color', [
					'label' => __('Border Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc' => 'border-color: {{VALUE}}; border-style: solid;']
				]);
				$this->add_responsive_control('box_border_width', [
					'label' => __('Border Width', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 0, 'max' => 10]],
					'selectors' => ['{{WRAPPER}} .ljc-atc' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;']
				]);
				$this->add_responsive_control('box_radius', [
					'label' => __('Border Radius', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 0, 'max' => 60]],
					'selectors' => ['{{WRAPPER}} .ljc-atc' => 'border-radius: {{SIZE}}{{UNIT}};']
				]);
				$this->add_responsive_control('box_padding', [
					'label' => __('Padding', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors' => ['{{WRAPPER}} .ljc-atc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
				]);
				$this->end_controls_section();

				// STYLE: Button (KEEP ALL EXISTING)
				$this->start_controls_section('section_style_button', ['label' => __('Button', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_responsive_control('btn_size', [
					'label' => __('Button Size', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 10, 'max' => 60]],
					'size_units' => ['px', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					]
				]);
				$this->add_control('btn_bg', ['label' => __('Background', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#F2B6A0', 'selectors' => ['{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt' => 'background-color: {{VALUE}};']]);
				$this->add_control('btn_text_color', [
					'label' => __('Text Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt' => 'color: {{VALUE}} !important;']
				]);
				$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
					'name' => 'btn_typography',
					'selector' => '{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt'
				]);
				$this->add_control('btn_fullwidth', [
					'label' => __('Full-width Button', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default' => '',
					'prefix_class' => 'ljc-btn-full-'
				]);
				$this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
					'name' => 'btn_border',
					'selector' => '{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt'
				]);
				$this->add_control('btn_border_style_force', [
					'label' => __('Border Type (force)', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'' => __('Default', 'ljc-add-to-cart'),
						'solid' => __('Solid', 'ljc-add-to-cart'),
						'dashed' => __('Dashed', 'ljc-add-to-cart'),
						'dotted' => __('Dotted', 'ljc-add-to-cart'),
						'none' => __('None', 'ljc-add-to-cart'),
					],
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt' => 'border-style: {{VALUE}} !important;'
					]
				]);
				$this->add_responsive_control('btn_border_width_force', [
					'label' => __('Border Width (force)', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'range' => ['px' => ['min' => 0, 'max' => 8]],
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt' => 'border-width: {{SIZE}}{{UNIT}} !important;'
					]
				]);
				$this->add_control('btn_border_color_force', [
					'label' => __('Border Color (force)', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt' => 'border-color: {{VALUE}} !important;'
					]
				]);
				$this->add_responsive_control('btn_radius', [
					'label' => __('Border Radius', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 0, 'max' => 60]],
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt' => 'border-radius: {{SIZE}}{{UNIT}} !important;'
					]
				]);
				$this->add_responsive_control('btn_padding', [
					'label' => __('Padding', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;'
					]
				]);
				$this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
					'name' => 'btn_shadow',
					'selector' => '{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt'
				]);
				$this->add_control('btn_bg_hover', [
					'label' => __('Background (Hover)', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt:hover' => 'background-color: {{VALUE}} !important;'
					]
				]);
				$this->add_control('btn_text_hover', [
					'label' => __('Text Color (Hover)', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt:hover' => 'color: {{VALUE}} !important;'
					]
				]);
				$this->add_control('btn_border_hover', [
					'label' => __('Border Color (Hover)', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .single_add_to_cart_button.button.alt:hover' => 'border-color: {{VALUE}} !important;'
					]
				]);
				$this->end_controls_section();

				// STYLE: Select Fields (KEEP EXISTING)
				$this->start_controls_section('section_style_fields', ['label' => __('Select Fields', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('field_bg', ['label' => __('Field Background', 'ljc-add-to-cart'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFF8F4', 'selectors' => ['{{WRAPPER}} .ljc-atc select, {{WRAPPER}} .ljc-atc input[type=number]' => 'background-color: {{VALUE}} !important;']]);
				$this->add_control('select_text_color', [
					'label' => __('Text Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc select' => 'color: {{VALUE}} !important;']
				]);
				$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
					'name' => 'select_typography',
					'selector' => '{{WRAPPER}} .ljc-atc select'
				]);
				$this->add_control('field_border_color', [
					'label' => __('Field Border Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc select, {{WRAPPER}} .ljc-atc .quantity .qty' => 'border-color: {{VALUE}} !important;']
				]);
				$this->add_responsive_control('field_radius', [
					'label' => __('Field Border Radius', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 0, 'max' => 30]],
					'selectors' => ['{{WRAPPER}} .ljc-atc select, {{WRAPPER}} .ljc-atc .quantity .qty' => 'border-radius: {{SIZE}}{{UNIT}};']
				]);
				$this->add_control('full_width_dropdowns', [
					'label' => __('Full-width Dropdowns', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default' => '',
					'prefix_class' => 'ljc-ffd-'
				]);
				$this->end_controls_section();

				// STYLE: Quantity (KEEP EXISTING)
				$this->start_controls_section('section_style_quantity', ['label' => __('Quantity', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('qty_text_color', [
					'label' => __('Text Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc .quantity .qty' => 'color: {{VALUE}} !important;']
				]);
				$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
					'name' => 'qty_typography',
					'selector' => '{{WRAPPER}} .ljc-atc .quantity .qty'
				]);
				$this->end_controls_section();

				// STYLE: Accents (KEEP EXISTING)
				$this->start_controls_section('section_style_accents', ['label' => __('Accents', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('accents_color', [
					'label' => __('Text Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-accents' => 'color: {{VALUE}};']
				]);
				$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
					'name' => 'accents_typography',
					'selector' => '{{WRAPPER}} .ljc-atc .ljc-accents'
				]);
				$this->end_controls_section();

				// NEW STYLE SECTIONS
				// STYLE: Product Info
				$this->start_controls_section('section_style_info', ['label' => __('Product Info', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('info_color', [
					'label' => __('Text Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-product-info' => 'color: {{VALUE}};']
				]);
				$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
					'name' => 'info_typography',
					'selector' => '{{WRAPPER}} .ljc-atc .ljc-product-info'
				]);
				$this->add_responsive_control('info_spacing', [
					'label' => __('Spacing', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 0, 'max' => 30]],
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-product-info' => 'margin-bottom: {{SIZE}}{{UNIT}};']
				]);
				$this->end_controls_section();

				// STYLE: Sale Badge
				$this->start_controls_section('section_style_badge', ['label' => __('Sale Badge', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('badge_bg', [
					'label' => __('Background', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#FF6B6B',
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-sale-badge' => 'background-color: {{VALUE}};']
				]);
				$this->add_control('badge_color', [
					'label' => __('Text Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#FFFFFF',
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-sale-badge' => 'color: {{VALUE}};']
				]);
				$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
					'name' => 'badge_typography',
					'selector' => '{{WRAPPER}} .ljc-atc .ljc-sale-badge'
				]);
				$this->add_responsive_control('badge_padding', [
					'label' => __('Padding', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => ['px'],
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-sale-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
				]);
				$this->add_responsive_control('badge_radius', [
					'label' => __('Border Radius', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 0, 'max' => 30]],
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-sale-badge' => 'border-radius: {{SIZE}}{{UNIT}};']
				]);
				$this->end_controls_section();

				// STYLE: Variation Swatches
				$this->start_controls_section('section_style_swatches', ['label' => __('Variation Swatches', 'ljc-add-to-cart'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
				$this->add_control('swatch_bg', [
					'label' => __('Background', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-swatch' => 'background-color: {{VALUE}};']
				]);
				$this->add_control('swatch_border', [
					'label' => __('Border Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-swatch' => 'border-color: {{VALUE}};']
				]);
				$this->add_control('swatch_active_border', [
					'label' => __('Active Border Color', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .ljc-atc .ljc-swatch.active' => 'border-color: {{VALUE}};']
				]);
				$this->add_responsive_control('swatch_size', [
					'label' => __('Size', 'ljc-add-to-cart'),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => ['px' => ['min' => 20, 'max' => 80]],
					'selectors' => [
						'{{WRAPPER}} .ljc-atc .ljc-swatch' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'
					]
				]);
				$this->end_controls_section();
			}

			private function get_product_from_settings()
			{
				$settings = $this->get_settings_for_display();
				if ('by_id' === $settings['product_source']) {
					return wc_get_product(absint($settings['product_id']));
				}
				global $product;
				return $product ?: null;
			}

			public function render()
			{
				wp_enqueue_script('wc-add-to-cart-variation');
				$settings = $this->get_settings_for_display();
				$product  = $this->get_product_from_settings();
				
				if (! $product) {
					echo '<div class="ljc-atc">' . __('No product found.', 'ljc-add-to-cart') . '</div>';
					return;
				}
				
				// Custom CSS
				if (!empty($settings['custom_css'])) {
					echo '<style>' . str_replace('{{WRAPPER}}', '.elementor-element-' . $this->get_id(), $settings['custom_css']) . '</style>';
				}
				
				// Entrance animation
				$wrapper_attrs = '';
				if (!empty($settings['entrance_animation']) && $settings['entrance_animation'] !== 'none') {
					$wrapper_attrs = ' data-animation="' . esc_attr($settings['entrance_animation']) . '"';
				}
				
				// Cute emojis
				if ($settings['cute_emojis']) echo '<div class="ljc-accents">' . esc_html($settings['cute_emojis']) . '</div>';
				
				echo '<div class="ljc-atc' . (! empty($settings['center_content']) ? ' ljc-legacy-center' : '') . '"' . $wrapper_attrs . '>';
				
				// Product image
				if ('yes' === $settings['show_product_image'] && has_post_thumbnail($product->get_id())) {
					echo '<div class="ljc-product-image">';
					if ($product->is_on_sale() && 'yes' === $settings['show_sale_badge']) {
						echo '<span class="ljc-sale-badge">' . esc_html($settings['sale_badge_text']) . '</span>';
					}
					echo get_the_post_thumbnail($product->get_id(), $settings['image_size']);
					echo '</div>';
				}
				
				// Product info
				$show_info = ('yes' === $settings['show_sku'] || 'yes' === $settings['show_stock'] || 
							 'yes' === $settings['show_categories'] || 'yes' === $settings['show_tags'] || 
							 'yes' === $settings['show_short_desc']);
				
				if ($show_info) {
					echo '<div class="ljc-product-info">';
					
					if ('yes' === $settings['show_sku'] && $product->get_sku()) {
						echo '<div class="ljc-sku">' . __('SKU: ', 'ljc-add-to-cart') . esc_html($product->get_sku()) . '</div>';
					}
					
					if ('yes' === $settings['show_stock']) {
						$availability = $product->get_availability();
						echo '<div class="ljc-stock ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</div>';
					}
					
					if ('yes' === $settings['show_categories']) {
						$categories = wc_get_product_category_list($product->get_id());
						if ($categories) {
							echo '<div class="ljc-categories">' . $categories . '</div>';
						}
					}
					
					if ('yes' === $settings['show_tags']) {
						$tags = wc_get_product_tag_list($product->get_id());
						if ($tags) {
							echo '<div class="ljc-tags">' . $tags . '</div>';
						}
					}
					
					if ('yes' === $settings['show_short_desc'] && $product->get_short_description()) {
						echo '<div class="ljc-short-desc">' . wp_kses_post($product->get_short_description()) . '</div>';
					}
					
					echo '</div>';
				}
				
				// Price
				if ('yes' === $settings['show_price']) {
					echo '<div class="ljc-price">' . $product->get_price_html() . '</div>';
				}
				
				// Build custom attributes
				$custom_attrs = '';
				if (!empty($settings['custom_attributes'])) {
					$attrs = explode("\n", $settings['custom_attributes']);
					foreach ($attrs as $attr) {
						$custom_attrs .= ' ' . esc_attr(trim($attr));
					}
				}
				
				// GA tracking
				if ('yes' === $settings['enable_ga_tracking']) {
					$custom_attrs .= ' data-ga-event="' . esc_attr($settings['ga_event_name']) . '"';
					$custom_attrs .= ' data-product-id="' . esc_attr($product->get_id()) . '"';
					$custom_attrs .= ' data-product-name="' . esc_attr($product->get_name()) . '"';
					$custom_attrs .= ' data-product-price="' . esc_attr($product->get_price()) . '"';
				}
				
				// Render form based on product type
				if ($product->is_type('simple')) {
					$this->render_simple_form($product, $settings, $custom_attrs);
				} elseif ($product->is_type('variable')) {
					$this->render_variable_form($product, $settings, $custom_attrs);
				}
				
				// Success message container
				echo '<div class="ljc-success-message" style="display:none;">';
				if ('yes' === $settings['show_success_icon']) {
					echo '<span class="ljc-success-icon">✓</span>';
				}
				echo '<span class="ljc-success-text">' . esc_html($settings['success_message']) . '</span>';
				echo '</div>';
				
				echo '</div>';
				
				// Add JavaScript for enhanced functionality
				$this->render_scripts($settings);
			}
			
			private function render_simple_form($product, $settings, $custom_attrs) {
				$ajax_class = ('yes' === $settings['ajax_cart']) ? ' ajax_add_to_cart' : '';
				
				echo '<form class="cart" method="post">';
				echo '<div class="ljc-actions">';
				
				if ('yes' === $settings['show_quantity']) {
					echo '<div class="ljc-qty-wrap">';
					woocommerce_quantity_input([
						'min_value' => $settings['min_quantity'],
						'max_value' => $settings['max_quantity'] > 0 ? $settings['max_quantity'] : '',
						'input_value' => $settings['default_quantity'],
						'step' => $settings['quantity_step']
					]);
					echo '</div>';
				}
				
				$button_text = $settings['button_text'];
				
				// Add icon to button
				if (!empty($settings['button_icon']['value'])) {
					$icon_html = \Elementor\Icons_Manager::render_icon($settings['button_icon'], ['aria-hidden' => 'true']);
					if ($settings['icon_position'] === 'before') {
						$button_text = $icon_html . ' ' . $button_text;
					} else {
						$button_text = $button_text . ' ' . $icon_html;
					}
				}
				
				echo '<button type="submit" name="add-to-cart" value="' . esc_attr($product->get_id()) . '" 
					  class="single_add_to_cart_button button alt' . $ajax_class . '" 
					  data-loading-text="' . esc_attr($settings['loading_text']) . '"' . $custom_attrs . '>' . 
					  $button_text . '</button>';
				
				echo '</div>';
				echo '</form>';
			}
			
			private function render_variable_form($product, $settings, $custom_attrs) {
				$attributes = $product->get_variation_attributes();
				$available_variations = $product->get_available_variations();
				$ajax_class = ('yes' === $settings['ajax_cart']) ? ' ajax_add_to_cart' : '';
				
				echo '<form class="variations_form cart" method="post" 
					  data-product_variations="' . esc_attr(wp_json_encode($available_variations)) . '"
					  data-product_id="' . esc_attr($product->get_id()) . '">';
				
				if ($settings['variation_display'] === 'dropdown') {
					// Original dropdown display
					echo '<table class="variations"><tbody>';
					foreach ($attributes as $attribute_name => $options) {
						echo '<tr><td class="label">' . 
							 ($settings['hide_variation_labels'] ? '' : '<label>' . wc_attribute_label($attribute_name) . '</label>') . 
							 '</td><td class="value">';
						wc_dropdown_variation_attribute_options(['options' => $options, 'product' => $product, 'attribute' => $attribute_name]);
						echo '</td></tr>';
					}
					echo '</tbody></table>';
				} else {
					// Radio or button swatches
					echo '<div class="ljc-variations-wrapper">';
					foreach ($attributes as $attribute_name => $options) {
						echo '<div class="ljc-variation-group">';
						if (!$settings['hide_variation_labels']) {
							echo '<label class="ljc-variation-label">' . wc_attribute_label($attribute_name) . '</label>';
						}
						
						if ($settings['variation_display'] === 'radio') {
							$this->render_radio_options($attribute_name, $options, $product);
						} else {
							$this->render_button_swatches($attribute_name, $options, $product);
						}
						echo '</div>';
					}
					echo '</div>';
				}
				
				// Clear selection link
				if ('yes' === $settings['show_clear_link']) {
					echo '<a class="reset_variations" href="#">' . esc_html($settings['clear_text']) . '</a>';
				}
				
				// Single variation wrap for price/description
				echo '<div class="single_variation_wrap">';
				if ('yes' === $settings['show_variation_price']) {
					echo '<div class="woocommerce-variation single_variation"></div>';
				}
				if ('yes' === $settings['show_variation_description']) {
					echo '<div class="woocommerce-variation-description"></div>';
				}
				echo '</div>';
				
				echo '<div class="ljc-actions">';
				
				if ('yes' === $settings['show_quantity']) {
					echo '<div class="ljc-qty-wrap">';
					woocommerce_quantity_input([
						'min_value' => $settings['min_quantity'],
						'max_value' => $settings['max_quantity'] > 0 ? $settings['max_quantity'] : '',
						'input_value' => $settings['default_quantity'],
						'step' => $settings['quantity_step']
					]);
					echo '</div>';
				}
				
				$button_text = $settings['button_text'];
				
				// Add icon to button
				if (!empty($settings['button_icon']['value'])) {
					$icon_html = \Elementor\Icons_Manager::render_icon($settings['button_icon'], ['aria-hidden' => 'true']);
					if ($settings['icon_position'] === 'before') {
						$button_text = $icon_html . ' ' . $button_text;
					} else {
						$button_text = $button_text . ' ' . $icon_html;
					}
				}
				
				echo '<button type="submit" class="single_add_to_cart_button button alt' . $ajax_class . '" 
					  data-loading-text="' . esc_attr($settings['loading_text']) . '"' . $custom_attrs . '>' . 
					  $button_text . '</button>';
				
				echo '</div>';
				echo '<input type="hidden" name="add-to-cart" value="' . esc_attr($product->get_id()) . '" />';
				echo '<input type="hidden" name="product_id" value="' . esc_attr($product->get_id()) . '" />';
				echo '<input type="hidden" name="variation_id" class="variation_id" value="0" />';
				
				echo '</form>';
			}
			
			private function render_radio_options($attribute_name, $options, $product) {
				$selected = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) 
						   ? wc_clean(stripslashes(urldecode($_REQUEST['attribute_' . sanitize_title($attribute_name)]))) 
						   : $product->get_variation_default_attribute($attribute_name);
				
				echo '<div class="ljc-radio-options">';
				foreach ($options as $option) {
					$id = 'ljc-' . sanitize_title($attribute_name) . '-' . sanitize_title($option);
					echo '<label for="' . esc_attr($id) . '" class="ljc-radio-label">';
					echo '<input type="radio" id="' . esc_attr($id) . '" 
						  name="attribute_' . esc_attr(sanitize_title($attribute_name)) . '" 
						  value="' . esc_attr($option) . '"' . 
						  checked(sanitize_title($selected), sanitize_title($option), false) . '>';
					echo '<span>' . esc_html(apply_filters('woocommerce_variation_option_name', $option)) . '</span>';
					echo '</label>';
				}
				echo '</div>';
			}
			
			private function render_button_swatches($attribute_name, $options, $product) {
				$selected = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) 
						   ? wc_clean(stripslashes(urldecode($_REQUEST['attribute_' . sanitize_title($attribute_name)]))) 
						   : $product->get_variation_default_attribute($attribute_name);
				
				echo '<div class="ljc-button-swatches">';
				foreach ($options as $option) {
					$active = (sanitize_title($selected) === sanitize_title($option)) ? ' active' : '';
					echo '<button type="button" class="ljc-swatch' . $active . '" 
						  data-attribute="attribute_' . esc_attr(sanitize_title($attribute_name)) . '" 
						  data-value="' . esc_attr($option) . '">' . 
						  esc_html(apply_filters('woocommerce_variation_option_name', $option)) . 
						  '</button>';
				}
				echo '<input type="hidden" name="attribute_' . esc_attr(sanitize_title($attribute_name)) . '" 
					  value="' . esc_attr($selected) . '" />';
				echo '</div>';
			}
			
			private function render_scripts($settings) {
				?>
				<script>
				jQuery(function($) {
					var widget = $('.elementor-element-<?php echo $this->get_id(); ?>');
					
					// Button swatches functionality
					widget.find('.ljc-swatch').on('click', function() {
						var $this = $(this);
						var attribute = $this.data('attribute');
						var value = $this.data('value');
						
						$this.siblings().removeClass('active');
						$this.addClass('active');
						
						widget.find('input[name="' + attribute + '"]').val(value).trigger('change');
						widget.find('.variations_form').trigger('woocommerce_variation_select_change');
						widget.find('.variations_form').trigger('check_variations');
					});
					
					// Radio functionality
					widget.find('.ljc-radio-options input[type="radio"]').on('change', function() {
						widget.find('.variations_form').trigger('woocommerce_variation_select_change');
						widget.find('.variations_form').trigger('check_variations');
					});
					
					// Success animation
					<?php if ($settings['success_animation'] !== 'none'): ?>
					widget.on('added_to_cart', function(e, fragments, cart_hash, $button) {
						var successMsg = widget.find('.ljc-success-message');
						successMsg.fadeIn(300).delay(2000).fadeOut(300);
						
						<?php if ($settings['success_animation'] === 'confetti'): ?>
						// Add confetti effect
						var confettiCount = 30;
						for (var i = 0; i < confettiCount; i++) {
							var confetti = $('<div class="ljc-confetti"></div>');
							confetti.css({
								left: Math.random() * 100 + '%',
								animationDelay: Math.random() * 3 + 's',
								backgroundColor: ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8'][Math.floor(Math.random() * 5)]
							});
							widget.append(confetti);
							setTimeout(function() { confetti.remove(); }, 3000);
						}
						<?php endif; ?>
					});
					<?php endif; ?>
					
					// Redirect after add to cart
					<?php if ($settings['redirect_after_add'] !== 'no'): ?>
					widget.on('added_to_cart', function(e, fragments, cart_hash, $button) {
						setTimeout(function() {
							<?php if ($settings['redirect_after_add'] === 'cart'): ?>
							window.location.href = '<?php echo wc_get_cart_url(); ?>';
							<?php elseif ($settings['redirect_after_add'] === 'checkout'): ?>
							window.location.href = '<?php echo wc_get_checkout_url(); ?>';
							<?php elseif ($settings['redirect_after_add'] === 'custom' && !empty($settings['redirect_custom_url']['url'])): ?>
							window.location.href = '<?php echo esc_url($settings['redirect_custom_url']['url']); ?>';
							<?php endif; ?>
						}, 500);
					});
					<?php endif; ?>
					
					// Loading state
					<?php if ($settings['show_loading_spinner'] === 'yes'): ?>
					widget.find('.single_add_to_cart_button').on('click', function() {
						var $btn = $(this);
						if (!$btn.hasClass('disabled')) {
							var originalText = $btn.html();
							$btn.data('original-text', originalText);
							$btn.html('<span class="ljc-spinner"></span> ' + $btn.data('loading-text'));
						}
					});
					
					widget.on('added_to_cart', function(e, fragments, cart_hash, $button) {
						if ($button && $button.data('original-text')) {
							$button.html($button.data('original-text'));
						}
					});
					<?php endif; ?>
					
					// Entrance animation
					<?php if (!empty($settings['entrance_animation']) && $settings['entrance_animation'] !== 'none'): ?>
					widget.find('.ljc-atc').addClass('animated <?php echo esc_attr($settings['entrance_animation']); ?>');
					<?php endif; ?>
				});
				</script>
				<?php
			}
		}
		$widgets_manager->register(new LJC_Add_To_Cart_Widget());
	});

	add_action('wp_head', function () {
?>
		<style id="ljc-atc-styles">
			/* KEEP ALL EXISTING STYLES */
			.ljc-accents {
				text-align: center;
				margin-bottom: 8px
			}

			.ljc-atc {
				display: flex;
				flex-direction: column;
				gap: 10px;
				padding: 18px;
				border-radius: 20px;
				background-image: none;
			}

			/* Grid-paper background toggle via widget prefix class */
			.ljc-grid-yes .ljc-atc {
				background-image: repeating-linear-gradient(0deg, #f7eae2 0, #f7eae2 1px, transparent 1px, transparent 22px);
			}

			/* Forms stack vertically; spacing is controllable */
			.ljc-atc form.cart {
				display: flex;
				flex-direction: column;
				gap: 10px;
				align-items: flex-start;
			}

			.ljc-atc .ljc-actions {
				display: flex;
				align-items: center;
				gap: 10px;
			}

			.ljc-align-center .ljc-atc .ljc-actions,
			.ljc-atc.ljc-legacy-center .ljc-actions {
				justify-content: center;
			}

			.ljc-align-right .ljc-atc .ljc-actions {
				justify-content: flex-end;
			}

			/* Alignment helpers via widget prefix class */
			.ljc-align-center .ljc-atc,
			.ljc-atc.ljc-legacy-center {
				text-align: center;
			}

			.ljc-align-center .ljc-atc form.cart,
			.ljc-atc.ljc-legacy-center form.cart {
				align-items: center;
			}

			.ljc-align-right .ljc-atc {
				text-align: right;
			}

			.ljc-align-right .ljc-atc form.cart {
				align-items: flex-end;
			}

			/* Quantity wrapper follows alignment */
			.ljc-atc .ljc-qty-wrap {
				display: flex;
				width: 100%;
				justify-content: flex-start;
			}

			.ljc-align-center .ljc-atc .ljc-qty-wrap,
			.ljc-atc.ljc-legacy-center .ljc-qty-wrap {
				justify-content: center;
			}

			.ljc-align-right .ljc-atc .ljc-qty-wrap {
				justify-content: flex-end;
			}

			/* Field base styles */
			.ljc-atc select,
			.ljc-atc .quantity .qty {
				border: 1px solid #f0d9cf;
				padding: 10px 12px;
				outline: none;
				border-radius: 8px;
			}

			/* Neutralize theme table hover background leaking into selects */
			.ljc-atc select,
			.ljc-atc .quantity .qty,
			.ljc-atc table td,
			.ljc-atc table th {
				background-color: transparent !important;
			}

			/* Kill native select appearance and draw a chevron */
			.ljc-atc select {
				appearance: none;
				-webkit-appearance: none;
				-moz-appearance: none;
				background-clip: padding-box;
				background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 20 20'%3E%3Cpath fill='%23888888' d='M5.516 7.548a1 1 0 0 1 1.414 0L10 10.618l3.07-3.07a1 1 0 1 1 1.414 1.414l-3.777 3.777a1.5 1.5 0 0 1-2.121 0L5.516 8.962a1 1 0 0 1 0-1.414z'/%3E%3C/svg%3E");
				background-repeat: no-repeat;
				background-position: right 12px center;
				background-size: 14px 14px;
				padding-right: 40px;
			}

			.ljc-atc select::-ms-expand {
				display: none;
			}

			/* Full-width dropdowns toggle via widget prefix class */
			.ljc-ffd-yes .ljc-atc table.variations {
				width: 100%;
			}

			.ljc-ffd-yes .ljc-atc table.variations tbody,
			.ljc-ffd-yes .ljc-atc table.variations tr,
			.ljc-ffd-yes .ljc-atc table.variations td {
				display: block;
				width: 100%;
			}

			.ljc-ffd-yes .ljc-atc table.variations .label {
				margin-bottom: 6px;
			}

			.ljc-ffd-yes .ljc-atc table.variations .value {
				width: 100%;
			}

			.ljc-ffd-yes .ljc-atc table.variations select {
				width: 100%;
			}

			/* Full-width button toggle via widget prefix class */
			.ljc-btn-full-yes .ljc-atc .single_add_to_cart_button {
				width: 100%;
			}
			
			/* NEW STYLES */
			
			/* Product Info */
			.ljc-atc .ljc-product-info {
				display: flex;
				flex-direction: column;
				gap: 8px;
			}
			
			.ljc-atc .ljc-product-image {
				position: relative;
				margin-bottom: 15px;
			}
			
			.ljc-atc .ljc-product-image img {
				width: 100%;
				height: auto;
				border-radius: 12px;
			}
			
			/* Sale Badge */
			.ljc-atc .ljc-sale-badge {
				position: absolute;
				top: 10px;
				right: 10px;
				padding: 5px 10px;
				background: #FF6B6B;
				color: white;
				border-radius: 20px;
				font-weight: bold;
				font-size: 12px;
				z-index: 1;
			}
			
			/* Stock Status */
			.ljc-atc .ljc-stock {
				font-size: 14px;
			}
			
			.ljc-atc .ljc-stock.in-stock {
				color: #4CAF50;
			}
			
			.ljc-atc .ljc-stock.out-of-stock {
				color: #F44336;
			}
			
			/* Success Message */
			.ljc-atc .ljc-success-message {
				display: flex;
				align-items: center;
				gap: 10px;
				padding: 12px 20px;
				background: #4CAF50;
				color: white;
				border-radius: 8px;
				margin-top: 10px;
			}
			
			.ljc-atc .ljc-success-icon {
				width: 24px;
				height: 24px;
				background: white;
				color: #4CAF50;
				border-radius: 50%;
				display: flex;
				align-items: center;
				justify-content: center;
				font-weight: bold;
			}
			
			/* Variation Radio Options */
			.ljc-atc .ljc-variations-wrapper {
				display: flex;
				flex-direction: column;
				gap: 15px;
			}
			
			.ljc-atc .ljc-variation-group {
				display: flex;
				flex-direction: column;
				gap: 10px;
			}
			
			.ljc-atc .ljc-variation-label {
				font-weight: 600;
				margin-bottom: 5px;
			}
			
			.ljc-atc .ljc-radio-options {
				display: flex;
				flex-wrap: wrap;
				gap: 10px;
			}
			
			.ljc-atc .ljc-radio-label {
				display: flex;
				align-items: center;
				gap: 8px;
				padding: 8px 15px;
				background: #f5f5f5;
				border: 2px solid transparent;
				border-radius: 8px;
				cursor: pointer;
				transition: all 0.3s ease;
			}
			
			.ljc-atc .ljc-radio-label:hover {
				border-color: #f0d9cf;
			}
			
			.ljc-atc .ljc-radio-label input[type="radio"]:checked + span {
				font-weight: 600;
			}
			
			.ljc-atc .ljc-radio-label input[type="radio"]:checked ~ * {
				color: #333;
			}
			
			/* Button Swatches */
			.ljc-atc .ljc-button-swatches {
				display: flex;
				flex-wrap: wrap;
				gap: 10px;
			}
			
			.ljc-atc .ljc-swatch {
				padding: 10px 20px;
				background: #f5f5f5;
				border: 2px solid transparent;
				border-radius: 8px;
				cursor: pointer;
				transition: all 0.3s ease;
				font-weight: 500;
			}
			
			.ljc-atc .ljc-swatch:hover {
				border-color: #f0d9cf;
				transform: translateY(-2px);
			}
			
			.ljc-atc .ljc-swatch.active {
				background: #FFF8F4;
				border-color: #F2B6A0;
				font-weight: 600;
			}
			
			/* Clear variations link */
			.ljc-atc .reset_variations {
				color: #999;
				font-size: 14px;
				text-decoration: underline;
				margin-top: 5px;
			}
			
			/* Loading Spinner */
			.ljc-spinner {
				display: inline-block;
				width: 16px;
				height: 16px;
				border: 2px solid rgba(255,255,255,.3);
				border-radius: 50%;
				border-top-color: #fff;
				animation: ljc-spin 0.6s linear infinite;
			}
			
			@keyframes ljc-spin {
				to { transform: rotate(360deg); }
			}
			
			/* Button Style Presets */
			.ljc-btn-style-gradient .ljc-atc .single_add_to_cart_button {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			}
			
			.ljc-btn-style-outline .ljc-atc .single_add_to_cart_button {
				background: transparent !important;
				border: 2px solid currentColor !important;
			}
			
			.ljc-btn-style-3d .ljc-atc .single_add_to_cart_button {
				box-shadow: 0 4px 0 rgba(0,0,0,0.2);
				transform: translateY(-2px);
			}
			
			.ljc-btn-style-3d .ljc-atc .single_add_to_cart_button:active {
				transform: translateY(0);
				box-shadow: 0 2px 0 rgba(0,0,0,0.2);
			}
			
			.ljc-btn-style-glow .ljc-atc .single_add_to_cart_button {
				box-shadow: 0 0 20px rgba(242, 182, 160, 0.5);
			}
			
			/* Hover Animations */
			.ljc-hover-pulse .ljc-atc .single_add_to_cart_button:hover {
				animation: ljc-pulse 1s infinite;
			}
			
			.ljc-hover-bounce .ljc-atc .single_add_to_cart_button:hover {
				animation: ljc-bounce 0.5s;
			}
			
			.ljc-hover-shake .ljc-atc .single_add_to_cart_button:hover {
				animation: ljc-shake 0.5s;
			}
			
			.ljc-hover-grow .ljc-atc .single_add_to_cart_button:hover {
				transform: scale(1.05);
			}
			
			@keyframes ljc-pulse {
				0% { transform: scale(1); }
				50% { transform: scale(1.05); }
				100% { transform: scale(1); }
			}
			
			@keyframes ljc-bounce {
				0%, 100% { transform: translateY(0); }
				50% { transform: translateY(-10px); }
			}
			
			@keyframes ljc-shake {
				0%, 100% { transform: translateX(0); }
				25% { transform: translateX(-5px); }
				75% { transform: translateX(5px); }
			}
			
			/* Confetti Animation */
			.ljc-confetti {
				position: absolute;
				width: 10px;
				height: 10px;
				animation: ljc-confetti-fall 3s ease-out forwards;
			}
			
			@keyframes ljc-confetti-fall {
				to {
					transform: translateY(100vh) rotate(720deg);
					opacity: 0;
				}
			}
			
			/* Entrance Animations */
			.animated {
				animation-duration: 0.8s;
				animation-fill-mode: both;
			}
			
			@keyframes fadeIn {
				from { opacity: 0; }
				to { opacity: 1; }
			}
			
			@keyframes slideInUp {
				from {
					transform: translateY(30px);
					opacity: 0;
				}
				to {
					transform: translateY(0);
					opacity: 1;
				}
			}
			
			@keyframes slideInDown {
				from {
					transform: translateY(-30px);
					opacity: 0;
				}
				to {
					transform: translateY(0);
					opacity: 1;
				}
			}
			
			@keyframes bounceIn {
				0% {
					opacity: 0;
					transform: scale(0.3);
				}
				50% {
					opacity: 1;
					transform: scale(1.05);
				}
				70% {
					transform: scale(0.9);
				}
				100% {
					transform: scale(1);
				}
			}
			
			@keyframes zoomIn {
				from {
					opacity: 0;
					transform: scale(0.5);
				}
				to {
					opacity: 1;
					transform: scale(1);
				}
			}
			
			.animated.fadeIn { animation-name: fadeIn; }
			.animated.slideInUp { animation-name: slideInUp; }
			.animated.slideInDown { animation-name: slideInDown; }
			.animated.bounceIn { animation-name: bounceIn; }
			.animated.zoomIn { animation-name: zoomIn; }
		</style>
<?php
	});
});
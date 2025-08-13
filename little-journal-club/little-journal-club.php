<?php

/**
 * Plugin Name: Little Journal Club
 * Description: A custom plugin to create Elementor components.
 * Version: 1.0.5
 * Author: Richard McLain
 * Text Domain: little-journal-club
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
/**
 * Register a custom Elementor widget category: Little Journal Club.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
 */
function ljc_register_elementor_category($elements_manager)
{
    $elements_manager->add_category(
        'little-journal-club', // Unique slug for your category
        [
            'title' => __('Little Journal Club', 'little-journal-club'), // Display name
            'icon' => 'fa fa-book', // Optional icon
        ]
    );
}
add_action('elementor/elements/categories_registered', 'ljc_register_elementor_category');


/**
 * Register the Cool Image Box widget.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 */
function ljc_register_cool_image_box_widget($widgets_manager)
{
    require_once plugin_dir_path(__FILE__) . 'widgets/inset-image-box.php';

    if (class_exists('Inset_Image_Box_Widget')) {
        // Use the new registration method.
        $widgets_manager->register(new \Inset_Image_Box_Widget());
    } else {
        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-error">
                <p><?php esc_html_e('Inset Image Box Widget class not found. Please ensure the widget file exists and is correct.', 'little-journal-club'); ?>
                </p>
            </div>
            <?php
        });
    }
}

if (ljc_check_elementor_active()) {
    // Use the new registration hook.
    add_action('elementor/widgets/register', 'ljc_register_cool_image_box_widget');
}

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Check if Elementor is active.
 */
function ljc_check_elementor_active()
{
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'ljc_elementor_missing_notice');
        return false;
    }
    return true;
}

/**
 * Display notice if Elementor is missing.
 */
function ljc_elementor_missing_notice()
{
    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e('Elementor must be installed and activated for the Little Journal Club plugin to work.', 'little-journal-club'); ?>
        </p>
    </div>
    <?php
}

/**
 * Load plugin textdomain.
 */
function ljc_load_textdomain()
{
    load_plugin_textdomain('little-journal-club', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'ljc_load_textdomain');

/**
 * Enqueue scripts and styles for the Shop New Carousel widget.
 */
function ljc_enqueue_shop_new_carousel_scripts()
{
    // Register Swiper using CDN.
    wp_register_style(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11.0.0'
    );
    wp_register_script(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11.0.0',
        true
    );

    // Register your custom carousel script & style.
    wp_register_style(
        'ljc-shop-new-carousel-style',
        plugin_dir_url(__FILE__) . 'assets/css/shop-new-carousel.css',
        ['swiper'],
        '1.0.0'
    );
    wp_register_script(
        'ljc-shop-new-carousel-script',
        plugin_dir_url(__FILE__) . 'assets/js/shop-new-carousel.js',
        ['swiper', 'jquery'],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'ljc_enqueue_shop_new_carousel_scripts');

/**
 * Register the Shop New Carousel widget with Elementor.
 */
function ljc_register_shop_new_carousel_widget($widgets_manager)
{
    require_once plugin_dir_path(__FILE__) . 'widgets/shop-new-carousel.php';

    if (class_exists('Shop_New_Carousel_Widget')) {
        $widgets_manager->register(new \Shop_New_Carousel_Widget());
    } else {
        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-error">
                <p><?php esc_html_e('Shop_New_Carousel_Widget class not found. Please ensure the file exists and is correct.', 'little-journal-club'); ?>
                </p>
            </div>
            <?php
        });
    }
}

if (ljc_check_elementor_active()) {
    add_action('elementor/widgets/register', 'ljc_register_shop_new_carousel_widget');
}

// Register Washi Tape Div Widget
function ljc_register_washi_tape_div_widget($widgets_manager)
{
    require_once(plugin_dir_path(__FILE__) . 'widgets/washi-tape-div.php');
    $widgets_manager->register(new \Washi_Tape_Div_Widget());
}
add_action('elementor/widgets/register', 'ljc_register_washi_tape_div_widget');

// Register and enqueue Washi Tape CSS
function ljc_register_washi_tape_styles()
{
    wp_register_style(
        'ljc-washi-tape-style',
        plugin_dir_url(__FILE__) . 'assets/css/washi.css',
        [],
        '1.0.0'
    );
    
    // Also register the dedicated widget CSS file
    wp_register_style(
        'ljc-widget-washi-tape-style',
        plugin_dir_url(__FILE__) . 'assets/css/widget-washi-tape.css',
        [],
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'ljc_register_washi_tape_styles');
add_action('elementor/frontend/after_enqueue_styles', 'ljc_register_washi_tape_styles');

// Add this near your other includes
require_once plugin_dir_path(__FILE__) . 'includes/elementor/washi-tape-controls.php';

// Make sure this is added after the plugin constants
function ljc_initialize_elementor_washi_tape() {
    // Check if Elementor is installed and activated
    if (!did_action('elementor/loaded')) {
        return;
    }

    // Initialize Washi Tape Controls
    new \LJC\Elementor\Washi_Tape_Controls();
}
add_action('elementor/init', 'ljc_initialize_elementor_washi_tape');

// Plugin Constants
define('LJC_VERSION', '1.0.0'); // Update this with your plugin version
define('LJC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LJC_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Add this near your other includes
function ljc_register_elementor_assets() {
    // Register main washi tape styles
    wp_register_style(
        'ljc-washi-tape',
        plugin_dir_url(__FILE__) . 'assets/css/washi.css',
        [],
        LJC_VERSION
    );

    // Register editor-specific styles
    wp_register_style(
        'ljc-washi-tape-editor',
        plugin_dir_url(__FILE__) . 'assets/css/washi-editor.css',
        [],
        LJC_VERSION
    );

    // Register preview-specific styles
    wp_register_style(
        'ljc-washi-tape-preview',
        plugin_dir_url(__FILE__) . 'assets/css/washi-preview.css',
        [],
        LJC_VERSION
    );

    // Register preview script
    wp_register_script(
        'ljc-washi-tape-preview',
        plugin_dir_url(__FILE__) . 'assets/js/washi-preview.js',
        ['jquery', 'elementor-frontend'],
        LJC_VERSION,
        true
    );
}
add_action('init', 'ljc_register_elementor_assets');

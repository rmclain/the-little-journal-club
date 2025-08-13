<?php

/**
 * Plugin Name: Scrapbook Shop for Elementor
 * Description: Beautiful scrapbook-style WooCommerce product displays with Washi Tape integration for Elementor
 * Version: 1.0.7
 * Author: Richard McLain
 * Text Domain: scrapbook-shop
 * Domain Path: /languages
 * Requires Plugins: elementor, woocommerce
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('SCRAPBOOK_SHOP_VERSION', '1.0.7');
define('SCRAPBOOK_SHOP_PATH', plugin_dir_path(__FILE__));
define('SCRAPBOOK_SHOP_URL', plugin_dir_url(__FILE__));

/**
 * Main Scrapbook Shop Plugin Class
 */
class Scrapbook_Shop_Plugin
{

    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        // Check dependencies
        add_action('admin_init', [$this, 'check_dependencies']);

        // Load translations as early as possible to avoid JIT notices
        add_action('plugins_loaded', [$this, 'load_textdomain'], 0);

        // Initialize the plugin after all plugins are loaded
        add_action('plugins_loaded', [$this, 'init']);

        // Register styles and scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles']);

        // Register Elementor widgets
        add_action('elementor/widgets/register', [$this, 'register_widgets']);

        // Add custom widget category
        add_action('elementor/elements/categories_registered', [$this, 'add_widget_category']);

        // Register custom image sizes
        add_action('after_setup_theme', [$this, 'register_image_sizes']);

        // Add theme support
        add_action('after_setup_theme', [$this, 'add_theme_support']);
    }

    /**
     * Check plugin dependencies
     */
    public function check_dependencies()
    {
        $missing_plugins = [];

        if (!did_action('elementor/loaded')) {
            $missing_plugins[] = 'Elementor';
        }

        if (!class_exists('WooCommerce')) {
            $missing_plugins[] = 'WooCommerce';
        }

        if (!empty($missing_plugins)) {
            add_action('admin_notices', function () use ($missing_plugins) {
?>
                <div class="notice notice-error">
                    <p>
                        <?php
                        printf(
                            esc_html__('Scrapbook Shop requires the following plugins to be installed and activated: %s', 'scrapbook-shop'),
                            implode(', ', $missing_plugins)
                        );
                        ?>
                    </p>
                </div>
            <?php
            });
        }
    }

    /**
     * Initialize the plugin
     */
    public function init()
    {
        // Check if Elementor is loaded
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Add AJAX handlers for dynamic content
        add_action('wp_ajax_scrapbook_load_more_products', [$this, 'ajax_load_more_products']);
        add_action('wp_ajax_nopriv_scrapbook_load_more_products', [$this, 'ajax_load_more_products']);
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain()
    {
        load_plugin_textdomain('scrapbook-shop', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Include widget files
     */
    private function include_widget_files()
    {
        require_once SCRAPBOOK_SHOP_PATH . 'widgets/scrapbook-product-card.php';
        require_once SCRAPBOOK_SHOP_PATH . 'widgets/scrapbook-products-grid.php';
    }

    /**
     * Register Elementor widgets
     */
    public function register_widgets($widgets_manager)
    {
        // Ensure Elementor base class is available
        if (!class_exists('Elementor\\Widget_Base')) {
            return;
        }

        // Include widget files only when Elementor is registering widgets
        $this->include_widget_files();

        // Register widgets if classes exist
        if (class_exists('Scrapbook_Shop\\Widgets\\Scrapbook_Product_Card')) {
            $widgets_manager->register(new \Scrapbook_Shop\Widgets\Scrapbook_Product_Card());
        }

        if (class_exists('Scrapbook_Shop\\Widgets\\Scrapbook_Products_Grid')) {
            $widgets_manager->register(new \Scrapbook_Shop\Widgets\Scrapbook_Products_Grid());
        }
    }

    /**
     * Add custom widget category
     */
    public function add_widget_category($elements_manager)
    {
        $elements_manager->add_category(
            'scrapbook-shop',
            [
                'title' => esc_html__('Scrapbook Shop', 'scrapbook-shop'),
                'icon' => 'fa fa-shopping-cart',
            ]
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets()
    {
        // Main styles
        wp_enqueue_style(
            'scrapbook-shop-styles',
            SCRAPBOOK_SHOP_URL . 'assets/css/scrapbook-shop.css',
            ['woocommerce-general'],
            SCRAPBOOK_SHOP_VERSION
        );

        // Responsive styles
        wp_enqueue_style(
            'scrapbook-shop-responsive',
            SCRAPBOOK_SHOP_URL . 'assets/css/scrapbook-shop-responsive.css',
            ['scrapbook-shop-styles'],
            SCRAPBOOK_SHOP_VERSION
        );

        // Frontend scripts
        wp_enqueue_script(
            'scrapbook-shop-scripts',
            SCRAPBOOK_SHOP_URL . 'assets/js/scrapbook-shop.js',
            ['jquery'],
            SCRAPBOOK_SHOP_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script('scrapbook-shop-scripts', 'scrapbook_shop_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('scrapbook_shop_nonce'),
        ]);
    }

    /**
     * Enqueue editor styles
     */
    public function enqueue_editor_styles()
    {
        wp_enqueue_style(
            'scrapbook-shop-editor',
            SCRAPBOOK_SHOP_URL . 'assets/css/scrapbook-shop-editor.css',
            [],
            SCRAPBOOK_SHOP_VERSION
        );
    }

    /**
     * Register custom image sizes
     */
    public function register_image_sizes()
    {
        // Polaroid-style image size
        add_image_size('scrapbook_polaroid', 400, 400, true);

        // Thumbnail for grid
        add_image_size('scrapbook_grid', 300, 300, true);
    }

    /**
     * Add theme support
     */
    public function add_theme_support()
    {
        // Add WooCommerce support
        add_theme_support('woocommerce');

        // Add support for WooCommerce galleries
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }

    /**
     * AJAX handler for loading more products
     */
    public function ajax_load_more_products()
    {
        check_ajax_referer('scrapbook_shop_nonce', 'nonce');

        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 12;
        $query_type = isset($_POST['query_type']) ? sanitize_text_field($_POST['query_type']) : 'all';

        // Build query
        $args = [
            'post_type' => 'product',
            'posts_per_page' => $per_page,
            'paged' => $paged,
        ];

        // Add query type specific args
        switch ($query_type) {
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
        }

        $products = new WP_Query($args);

        ob_start();

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                global $product;

                // Output product HTML
            ?>
                <div class="scrapbook-grid-item">
                    <!-- Product card HTML here -->
                </div>
    <?php
            }
        }

        $html = ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'max_pages' => $products->max_num_pages,
        ]);

        wp_die();
    }
}

// Initialize the plugin
function scrapbook_shop_init()
{
    return Scrapbook_Shop_Plugin::get_instance();
}

add_action('plugins_loaded', 'scrapbook_shop_init', 0);

/**
 * Helper function to get washi tape SVG
 */
function scrapbook_get_washi_tape($tape_id = null)
{
    if (!class_exists('Washi_Tape_DB')) {
        return '';
    }

    $db = new Washi_Tape_DB();

    if ($tape_id) {
        $tape = $db->get_washi_tape($tape_id);
    } else {
        // Get random tape
        $tapes = $db->get_all_washi_tapes();
        if (!empty($tapes)) {
            $tape = $tapes[array_rand($tapes)];
        }
    }

    return ($tape && !empty($tape->svg)) ? $tape->svg : '';
}

/**
 * Template function for product card
 */
function scrapbook_product_card($product_id = null)
{
    if (!$product_id) {
        global $product;
    } else {
        $product = wc_get_product($product_id);
    }

    if (!$product) {
        return;
    }

    // Get random washi tape
    $washi_tape = scrapbook_get_washi_tape();
    $rotation = rand(-10, 10);

    ?>
    <div class="scrapbook-product-card">
        <div class="product-card-inner">
            <div class="product-image-wrapper">
                <?php if ($washi_tape): ?>
                    <div class="washi-tape-decoration" style="transform: rotate(<?php echo esc_attr($rotation); ?>deg);">
                        <?php echo $washi_tape; ?>
                    </div>
                <?php endif; ?>

                <?php if ($product->is_on_sale()): ?>
                    <span class="onsale"><?php esc_html_e('Sale!', 'woocommerce'); ?></span>
                <?php endif; ?>

                <div class="polaroid-frame">
                    <a href="<?php echo esc_url($product->get_permalink()); ?>">
                        <?php echo $product->get_image('scrapbook_polaroid'); ?>
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

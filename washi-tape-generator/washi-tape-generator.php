<?php

/**
 * Plugin Name: Washi Tape Generator
 * Description: Create and apply decorative washi tape designs to your Elementor elements
 * Version: 1.0.3
 * Author: Richard McLain
 * Text Domain: frugle.com
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Washi Tape Generator Plugin Class
 */
class Washi_Tape_Generator
{

    /**
     * Plugin instance
     */
    private static $instance = null;

    /**
     * Plugin version
     */
    const VERSION = '1.0.0';

    /**
     * Get plugin instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        // Define constants
        $this->define_constants();

        // Include required files
        $this->includes();

        // Initialize hooks
        $this->init_hooks();

        // Make sure table exists
        $this->check_and_create_table();

        // Initialize admin page
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
            add_action('wp_ajax_save_washi_tape', array($this, 'save_washi_tape'));
            add_action('wp_ajax_delete_washi_tape', array($this, 'delete_washi_tape'));
            add_action('wp_ajax_get_washi_tapes', array($this, 'get_washi_tapes'));
            add_action('wp_ajax_get_washi_tape_svg', array($this, 'get_washi_tape_svg'));
        }
    }

    /**
     * Define plugin constants
     */
    private function define_constants()
    {
        define('WASHI_TAPE_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('WASHI_TAPE_URL', plugin_dir_url(__FILE__));
        define('WASHI_TAPE_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('WASHI_TAPE_VERSION', self::VERSION);
    }

    /**
     * Include required files
     */
    private function includes()
    {
        // Include admin class
        require_once WASHI_TAPE_PLUGIN_PATH . 'includes/admin/class-washi-tape-admin.php';

        // Include database class
        require_once WASHI_TAPE_PLUGIN_PATH . 'includes/class-washi-tape-db.php';

        // Include Elementor integration if Elementor is active
        if (did_action('elementor/loaded')) {
            require_once WASHI_TAPE_PLUGIN_PATH . 'includes/elementor/class-washi-tape-elementor.php';
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks()
    {
        // Register activation hook
        register_activation_hook(__FILE__, array($this, 'activate'));

        // Add action for Elementor integration
        add_action('plugins_loaded', array($this, 'init_elementor_integration'));

        // Add front-end scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    /**
     * Plugin activation
     */
    public function activate()
    {
        // Create database tables
        require_once WASHI_TAPE_PLUGIN_PATH . 'includes/class-washi-tape-db.php';
        $db = new Washi_Tape_DB();
        $db->create_tables();

        // Add plugin version to database
        update_option('washi_tape_version', self::VERSION);
    }

    /**
     * Initialize Elementor integration
     */
    public function init_elementor_integration()
    {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Make sure this happens after Elementor and WooCommerce are fully loaded
        add_action('elementor/init', function () {
            // Include and initialize the Elementor integration
            require_once WASHI_TAPE_PLUGIN_PATH . 'includes/elementor/class-washi-tape-elementor.php';
            new \Washi_Tape\Elementor\Washi_Tape_Controls();
        });
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu()
    {
        add_menu_page(
            __('Washi Tape Generator', 'washi-tape-generator'),
            __('Washi Tape', 'washi-tape-generator'),
            'manage_options',
            'washi-tape-generator',
            array($this, 'render_admin_page'),
            'dashicons-admin-appearance',
            30
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page()
    {
        require_once WASHI_TAPE_PLUGIN_PATH . 'includes/admin/views/admin-page.php';
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook)
    {
        if ('toplevel_page_washi-tape-generator' !== $hook) {
            return;
        }

        // Enqueue styles
        wp_enqueue_style(
            'washi-tape-admin-style',
            WASHI_TAPE_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            WASHI_TAPE_VERSION
        );

        // Enqueue scripts
        wp_enqueue_script(
            'washi-tape-admin-script',
            WASHI_TAPE_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            WASHI_TAPE_VERSION,
            true
        );

        // Localize script with ajax url
        wp_localize_script(
            'washi-tape-admin-script',
            'washiTapeParams',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('washi_tape_nonce')
            )
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets()
    {
        // Always enqueue frontend styles and scripts
        wp_enqueue_style(
            'washi-tape-frontend-style',
            WASHI_TAPE_PLUGIN_URL . 'assets/css/frontend-style.css',
            array(),
            WASHI_TAPE_VERSION
        );

        wp_enqueue_script(
            'washi-tape-frontend-script',
            WASHI_TAPE_PLUGIN_URL . 'assets/js/frontend-script.js',
            array('jquery'),
            WASHI_TAPE_VERSION,
            true
        );

        // Add Elementor-specific assets if Elementor is active
        if (did_action('elementor/loaded')) {
            wp_enqueue_style(
                'washi-tape-elementor-style',
                WASHI_TAPE_PLUGIN_URL . 'assets/css/elementor.css',
                array(),
                WASHI_TAPE_VERSION
            );
        }
    }

    /**
     * Check if table exists and create it if it doesn't
     * This serves as a failsafe if the activation hook didn't work
     */
    public function check_and_create_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'washi_tapes';

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

        if (!$table_exists) {
            // Table doesn't exist, so create it
            error_log('Washi Tape Generator: Table does not exist, creating it now');

            $charset_collate = $wpdb->get_charset_collate();

            // More compatible SQL version without ON UPDATE
            $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            svg longtext NOT NULL,
            settings longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);

            // Log any errors
            if ($wpdb->last_error) {
                error_log('Washi Tape Table Creation Error: ' . $wpdb->last_error);
            } else {
                error_log('Washi Tape Table Created Successfully');
            }
        }
    }

    /**
     * AJAX: Save washi tape design
     */
    public function save_washi_tape()
    {
        // Check nonce
        check_ajax_referer('washi_tape_nonce', 'nonce');

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'washi-tape-generator')));
        }

        // Get data
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $svg = isset($_POST['svg']) ? $_POST['svg'] : ''; // We'll sanitize SVG specifically
        $settings = isset($_POST['settings']) ? $_POST['settings'] : ''; // We'll sanitize JSON later

        // Validate data
        if (empty($title) || empty($svg)) {
            wp_send_json_error(array('message' => __('Required fields are missing.', 'washi-tape-generator')));
        }

        // Clean up the SVG
        $svg = $this->sanitize_svg($svg);

        // Remove any inline styles
        $svg = preg_replace('/style="[^"]*"/', '', $svg);

        // Ensure proper xmlns attribute
        if (strpos($svg, 'xmlns="http://www.w3.org/2000/svg"') === false) {
            $svg = str_replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"', $svg);
        }

        // Sanitize settings JSON
        $settings = json_decode(stripslashes($settings), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => __('Invalid settings format.', 'washi-tape-generator')));
        }
        $settings = json_encode($settings);

        // Save to database
        $db = new Washi_Tape_DB();
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id > 0) {
            // Update existing
            $result = $db->update_washi_tape($id, $title, $svg, $settings);
        } else {
            // Create new
            $result = $db->create_washi_tape($title, $svg, $settings);
        }

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Washi tape saved successfully.', 'washi-tape-generator'),
                'id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => __('Error saving washi tape.', 'washi-tape-generator')));
        }
    }

    /**
     * AJAX: Delete washi tape design
     */
    public function delete_washi_tape()
    {
        // Check nonce
        check_ajax_referer('washi_tape_nonce', 'nonce');

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'washi-tape-generator')));
        }

        // Get data
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        // Validate data
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid washi tape ID.', 'washi-tape-generator')));
        }

        // Delete from database
        $db = new Washi_Tape_DB();
        $result = $db->delete_washi_tape($id);

        if ($result) {
            wp_send_json_success(array('message' => __('Washi tape deleted successfully.', 'washi-tape-generator')));
        } else {
            wp_send_json_error(array('message' => __('Error deleting washi tape.', 'washi-tape-generator')));
        }
    }

    /**
     * AJAX: Get all washi tapes
     */
    public function get_washi_tapes()
    {
        // Check nonce
        check_ajax_referer('washi_tape_nonce', 'nonce');

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'washi-tape-generator')));
        }

        // Get from database
        $db = new Washi_Tape_DB();
        $washi_tapes = $db->get_all_washi_tapes();

        wp_send_json_success(array('washi_tapes' => $washi_tapes));
    }

    /**
     * AJAX: Get washi tape SVG
     */
    public function get_washi_tape_svg()
    {
        // Check nonce
        check_ajax_referer('washi_tape_nonce', 'nonce');

        // Get tape ID
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid washi tape ID.', 'washi-tape-generator')));
        }

        // Get from database
        $db = new Washi_Tape_DB();
        $washi_tape = $db->get_washi_tape($id);

        if (!$washi_tape) {
            wp_send_json_error(array('message' => __('Washi tape not found.', 'washi-tape-generator')));
        }

        wp_send_json_success(array(
            'id' => $washi_tape->id,
            'svg' => $washi_tape->svg
        ));
    }

    /**
     * Sanitize SVG
     * 
     * Basic SVG sanitization (consider using a more robust solution for production)
     */
    private function sanitize_svg($svg)
    {
        // Remove any scripts
        $svg = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $svg);

        // Remove event handlers
        $svg = preg_replace('/\bon\w+\s*=\s*["\'][^"\']*["\']/i', '', $svg);

        // Clean up quotes and spaces
        $svg = str_replace('&quot;', '"', $svg);
        $svg = str_replace('\&quot;', '"', $svg);
        $svg = preg_replace('/\s+/', ' ', $svg);
        $svg = str_replace('" >', '">', $svg);
        $svg = str_replace('> <', '><', $svg);

        // Remove any inline styles
        $svg = preg_replace('/style="[^"]*"/', '', $svg);

        // Ensure proper xmlns attribute
        if (strpos($svg, 'xmlns="http://www.w3.org/2000/svg"') === false) {
            $svg = str_replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"', $svg);
        }

        return $svg;
    }
}

// Initialize the plugin
function washi_tape_generator_init()
{
    Washi_Tape_Generator::get_instance();
}
add_action('plugins_loaded', 'washi_tape_generator_init');

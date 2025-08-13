<?php
/**
 * Plugin Name:       Washi Tape Attacher for Elementor
 * Description:       A simple and stable plugin to attach washi tape designs to any Elementor element.
 * Version:           1.0.0
 * Author:            Your Name
 * Text Domain:       washi-tape-attacher
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('WASHI_TAPE_ATTACHER_VERSION', '1.0.0');
define('WASHI_TAPE_ATTACHER_URL', plugin_dir_url(__FILE__));
define('WASHI_TAPE_ATTACHER_PATH', plugin_dir_path(__FILE__));

/**
 * Main Plugin Class
 */
final class Washi_Tape_Attacher {

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init() {
        // Check if Elementor is active
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }

        // Include the integration class
        require_once WASHI_TAPE_ATTACHER_PATH . 'includes/class-elementor-integration.php';

        // Initialize the integration
        \Washi_Tape_Attacher\Elementor_Integration::instance();
    }

    public function admin_notice_missing_elementor() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'washi-tape-attacher'),
            '<strong>' . esc_html__('Washi Tape Attacher', 'washi-tape-attacher') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'washi-tape-attacher') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

// Instantiate the plugin
Washi_Tape_Attacher::instance();

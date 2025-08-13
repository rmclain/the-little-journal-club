<?php
/**
 * Plugin Name: TLJC Posts Collection
 * Description: A beautiful blog post archive widget for The Little Journal Club
 * Version: 1.0.2
 * Author: The Little Journal Club
 * Text Domain: tljc-posts
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

final class TLJC_Posts_Plugin
{

    const VERSION = '1.0.0';
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
    const MINIMUM_PHP_VERSION = '7.0';

    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function i18n()
    {
        load_plugin_textdomain('tljc-posts');
    }

    public function init()
    {
        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }

        // Check for Washi Tape Generator plugin (recommended but not required)
        if (!class_exists('Washi_Tape_DB')) {
            add_action('admin_notices', [$this, 'admin_notice_washi_tape_recommended']);
        }

        // Add Plugin actions
        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function admin_notice_missing_main_plugin()
    {
        if (isset($_GET['activate']))
            unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'tljc-posts'),
            '<strong>' . esc_html__('TLJC Posts Collection', 'tljc-posts') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'tljc-posts') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_minimum_elementor_version()
    {
        if (isset($_GET['activate']))
            unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'tljc-posts'),
            '<strong>' . esc_html__('TLJC Posts Collection', 'tljc-posts') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'tljc-posts') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_minimum_php_version()
    {
        if (isset($_GET['activate']))
            unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'tljc-posts'),
            '<strong>' . esc_html__('TLJC Posts Collection', 'tljc-posts') . '</strong>',
            '<strong>' . esc_html__('PHP', 'tljc-posts') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_washi_tape_recommended()
    {
        $message = sprintf(
            esc_html__('"%1$s" works best with the "%2$s" plugin for authentic washi tape designs with torn edges.', 'tljc-posts'),
            '<strong>' . esc_html__('TLJC Posts Collection', 'tljc-posts') . '</strong>',
            '<strong>' . esc_html__('Washi Tape Generator', 'tljc-posts') . '</strong>'
        );
        printf('<div class="notice notice-info is-dismissible"><p>%1$s <a href="admin.php?page=washi-tape-generator">Create washi tapes &rarr;</a></p></div>', $message);
    }

    public function init_widgets()
    {
        // Check if Typography control is available
        if (!class_exists('\Elementor\Group_Control_Typography')) {
            return;
        }

        // Include Widget files
        require_once(__DIR__ . '/widgets/class-tljc-posts-widget.php');

        // Register widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \TLJC_Posts_Widget());
    }

    public function enqueue_styles()
    {
        // Enqueue our styles
        wp_enqueue_style(
            'tljc-posts-style',
            plugin_dir_url(__FILE__) . 'assets/css/tljc-posts.css',
            [],
            self::VERSION
        );

        // If Washi Tape Generator is active, ensure its scripts are loaded
        if (class_exists('Washi_Tape_DB')) {
            // Enqueue Washi Tape Generator's frontend script if not already enqueued
            if (!wp_script_is('washi-tape-frontend-script', 'enqueued')) {
                wp_enqueue_script(
                    'washi-tape-frontend-script',
                    WASHI_TAPE_PLUGIN_URL . 'assets/js/frontend-script.js',
                    ['jquery'],
                    WASHI_TAPE_VERSION,
                    true
                );
            }

            // Enqueue Washi Tape Generator's frontend styles if not already enqueued
            if (!wp_style_is('washi-tape-frontend-style', 'enqueued')) {
                wp_enqueue_style(
                    'washi-tape-frontend-style',
                    WASHI_TAPE_PLUGIN_URL . 'assets/css/frontend-style.css',
                    [],
                    WASHI_TAPE_VERSION
                );
            }
        }
    }
}

TLJC_Posts_Plugin::instance();
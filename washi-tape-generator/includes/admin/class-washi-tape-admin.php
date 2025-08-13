<?php

/**
 * Washi Tape Admin Class
 */
class Washi_Tape_Admin
{

    /**
     * Admin instance
     */
    private static $instance = null;

    /**
     * Get admin instance
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
        // Add actions and filters
        add_action('admin_init', array($this, 'admin_init'));
    }

    /**
     * Admin init
     */
    public function admin_init()
    {
        // Register media uploader scripts for the image overlay
        add_action('admin_enqueue_scripts', array($this, 'enqueue_media_uploader'));
    }

    /**
     * Enqueue media uploader
     */
    public function enqueue_media_uploader($hook)
    {
        if ('toplevel_page_washi-tape-generator' !== $hook) {
            return;
        }

        wp_enqueue_media();
    }
}

// Initialize the admin class
Washi_Tape_Admin::get_instance();

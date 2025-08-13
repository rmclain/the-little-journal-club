<?php
namespace Washi_Tape_Attacher;

use Elementor\Controls_Manager;
use Elementor\Element_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Handles the integration with Elementor.
 * NOTE: This plugin is now DISABLED as the Washi Tape Generator will now be the single source of truth for all washi tape controls.
 * This file is kept for backward compatibility but no longer adds controls.
 */
class Elementor_Integration {

    private static $_instance = null;
    private static $washi_tapes_cache = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        // DISABLED: No longer registering controls to prevent conflicts
        // The Washi Tape Generator now provides unified controls for all washi tape functionality
        
        // Only keep the render functionality for existing content
        \add_action('elementor/frontend/before_render', [$this, 'before_render'], 10, 1);
        \add_action('elementor/frontend/after_render', [$this, 'after_render'], 10, 1);

        // Enqueue assets
        \add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_frontend_styles']);
    }

    /**
     * Fetches all washi tapes from the database and caches the result.
     */
    private function get_washi_tapes() {
        if (self::$washi_tapes_cache === null) {
            self::$washi_tapes_cache = [];
            if (class_exists('Washi_Tape_DB')) {
                $db = new \Washi_Tape_DB();
                $tapes = $db->get_all_washi_tapes();
                if (!empty($tapes)) {
                    foreach ($tapes as $tape) {
                        self::$washi_tapes_cache[$tape->id] = \esc_html($tape->title);
                    }
                }
            }
        }
        return ['0' => \__('— Select Tape —', 'washi-tape-attacher')] + self::$washi_tapes_cache;
    }

    /**
     * Register the controls in the Elementor panel.
     * DISABLED: This method no longer adds controls to prevent conflicts.
     */
    public function register_controls(Element_Base $element) {
        // DISABLED: Controls are now provided by Washi Tape Generator
        // This prevents duplicate controls and conflicts
        return;
    }

    /**
     * Before the element renders, add a class to its wrapper.
     */
    public function before_render(Element_Base $element) {
        $settings = $element->get_settings_for_display();
        if (isset($settings['wta_enable']) && $settings['wta_enable'] === 'yes' && !empty($settings['wta_tape_id']) && $settings['wta_tape_id'] !== '0') {
            $element->add_render_attribute('_wrapper', 'class', 'wta-element-wrapper');
        }
    }

    /**
     * After the element renders, output the washi tape HTML.
     */
    public function after_render(Element_Base $element) {
        $settings = $element->get_settings_for_display();
        if (isset($settings['wta_enable']) && $settings['wta_enable'] === 'yes' && !empty($settings['wta_tape_id']) && $settings['wta_tape_id'] !== '0') {
            
            $svg_content = '';
            if (class_exists('Washi_Tape_DB')) {
                $db = new \Washi_Tape_DB();
                $tape = $db->get_washi_tape($settings['wta_tape_id']);
                if ($tape && !empty($tape->svg)) {
                    $svg_content = stripslashes($tape->svg);
                }
            }

            if (empty($svg_content)) {
                return;
            }
            
            $position_class = 'wta-pos-' . \esc_attr($settings['wta_position']);

            // Output the tape div. The CSS selectors will handle positioning.
            \printf(
                '<div class="wta-tape-instance %s">%s</div>',
                $position_class,
                $svg_content
            );
        }
    }
    
    /**
     * Enqueue frontend styles.
     */
    public function enqueue_frontend_styles() {
        \wp_enqueue_style(
            'washi-tape-attacher-frontend',
            WASHI_TAPE_ATTACHER_URL . 'assets/css/frontend.css',
            [],
            WASHI_TAPE_ATTACHER_VERSION
        );
    }
}

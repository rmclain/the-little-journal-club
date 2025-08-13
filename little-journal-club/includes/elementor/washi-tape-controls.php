<?php
namespace LJC\Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add Washi Tape Controls to Elementor widgets
 * NOTE: This plugin is now DISABLED as the Washi Tape Generator will now be the single source of truth for all washi tape controls.
 * This file is kept for backward compatibility but no longer adds controls.
 */
class Washi_Tape_Controls
{

    /**
     * Initialize the class
     */
    public function __construct()
    {
        // DISABLED: No longer adding controls to prevent conflicts
        // The Washi Tape Generator now provides unified controls for all washi tape functionality
        
        // Only keep the render functionality for existing content
        \add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
        \add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        
        // Editor styles
        \add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles']);
        \add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_preview_styles']);
        
        // Content filter
        \add_filter('elementor/widget/render_content', [$this, 'apply_washi_tape'], 10, 2);

        // Add editor script loading
        \add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_preview_scripts']);
    }

    /**
     * Add Washi Tape controls to Elementor widgets
     * DISABLED: This method no longer adds controls to prevent conflicts.
     */
    public function add_washi_tape_controls($element, $section_id, $args)
    {
        // DISABLED: Controls are now provided by Washi Tape Generator
        // This prevents duplicate controls and conflicts
        return;
    }

    /**
     * Apply Washi Tape to widget content
     */
    public function apply_washi_tape($content, $widget)
    {
        $settings = $widget->get_settings_for_display();

        if (empty($settings['enable_washi_tape']) || $settings['enable_washi_tape'] !== 'yes') {
            return $content;
        }

        $tape_position = $settings['tape_position'];
        $tape_style = $settings['tape_style'];
        $tape_width = isset($settings['tape_width']['size']) ? $settings['tape_width']['size'] : 125;
        $tape_width .= isset($settings['tape_width']['unit']) ? $settings['tape_width']['unit'] : 'px';
        $tape_height = isset($settings['tape_height']['size']) ? $settings['tape_height']['size'] : 52;
        $tape_height .= 'px';
        $tape_rotation = isset($settings['tape_rotation']['size']) ? $settings['tape_rotation']['size'] : 33;
        $tape_rotation .= 'deg';
        $vertical_offset = isset($settings['tape_vertical_offset']['size']) ? $settings['tape_vertical_offset']['size'] : 25;
        $vertical_offset .= 'px';

        // Create unique ID for this instance
        $unique_id = 'ljc-washi-' . uniqid();

        // Handle custom image background
        $custom_bg_style = '';
        if ($tape_style === 'custom-image' && !empty($settings['tape_custom_image']['url'])) {
            $custom_bg_style = \sprintf(
                'background-image: url(%s); background-size: cover; background-position: center; background-repeat: no-repeat;',
                \esc_url($settings['tape_custom_image']['url'])
            );
        }

        // Start building the wrapper
        $output = '<div class="ljc-washi-tape-wrapper" id="' . \esc_attr($unique_id) . '" data-tape-enabled="true">';
        
        // Base tape styles
        $tape_style_attr = \sprintf(
            'width: %s; height: %s; margin-top: %s; %s',
            \esc_attr($tape_width),
            \esc_attr($tape_height),
            \esc_attr($vertical_offset),
            $custom_bg_style
        );

        // Function to create tape element
        $create_tape = function($position) use ($tape_style, $tape_style_attr) {
            $output = \sprintf(
                '<div class="ljc-tape ljc-tape-%s ljc-tape-%s" style="%s">',
                \esc_attr($position),
                \esc_attr($tape_style),
                $tape_style_attr
            );
            
            if ($tape_style === 'custom-image') {
                $output .= '<div class="ljc-tape-overlay"></div>';
            }
            
            $output .= '</div>';
            return $output;
        };

        // Add tape elements based on position
        if ($tape_position === 'both' || $tape_position === 'left') {
            $output .= $create_tape('left');
        }
        
        if ($tape_position === 'both' || $tape_position === 'right') {
            $output .= $create_tape('right');
        }
        
        if ($tape_position === 'center') {
            $output .= $create_tape('center');
        }

        // Add the content
        $output .= '<div class="ljc-washi-content">';
        $output .= $content;
        $output .= '</div>';
        
        $output .= '</div>';

        // Add custom CSS for this instance
        $custom_css = '<style>
            #' . $unique_id . ' {
                position: relative;
                z-index: 0;
            }
            #' . $unique_id . ' .ljc-tape {
                position: absolute;
                z-index: 2;
            }
            #' . $unique_id . ' .ljc-tape-left {
                transform: rotate(-' . $tape_rotation . ');
                left: -20px;
            }
            #' . $unique_id . ' .ljc-tape-right {
                transform: rotate(' . $tape_rotation . ');
                right: -20px;
            }
            #' . $unique_id . ' .ljc-tape-center {
                left: 50%;
                transform: translateX(-50%);
            }
            #' . $unique_id . ' .ljc-washi-content {
                position: relative;
                z-index: 1;
            }
        </style>';

        return $custom_css . $output;
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_styles()
    {
        \wp_enqueue_style(
            'ljc-washi-tape',
            LJC_PLUGIN_URL . 'assets/css/washi.css',
            [],
            LJC_VERSION
        );
    }

    /**
     * Enqueue editor styles
     */
    public function enqueue_editor_styles()
    {
        \wp_enqueue_style(
            'ljc-washi-tape-editor',
            LJC_PLUGIN_URL . 'assets/css/washi-editor.css',
            [],
            LJC_VERSION
        );
    }

    /**
     * Enqueue preview styles
     */
    public function enqueue_preview_styles()
    {
        \wp_enqueue_style(
            'ljc-washi-tape-preview',
            LJC_PLUGIN_URL . 'assets/css/washi-preview.css',
            [],
            LJC_VERSION
        );
        
        // Also enqueue main styles to ensure they're available in preview
        \wp_enqueue_style(
            'ljc-washi-tape',
            LJC_PLUGIN_URL . 'assets/css/washi.css',
            [],
            LJC_VERSION
        );
    }

    public function enqueue_preview_scripts()
    {
        \wp_enqueue_script(
            'ljc-washi-tape-preview',
            LJC_PLUGIN_URL . 'assets/js/washi-preview.js',
            ['jquery', 'elementor-frontend'],
            LJC_VERSION,
            true
        );
    }
}
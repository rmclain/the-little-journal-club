<?php

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Inset_Image_Box_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'inset_image_box';
    }

    public function get_title()
    {
        return __('Inset Image Box', 'little-journal-club');
    }

    public function get_icon()
    {
        return 'eicon-image-box';
    }

    public function get_categories()
    {
        return ['little-journal-club'];
    }


    protected function _register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'little-journal-club'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'image',
            [
                'label'   => __('Choose Image', 'little-journal-club'),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'padding',
            [
                'label'      => __('Padding', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 10,
                ],
            ]
        );

        $this->add_control(
            'frame_size',
            [
                'label'      => __('Frame Size', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%', 'px'],
                'range'      => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 5,
                ],
            ]
        );

        $this->add_control(
            'frame_width',
            [
                'label'      => __('Frame Width', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%', 'px'],
                'range'      => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 50,
                ],
            ]
        );

        $this->add_control(
            'frame_height',
            [
                'label'      => __('Frame Height', 'little-journal-club'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%', 'px'],
                'range'      => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 50,
                ],
            ]
        );

        $this->add_control(
            'frame_background_color',
            [
                'label'     => __('Frame Background Color', 'little-journal-club'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
            ]
        );

        $this->add_control(
            'image_link',
            [
                'label'       => __('Image Link', 'little-journal-club'),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'little-journal-club'),
                'default'     => [
                    'url' => '',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
?>
        <div class="inset-image-box" style="padding: <?php echo esc_attr($settings['padding']['size'] . $settings['padding']['unit']); ?>; border-width: <?php echo esc_attr($settings['frame_size']['size']); ?>px; background-color: <?php echo esc_attr($settings['frame_background_color']); ?>; width: <?php echo esc_attr($settings['frame_width']['size'] . $settings['frame_width']['unit']); ?>; height: <?php echo esc_attr($settings['frame_height']['size'] . $settings['frame_height']['unit']); ?>;">
            <a href="<?php echo esc_url($settings['image_link']['url']); ?>" target="<?php echo esc_attr($settings['image_link']['is_external'] ? '_blank' : '_self'); ?>">
                <img src="<?php echo esc_url($settings['image']['url']); ?>" alt="">
            </a>
        </div>
<?php
    }
}

function enqueue_inset_image_box_styles()
{
    wp_enqueue_style(
        'inset-image-box-style',
        plugin_dir_url(__FILE__) . 'assets/css/inset-image-box.css', // Adjust the path if needed.
        [],
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_inset_image_box_styles');

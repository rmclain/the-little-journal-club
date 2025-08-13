<?php
/**
 * TLJC Posts Widget
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class TLJC_Posts_Widget extends \Elementor\Widget_Base
{
    const VERSION = '1.0.0';

    public function get_name()
    {
        return 'tljc_posts';
    }

    public function get_title()
    {
        return __('TLJC Posts Collection', 'tljc-posts');
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['blog', 'posts', 'tljc', 'journal', 'archive'];
    }

    protected function get_washi_tape_options()
    {
        $options = [
            'none' => __('None', 'tljc-posts'),
        ];

        // Check if Washi Tape Generator plugin is active
        if (class_exists('Washi_Tape_DB')) {
            try {
                $db = new \Washi_Tape_DB();
                $washi_tapes = $db->get_all_washi_tapes();

                if (!empty($washi_tapes) && is_array($washi_tapes)) {
                    foreach ($washi_tapes as $tape) {
                        if (isset($tape->id) && isset($tape->title)) {
                            $options[$tape->id] = $tape->title;
                        }
                    }
                } else {
                    $options['create'] = __('Create washi tapes in admin first', 'tljc-posts');
                }
            } catch (Exception $e) {
                $options['error'] = __('Error loading washi tapes', 'tljc-posts');
            }
        } else {
            $options['install'] = __('Install Washi Tape Generator plugin', 'tljc-posts');
        }

        return $options;
    }

    /**
     * Register widget controls
     */
    protected function _register_controls()
    {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'tljc-posts'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 12,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columns', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => __('1 Column', 'tljc-posts'),
                    '2' => __('2 Columns', 'tljc-posts'),
                    '3' => __('3 Columns', 'tljc-posts'),
                    '4' => __('4 Columns', 'tljc-posts'),
                ],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Order By', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => __('Date', 'tljc-posts'),
                    'title' => __('Title', 'tljc-posts'),
                    'rand' => __('Random', 'tljc-posts'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => __('Descending', 'tljc-posts'),
                    'ASC' => __('Ascending', 'tljc-posts'),
                ],
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => __('Show Excerpt', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'tljc-posts'),
                'label_off' => __('Hide', 'tljc-posts'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label' => __('Excerpt Length', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 20,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_meta',
            [
                'label' => __('Show Meta', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'tljc-posts'),
                'label_off' => __('Hide', 'tljc-posts'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => __('Show Date', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'tljc-posts'),
                'label_off' => __('Hide', 'tljc-posts'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_read_more',
            [
                'label' => __('Show Read More', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'tljc-posts'),
                'label_off' => __('Hide', 'tljc-posts'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => __('Read More Text', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Read More', 'tljc-posts'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Washi Tape Section
        $this->start_controls_section(
            'washi_tape_section',
            [
                'label' => __('Washi Tape', 'tljc-posts'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_washi_tape',
            [
                'label' => __('Show Washi Tape', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'tljc-posts'),
                'label_off' => __('Hide', 'tljc-posts'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'washi_tape_style',
            [
                'label' => __('Washi Tape Style', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $this->get_washi_tape_options(),
                'condition' => [
                    'show_washi_tape' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'washi_tape_position',
            [
                'label' => __('Position', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'top-center',
                'options' => [
                    'top-center' => __('Top Center', 'tljc-posts'),
                    'top-left' => __('Top Left', 'tljc-posts'),
                    'top-right' => __('Top Right', 'tljc-posts'),
                    'bottom-center' => __('Bottom Center', 'tljc-posts'),
                    'bottom-left' => __('Bottom Left', 'tljc-posts'),
                    'bottom-right' => __('Bottom Right', 'tljc-posts'),
                ],
                'condition' => [
                    'show_washi_tape' => 'yes',
                    'washi_tape_style!' => '',
                ],
            ]
        );

        $this->add_control(
            'washi_tape_rotation',
            [
                'label' => __('Rotation', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => 0,
                ],
                'condition' => [
                    'show_washi_tape' => 'yes',
                    'washi_tape_style!' => '',
                ],
            ]
        );

        $this->add_control(
            'washi_tape_width',
            [
                'label' => __('Width', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 180,
                ],
                'condition' => [
                    'show_washi_tape' => 'yes',
                    'washi_tape_style!' => '',
                ],
            ]
        );

        $this->add_control(
            'washi_tape_height',
            [
                'label' => __('Height', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 5,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 45,
                ],
                'condition' => [
                    'show_washi_tape' => 'yes',
                    'washi_tape_style!' => '',
                ],
            ]
        );

        $this->add_control(
            'washi_tape_offset_x',
            [
                'label' => __('Horizontal Offset', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'condition' => [
                    'show_washi_tape' => 'yes',
                    'washi_tape_style!' => '',
                ],
            ]
        );

        $this->add_control(
            'washi_tape_offset_y',
            [
                'label' => __('Vertical Offset', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'condition' => [
                    'show_washi_tape' => 'yes',
                    'washi_tape_style!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'tljc-posts'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background_color',
            [
                'label' => __('Card Background Color', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tljc-post-card' => 'background-color: {{VALUE}}',
                ],
                'default' => '#ffffff',
            ]
        );

        $this->add_control(
            'card_padding',
            [
                'label' => __('Card Padding', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tljc-post-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '20',
                    'right' => '20',
                    'bottom' => '20',
                    'left' => '20',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => __('Card Border', 'tljc-posts'),
                'selector' => '{{WRAPPER}} .tljc-post-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tljc-post-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => __('Box Shadow', 'tljc-posts'),
                'selector' => '{{WRAPPER}} .tljc-post-card',
            ]
        );

        $this->add_control(
            'read_more_color',
            [
                'label' => __('Read More Button Color', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tljc-read-more' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tljc-read-more' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Heading Typography
        $this->add_control(
            'heading_style_heading',
            [
                'label' => __('Heading', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'heading_color',
            [
                'label' => __('Heading Color', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tljc-post-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => __('Heading Typography', 'tljc-posts'),
                'selector' => '{{WRAPPER}} .tljc-post-title',
            ]
        );
        // Body Typography
        $this->add_control(
            'body_style_heading',
            [
                'label' => __('Body', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'body_color',
            [
                'label' => __('Body Color', 'tljc-posts'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tljc-post-content' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'body_typography',
                'label' => __('Body Typography', 'tljc-posts'),
                'selector' => '{{WRAPPER}} .tljc-post-content',
            ]
        );

        $this->end_controls_section();

        // Add editor preview script
        add_action('elementor/editor/after_enqueue_scripts', function () {
            wp_enqueue_script(
                'tljc-posts-editor',
                plugin_dir_url(__FILE__) . '../assets/js/editor.js',
                ['jquery'],
                self::VERSION,
                true
            );
        });
    }

    /**
     * Render widget output on the frontend
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $columns = isset($settings['columns']) ? $settings['columns'] : '3';
        $container_class = 'tljc-posts-container tljc-posts-columns-' . $columns;

        // Query posts
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
        );

        $query = new \WP_Query($args);

        // Collect all tape IDs used in this widget
        $tape_ids = [];
        if ($settings['show_washi_tape'] === 'yes' && !empty($settings['washi_tape_style'])) {
            $tape_ids[] = intval($settings['washi_tape_style']);
        }

        // Output all unique washi tape templates at the top
        if (!empty($tape_ids)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'washi_tapes';
            foreach (array_unique($tape_ids) as $tape_id) {
                $tape = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $tape_id));
                if ($tape && !empty($tape->svg)) {
                    $svg = $tape->svg;
                    $svg = str_replace('\\&quot;', '"', $svg);
                    $svg = str_replace('&quot;', '"', $svg);
                    $svg = str_replace('\\"', '"', $svg);
                    $svg = preg_replace('/\s+/', ' ', $svg);
                    $svg = str_replace('" >', '">', $svg);
                    $svg = str_replace('> <', '><', $svg);
                    $svg = preg_replace('/style="[^"]*"/', '', $svg);
                    // Output hidden template
                    echo '<div class="washi-tape-template" id="washi-tape-' . esc_attr($tape_id) . '" style="display:none;">' . $svg . '</div>';
                }
            }
        }

        if ($query->have_posts()):
            ?>
            <div class="<?php echo esc_attr($container_class); ?>">
                <?php
                $i = 0;
                while ($query->have_posts()):
                    $query->the_post();
                    $i++;
                    $show_tape = $settings['show_washi_tape'] === 'yes' && !empty($settings['washi_tape_style']);
                    $tape_attrs = $show_tape ? 'data-tape-id="' . esc_attr($settings['washi_tape_style']) . '" '
                        . 'data-position="' . esc_attr($settings['washi_tape_position']) . '" '
                        . 'data-rotation="' . esc_attr(isset($settings['washi_tape_rotation']['size']) ? $settings['washi_tape_rotation']['size'] : 0) . '" '
                        . 'data-width="' . esc_attr(isset($settings['washi_tape_width']['size']) ? $settings['washi_tape_width']['size'] : 180) . '" '
                        . 'data-height="' . esc_attr(isset($settings['washi_tape_height']['size']) ? $settings['washi_tape_height']['size'] : 45) . '" '
                        . 'data-vertical-offset="' . esc_attr(isset($settings['washi_tape_offset_y']['size']) ? $settings['washi_tape_offset_y']['size'] : 0) . '" '
                        . 'data-horizontal-offset="' . esc_attr(isset($settings['washi_tape_offset_x']['size']) ? $settings['washi_tape_offset_x']['size'] : 0) . '"' : '';
                    ?>
                    <div class="tljc-posts-card-wrapper" style="position: relative;">
                        <?php if ($show_tape): ?>
                            <div class="tljc-washi-tape-element" <?php echo $tape_attrs; ?>></div>
                        <?php endif; ?>
                        <div class="tljc-post-card">
                            <div class="tljc-post-image">
                                <?php if (has_post_thumbnail()): ?>
                                    <?php the_post_thumbnail('large'); ?>
                                <?php endif; ?>
                            </div>
                            <div class="tljc-post-content">
                                <h2 class="tljc-post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <?php if ($settings['show_meta'] === 'yes' && $settings['show_date'] === 'yes'): ?>
                                    <div class="tljc-post-meta">
                                        <span class="tljc-post-date"><?php echo get_the_date(); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($settings['show_excerpt'] === 'yes'): ?>
                                    <div class="tljc-post-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), $settings['excerpt_length']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($settings['show_read_more'] === 'yes'): ?>
                                    <div class="tljc-post-link">
                                        <a href="<?php the_permalink(); ?>" class="tljc-read-more">
                                            <?php echo esc_html($settings['read_more_text']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
            <?php
        endif;
    }
}
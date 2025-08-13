<?php
/**
 * Plugin Name: Crafty Chic Navigation
 * Description: A beautiful, responsive navigation menu with crafty chic styling perfect for creative websites
 * Version: 1.0.0
 * Author: Kayla Brasher
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CraftyChicNavigation {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Enqueue styles and scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Add shortcode
        add_shortcode('crafty_nav', array($this, 'render_navigation'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // AJAX handlers for cart count
        add_action('wp_ajax_get_cart_count', array($this, 'ajax_get_cart_count'));
        add_action('wp_ajax_nopriv_get_cart_count', array($this, 'ajax_get_cart_count'));
        
        // Register Elementor widget if Elementor is active
        if (class_exists('\Elementor\Plugin')) {
            add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widget'));
        }
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('crafty-nav-style', plugin_dir_url(__FILE__) . 'assets/style.css', array(), '1.0.0');
        wp_enqueue_script('crafty-nav-script', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), '1.0.0', true);
    }
    
    public function render_navigation($atts) {
        $atts = shortcode_atts(array(
            'menu_name' => '',
            'style' => 'minimal',
            'show_cart' => 'true'
        ), $atts);
        
        $menu_items = $this->get_menu_items($atts['menu_name']);
        
        ob_start();
        ?>
        <nav class="crafty-nav-container" data-style="<?php echo esc_attr($atts['style']); ?>">
            <div class="crafty-nav-wrapper">
                <ul class="crafty-nav-menu">
                    <?php foreach ($menu_items as $item) : ?>
                        <li class="crafty-nav-item <?php echo $item['has_children'] ? 'has-dropdown' : ''; ?>">
                            <a href="<?php echo esc_url($item['url']); ?>" class="crafty-nav-link">
                                <?php echo esc_html($item['title']); ?>
                                <?php if ($item['has_children']) : ?>
                                    <span class="crafty-nav-arrow">▾</span>
                                <?php endif; ?>
                            </a>
                            
                            <?php if ($item['has_children']) : ?>
                                <ul class="crafty-nav-dropdown">
                                    <?php foreach ($item['children'] as $child) : ?>
                                        <li class="crafty-nav-dropdown-item">
                                            <a href="<?php echo esc_url($child['url']); ?>" class="crafty-nav-dropdown-link">
                                                <?php echo esc_html($child['title']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="crafty-nav-actions">
                    <?php if ($atts['show_cart'] === 'true') : ?>
                        <a href="<?php echo $this->get_cart_url(); ?>" class="crafty-nav-cart">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                            <span class="crafty-nav-cart-count"><?php echo $this->get_cart_count(); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mobile Toggle Bar -->
            <div class="crafty-nav-mobile-bar">
                <button class="crafty-nav-toggle" aria-label="Toggle navigation">
                    <span class="crafty-nav-toggle-line"></span>
                    <span class="crafty-nav-toggle-line"></span>
                    <span class="crafty-nav-toggle-line"></span>
                </button>
            </div>
        </nav>
        <?php
        return ob_get_clean();
    }
    
    private function get_menu_items($menu_name) {
        // Try to get the specified menu first
        $menu = null;
        
        if (!empty($menu_name)) {
            // Try by slug first, then by name
            $menu = wp_get_nav_menu_object($menu_name);
            if (!$menu) {
                // Try by name if slug didn't work
                $menus = wp_get_nav_menus();
                foreach ($menus as $menu_obj) {
                    if ($menu_obj->name === $menu_name || $menu_obj->slug === $menu_name) {
                        $menu = $menu_obj;
                        break;
                    }
                }
            }
        }
        
        // If no specific menu found, try to get primary menu location
        if (!$menu) {
            $locations = get_nav_menu_locations();
            if (isset($locations['primary'])) {
                $menu = wp_get_nav_menu_object($locations['primary']);
            }
        }
        
        // If still no menu, try to get the first available menu
        if (!$menu) {
            $menus = wp_get_nav_menus();
            if (!empty($menus)) {
                $menu = $menus[0];
            }
        }
        
        // If we have a menu, format it
        if ($menu) {
            $menu_items = wp_get_nav_menu_items($menu);
            if ($menu_items) {
                return $this->format_menu_items($menu_items);
            }
        }
        
        // Fallback to default menu items with working URLs
        return array(
            array('title' => 'Home', 'url' => home_url('/'), 'has_children' => false),
            array('title' => 'Shop', 'url' => home_url('/shop/'), 'has_children' => true, 'children' => array(
                array('title' => 'All Products', 'url' => home_url('/shop/')),
                array('title' => 'Journals', 'url' => home_url('/product-category/journals/')),
                array('title' => 'Stickers', 'url' => home_url('/product-category/stickers/')),
                array('title' => 'Washi Tape', 'url' => home_url('/product-category/washi-tape/')),
                array('title' => 'Gift Sets', 'url' => home_url('/product-category/gift-sets/'))
            )),
            array('title' => 'Blog', 'url' => home_url('/blog/'), 'has_children' => false),
            array('title' => 'About', 'url' => home_url('/about/'), 'has_children' => false),
            array('title' => 'Contact', 'url' => home_url('/contact/'), 'has_children' => false)
        );
    }
    
    private function format_menu_items($menu_items) {
        if (!$menu_items || !is_array($menu_items)) return array();
        
        $formatted = array();
        $parent_items = array();
        $child_items = array();
        
        // Separate parent and child items
        foreach ($menu_items as $item) {
            if ($item->menu_item_parent == 0) {
                $parent_items[$item->ID] = array(
                    'title' => $item->title,
                    'url' => $item->url,
                    'has_children' => false,
                    'children' => array(),
                    'menu_order' => $item->menu_order
                );
            } else {
                $child_items[] = array(
                    'parent_id' => $item->menu_item_parent,
                    'title' => $item->title,
                    'url' => $item->url,
                    'menu_order' => $item->menu_order
                );
            }
        }
        
        // Assign children to parents
        foreach ($child_items as $child) {
            if (isset($parent_items[$child['parent_id']])) {
                $parent_items[$child['parent_id']]['has_children'] = true;
                $parent_items[$child['parent_id']]['children'][] = array(
                    'title' => $child['title'],
                    'url' => $child['url'],
                    'menu_order' => $child['menu_order']
                );
            }
        }
        
        // Sort children by menu order
        foreach ($parent_items as &$parent) {
            if ($parent['has_children']) {
                usort($parent['children'], function($a, $b) {
                    return $a['menu_order'] - $b['menu_order'];
                });
            }
        }
        
        // Sort parent items by menu order and return
        uasort($parent_items, function($a, $b) {
            return $a['menu_order'] - $b['menu_order'];
        });
        
        return array_values($parent_items);
    }
    
    private function get_cart_url() {
        if (function_exists('wc_get_cart_url')) {
            return wc_get_cart_url();
        }
        return home_url('/cart/');
    }
    
    private function get_cart_count() {
        if (function_exists('WC') && WC()->cart) {
            return WC()->cart->get_cart_contents_count();
        }
        return 0;
    }
    
    public function ajax_get_cart_count() {
        wp_send_json_success(array(
            'count' => $this->get_cart_count()
        ));
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Crafty Navigation Settings',
            'Crafty Navigation',
            'manage_options',
            'crafty-nav-settings',
            array($this, 'admin_page')
        );
    }
    
    public function admin_page() {
        // Get available menus for display
        $menus = wp_get_nav_menus();
        $menu_list = '';
        if (!empty($menus)) {
            foreach ($menus as $menu) {
                $menu_list .= '<li><code>[crafty_nav menu_name="' . $menu->slug . '"]</code> - ' . $menu->name . '</li>';
            }
        } else {
            $menu_list = '<li>No menus found. <a href="' . admin_url('nav-menus.php') . '">Create a menu first</a>.</li>';
        }
        
        ?>
        <div class="wrap">
            <h1>Crafty Chic Navigation Settings</h1>
            <p>Use the shortcode <code>[crafty_nav]</code> to display your navigation menu.</p>
            <p>For Elementor, search for "Crafty Navigation" in the widget panel.</p>
            
            <h2>Shortcode Options</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Basic Usage</th>
                    <td><code>[crafty_nav]</code></td>
                </tr>
                <tr>
                    <th scope="row">Style Options</th>
                    <td>
                        <code>[crafty_nav style="default"]</code> - Full decorative style<br>
                        <code>[crafty_nav style="minimal"]</code> - Clean minimal style
                    </td>
                </tr>
                <tr>
                    <th scope="row">Cart Options</th>
                    <td>
                        <code>[crafty_nav show_cart="true"]</code> - Show cart (default)<br>
                        <code>[crafty_nav show_cart="false"]</code> - Hide cart
                    </td>
                </tr>
                <tr>
                    <th scope="row">Available Menus</th>
                    <td>
                        <ul>
                            <?php echo $menu_list; ?>
                        </ul>
                    </td>
                </tr>
            </table>
            
            <h2>Features</h2>
            <ul>
                <li>✅ WordPress menu integration with dropdown selection</li>
                <li>✅ Mobile responsive with full-width hamburger bar</li>
                <li>✅ WooCommerce cart integration with live count updates</li>
                <li>✅ Dropdown support for sub-categories</li>
                <li>✅ Meloso font family for minimal style</li>
                <li>✅ Custom mobile background color (#FBF2F1)</li>
                <li>✅ Accessibility features and keyboard navigation</li>
                <li>✅ Elementor widget with menu selection dropdown</li>
            </ul>
            
            <h2>Elementor Widget</h2>
            <p>When using the Elementor widget, you can:</p>
            <ul>
                <li><strong>Select Menu:</strong> Choose from any WordPress menu you've created</li>
                <li><strong>Choose Style:</strong> Toggle between Default and Minimal styles</li>
                <li><strong>Toggle Cart:</strong> Show/hide the shopping cart icon</li>
            </ul>
            
            <h2>Creating Menus</h2>
            <p>To create or edit menus, go to <a href="<?php echo admin_url('nav-menus.php'); ?>">Appearance → Menus</a> in your WordPress admin.</p>
            
            <h2>Mobile Menu</h2>
            <p>On mobile devices:</p>
            <ul>
                <li>Cart icon appears in top area</li>
                <li>Full-width hamburger bar appears below cart</li>
                <li>Menu slides down with custom #FBF2F1 background</li>
                <li>Smooth animations and transitions</li>
            </ul>
        </div>
        <?php
    }
    
    public function register_elementor_widget() {
        // Only register if Elementor is properly loaded
        if (!class_exists('\Elementor\Widget_Base')) {
            return;
        }
        
        // Define the widget class inline to avoid loading issues
        $widget_class = new class extends \Elementor\Widget_Base {
            
            public function get_name() {
                return 'crafty_navigation';
            }
            
            public function get_title() {
                return 'Crafty Navigation';
            }
            
            public function get_icon() {
                return 'eicon-nav-menu';
            }
            
            public function get_categories() {
                return ['general'];
            }
            
            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    [
                        'label' => 'Navigation Settings',
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]
                );
                
                $this->add_control(
                    'menu_style',
                    [
                        'label' => 'Style',
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'minimal',
                        'options' => [
                            'default' => 'Default',
                            'minimal' => 'Minimal',
                        ],
                    ]
                );
                
                // Get all WordPress menus
                $menus = wp_get_nav_menus();
                $menu_options = [];
                $menu_options[''] = 'Select a menu...';
                
                foreach ($menus as $menu) {
                    $menu_options[$menu->slug] = $menu->name;
                }
                
                $this->add_control(
                    'selected_menu',
                    [
                        'label' => 'Select Menu',
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => '',
                        'options' => $menu_options,
                        'description' => 'Choose which WordPress menu to display. Create menus in Appearance > Menus.',
                    ]
                );
                
                $this->add_control(
                    'show_cart',
                    [
                        'label' => 'Show Cart',
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => 'Show',
                        'label_off' => 'Hide',
                        'return_value' => 'true',
                        'default' => 'true',
                    ]
                );
                
                $this->end_controls_section();
            }
            
            protected function render() {
                $settings = $this->get_settings_for_display();
                $shortcode_atts = [
                    'style="' . $settings['menu_style'] . '"',
                    'show_cart="' . $settings['show_cart'] . '"'
                ];
                
                if (!empty($settings['selected_menu'])) {
                    $shortcode_atts[] = 'menu_name="' . $settings['selected_menu'] . '"';
                }
                
                echo do_shortcode('[crafty_nav ' . implode(' ', $shortcode_atts) . ']');
            }
        };
        
        \Elementor\Plugin::instance()->widgets_manager->register($widget_class);
    }
}

// Initialize the plugin
new CraftyChicNavigation();
?>
This file is a merged representation of the entire codebase, combined into a single document by Repomix.

# File Summary

## Purpose
This file contains a packed representation of the entire repository's contents.
It is designed to be easily consumable by AI systems for analysis, code review,
or other automated processes.

## File Format
The content is organized as follows:
1. This summary section
2. Repository information
3. Directory structure
4. Repository files (if enabled)
5. Multiple file entries, each consisting of:
  a. A header with the file path (## File: path/to/file)
  b. The full contents of the file in a code block

## Usage Guidelines
- This file should be treated as read-only. Any changes should be made to the
  original repository files, not this packed version.
- When processing this file, use the file path to distinguish
  between different files in the repository.
- Be aware that this file may contain sensitive information. Handle it with
  the same level of security as you would the original repository.

## Notes
- Some files may have been excluded based on .gitignore rules and Repomix's configuration
- Binary files are not included in this packed representation. Please refer to the Repository Structure section for a complete list of file paths, including binary files
- Files matching patterns in .gitignore are excluded
- Files matching default ignore patterns are excluded
- Files are sorted by Git change count (files with more changes are at the bottom)

# Directory Structure
```
assets/
  css/
    scrapbook-shop.css
widgets/
  scrapbook-product-card.php
  scrapbook-products-grid.php
scrapbook-shop.php
```

# Files

## File: assets/css/scrapbook-shop.css
```css
/**
 * Scrapbook Shop for Elementor - Main Styles
 * Replicates the polaroid/scrapbook aesthetic from the original theme
 */

/* ========================================
   CSS Custom Properties (Variables)
   ======================================== */
:root {
  /* Colors - matching original theme */
  --scrapbook-primary: #2C2C2C;
  --scrapbook-secondary: #B58C67;
  --scrapbook-accent: #91A4BA;
  --scrapbook-text: #484848;
  --scrapbook-text-light: #7D7D7D;
  --scrapbook-text-lighter: #5D5D5D;
  
  /* Background Colors */
  --scrapbook-bg-card: #FAF2F1;
  --scrapbook-bg-card-hover: #F5EDE8;
  --scrapbook-bg-white: #ffffff;
  
  /* Border Colors */
  --scrapbook-border-primary: #EFE5DA;
  --scrapbook-border-secondary: #DDCABE;
  
  /* Shadows */
  --scrapbook-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
  --scrapbook-shadow-md: 0 4px 8px rgba(0, 0, 0, 0.07);
  --scrapbook-shadow-lg: 0 10px 20px rgba(0, 0, 0, 0.12);
  --scrapbook-shadow-focus: 0 0 0 3px rgba(181, 140, 103, 0.1);
  
  /* Spacing */
  --scrapbook-space-xs: clamp(0.25rem, 1vw, 0.5rem);
  --scrapbook-space-sm: clamp(0.5rem, 2vw, 1rem);
  --scrapbook-space-md: clamp(1rem, 3vw, 2rem);
  --scrapbook-space-lg: clamp(2rem, 5vw, 3rem);
  --scrapbook-space-xl: clamp(3rem, 8vw, 5rem);
}

/* ========================================
   ARCHIVE HEADER
   ======================================== */
.scrapbook-archive-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: var(--scrapbook-space-md);
  margin-bottom: var(--scrapbook-space-lg);
  padding: var(--scrapbook-space-md) 0;
  border-bottom: none;
}

.scrapbook-archive-title {
  font-family: 'Meloso', sans-serif;
  font-size: clamp(2rem, 6vw, 3.5rem);
  color: var(--scrapbook-primary);
  font-weight: 400;
  margin: 0;
  padding: 0;
  line-height: 1.2;
  letter-spacing: -0.02em;
  flex: 1;
}

.scrapbook-sorting {
  flex-shrink: 0;
}

/* Mobile header layout */
@media (max-width: 767px) {
  .scrapbook-archive-header {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--scrapbook-space-sm);
  }
  
  .scrapbook-sorting {
    align-self: flex-end;
  }
}

/* ========================================
   PRODUCTS GRID
   ======================================== */
.scrapbook-products-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 75px 25px;
  padding: 0;
  margin: 25px 0 30px 0;
  width: 100%;
  box-sizing: border-box;
}

/* Responsive grid */
@media (max-width: 480px) {
  .scrapbook-products-grid {
    grid-template-columns: 1fr;
    gap: 35px 10px;
    margin: 15px 0 20px 0;
  }
}

@media (min-width: 481px) and (max-width: 767px) {
  .scrapbook-products-grid {
    grid-template-columns: 1fr;
    gap: 45px 15px;
  }
}

@media (min-width: 768px) and (max-width: 991px) {
  .scrapbook-products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 60px 20px;
  }
}

@media (min-width: 1400px) {
  .scrapbook-products-grid {
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
  }
}

/* ========================================
   PRODUCT CARD
   ======================================== */
.scrapbook-product-card {
  background-color: var(--scrapbook-bg-card);
  border: 1px solid var(--scrapbook-border-primary);
  border-radius: 4px;
  padding: 0 15px 15px 15px;
  text-align: center;
  box-shadow: var(--scrapbook-shadow-md);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
              box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: visible;
  height: 100%;
  min-height: 400px;
}

.scrapbook-product-card-inner,
.product-card-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  width: 100%;
}

/* Hover effects */
@media (hover: hover) and (pointer: fine) {
  .scrapbook-product-card:hover {
    transform: translateY(-5px) rotate(-1.5deg);
    box-shadow: var(--scrapbook-shadow-lg);
  }
}

/* Touch feedback for mobile */
@media (hover: none) and (pointer: coarse) {
  .scrapbook-product-card:active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
  }
}

/* ========================================
   PRODUCT IMAGE & WASHI TAPE
   ======================================== */
.product-image-wrapper {
  position: relative;
  margin-bottom: 20px;
  padding-top: 20px;
}

/* Polaroid Frame */
.polaroid-frame {
  position: relative;
  background-color: var(--scrapbook-bg-white);
  padding: 10px;
  padding-bottom: 25px;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
  border-radius: 3px;
  margin-left: auto;
  margin-right: auto;
  max-width: calc(100% - 20px);
  z-index: 1;
}

/* Washi Tape Decoration */
.washi-tape-decoration {
  position: absolute;
  top: -30px;
  left: 50%;
  transform: translateX(-50%) rotate(0deg);
  width: 120px;
  height: 45px;
  z-index: 2;
  filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.15));
}

.washi-tape-decoration svg {
  width: 100%;
  height: 100%;
}

/* Enhanced mobile washi tape sizing */
@media (max-width: 480px) {
  .washi-tape-decoration {
    width: 110px;
    height: 44px;
    top: -32px;
  }
}

/* Product image inside polaroid */
.polaroid-frame img {
  max-width: 100%;
  height: auto;
  display: block;
  border-radius: 2px;
  object-fit: cover;
  width: 100%;
}

/* ========================================
   SALE BADGE
   ======================================== */
.scrapbook-product-card .onsale {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: #F8C885;
  color: var(--scrapbook-bg-white);
  width: 20%;
  max-width: 60px;
  min-width: 40px;
  aspect-ratio: 1 / 1;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  z-index: 3;
  line-height: 1;
  font-weight: bold;
  text-transform: uppercase;
}

/* ========================================
   PRODUCT DETAILS
   ======================================== */
.product-details {
  margin-bottom: 15px;
  flex-grow: 1;
  padding: 0 5px;
}

.product-title {
  font-size: clamp(0.9rem, 3vw, 1rem);
  color: var(--scrapbook-text);
  margin-bottom: 8px;
  font-weight: 600;
  line-height: 1.3;
  word-wrap: break-word;
  hyphens: auto;
}

.product-title a {
  color: var(--scrapbook-text);
  text-decoration: none;
  display: block;
  padding: 4px;
  transition: color 0.2s ease;
}

.product-title a:hover,
.product-title a:focus {
  color: var(--scrapbook-secondary);
  outline: none;
}

.product-title a:focus-visible {
  outline: 2px solid var(--scrapbook-secondary);
  outline-offset: 2px;
}

/* Price styling */
.product-details .price {
  font-size: 0.95rem;
  color: #000000;
  font-weight: bold;
  display: block;
  margin-bottom: 8px;
}

/* Struck-through original prices */
.product-details .price del {
  color: #666666;
  font-weight: normal;
}

/* Star rating */
.product-details .star-rating {
  margin: 0 auto 10px auto;
}

/* ========================================
   PRODUCT ACTIONS
   ======================================== */
.product-actions {
  margin-top: auto;
  padding: 0 10px;
}

.product-actions .button,
.product-actions .add_to_cart_button {
  background-color: var(--scrapbook-accent);
  color: var(--scrapbook-bg-white);
  text-transform: capitalize;
  padding: 10px 18px;
  text-decoration: none;
  border-radius: 4px;
  display: inline-block;
  font-size: 0.9rem;
  border: 1px solid var(--scrapbook-border-secondary);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-weight: 500;
  box-shadow: var(--scrapbook-shadow-sm);
  line-height: normal;
  margin-bottom: 0;
  min-height: 44px;
  width: 100%;
  max-width: 250px;
  cursor: pointer;
}

/* Hover effects */
@media (hover: hover) and (pointer: fine) {
  .product-actions .button:hover,
  .product-actions .add_to_cart_button:hover {
    background-color: #DDB0A7;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    color: var(--scrapbook-bg-white);
  }
}

/* Focus styles */
.product-actions .button:focus,
.product-actions .add_to_cart_button:focus {
  outline: none;
  box-shadow: var(--scrapbook-shadow-focus), var(--scrapbook-shadow-sm);
}

.product-actions .button:focus-visible,
.product-actions .add_to_cart_button:focus-visible {
  outline: 2px solid var(--scrapbook-secondary);
  outline-offset: 2px;
}

/* Touch feedback */
@media (hover: none) and (pointer: coarse) {
  .product-actions .button:active,
  .product-actions .add_to_cart_button:active {
    background-color: #DDB0A7;
    transform: scale(0.98);
  }
}

.product-actions .added_to_cart {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 5px;
  font-size: 0.85rem;
  color: var(--scrapbook-secondary);
  text-decoration: none;
  min-height: 44px;
  transition: color 0.2s ease;
}

.product-actions .added_to_cart:hover,
.product-actions .added_to_cart:focus {
  color: var(--scrapbook-text);
}

/* ========================================
   SORTING DROPDOWN
   ======================================== */
.scrapbook-ordering {
  margin: 0;
  padding: 0;
  position: relative;
  z-index: 5;
}

.scrapbook-ordering::before {
  content: "Sort by:";
  font-family: 'Meloso', sans-serif;
  font-size: 1.1rem;
  color: var(--scrapbook-text-light);
  margin-right: 12px;
  font-weight: 400;
}

.scrapbook-ordering select {
  background-color: var(--scrapbook-bg-card);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23B58C67' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
  border: 2px solid var(--scrapbook-border-primary);
  border-radius: 12px;
  padding: 12px 45px 12px 16px;
  color: var(--scrapbook-text-lighter);
  font-size: 1rem;
  font-family: inherit;
  font-weight: 500;
  box-shadow: 0 3px 8px rgba(181, 140, 103, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.7);
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  appearance: none;
  min-height: 48px;
  min-width: 180px;
  transform: rotate(-0.5deg);
}

.scrapbook-ordering select:hover {
  border-color: var(--scrapbook-border-secondary);
  background-color: var(--scrapbook-bg-card-hover);
  box-shadow: 0 4px 12px rgba(181, 140, 103, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.9);
  transform: rotate(0deg) translateY(-1px);
}

.scrapbook-ordering select:focus {
  outline: none;
  border-color: var(--scrapbook-secondary);
  box-shadow: var(--scrapbook-shadow-focus), 0 4px 12px rgba(181, 140, 103, 0.25);
  transform: rotate(0deg);
}

.scrapbook-ordering select:focus-visible {
  outline: 2px solid var(--scrapbook-secondary);
  outline-offset: 2px;
}

/* ========================================
   PAGINATION
   ======================================== */
.scrapbook-pagination {
  margin-top: var(--scrapbook-space-lg);
  text-align: center;
}

.scrapbook-pagination .page-numbers {
  display: inline-block;
  padding: 8px 12px;
  margin: 0 4px;
  background-color: var(--scrapbook-bg-card);
  border: 1px solid var(--scrapbook-border-primary);
  border-radius: 4px;
  color: var(--scrapbook-text);
  text-decoration: none;
  transition: all 0.2s ease;
}

.scrapbook-pagination .page-numbers:hover {
  background-color: var(--scrapbook-secondary);
  color: var(--scrapbook-bg-white);
  border-color: var(--scrapbook-secondary);
}

.scrapbook-pagination .page-numbers.current {
  background-color: var(--scrapbook-accent);
  color: var(--scrapbook-bg-white);
  border-color: var(--scrapbook-accent);
}

/* ========================================
   NO PRODUCTS MESSAGE
   ======================================== */
.scrapbook-no-products {
  text-align: center;
  padding: var(--scrapbook-space-xl);
  color: var(--scrapbook-text-light);
  font-size: 1.1rem;
}

/* ========================================
   RESULT COUNT
   ======================================== */
.scrapbook-result-count {
  color: var(--scrapbook-text-light);
  font-size: 0.9rem;
  margin-bottom: var(--scrapbook-space-md);
}

/* ========================================
   LOADING STATE
   ======================================== */
.scrapbook-loading {
  text-align: center;
  padding: var(--scrapbook-space-lg);
}

.scrapbook-loading::after {
  content: '';
  display: inline-block;
  width: 30px;
  height: 30px;
  border: 3px solid var(--scrapbook-border-primary);
  border-top-color: var(--scrapbook-secondary);
  border-radius: 50%;
  animation: scrapbook-spin 1s linear infinite;
}

@keyframes scrapbook-spin {
  to {
    transform: rotate(360deg);
  }
}

/* ========================================
   REDUCED MOTION PREFERENCES
   ======================================== */
@media (prefers-reduced-motion: reduce) {
  .scrapbook-product-card,
  .product-actions .button,
  .scrapbook-ordering select {
    transition: none;
  }
  
  .scrapbook-product-card:hover {
    transform: none;
  }
  
  .scrapbook-loading::after {
    animation: none;
  }
}
```

## File: widgets/scrapbook-product-card.php
```php
<?php

/**
 * Scrapbook Product Card Widget
 * 
 * Custom Elementor widget that creates a scrapbook-style product card
 * with integrated washi tape support
 */

namespace Scrapbook_Shop\Widgets;

if (!defined('ABSPATH')) {
    exit;
}

class Scrapbook_Product_Card extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'scrapbook_product_card';
    }

    public function get_title()
    {
        return esc_html__('Scrapbook Product Card', 'scrapbook-shop');
    }

    public function get_icon()
    {
        return 'eicon-product-images';
    }

    public function get_categories()
    {
        return ['woocommerce-elements'];
    }

    public function get_keywords()
    {
        return ['product', 'woocommerce', 'scrapbook', 'polaroid', 'washi tape'];
    }

    protected function register_controls()
    {

        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Content', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'product_id',
            [
                'label' => esc_html__('Product', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_products_list(),
                'label_block' => true,
                'description' => esc_html__('Leave empty to use in loop', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => esc_html__('Show Price', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => esc_html__('Show Rating', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_add_to_cart',
            [
                'label' => esc_html__('Show Add to Cart', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_sale_badge',
            [
                'label' => esc_html__('Show Sale Badge', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Washi Tape Section
        $this->start_controls_section(
            'section_washi_tape',
            [
                'label' => esc_html__('Washi Tape', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'use_washi_tape',
            [
                'label' => esc_html__('Use Washi Tape', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'washi_tape_source',
            [
                'label' => esc_html__('Washi Tape Source', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'plugin',
                'options' => [
                    'plugin' => esc_html__('Washi Tape Plugin', 'scrapbook-shop'),
                    'image' => esc_html__('Custom Image', 'scrapbook-shop'),
                    'random' => esc_html__('Random from Plugin', 'scrapbook-shop'),
                ],
                'condition' => [
                    'use_washi_tape' => 'yes',
                ],
            ]
        );

        // If Washi Tape plugin is active, show tape selector
        if (class_exists('Washi_Tape_DB')) {
            $this->add_control(
                'washi_tape_id',
                [
                    'label' => esc_html__('Select Washi Tape', 'scrapbook-shop'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $this->get_washi_tapes_list(),
                    'condition' => [
                        'use_washi_tape' => 'yes',
                        'washi_tape_source' => 'plugin',
                    ],
                ]
            );
        }

        $this->add_control(
            'washi_tape_image',
            [
                'label' => esc_html__('Washi Tape Image', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'condition' => [
                    'use_washi_tape' => 'yes',
                    'washi_tape_source' => 'image',
                ],
            ]
        );

        $this->add_control(
            'washi_tape_rotation',
            [
                'label' => esc_html__('Tape Rotation', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => -15,
                        'max' => 15,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => 0,
                ],
                'condition' => [
                    'use_washi_tape' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Card
        $this->start_controls_section(
            'section_style_card',
            [
                'label' => esc_html__('Card Style', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Background Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FAF2F1',
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_color',
            [
                'label' => esc_html__('Border Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#EFE5DA',
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_rotate',
            [
                'label' => esc_html__('Hover Rotation', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => -5,
                        'max' => 5,
                        'step' => 0.5,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => -1.5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-product-card:hover' => 'transform: translateY(-5px) rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_shadow',
                'selector' => '{{WRAPPER}} .scrapbook-product-card',
            ]
        );

        $this->end_controls_section();

        // Style Section - Polaroid Frame
        $this->start_controls_section(
            'section_style_polaroid',
            [
                'label' => esc_html__('Polaroid Frame', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'polaroid_background',
            [
                'label' => esc_html__('Frame Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .polaroid-frame' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'polaroid_padding',
            [
                'label' => esc_html__('Frame Padding', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 25,
                    'left' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .polaroid-frame' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Typography
        $this->start_controls_section(
            'section_typography',
            [
                'label' => esc_html__('Typography', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title', 'scrapbook-shop'),
                'selector' => '{{WRAPPER}} .product-title',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => esc_html__('Price', 'scrapbook-shop'),
                'selector' => '{{WRAPPER}} .price',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Get product
        $product = $this->get_product($settings);

        if (!$product) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="elementor-alert elementor-alert-info">' .
                    esc_html__('Please select a product or use this widget in a loop.', 'scrapbook-shop') .
                    '</div>';
            }
            return;
        }

        // Get washi tape
        $washi_tape_html = $this->get_washi_tape_html($settings);

?>
        <div class="scrapbook-product-card">
            <div class="product-card-inner">
                <div class="product-image-wrapper">
                    <?php if ($washi_tape_html): ?>
                        <div class="washi-tape-decoration" style="transform: rotate(<?php echo esc_attr($settings['washi_tape_rotation']['size']); ?>deg);">
                            <?php echo $washi_tape_html; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($product->is_on_sale() && $settings['show_sale_badge'] === 'yes'): ?>
                        <span class="onsale"><?php esc_html_e('Sale!', 'scrapbook-shop'); ?></span>
                    <?php endif; ?>

                    <div class="polaroid-frame">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                            <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                        </a>
                    </div>
                </div>

                <div class="product-details">
                    <?php if ($settings['show_title'] === 'yes'): ?>
                        <h3 class="product-title">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                        </h3>
                    <?php endif; ?>

                    <?php if ($settings['show_rating'] === 'yes'): ?>
                        <?php woocommerce_template_loop_rating(); ?>
                    <?php endif; ?>

                    <?php if ($settings['show_price'] === 'yes'): ?>
                        <div class="price"><?php echo $product->get_price_html(); ?></div>
                    <?php endif; ?>
                </div>

                <?php if ($settings['show_add_to_cart'] === 'yes'): ?>
                    <div class="product-actions">
                        <?php woocommerce_template_loop_add_to_cart(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php
    }

    /**
     * Get product for the widget
     */
    private function get_product($settings)
    {
        global $product;

        // If specific product is selected
        if (!empty($settings['product_id'])) {
            return wc_get_product($settings['product_id']);
        }

        // If in loop, use current product
        if ($product instanceof \WC_Product) {
            return $product;
        }

        // Try to get from post
        $post_id = get_the_ID();
        if ($post_id && get_post_type($post_id) === 'product') {
            return wc_get_product($post_id);
        }

        return false;
    }

    /**
     * Get washi tape HTML
     */
    private function get_washi_tape_html($settings)
    {
        if ($settings['use_washi_tape'] !== 'yes') {
            return '';
        }

        $html = '';

        switch ($settings['washi_tape_source']) {
            case 'plugin':
                if (class_exists('Washi_Tape_DB') && !empty($settings['washi_tape_id'])) {
                    $db = new \Washi_Tape_DB();
                    $tape = $db->get_washi_tape($settings['washi_tape_id']);
                    if ($tape && !empty($tape->svg)) {
                        $html = $tape->svg;
                    }
                }
                break;

            case 'random':
                if (class_exists('Washi_Tape_DB')) {
                    $db = new \Washi_Tape_DB();
                    $tapes = $db->get_all_washi_tapes();
                    if (!empty($tapes)) {
                        $random_tape = $tapes[array_rand($tapes)];
                        $html = $random_tape->svg;
                    }
                }
                break;

            case 'image':
                if (!empty($settings['washi_tape_image']['url'])) {
                    $html = '<img src="' . esc_url($settings['washi_tape_image']['url']) . '" alt="Washi Tape">';
                }
                break;
        }

        return $html;
    }

    /**
     * Get list of products for selector
     */
    private function get_products_list()
    {
        $products = wc_get_products([
            'limit' => -1,
            'orderby' => 'name',
            'order' => 'ASC',
            'return' => 'ids',
        ]);

        $options = ['' => esc_html__('None (Use in Loop)', 'scrapbook-shop')];

        foreach ($products as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                $options[$product_id] = $product->get_name();
            }
        }

        return $options;
    }

    /**
     * Get list of washi tapes from plugin
     */
    private function get_washi_tapes_list()
    {
        $options = ['' => esc_html__('Select Tape', 'scrapbook-shop')];

        if (class_exists('Washi_Tape_DB')) {
            $db = new \Washi_Tape_DB();
            $tapes = $db->get_all_washi_tapes();

            if (!empty($tapes)) {
                foreach ($tapes as $tape) {
                    $options[$tape->id] = $tape->title;
                }
            }
        }

        return $options;
    }
}
```

## File: widgets/scrapbook-products-grid.php
```php
<?php

/**
 * Scrapbook Products Archive Grid Widget
 * 
 * Creates the full archive layout with sorting and grid
 */

namespace Scrapbook_Shop\Widgets;

if (!defined('ABSPATH')) {
    exit;
}

class Scrapbook_Products_Grid extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'scrapbook_products_grid';
    }

    public function get_title()
    {
        return esc_html__('Scrapbook Products Grid', 'scrapbook-shop');
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['woocommerce-elements'];
    }

    protected function register_controls()
    {

        // Query Section
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'query_type',
            [
                'label' => esc_html__('Query Type', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => esc_html__('All Products', 'scrapbook-shop'),
                    'featured' => esc_html__('Featured Products', 'scrapbook-shop'),
                    'sale' => esc_html__('On Sale', 'scrapbook-shop'),
                    'best_selling' => esc_html__('Best Selling', 'scrapbook-shop'),
                    'top_rated' => esc_html__('Top Rated', 'scrapbook-shop'),
                    'category' => esc_html__('By Category', 'scrapbook-shop'),
                    'manual' => esc_html__('Manual Selection', 'scrapbook-shop'),
                ],
            ]
        );

        $this->add_control(
            'product_categories',
            [
                'label' => esc_html__('Categories', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_product_categories(),
                'condition' => [
                    'query_type' => 'category',
                ],
            ]
        );

        $this->add_control(
            'products_per_page',
            [
                'label' => esc_html__('Products Per Page', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'scrapbook-shop'),
                    'title' => esc_html__('Title', 'scrapbook-shop'),
                    'price' => esc_html__('Price', 'scrapbook-shop'),
                    'popularity' => esc_html__('Popularity', 'scrapbook-shop'),
                    'rating' => esc_html__('Rating', 'scrapbook-shop'),
                    'rand' => esc_html__('Random', 'scrapbook-shop'),
                    'menu_order' => esc_html__('Menu Order', 'scrapbook-shop'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => esc_html__('Ascending', 'scrapbook-shop'),
                    'DESC' => esc_html__('Descending', 'scrapbook-shop'),
                ],
            ]
        );

        $this->end_controls_section();

        // Layout Section
        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('Layout', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'show_page_title',
            [
                'label' => esc_html__('Show Page Title', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'page_title_text',
            [
                'label' => esc_html__('Custom Title', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Shop', 'scrapbook-shop'),
                'condition' => [
                    'show_page_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_sorting',
            [
                'label' => esc_html__('Show Sorting Dropdown', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_result_count',
            [
                'label' => esc_html__('Show Result Count', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-products-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_gap',
            [
                'label' => esc_html__('Grid Gap', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 25,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-products-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__('Show Pagination', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Washi Tape Settings
        $this->start_controls_section(
            'section_washi_tape_settings',
            [
                'label' => esc_html__('Washi Tape Settings', 'scrapbook-shop'),
            ]
        );

        $this->add_control(
            'enable_washi_tape',
            [
                'label' => esc_html__('Enable Washi Tape', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'washi_tape_mode',
            [
                'label' => esc_html__('Washi Tape Mode', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'random',
                'options' => [
                    'random' => esc_html__('Random from Plugin', 'scrapbook-shop'),
                    'specific' => esc_html__('Specific Tape', 'scrapbook-shop'),
                    'rotating' => esc_html__('Rotating Selection', 'scrapbook-shop'),
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        if (class_exists('Washi_Tape_DB')) {
            $this->add_control(
                'specific_washi_tape',
                [
                    'label' => esc_html__('Select Washi Tape', 'scrapbook-shop'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $this->get_washi_tapes_list(),
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'washi_tape_mode' => 'specific',
                    ],
                ]
            );

            $this->add_control(
                'rotating_washi_tapes',
                [
                    'label' => esc_html__('Select Tapes to Rotate', 'scrapbook-shop'),
                    'type' => \Elementor\Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => $this->get_washi_tapes_list(),
                    'condition' => [
                        'enable_washi_tape' => 'yes',
                        'washi_tape_mode' => 'rotating',
                    ],
                ]
            );
        }

        $this->add_control(
            'randomize_tape_rotation',
            [
                'label' => esc_html__('Randomize Tape Angles', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_washi_tape' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'tape_rotation_range',
            [
                'label' => esc_html__('Rotation Range', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [
                    'deg' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'deg',
                    'size' => 10,
                ],
                'condition' => [
                    'enable_washi_tape' => 'yes',
                    'randomize_tape_rotation' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Style', 'scrapbook-shop'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'page_title_typography',
                'label' => esc_html__('Page Title Typography', 'scrapbook-shop'),
                'selector' => '{{WRAPPER}} .scrapbook-archive-title',
                'condition' => [
                    'show_page_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'page_title_color',
            [
                'label' => esc_html__('Page Title Color', 'scrapbook-shop'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2C2C2C',
                'selectors' => [
                    '{{WRAPPER}} .scrapbook-archive-title' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_page_title' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Setup query
        $query_args = $this->get_query_args($settings);
        $products = new \WP_Query($query_args);

?>
        <div class="scrapbook-products-archive">
            <?php if ($settings['show_page_title'] === 'yes' || $settings['show_sorting'] === 'yes'): ?>
                <div class="scrapbook-archive-header">
                    <?php if ($settings['show_page_title'] === 'yes'): ?>
                        <h1 class="scrapbook-archive-title">
                            <?php echo esc_html($settings['page_title_text']); ?>
                        </h1>
                    <?php endif; ?>

                    <?php if ($settings['show_sorting'] === 'yes'): ?>
                        <div class="scrapbook-sorting">
                            <?php $this->render_sorting_dropdown(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_result_count'] === 'yes'): ?>
                <div class="scrapbook-result-count">
                    <?php
                    $total = $products->found_posts;
                    $per_page = $settings['products_per_page'];
                    $current = max(1, get_query_var('paged'));
                    $showing_start = ($current - 1) * $per_page + 1;
                    $showing_end = min($current * $per_page, $total);

                    printf(
                        esc_html__('Showing %1$d–%2$d of %3$d results', 'scrapbook-shop'),
                        $showing_start,
                        $showing_end,
                        $total
                    );
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($products->have_posts()): ?>
                <div class="scrapbook-products-grid">
                    <?php
                    $index = 0;
                    while ($products->have_posts()):
                        $products->the_post();
                        global $product;

                        // Get washi tape for this product
                        $washi_tape_html = $this->get_washi_tape_for_product($settings, $index);
                        $rotation = $this->get_rotation_for_product($settings, $index);

                    ?>
                        <div class="scrapbook-grid-item">
                            <?php
                            // Use template part if exists, otherwise use inline template
                            if (locate_template('woocommerce/content-product-scrapbook.php')) {
                                wc_get_template_part('content', 'product-scrapbook');
                            } else {
                                $this->render_product_card($product, $washi_tape_html, $rotation);
                            }
                            ?>
                        </div>
                    <?php
                        $index++;
                    endwhile;
                    ?>
                </div>

                <?php if ($settings['show_pagination'] === 'yes'): ?>
                    <div class="scrapbook-pagination">
                        <?php
                        echo paginate_links([
                            'total' => $products->max_num_pages,
                            'current' => max(1, get_query_var('paged')),
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                        ]);
                        ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="scrapbook-no-products">
                    <?php esc_html_e('No products found.', 'scrapbook-shop'); ?>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </div>
    <?php
    }

    /**
     * Render individual product card
     */
    private function render_product_card($product, $washi_tape_html, $rotation)
    {
    ?>
        <div class="scrapbook-product-card">
            <div class="product-card-inner">
                <div class="product-image-wrapper">
                    <?php if ($washi_tape_html): ?>
                        <div class="washi-tape-decoration" style="transform: rotate(<?php echo esc_attr($rotation); ?>deg);">
                            <?php echo $washi_tape_html; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($product->is_on_sale()): ?>
                        <span class="onsale"><?php esc_html_e('Sale!', 'scrapbook-shop'); ?></span>
                    <?php endif; ?>

                    <div class="polaroid-frame">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                            <?php echo $product->get_image('woocommerce_thumbnail'); ?>
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

    /**
     * Get query arguments
     */
    private function get_query_args($settings)
    {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => $settings['products_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'paged' => max(1, get_query_var('paged')),
        ];

        // Handle different query types
        switch ($settings['query_type']) {
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

            case 'best_selling':
                $args['meta_key'] = 'total_sales';
                $args['orderby'] = 'meta_value_num';
                break;

            case 'top_rated':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                break;

            case 'category':
                if (!empty($settings['product_categories'])) {
                    $args['tax_query'][] = [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $settings['product_categories'],
                    ];
                }
                break;
        }

        return $args;
    }

    /**
     * Get washi tape HTML for a product
     */
    private function get_washi_tape_for_product($settings, $index)
    {
        if ($settings['enable_washi_tape'] !== 'yes' || !class_exists('Washi_Tape_DB')) {
            return '';
        }

        $db = new \Washi_Tape_DB();
        $tape = null;

        switch ($settings['washi_tape_mode']) {
            case 'specific':
                if (!empty($settings['specific_washi_tape'])) {
                    $tape = $db->get_washi_tape($settings['specific_washi_tape']);
                }
                break;

            case 'rotating':
                if (!empty($settings['rotating_washi_tapes'])) {
                    $tape_ids = $settings['rotating_washi_tapes'];
                    $tape_id = $tape_ids[$index % count($tape_ids)];
                    $tape = $db->get_washi_tape($tape_id);
                }
                break;

            case 'random':
            default:
                $tapes = $db->get_all_washi_tapes();
                if (!empty($tapes)) {
                    $tape = $tapes[array_rand($tapes)];
                }
                break;
        }

        return ($tape && !empty($tape->svg)) ? $tape->svg : '';
    }

    /**
     * Get rotation for a product
     */
    private function get_rotation_for_product($settings, $index)
    {
        if ($settings['randomize_tape_rotation'] !== 'yes') {
            return 0;
        }

        $range = $settings['tape_rotation_range']['size'];
        return rand(-$range, $range);
    }

    /**
     * Render sorting dropdown
     */
    private function render_sorting_dropdown()
    {
    ?>
        <form class="scrapbook-ordering" method="get">
            <select name="orderby" class="orderby">
                <option value="menu_order"><?php esc_html_e('Default sorting', 'scrapbook-shop'); ?></option>
                <option value="popularity"><?php esc_html_e('Sort by popularity', 'scrapbook-shop'); ?></option>
                <option value="rating"><?php esc_html_e('Sort by average rating', 'scrapbook-shop'); ?></option>
                <option value="date"><?php esc_html_e('Sort by latest', 'scrapbook-shop'); ?></option>
                <option value="price"><?php esc_html_e('Sort by price: low to high', 'scrapbook-shop'); ?></option>
                <option value="price-desc"><?php esc_html_e('Sort by price: high to low', 'scrapbook-shop'); ?></option>
            </select>
            <?php wc_query_string_form_fields(null, ['orderby', 'submit']); ?>
        </form>
<?php
    }

    /**
     * Get product categories for selector
     */
    private function get_product_categories()
    {
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ]);

        $options = [];
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }

        return $options;
    }

    /**
     * Get washi tapes list
     */
    private function get_washi_tapes_list()
    {
        $options = [];

        if (class_exists('Washi_Tape_DB')) {
            $db = new \Washi_Tape_DB();
            $tapes = $db->get_all_washi_tapes();

            if (!empty($tapes)) {
                foreach ($tapes as $tape) {
                    $options[$tape->id] = $tape->title;
                }
            }
        }

        return $options;
    }
}
```

## File: scrapbook-shop.php
```php
<?php

/**
 * Plugin Name: Scrapbook Shop for Elementor
 * Description: Beautiful scrapbook-style WooCommerce product displays with Washi Tape integration for Elementor
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: scrapbook-shop
 * Requires Plugins: elementor, woocommerce
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('SCRAPBOOK_SHOP_VERSION', '1.0.0');
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

        // Initialize the plugin
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
        // Load text domain
        load_plugin_textdomain('scrapbook-shop', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Check if Elementor is loaded
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Include widget files
        $this->include_widget_files();

        // Add AJAX handlers for dynamic content
        add_action('wp_ajax_scrapbook_load_more_products', [$this, 'ajax_load_more_products']);
        add_action('wp_ajax_nopriv_scrapbook_load_more_products', [$this, 'ajax_load_more_products']);
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
        // Make sure the widget classes exist
        if (class_exists('Scrapbook_Shop\Widgets\Scrapbook_Product_Card')) {
            $widgets_manager->register(new \Scrapbook_Shop\Widgets\Scrapbook_Product_Card());
        }

        if (class_exists('Scrapbook_Shop\Widgets\Scrapbook_Products_Grid')) {
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
```

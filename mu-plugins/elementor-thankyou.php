<?php
/**
 * Plugin Name: Elementor Thank You Redirect (MU)
 * Description: Redirect WooCommerce "order received" to a custom Elementor page, render order details via shortcode, and empty cart after verified successful checkout. Also forces wp_mail() (incl. Woo) to use SMTP while leaving credentials to your SMTP plugin.
 */

/**
 * --- Force SMTP transport globally (let your SMTP plugin supply credentials) ---
 *
 * Why here? MU-plugins load before normal plugins, so we set only the transport
 * (Mailer = 'smtp'). Your SMTP plugin will then configure Host/Port/Auth/User/Pass.
 */
add_action('phpmailer_init', function ($phpmailer) {
    // Always force the transport to SMTP; do NOT hardcode creds here.
    $phpmailer->Mailer = 'smtp';
}, 5);

/**
 * Optional: make sure From headers are consistent. If your SMTP provider requires
 * a specific From, you can set it here, or let your SMTP plugin manage it.
 */
// add_filter('wp_mail_from', function ($from) {
//     return 'no-reply@yourdomain.com';
// }, 20);
// add_filter('wp_mail_from_name', function ($name) {
//     return 'Your Site';
// }, 20);

// -----------------------------------------------------------------------------
// WooCommerce thank-you redirection + receipt rendering + safe cart emptying
// -----------------------------------------------------------------------------

// Only run Woo-specific pieces if WooCommerce is active.
add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce')) {
        return;
    }

    /**
     * Helper: get the Elementor Thank You page ID.
     * Keep this in one place so it’s easy to change later.
     */
    function ljc_thankyou_page_id()
    {
        return 477; // <-- your Elementor Thank You page ID
    }

    /**
     * STEP A (optional but recommended):
     * Disable caching for the custom Thank You page to avoid stale carts / stale notices.
     */
    add_action('template_redirect', function () {
        if (is_page(ljc_thankyou_page_id())) {
            nocache_headers();
        }
    });

    /**
     * STEP B: Redirect the native thank-you URL to your Elementor page.
     */
    add_filter('woocommerce_get_checkout_order_received_url', function ($url, $order) {
        if (!$order instanceof WC_Order) {
            return $url;
        }

        $page_id = ljc_thankyou_page_id();
        $page_url = get_permalink($page_id);
        if (!$page_url) {
            // If the page is missing, fall back to the default URL.
            return $url;
        }

        // Pass order id + order key so we can securely render details on the custom page.
        return add_query_arg(
            array(
                'order' => $order->get_id(),
                'key' => $order->get_order_key(),
            ),
            $page_url
        );
    }, 10, 2);

    /**
     * STEP C: Shortcode to print WooCommerce order details inside your Elementor page,
     * and (once verified) empty the cart.
     */
    add_shortcode('my_order_summary', function () {

        $order_id = isset($_GET['order']) ? absint($_GET['order']) : 0;
        $key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

        if (!$order_id || !$key) {
            return '<div class="wc-order-msg">We couldn’t find your order details. If you completed checkout, please check your email.</div>';
        }

        $order = wc_get_order($order_id);
        if (!$order || $order->get_order_key() !== $key) {
            return '<div class="wc-order-msg">Order not found or key mismatch.</div>';
        }

        /**
         * EMPTY THE CART SAFELY
         *
         * Conditions:
         * - The order should be paid / in a “successful” state (processing or completed).
         * - Only empty if the current session still has items (idempotent).
         * - This happens client-side when the customer lands on the thank-you page,
         *   ensuring we affect the right session/cart.
         */
        $is_success_state = $order->is_paid() || $order->has_status(array('processing', 'completed'));

        if ($is_success_state && function_exists('WC') && WC()->cart) {
            if (!WC()->cart->is_empty()) {
                WC()->cart->empty_cart(true);
                // Optional: Add a one-time success notice visible above the receipt.
                wc_add_notice(__('Thanks! Your cart has been cleared after successful checkout.', 'your-textdomain'), 'success');
            }
        }

        ob_start(); ?>
        <div id="ljc-ty">
            <script>
                // Update cart count to 0 on successful checkout
                document.addEventListener('DOMContentLoaded', function() {
                    // Find all cart count elements and set them to 0
                    const cartCountElements = document.querySelectorAll('.crafty-nav-cart-count');
                    cartCountElements.forEach(function(element) {
                        element.textContent = '0';
                    });
                    
                    // Also update any other common cart count selectors
                    const alternativeCartCounts = document.querySelectorAll('.cart-count, .cart-count-number, .header-cart-count');
                    alternativeCartCounts.forEach(function(element) {
                        element.textContent = '0';
                    });
                });
            </script>
            <style>
                /* ---------- LJC Thank You / Receipt Styles ---------- */
                #ljc-ty {
                    --ljc-bg: #ffffff;
                    --ljc-ink: #0f172a;
                    /* slate-900 */
                    --ljc-ink-2: #334155;
                    /* slate-700 */
                    --ljc-ink-3: #64748b;
                    /* slate-500 */
                    --ljc-line: #e5e7eb;
                    /* gray-200 */
                    --ljc-soft: #f8fafc;
                    /* slate-50 */
                    --ljc-accent: #111827;
                    /* gray-900 for headers */
                    --ljc-muted: #9ca3af;
                    /* gray-400 */
                    --ljc-pill: #eef2ff;
                    /* indigo-50 */
                    --ljc-pill-text: #3730a3;
                    /* indigo-800 */
                    color: var(--ljc-ink) !important;
                }

                #ljc-ty,
                #ljc-ty * {
                    color: var(--ljc-ink);
                }

                #ljc-ty a {
                    color: var(--ljc-ink-2);
                    text-decoration: underline;
                }

                #ljc-ty strong {
                    color: var(--ljc-ink);
                }

                .ljc-card {
                    background: var(--ljc-bg);
                    border: 1px solid var(--ljc-line);
                    border-radius: 14px;
                    box-shadow: 0 1px 1px rgba(0, 0, 0, .04), 0 8px 24px rgba(0, 0, 0, .06);
                    padding: clamp(16px, 2.2vw, 28px);
                    margin: 8px 0 28px;
                }

                #ljc-ty h1,
                #ljc-ty h2,
                #ljc-ty h3 {
                    margin: .2em 0 .6em;
                    line-height: 1.2;
                    color: var(--ljc-accent);
                }

                #ljc-ty h2 {
                    font-size: clamp(18px, 2.2vw, 22px);
                }

                #ljc-ty h3 {
                    font-size: clamp(16px, 2vw, 18px);
                }

                #ljc-ty ul.woocommerce-order-overview {
                    list-style: none;
                    margin: 0 0 16px;
                    padding: 0;
                    display: grid;
                    gap: 6px 14px;
                    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                }

                #ljc-ty ul.woocommerce-order-overview li {
                    background: var(--ljc-soft);
                    border: 1px solid var(--ljc-line);
                    border-radius: 10px;
                    padding: 10px 12px;
                    color: var(--ljc-ink-2);
                }

                #ljc-ty table,
                #ljc-ty .shop_table {
                    width: 100%;
                    border: 1px solid var(--ljc-line) !important;
                    border-collapse: separate !important;
                    border-spacing: 0;
                    background: var(--ljc-bg) !important;
                    border-radius: 12px;
                    overflow: hidden;
                }

                #ljc-ty .shop_table th,
                #ljc-ty .shop_table td {
                    padding: 14px 16px !important;
                    border-top: 1px solid var(--ljc-line) !important;
                    vertical-align: middle;
                    color: var(--ljc-ink);
                }

                #ljc-ty .shop_table thead th {
                    border-top: 0 !important;
                    background: var(--ljc-soft) !important;
                    font-weight: 600;
                    color: var(--ljc-ink-2);
                }

                #ljc-ty .shop_table tr:nth-child(even) td,
                #ljc-ty .shop_table tr:nth-child(odd) td {
                    background: #fff;
                }

                #ljc-ty .shop_table td.product-name a {
                    color: var(--ljc-ink-2);
                }

                #ljc-ty .shop_table tfoot th {
                    color: var(--ljc-ink-2);
                    font-weight: 600;
                }

                #ljc-ty .shop_table tfoot tr.order-total th,
                #ljc-ty .shop_table tfoot tr.order-total td {
                    border-top: 2px solid var(--ljc-line) !important;
                    font-weight: 700;
                    font-size: 1.05em;
                }

                #ljc-ty .woocommerce-customer-details {
                    display: grid;
                    gap: 16px;
                    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                }

                #ljc-ty .woocommerce-customer-details address {
                    background: var(--ljc-bg);
                    border: 1px solid var(--ljc-line);
                    border-radius: 10px;
                    padding: 12px 14px;
                    color: var(--ljc-ink-2);
                    font-style: normal;
                    line-height: 1.55;
                }

                #ljc-ty .woocommerce-notice,
                #ljc-ty .woocommerce-thankyou-order-received {
                    background: #ecfdf5;
                    border: 1px solid #a7f3d0;
                    color: #065f46 !important;
                    border-radius: 10px;
                    padding: 12px 14px;
                    margin-bottom: 16px;
                    font-weight: 600;
                }

                @media print {

                    #wpadminbar,
                    .elementor-location-header,
                    .elementor-location-footer {
                        display: none !important;
                    }

                    #ljc-ty .ljc-card {
                        box-shadow: none;
                        border-color: #cbd5e1;
                    }

                    #ljc-ty a {
                        text-decoration: none;
                        color: #000;
                    }
                }
            </style>
            <?php
            // Render full native thank-you content so items/totals/addresses all show.
            wc_get_template('checkout/thankyou.php', ['order' => $order], '', WC()->plugin_path() . '/templates/');
            ?>
        </div>
        <?php
        return ob_get_clean();
    });
});

/**
 * Network / cURL hardening (unchanged).
 */
add_action('http_api_curl', function ($handle) {
    if (defined('CURLOPT_IPRESOLVE')) {
        @curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }
    if (defined('CURLOPT_TIMEOUT')) {
        @curl_setopt($handle, CURLOPT_TIMEOUT, 5);
    }
}, 10);

add_filter('http_request_timeout', function ($t) {
    return min($t, 5);
});

<?php
/**
 * Plugin Name: SMTP Test (MU)
 */
add_action('admin_init', function () {
    if (!current_user_can('manage_options'))
        return;

    if (!isset($_GET['smtp_test']))
        return;

    $to = 'mclainr@gmail.com';
    $sent = wp_mail($to, 'SMTP Test', 'If you received this, SMTP is working.');
    $notice = $sent ? '✅ Test email queued successfully.' : '❌ Failed to queue test email.';

    add_action('admin_notices', function () use ($notice) {
        echo '<div class="notice notice-info"><p>' . esc_html($notice) . '</p></div>';
    });
});

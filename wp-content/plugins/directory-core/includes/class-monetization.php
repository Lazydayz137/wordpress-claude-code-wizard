<?php
/**
 * Class Monetization_Manager
 * 
 * Handles Revenue features: AdSense, Niche Branding, Claims, and Lead Gen.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Monetization_Manager
{
    public function __construct()
    {
        // Settings
        add_action('admin_init', array($this, 'register_settings'));
        add_action('directory_settings_tabs', array($this, 'render_settings_tab')); // Hook into existing settings if possible, or valid hook
        add_action('directory_settings_content', array($this, 'render_settings_content'));

        // Claim Handling
        add_action('admin_post_submit_claim', array($this, 'handle_claim_submission'));
        add_action('admin_post_nopriv_submit_claim', array($this, 'handle_claim_submission'));

        // Frontend
        add_action('wp_head', array($this, 'inject_adsense_script'));
    }

    /**
     * Register Monetization Settings
     */
    public function register_settings()
    {
        register_setting('directory_monetization', 'directory_niche');
        register_setting('directory_monetization', 'directory_city');
        register_setting('directory_monetization', 'directory_adsense_id'); // e.g. ca-pub-XXXXXXXXXXXXXXXX
        register_setting('directory_monetization', 'directory_payment_link'); // e.g. Stripe Payment Link
    }

    /**
     * Render Tab in Settings Page (Requires Settings Manager to support hooks, 
     * but for now we can rely on standard WP Settings API or just append to the main page).
     * 
     * Since Settings_Manager is custom, I'll assume we need to modify it to include this,
     * OR we can just register 'directory_monetization' group and let Settings Manager handle it if it was generic.
     * 
     * Usage: We will just call these render methods from Settings_Manager or modify Settings_Manager to include them.
     * For isolation, let's keep logic here but might need to edit Settings_Manager to call it.
     */

    /**
     * Handle "Claim This Business" Submission
     */
    public function handle_claim_submission()
    {
        if (!isset($_POST['directory_claim_nonce']) || !wp_verify_nonce($_POST['directory_claim_nonce'], 'submit_claim')) {
            wp_die('Security check failed');
        }

        $listing_id = intval($_POST['listing_id']);
        $name = sanitize_text_field($_POST['claimer_name']);
        $email = sanitize_email($_POST['claimer_email']);
        $message = sanitize_textarea_field($_POST['claimer_message']);

        // 1. Create a "Claim" Post Type or Store as Option? 
        // Simplest: Send Email to Admin + Store Meta on Listing

        $admin_email = get_option('admin_email');
        $subject = "New Claim Request for Listing #$listing_id";
        $body = "Name: $name\nEmail: $email\nMessage:\n$message\n\nLink: " . get_permalink($listing_id);
        $headers = array('Content-Type: text/plain; charset=UTF-8');

        wp_mail($admin_email, $subject, $body, $headers);

        // Mark as "Pending Claim"
        update_post_meta($listing_id, '_claim_status', 'pending');
        update_post_meta($listing_id, '_claim_data', array(
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'date' => current_time('mysql')
        ));

        // Redirect back with success message
        wp_redirect(get_permalink($listing_id) . '?claim_success=1');
        exit;
    }

    /**
     * Inject AdSense Script in Head
     */
    public function inject_adsense_script()
    {
        $client_id = get_option('directory_adsense_id');
        if ($client_id) {
            echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr($client_id) . '" crossorigin="anonymous"></script>';
        }
    }

    /**
     * Helper: Render Ad Slot
     */
    public static function render_ad_slot($slot_name = 'sidebar')
    {
        $client_id = get_option('directory_adsense_id');
        if (!$client_id)
            return;

        echo '<div class="directory-ad-slot directory-ad-' . esc_attr($slot_name) . '" style="background:#f0f0f0; padding:20px; text-align:center; margin: 20px 0;">';
        echo '<small>Advertisement</small>';
        echo '<!-- Ins placeholder for ' . esc_attr($slot_name) . ' -->';
        // Real AdSense code would go here using the client_id
        echo '</div>';
    }

    /**
     * Helper: Is Listing Verified?
     */
    public static function is_verified($post_id)
    {
        return get_post_meta($post_id, '_is_claimed', true);
    }
}

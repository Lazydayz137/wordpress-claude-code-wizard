<?php
/**
 * Class Claim_Manager
 * 
 * Handles the "Claim this Listing" functionality.
 * Allows users to submit a claim for a listing.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Claim_Manager
{

    public function __construct()
    {
        add_action('init', array($this, 'register_claim_cpt'));
        add_action('init', array($this, 'handle_claim_submission'));
    }

    /**
     * Register the Claim Custom Post Type
     * Hidden from public, used for admin management.
     */
    public function register_claim_cpt()
    {
        register_post_type('claim', array(
            'labels' => array(
                'name' => __('Claims', 'directory-core'),
                'singular_name' => __('Claim', 'directory-core'),
            ),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-yes',
        ));
    }

    /**
     * Handle frontend claim form submission
     */
    public function handle_claim_submission()
    {
        if (!isset($_POST['directory_claim_nonce']) || !wp_verify_nonce($_POST['directory_claim_nonce'], 'submit_claim')) {
            return;
        }

        $listing_id = intval($_POST['listing_id']);
        $claimer_name = sanitize_text_field($_POST['claimer_name']);
        $claimer_email = sanitize_email($_POST['claimer_email']);
        $claimer_message = sanitize_textarea_field($_POST['claimer_message']);

        if (!$listing_id || empty($claimer_email)) {
            return;
        }

        // Create Claim Post
        $claim_id = wp_insert_post(array(
            'post_title' => sprintf(__('Claim for: %s', 'directory-core'), get_the_title($listing_id)),
            'post_content' => $claimer_message,
            'post_type' => 'claim',
            'post_status' => 'pending',
        ));

        if ($claim_id) {
            update_post_meta($claim_id, '_claim_listing_id', $listing_id);
            update_post_meta($claim_id, '_claim_email', $claimer_email);
            update_post_meta($claim_id, '_claim_name', $claimer_name);

            // Notify Admin (Mock)
            // wp_mail(get_option('admin_email'), 'New Listing Claim', 'Check admin panel.');

            // Redirect with success flag
            // wp_redirect(add_query_arg('claim_submitted', '1', get_permalink($listing_id)));
            // exit;
        }
    }
}

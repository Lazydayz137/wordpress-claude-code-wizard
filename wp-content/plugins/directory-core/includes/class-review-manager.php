<?php
/**
 * Class Review_Manager
 * 
 * Handles review submissions, calculations, and retrieval.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Review_Manager
{

    public function __construct()
    {
        add_action('init', array($this, 'handle_review_submission'));
        add_action('save_post_review', array($this, 'update_listing_aggregates'), 10, 3);
    }

    /**
     * Handle frontend form submission
     */
    public function handle_review_submission()
    {
        if (!isset($_POST['directory_review_nonce']) || !wp_verify_nonce($_POST['directory_review_nonce'], 'submit_review')) {
            return;
        }

        $listing_id = intval($_POST['listing_id']);
        $rating = intval($_POST['rating']);
        $title = sanitize_text_field($_POST['review_title']);
        $content = sanitize_textarea_field($_POST['review_content']);

        // Validation
        if (!$listing_id || !$rating || empty($title)) {
            return; // Add error handling
        }

        $review_id = wp_insert_post(array(
            'post_title' => $title,
            'post_content' => $content,
            'post_type' => 'review',
            'post_status' => 'pending', // Require moderation
        ));

        if ($review_id) {
            update_post_meta($review_id, '_review_listing_id', $listing_id);
            update_post_meta($review_id, '_review_rating', $rating);

            // Redirect or show success
            // wp_redirect(get_permalink($listing_id) . '?review_submitted=1');
            // exit;
        }
    }

    /**
     * Update average rating on the listing when a review is saved/published
     */
    public function update_listing_aggregates($post_id, $post, $update)
    {
        if ($post->post_status !== 'publish') {
            return;
        }

        $listing_id = get_post_meta($post_id, '_review_listing_id', true);
        if (!$listing_id) {
            return;
        }

        $reviews = get_posts(array(
            'post_type' => 'review',
            'meta_key' => '_review_listing_id',
            'meta_value' => $listing_id,
            'post_status' => 'publish',
            'numberposts' => -1
        ));

        $total_rating = 0;
        $count = count($reviews);

        foreach ($reviews as $review) {
            $total_rating += intval(get_post_meta($review->ID, '_review_rating', true));
        }

        $average = $count > 0 ? round($total_rating / $count, 1) : 0;

        update_post_meta($listing_id, '_listing_average_rating', $average);
        update_post_meta($listing_id, '_listing_review_count', $count);
    }
}

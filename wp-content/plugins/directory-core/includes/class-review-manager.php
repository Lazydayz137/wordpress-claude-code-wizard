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
        $verified = isset($_POST['verified_purchase']) ? 1 : 0;

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
            update_post_meta($review_id, '_review_verified', $verified);

            // Handle Photo Upload
            if (!empty($_FILES['review_photo']['name'])) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $attachment_id = media_handle_upload('review_photo', $review_id);

                if (!is_wp_error($attachment_id)) {
                    update_post_meta($review_id, '_review_photo_id', $attachment_id);
                }
            }

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

    /**
     * Render the review submission form
     */
    public static function render_review_form($listing_id)
    {
        ob_start();
        ?>
        <div class="review-form-container">
            <h3><?php _e('Leave a Review', 'directory-core'); ?></h3>
            <form method="post" enctype="multipart/form-data" id="directory-review-form">
                <?php wp_nonce_field('submit_review', 'directory_review_nonce'); ?>
                <input type="hidden" name="listing_id" value="<?php echo esc_attr($listing_id); ?>">

                <div class="directory-form-group">
                    <label for="review_title"><?php _e('Review Title', 'directory-core'); ?></label>
                    <input type="text" name="review_title" id="review_title" class="widefat" required>
                </div>

                <div class="directory-form-group">
                    <label><?php _e('Rating', 'directory-core'); ?></label>
                    <div class="star-rating-select">
                        <select name="rating" required>
                            <option value="5">5 Stars - Excellent</option>
                            <option value="4">4 Stars - Very Good</option>
                            <option value="3">3 Stars - Average</option>
                            <option value="2">2 Stars - Poor</option>
                            <option value="1">1 Star - Terrible</option>
                        </select>
                    </div>
                </div>

                <div class="directory-form-group">
                    <label for="review_content"><?php _e('Your Review', 'directory-core'); ?></label>
                    <textarea name="review_content" id="review_content" rows="5" class="widefat" required></textarea>
                </div>

                <div class="directory-form-group">
                    <label for="review_photo"><?php _e('Upload Photo (Optional)', 'directory-core'); ?></label>
                    <input type="file" name="review_photo" id="review_photo" accept="image/*">
                </div>

                <div class="directory-form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="verified_purchase" value="1">
                        <?php _e('I have used this product/service', 'directory-core'); ?>
                    </label>
                </div>

                <div class="directory-form-submit">
                    <button type="submit" class="button button-primary"><?php _e('Submit Review', 'directory-core'); ?></button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get reviews for a specific listing
     */
    public static function get_reviews_for_listing($listing_id)
    {
        $reviews = get_posts(array(
            'post_type' => 'review',
            'meta_key' => '_review_listing_id',
            'meta_value' => $listing_id,
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        // Attach meta data to review objects for easier template access
        foreach ($reviews as $review) {
            $review->rating = get_post_meta($review->ID, '_review_rating', true);
            $review->verified = get_post_meta($review->ID, '_review_verified', true);
            $review->photo_id = get_post_meta($review->ID, '_review_photo_id', true);
        }

        return $reviews;
    }
}

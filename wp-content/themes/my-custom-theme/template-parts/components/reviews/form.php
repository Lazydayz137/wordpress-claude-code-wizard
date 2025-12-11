<?php
/**
 * Component: Review Form
 * 
 * Displays the form for submitting a review.
 */

if (!defined('ABSPATH')) {
    exit;
}

$listing_id = get_the_ID();

// Check if user has already reviewed (optional logic for later)
?>

<div class="review-form-container">
    <h3><?php _e('Leave a Review', 'my-custom-theme'); ?></h3>

    <form action="" method="post" class="review-form" enctype="multipart/form-data">
        <?php wp_nonce_field('submit_review', 'directory_review_nonce'); ?>
        <input type="hidden" name="listing_id" value="<?php echo esc_attr($listing_id); ?>">

        <div class="form-group">
            <label for="rating"><?php _e('Rating', 'my-custom-theme'); ?></label>
            <select name="rating" id="rating" required>
                <option value="5">5 - <?php _e('Excellent', 'my-custom-theme'); ?></option>
                <option value="4">4 - <?php _e('Very Good', 'my-custom-theme'); ?></option>
                <option value="3">3 - <?php _e('Average', 'my-custom-theme'); ?></option>
                <option value="2">2 - <?php _e('Poor', 'my-custom-theme'); ?></option>
                <option value="1">1 - <?php _e('Terrible', 'my-custom-theme'); ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="review_title"><?php _e('Title', 'my-custom-theme'); ?></label>
            <input type="text" name="review_title" id="review_title" required>
        </div>

        <div class="form-group">
            <label for="review_content"><?php _e('Review', 'my-custom-theme'); ?></label>
            <textarea name="review_content" id="review_content" rows="5" required></textarea>
        </div>

        <div class="form-group">
            <label for="review_photo"><?php _e('Photo (Optional)', 'my-custom-theme'); ?></label>
            <input type="file" name="review_photo" id="review_photo" accept="image/*">
        </div>

        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" name="verified_purchase" value="1">
                <?php _e('I have used this product/service', 'my-custom-theme'); ?>
            </label>
        </div>

        <button type="submit" class="btn btn-primary"><?php _e('Submit Review', 'my-custom-theme'); ?></button>
    </form>
</div>
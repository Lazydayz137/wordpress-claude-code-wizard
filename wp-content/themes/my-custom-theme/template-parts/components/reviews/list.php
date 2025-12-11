<?php
/**
 * Component: Review List
 * 
 * Displays a list of reviews for the current listing.
 */

if (!defined('ABSPATH')) {
    exit;
}

$listing_id = get_the_ID();
$reviews = get_posts(array(
    'post_type' => 'review',
    'meta_key' => '_review_listing_id',
    'meta_value' => $listing_id,
    'post_status' => 'publish',
    'numberposts' => -1
));

if (empty($reviews)) {
    echo '<p>' . __('No reviews yet. Be the first to review!', 'my-custom-theme') . '</p>';
    return;
}
?>

<div class="review-list">
    <h3><?php _e('Reviews', 'my-custom-theme'); ?></h3>

    <?php foreach ($reviews as $review):
        $rating = get_post_meta($review->ID, '_review_rating', true);
        ?>
        <div class="review-item">
            <div class="review-header">
                <span class="review-rating"><?php echo str_repeat('★', intval($rating)); ?></span>
                <h4 class="review-title"><?php echo esc_html($review->post_title); ?></h4>
                <?php
                $verified = get_post_meta($review->ID, '_review_verified', true);
                if ($verified): ?>
                    <span class="verified-badge" style="color: green; font-size: 0.8em; margin-left: 10px;">
                        ✓ <?php _e('Verified Purchase', 'my-custom-theme'); ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="review-content">
                <?php echo wpautop(esc_html($review->post_content)); ?>

                <?php
                $photo_id = get_post_meta($review->ID, '_review_photo_id', true);
                if ($photo_id): ?>
                    <div class="review-photo" style="margin-top: 10px;">
                        <?php echo wp_get_attachment_image($photo_id, 'thumbnail', false, array('class' => 'review-attachment')); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="review-meta">
                <span class="review-date"><?php echo get_the_date('', $review); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
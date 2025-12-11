<?php
/**
 * Component: Claim Form
 * 
 * Displays the "Claim this Listing" form.
 */

if (!defined('ABSPATH')) {
    exit;
}

$listing_id = get_the_ID();

// Don't show if already claimed (logic to be added)
$is_claimed = get_post_meta($listing_id, '_is_claimed', true);
if ($is_claimed) {
    return;
}
?>

<div class="claim-form-container">
    <h3><?php _e('Is this your business?', 'my-custom-theme'); ?></h3>
    <p><?php _e('Claim this listing to manage details and reviews.', 'my-custom-theme'); ?></p>

    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="claim-form">
        <input type="hidden" name="action" value="submit_claim">
        <?php wp_nonce_field('submit_claim', 'directory_claim_nonce'); ?>
        <input type="hidden" name="listing_id" value="<?php echo esc_attr($listing_id); ?>">

        <div class="form-group">
            <label for="claimer_name"><?php _e('Your Name', 'my-custom-theme'); ?></label>
            <input type="text" name="claimer_name" id="claimer_name" required>
        </div>

        <div class="form-group">
            <label for="claimer_email"><?php _e('Business Email', 'my-custom-theme'); ?></label>
            <input type="email" name="claimer_email" id="claimer_email" required>
        </div>

        <div class="form-group">
            <label for="claimer_message"><?php _e('Verification Details', 'my-custom-theme'); ?></label>
            <textarea name="claimer_message" id="claimer_message" rows="3"
                placeholder="<?php _e('Tell us why you are the owner...', 'my-custom-theme'); ?>"></textarea>
        </div>

        <button type="submit" class="btn btn-secondary"><?php _e('Claim Listing', 'my-custom-theme'); ?></button>
    </form>
</div>
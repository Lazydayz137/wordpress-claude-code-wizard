<?php
/**
 * Template Part: User Dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
?>

<div class="directory-dashboard">
    <h2><?php printf(__('Welcome, %s', 'my-custom-theme'), esc_html($current_user->display_name)); ?></h2>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3><?php _e('My Listings', 'my-custom-theme'); ?></h3>
            <p><?php _e('You have no active listings.', 'my-custom-theme'); ?></p>
            <a href="#" class="btn btn-primary"><?php _e('Add New Listing', 'my-custom-theme'); ?></a>
        </div>

        <div class="dashboard-card">
            <h3><?php _e('My Reviews', 'my-custom-theme'); ?></h3>
            <p><?php _e('You have not written any reviews yet.', 'my-custom-theme'); ?></p>
        </div>

        <div class="dashboard-card">
            <h3><?php _e('Account Settings', 'my-custom-theme'); ?></h3>
            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>"
                class="btn btn-secondary"><?php _e('Logout', 'my-custom-theme'); ?></a>
        </div>
    </div>
</div>
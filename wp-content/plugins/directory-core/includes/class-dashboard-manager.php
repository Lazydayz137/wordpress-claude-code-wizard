<?php
/**
 * Class Dashboard_Manager
 * 
 * Handles the frontend user dashboard.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Dashboard_Manager
{

    public function __construct()
    {
        add_shortcode('directory_dashboard', array($this, 'render_dashboard'));
    }

    public function render_dashboard($atts)
    {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your dashboard.', 'directory-core') . '</p>';
        }

        ob_start();

        // Get User's Claims
        $user_id = get_current_user_id();

        // Logic to get user's listings (mocked for now)
        // In a real scenario, we would query posts where author = user_id

        include get_template_directory() . '/template-parts/dashboard/main.php';

        return ob_get_clean();
    }
}

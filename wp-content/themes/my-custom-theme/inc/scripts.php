<?php
/**
 * Enqueue Scripts & Styles
 */

if (!defined('ABSPATH')) {
    exit;
}

function my_custom_theme_scripts()
{
    // Enqueue Google Fonts (Plus Jakarta Sans & Inter)
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap', array(), null);

    // Main Stylesheet
    wp_enqueue_style('my-custom-theme-style', get_stylesheet_uri(), array(), '2.0.0');

    // Theme CSS Variables (Dynamic)
    wp_enqueue_style('my-custom-theme-vars', get_template_directory_uri() . '/assets/css/variables.css', array(), '1.0.0');

    // Navigation
    wp_enqueue_style('my-custom-theme-nav', get_template_directory_uri() . '/assets/css/navigation.css', array(), '1.0.0');
    wp_enqueue_script('my-custom-theme-nav', get_template_directory_uri() . '/assets/js/navigation.js', array(), '1.0.0', true);

    // Interactions (Phase 4)
    wp_enqueue_script('my-custom-theme-interactions', get_template_directory_uri() . '/assets/js/interactions.js', array(), '1.0.0', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'my_custom_theme_scripts');

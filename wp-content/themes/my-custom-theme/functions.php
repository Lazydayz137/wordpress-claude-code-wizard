<?php
/**
 * Theme Functions
 * 
 * This file acts as the bootloader for the theme.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Theme Constants
define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

// Include Modules
require_once THEME_DIR . '/inc/setup.php';
require_once THEME_DIR . '/inc/scripts.php';

// Future: Vault Integrator
// require_once THEME_DIR . '/inc/class-vault-integrator.php';

/**
 * Handle Directory Sorting
 */
function directory_sort_query($query)
{
    if (!is_admin() && $query->is_main_query() && (is_tax('industry') || is_tax('location'))) {
        if (isset($_GET['orderby'])) {
            switch ($_GET['orderby']) {
                case 'date':
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
                case 'title':
                    $query->set('orderby', 'title');
                    $query->set('order', 'ASC');
                    break;
                case 'rating':
                    $query->set('meta_key', '_listing_average_rating');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
            }
        }
    }
}
add_action('pre_get_posts', 'directory_sort_query');
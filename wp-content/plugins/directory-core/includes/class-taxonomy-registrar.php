<?php
/**
 * Class Taxonomy_Registrar
 * 
 * Registers custom taxonomies for the Directory:
 * - Location (Hierarchical)
 * - Industry (Hierarchical)
 */

if (!defined('ABSPATH')) {
    exit;
}

class Taxonomy_Registrar
{
    public function __construct()
    {
        add_action('init', array($this, 'register_taxonomies'));
    }

    public function register_taxonomies()
    {
        // Location
        $location_labels = array(
            'name' => _x('Locations', 'taxonomy general name', 'directory-core'),
            'singular_name' => _x('Location', 'taxonomy singular name', 'directory-core'),
            'search_items' => __('Search Locations', 'directory-core'),
            'all_items' => __('All Locations', 'directory-core'),
            'parent_item' => __('Parent Location', 'directory-core'),
            'parent_item_colon' => __('Parent Location:', 'directory-core'),
            'edit_item' => __('Edit Location', 'directory-core'),
            'update_item' => __('Update Location', 'directory-core'),
            'add_new_item' => __('Add New Location', 'directory-core'),
            'new_item_name' => __('New Location Name', 'directory-core'),
            'menu_name' => __('Location', 'directory-core'),
        );

        $location_args = array(
            'hierarchical' => true,
            'labels' => $location_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'location'),
            'show_in_rest' => true,
        );

        register_taxonomy('location', array('company'), $location_args);

        // Industry
        $industry_labels = array(
            'name' => _x('Industries', 'taxonomy general name', 'directory-core'),
            'singular_name' => _x('Industry', 'taxonomy singular name', 'directory-core'),
            'search_items' => __('Search Industries', 'directory-core'),
            'all_items' => __('All Industries', 'directory-core'),
            'parent_item' => __('Parent Industry', 'directory-core'),
            'parent_item_colon' => __('Parent Industry:', 'directory-core'),
            'edit_item' => __('Edit Industry', 'directory-core'),
            'update_item' => __('Update Industry', 'directory-core'),
            'add_new_item' => __('Add New Industry', 'directory-core'),
            'new_item_name' => __('New Industry Name', 'directory-core'),
            'menu_name' => __('Industry', 'directory-core'),
        );

        $industry_args = array(
            'hierarchical' => true,
            'labels' => $industry_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'industry'),
            'show_in_rest' => true,
        );

        register_taxonomy('industry', array('company'), $industry_args);
    }
}

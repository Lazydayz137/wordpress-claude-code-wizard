<?php
/**
 * Class CPT_Registrar
 * 
 * Dynamically registers CPTs based on JSON config.
 */

if (!defined('ABSPATH')) {
    exit;
}

class CPT_Registrar
{

    private $config_file;

    public function __construct()
    {
        $this->config_file = plugin_dir_path(dirname(__FILE__)) . 'config/cpts.json';
        add_action('init', array($this, 'register_cpts'));
    }

    public function register_cpts()
    {
        if (!file_exists($this->config_file)) {
            return;
        }

        $cpts = json_decode(file_get_contents($this->config_file), true);

        if (!$cpts) {
            return;
        }

        foreach ($cpts as $key => $data) {
            $this->register_single_cpt($key, $data);

            if (!empty($data['taxonomies'])) {
                foreach ($data['taxonomies'] as $tax) {
                    $this->register_single_taxonomy($tax, $key);
                }
            }
        }
    }

    private function register_single_cpt($key, $data)
    {
        // Fetch User Overrides
        $options = get_option('directory_cpt_labels', array());

        $singular = $data['singular'];
        $plural = $data['plural'];
        $slug = $data['slug'];

        if ($key === 'company') {
            if (!empty($options['company_singular']))
                $singular = $options['company_singular'];
            if (!empty($options['company_plural']))
                $plural = $options['company_plural'];
            if (!empty($options['company_slug']))
                $slug = sanitize_title($options['company_slug']);
        } elseif ($key === 'review') {
            if (!empty($options['review_singular']))
                $singular = $options['review_singular'];
            if (!empty($options['review_plural']))
                $plural = $options['review_plural'];
            if (!empty($options['review_slug']))
                $slug = sanitize_title($options['review_slug']);
        }

        $labels = array(
            'name' => _x($plural, 'Post Type General Name', 'directory-core'),
            'singular_name' => _x($singular, 'Post Type Singular Name', 'directory-core'),
            'menu_name' => __($plural, 'directory-core'),
            'all_items' => __('All ' . $plural, 'directory-core'),
            'add_new_item' => __('Add New ' . $singular, 'directory-core'),
            'edit_item' => __('Edit ' . $singular, 'directory-core'),
            'view_item' => __('View ' . $singular, 'directory-core'),
        );

        $args = array(
            'label' => __($singular, 'directory-core'),
            'labels' => $labels,
            'supports' => $data['supports'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => $data['icon'],
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'rewrite' => array('slug' => $slug),
            'show_in_rest' => true,
        );

        register_post_type($key, $args);
    }

    private function register_single_taxonomy($tax_key, $post_type)
    {
        // Simple capitalization for labels
        $name = ucfirst($tax_key);

        $labels = array(
            'name' => _x($name . 's', 'taxonomy general name', 'directory-core'),
            'singular_name' => _x($name, 'taxonomy singular name', 'directory-core'),
            'search_items' => __('Search ' . $name . 's', 'directory-core'),
            'all_items' => __('All ' . $name . 's', 'directory-core'),
            'edit_item' => __('Edit ' . $name, 'directory-core'),
            'update_item' => __('Update ' . $name, 'directory-core'),
            'add_new_item' => __('Add New ' . $name, 'directory-core'),
            'new_item_name' => __('New ' . $name . ' Name', 'directory-core'),
            'menu_name' => __($name . 's', 'directory-core'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => $tax_key),
            'show_in_rest' => true,
        );

        register_taxonomy($tax_key, array($post_type), $args);
    }
}

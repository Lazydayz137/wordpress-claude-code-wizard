<?php
/**
 * Plugin Name: Directory Core
 * Plugin URI: https://example.com/
 * Description: Core engine for Directory Applications. Dynamically loads CPTs from config.
 * Version: 2.0.0
 * Author: Claude Code Wizard
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Plugin Constants
define('DIRECTORY_CORE_PATH', plugin_dir_path(__FILE__));
define('DIRECTORY_CORE_URL', plugin_dir_url(__FILE__));

// Include Classes
require_once DIRECTORY_CORE_PATH . 'includes/class-cpt-registrar.php';
require_once DIRECTORY_CORE_PATH . 'includes/class-taxonomy-registrar.php';
require_once DIRECTORY_CORE_PATH . 'includes/class-review-manager.php';
require_once DIRECTORY_CORE_PATH . 'includes/class-vault-manager.php';
require_once DIRECTORY_CORE_PATH . 'includes/class-demo-importer.php';
require_once DIRECTORY_CORE_PATH . 'includes/class-claim-manager.php';
require_once DIRECTORY_CORE_PATH . 'includes/class-dashboard-manager.php';
require_once DIRECTORY_CORE_PATH . 'includes/class-data-importer.php';

require_once DIRECTORY_CORE_PATH . 'includes/class-contact-form.php';
require_once DIRECTORY_CORE_PATH . 'includes/class-settings-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-data-fetcher.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-monetization.php';

// Initialize Plugin
function directory_core_init()
{
    new CPT_Registrar();
    new Taxonomy_Registrar();
    new Review_Manager();
    Contact_Form_Manager::init(); // Contact Form Shortcode (static init)
    new Data_Fetcher(); // Automated Data Acquisition
    new Monetization_Manager(); // Phase 8: Revenue

    // Admin only
    if (is_admin()) {
        new Settings_Manager();
        new Data_Importer();
    }
}
add_action('plugins_loaded', 'directory_core_init');

/**
 * Enqueue Public Assets
 */
function directory_core_scripts()
{
    wp_enqueue_style(
        'directory-core-style',
        plugin_dir_url(__FILE__) . 'assets/css/directory-style.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'directory_core_scripts');

// Flush rewrite rules on activation
register_activation_hook(__FILE__, 'directory_core_activate');
function directory_core_activate()
{
    // Trigger CPT registration immediately for flush to work
    $registrar = new CPT_Registrar();
    $registrar->register_cpts();
    flush_rewrite_rules();
}

// Flush rewrite rules on deactivation
register_deactivation_hook(__FILE__, 'directory_core_deactivate');
function directory_core_deactivate()
{
    flush_rewrite_rules();
}
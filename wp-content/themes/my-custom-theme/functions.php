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
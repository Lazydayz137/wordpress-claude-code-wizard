<?php
/**
 * Class Vault_Manager
 * 
 * Scans the _vault directory and displays available assets in WP Admin.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Vault_Manager
{

    private $vault_dir;

    public function __construct()
    {
        // In Docker, we mounted ./_vault to /var/www/html/_vault
        $this->vault_dir = ABSPATH . '_vault';

        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
    }

    public function add_dashboard_widget()
    {
        wp_add_dashboard_widget(
            'directory_vault_status',
            'Festinger Vault Status',
            array($this, 'render_dashboard_widget')
        );
    }

    public function render_dashboard_widget()
    {
        if (!file_exists($this->vault_dir)) {
            echo '<div class="notice notice-warning inline"><p>Vault directory not found at ' . esc_html($this->vault_dir) . '</p></div>';
            return;
        }

        $plugins = glob($this->vault_dir . '/plugins/*.zip');
        $themes = glob($this->vault_dir . '/themes/*.zip');

        echo '<div class="vault-status">';

        echo '<h4>Plugins Found (' . count($plugins) . ')</h4>';
        if ($plugins) {
            echo '<ul>';
            foreach ($plugins as $plugin) {
                echo '<li>' . esc_html(basename($plugin)) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p><em>No plugins found in _vault/plugins/</em></p>';
        }

        echo '<h4>Themes Found (' . count($themes) . ')</h4>';
        if ($themes) {
            echo '<ul>';
            foreach ($themes as $theme) {
                echo '<li>' . esc_html(basename($theme)) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p><em>No themes found in _vault/themes/</em></p>';
        }

        echo '</div>';
    }
}

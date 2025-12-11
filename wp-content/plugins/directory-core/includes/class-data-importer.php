<?php
/**
 * Class Data_Importer
 * 
 * Imports companies from JSON data.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Data_Importer
{
    private $json_file;

    public function __construct()
    {
        $this->json_file = DIRECTORY_CORE_PATH . 'data/companies.json';
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_import'));
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'tools.php',
            'Directory JSON Import',
            'Directory JSON Import',
            'manage_options',
            'directory-json-import',
            array($this, 'render_page')
        );
    }

    public function render_page()
    {
        ?>
        <div class="wrap">
            <h1>Directory JSON Import</h1>
            <p>Import companies from a JSON file. This will replace the current data source.</p>
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('directory_import_json', 'directory_import_nonce'); ?>

                <p>
                    <label for="json_file">Select JSON File:</label>
                    <input type="file" name="json_file" id="json_file" accept=".json" required>
                </p>

                <p><button type="submit" name="import_json_data" class="button button-primary">Upload & Import</button></p>
            </form>
        </div>
        <?php
    }

    public function handle_import()
    {
        if (!isset($_POST['import_json_data'])) {
            return;
        }

        if (!check_admin_referer('directory_import_json', 'directory_import_nonce')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle File Upload
        if (isset($_FILES['json_file']) && $_FILES['json_file']['error'] === 0) {
            $uploaded_file = $_FILES['json_file']['tmp_name'];
            $file_type = mime_content_type($uploaded_file);

            // Validate JSON
            $json_data = file_get_contents($uploaded_file);
            if (json_decode($json_data) === null) {
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-error is-dismissible"><p>Invalid JSON file.</p></div>';
                });
                return;
            }

            // Move/Overwrite source file
            if (move_uploaded_file($uploaded_file, $this->json_file)) {
                $this->import_companies();
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-success is-dismissible"><p>companies.json updated and imported successfully!</p></div>';
                });
            } else {
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-error is-dismissible"><p>Failed to save JSON file.</p></div>';
                });
            }
        } elseif (file_exists($this->json_file)) {
            // Fallback to existing file if no upload (optional, but UI implies upload is required)
            $this->import_companies();
            add_action('admin_notices', function () {
                echo '<div class="notice notice-success is-dismissible"><p>Existing data re-imported successfully!</p></div>';
            });
        }
    }

    public function import_companies()
    {
        if (!file_exists($this->json_file)) {
            return;
        }

        $json_data = file_get_contents($this->json_file);
        $companies = json_decode($json_data, true);

        if (!$companies) {
            return;
        }

        foreach ($companies as $company) {
            $this->import_single_company($company);
        }
    }

    private function import_single_company($data)
    {
        $existing = get_posts(array(
            'post_type' => 'company',
            'meta_key' => '_company_import_id',
            'meta_value' => $data['id'],
            'post_status' => 'any',
            'numberposts' => 1
        ));

        $post_args = array(
            'post_title' => $data['basics']['name'],
            'post_content' => $data['basics']['description'],
            'post_type' => 'company',
            'post_status' => 'publish',
        );

        if ($existing) {
            $post_args['ID'] = $existing[0]->ID;
            $post_id = wp_update_post($post_args);
        } else {
            $post_id = wp_insert_post($post_args);
        }

        if (!$post_id || is_wp_error($post_id)) {
            return;
        }

        // Basics
        update_post_meta($post_id, '_company_import_id', $data['id']);
        update_post_meta($post_id, 'tagline', $data['basics']['tagline']);
        update_post_meta($post_id, 'founded', $data['basics']['founded']);
        update_post_meta($post_id, 'headquarters', $data['basics']['headquarters']);
        update_post_meta($post_id, 'employees', $data['basics']['employees']);
        update_post_meta($post_id, 'funding', $data['basics']['funding']);

        // Pricing
        update_post_meta($post_id, 'pricing_model', $data['pricing']['model']);
        update_post_meta($post_id, 'starter_price', $data['pricing']['starter_price']);

        // Images
        if (!empty($data['media']['logo'])) {
            update_post_meta($post_id, 'logo', $data['media']['logo']);
        }

        // Taxonomies
        $industries = [];
        if (!empty($data['use_cases'])) {
            foreach ($data['use_cases'] as $uc) {
                if (!empty($uc['industry'])) {
                    $industries[] = $uc['industry'];
                }
            }
        }
        $industries = array_unique($industries);

        if (!empty($industries)) {
            wp_set_object_terms($post_id, $industries, 'industry');
        }

        // Location
        if (!empty($data['basics']['headquarters'])) {
            $parts = explode(',', $data['basics']['headquarters']);
            $city = trim($parts[0]);
            wp_set_object_terms($post_id, $city, 'location');
        }
    }
}

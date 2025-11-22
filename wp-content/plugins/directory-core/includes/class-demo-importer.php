<?php
/**
 * Class Demo_Importer
 * 
 * Generates test data for the directory.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Demo_Importer
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_import'));
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'tools.php',
            'Directory Demo Import',
            'Directory Demo Import',
            'manage_options',
            'directory-demo-import',
            array($this, 'render_page')
        );
    }

    public function render_page()
    {
        ?>
        <div class="wrap">
            <h1>Directory Demo Import</h1>
            <p>Click below to generate test data (Companies, Reviews, Locations).</p>
            <form method="post" action="">
                <?php wp_nonce_field('directory_import_demo', 'directory_import_nonce'); ?>
                <p><button type="submit" name="import_demo_data" class="button button-primary">Import Demo Data</button></p>
            </form>
        </div>
        <?php
    }

    public function handle_import()
    {
        if (!isset($_POST['import_demo_data'])) {
            return;
        }

        if (!check_admin_referer('directory_import_demo', 'directory_import_nonce')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $this->create_demo_content();

        add_action('admin_notices', function () {
            echo '<div class="notice notice-success is-dismissible"><p>Demo content imported successfully!</p></div>';
        });
    }

    private function create_demo_content()
    {
        // Create Terms
        $locations = ['New York', 'San Francisco', 'London', 'Berlin'];
        $industries = ['Software', 'Marketing', 'Finance', 'Healthcare'];

        foreach ($locations as $loc) {
            if (!term_exists($loc, 'location')) {
                wp_insert_term($loc, 'location');
            }
        }

        foreach ($industries as $ind) {
            if (!term_exists($ind, 'industry')) {
                wp_insert_term($ind, 'industry');
            }
        }

        // Create Companies
        $companies = [
            'Acme Corp' => 'The leading provider of road runner traps.',
            'Globex' => 'We make things better.',
            'Soylent Corp' => 'Making food for the future.',
            'Initech' => 'Software for the modern era.',
            'Umbrella Corp' => 'Pharmaceuticals and more.'
        ];

        foreach ($companies as $name => $desc) {
            if (page_exists($name, '', '', 'company')) {
                continue;
            }

            $post_id = wp_insert_post(array(
                'post_title' => $name,
                'post_content' => $desc,
                'post_type' => 'company',
                'post_status' => 'publish'
            ));

            if ($post_id) {
                // Assign random terms
                $loc = $locations[array_rand($locations)];
                $ind = $industries[array_rand($industries)];
                wp_set_object_terms($post_id, $loc, 'location');
                wp_set_object_terms($post_id, $ind, 'industry');

                // Create dummy review
                $review_id = wp_insert_post(array(
                    'post_title' => 'Great service!',
                    'post_content' => 'I really liked working with them.',
                    'post_type' => 'review',
                    'post_status' => 'publish'
                ));

                update_post_meta($review_id, '_review_listing_id', $post_id);
                update_post_meta($review_id, '_review_rating', rand(3, 5));

                // Trigger aggregate update
                $manager = new Review_Manager();
                $manager->update_listing_aggregates($review_id, get_post($review_id), true);
            }
        }
    }
}

function page_exists($title, $content = '', $date = '', $type = 'page')
{
    $page = get_page_by_title($title, OBJECT, $type);
    return $page ? true : false;
}

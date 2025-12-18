<?php
/**
 * Class Settings Manager
 * 
 * Handles plugin configuration page for renaming CPTs.
 */
class Settings_Manager
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'tools.php',
            'Directory Settings',
            'Directory Settings',
            'manage_options',
            'directory-settings',
            array($this, 'render_page')
        );
    }

    public function register_settings()
    {
        register_setting('directory_settings_group', 'directory_cpt_labels');
    }

    public function render_page()
    {
        $options = get_option('directory_cpt_labels', array());

        // Defaults
        $company_singular = isset($options['company_singular']) ? $options['company_singular'] : 'Company';
        $company_plural = isset($options['company_plural']) ? $options['company_plural'] : 'Companies';
        $company_slug = isset($options['company_slug']) ? $options['company_slug'] : 'company';

        $review_singular = isset($options['review_singular']) ? $options['review_singular'] : 'Review';
        $review_plural = isset($options['review_plural']) ? $options['review_plural'] : 'Reviews';
        $review_slug = isset($options['review_slug']) ? $options['review_slug'] : 'reviews';
        // The old $options variable is no longer needed as we use get_option directly for each field.
        // $options = get_option('directory_cpt_labels', array());

        // Defaults are now handled directly in get_option calls in the HTML.
        ?>
        <div class="wrap">
            <h1>Directory Settings</h1>
            <p>Customize the labels and slugs for your directory listings.</p>

            <form method="post" action="options.php">
                <?php
                settings_fields('directory_cpt_settings');
                settings_fields('directory_monetization'); // Register this group
                do_settings_sections('directory-settings');
                ?>

                <table class="form-table">
                    <th scope="row" colspan="2">
                        <hr>
                        <h2>Review Type</h2>
                    </th>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Singular Name</th>
                        <td><input type="text" name="directory_cpt_labels[review_singular]"
                                value="<?php echo esc_attr($review_singular); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Plural Name</th>
                        <td><input type="text" name="directory_cpt_labels[review_plural]"
                                value="<?php echo esc_attr($review_plural); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">URL Slug</th>
                        <td><input type="text" name="directory_cpt_labels[review_slug]"
                                value="<?php echo esc_attr($review_slug); ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" colspan="2">
                            <hr>
                            <h2>Niche Branding & Monetization</h2>
                        </th>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Directory Niche</th>
                        <td>
                            <input type="text" name="directory_niche" class="regular-text"
                                value="<?php echo esc_attr(get_option('directory_niche', 'local businesses')); ?>"
                                placeholder="e.g. plumbers, restaurants, dentists" />
                            <p class="description">Your target niche - used in homepage and footer text.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Target City/Region</th>
                        <td>
                            <input type="text" name="directory_city" class="regular-text"
                                value="<?php echo esc_attr(get_option('directory_city', '')); ?>"
                                placeholder="e.g. Austin, TX or Greater Chicago Area" />
                            <p class="description">Primary location focus for SEO and branding.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">AdSense Publisher ID</th>
                        <td>
                            <input type="text" name="directory_adsense_id" class="regular-text"
                                value="<?php echo esc_attr(get_option('directory_adsense_id', '')); ?>"
                                placeholder="ca-pub-XXXXXXXXXXXXXXXX" />
                            <p class="description">Your Google AdSense publisher ID. Leave blank to disable ads.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" colspan="2">
                            <hr>
                            <h2>Automated Data Acquisition (Phase 6)</h2>
                        </th>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Data Provider</th>
                        <td>
                            <select id="fetch_provider">
                                <option value="osm">OpenStreetMap (Nominatim) - Free</option>
                                <option value="google">Google Places API - Premium</option>
                                <option value="brightdata">Bright Data (Web Unlocker) - Scraper</option>
                            </select>
                            <p class="description">Select the source for the Fetch Engine.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">API Key (if required)</th>
                        <td>
                            <input type="password" id="fetch_api_key" class="regular-text"
                                placeholder="e.g. Google Places / Scraper API Key" />
                            <p class="description">Required for Google Places and Bright Data.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Topic / Keyword</th>
                        <td><input type="text" id="fetch_keyword" class="regular-text"
                                placeholder="e.g. Sushi, Plumbers, CRM" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Target Location</th>
                        <td><input type="text" id="fetch_location" class="regular-text" placeholder="e.g. New York, NY" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Action</th>
                        <td>
                            <button type="button" id="btn_fetch_data" class="button button-secondary">Fetch & Import
                                Data</button>
                            <span id="fetch_status" style="margin-left: 10px; font-weight: bold;"></span>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" colspan="2">
                            <hr>
                            <h2>🚀 Metro Orchestrator (Autonomous)</h2>
                        </th>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Target Metro Area</th>
                        <td>
                            <input type="text" id="metro_city" class="regular-text" placeholder="e.g. Austin, TX" />
                            <p class="description">The Orchestrator will autonomously loop through top niches (Plumbers,
                                Electricians, etc.) for this city.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Launch Campaign</th>
                        <td>
                            <button type="button" id="btn_launch_metro" class="button button-primary button-large">LAUNCH METRO
                                CAMPAIGN</button>
                            <div id="metro_status"
                                style="margin-top: 10px; padding: 10px; background: #f0f0f1; display:none; max-height: 200px; overflow-y: auto;">
                            </div>
                        </td>
                    </tr>
                </table>

                <script>
                    jQuery(document).ready(function ($) {
                        $('#btn_fetch_data').on('click', function () {
                            var btn = $(this);
                            var status = $('#fetch_status');

                            var provider = $('#fetch_provider').val();
                            var keyword = $('#fetch_keyword').val();
                            var location = $('#fetch_location').val();
                            var apiKey = $('#fetch_api_key').val();

                            if (!keyword) {
                                alert('Please enter a Keyword');
                                return;
                            }

                            if (provider !== 'osm' && !apiKey) {
                                if (!confirm('You selected a premium provider but provided no API Key. Continue?')) {
                                    return;
                                }
                            }

                            btn.prop('disabled', true).text('Fetching from ' + provider + '...');
                            status.text('Connecting to ' + provider + '...');

                            $.post(ajaxurl, {
                                action: 'directory_fetch_data',
                                security: '<?php echo wp_create_nonce("directory_fetch_nonce"); ?>',
                                provider: provider,
                                keyword: keyword,
                                location: location,
                                api_key: apiKey
                            }, function (response) {
                                btn.prop('disabled', false).text('Fetch & Import Data');
                                if (response.success) {
                                    status.css('color', 'green').html(response.data.message + '<br><small>' + response.data.sync_status + '</small>');
                                } else {
                                    status.css('color', 'red').text('Error: ' + response.data);
                                }
                            });
                        });
                    });

                    // Metro Launch Handler
                    $('#btn_launch_metro').on('click', function () {
                        var btn = $(this);
                        var city = $('#metro_city').val();
                        var status = $('#metro_status');

                        if (!city) {
                            alert('Please enter a Target Metro Area');
                            return;
                        }

                        if (!confirm('⚠ WARNING: This will autonomous fetch listings and trigger simulated outreach emails for multiple niches in ' + city + '. Proceed?')) {
                            return;
                        }

                        btn.prop('disabled', true).text('Orchestrating...');
                        status.show().html('Initializing Campaign...<br>');

                        $.post(ajaxurl, {
                            action: 'directory_launch_metro',
                            security: '<?php echo wp_create_nonce("directory_fetch_nonce"); ?>', // Reusing nonce for MVP
                            city: city
                        }, function (response) {
                            btn.prop('disabled', false).text('LAUNCH METRO CAMPAIGN');
                            if (response.success) {
                                var logHtml = '<strong>Campaign Complete!</strong><br>';
                                $.each(response.data.log, function (i, item) {
                                    logHtml += '✅ ' + item + '<br>';
                                });
                                status.append(logHtml);
                            } else {
                                status.append('<span style="color:red">Error: ' + response.data + '</span><br>');
                            }
                        });
                    });
                            });
                </script>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

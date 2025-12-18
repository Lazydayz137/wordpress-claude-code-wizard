<?php
/**
 * Class Metro_Orchestrator
 * 
 * "The Director"
 * Orchestrates the entire lifecycle:
 * 1. Loop Niches
 * 2. Fetch Data
 * 3. Enrich (via Data_Fetcher hook)
 * 4. Trigger Outreach
 */

if (!defined('ABSPATH')) {
    exit;
}

class Metro_Orchestrator
{
    private $data_fetcher;
    private $outreach_manager;

    public function __construct()
    {
        $this->data_fetcher = new Data_Fetcher();
        $this->outreach_manager = new Outreach_Manager();

        add_action('wp_ajax_directory_launch_metro', array($this, 'handle_ajax_launch'));
    }

    public function handle_ajax_launch()
    {
        // Security Check
        check_ajax_referer('directory_fetch_nonce', 'security');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $city = sanitize_text_field($_POST['city']);

        if (empty($city)) {
            wp_send_json_error('City is required');
        }

        // Launch
        $log = $this->launch_metro_campaign($city);

        wp_send_json_success(array(
            'message' => 'Campaign Finished',
            'log' => $log
        ));
    }

    /**
     * Launch a campaign for a specific metro area
     * @param string $city City/Metro Name (e.g. "Austin, TX")
     * @param array $niches List of niches to target
     */
    public function launch_metro_campaign($city, $niches = [])
    {
        if (empty($niches)) {
            // Default set if none provided
            $niches = ['Plumbers', 'Electricians', 'Roofers', 'Dentists', 'Lawyers'];
        }

        $results_log = [];

        foreach ($niches as $niche) {
            $this->log("Processing Niche: $niche in $city...");

            // 1. Fetch & Store (This triggers AI Enrichment via hooks in Data_Fetcher)
            // We need to bypass the AJAX handler and call the logic directly.
            // Data_Fetcher needs a public method for this.

            // For now, we simulate the fetch call or rely on a new method we'll add to Data_Fetcher
            $fetched_perm = $this->data_fetcher->fetch_and_process($niche, $city);

            $count = count($fetched_perm);
            $results_log[] = "$niche: Found $count";

            // 2. Outreach
            foreach ($fetched_perm as $listing_data) {
                // We need the post ID, which import_to_wordpress returns or we find it
                // This requires Data_Fetcher to return IDs or objects.
                // For this MVP, we will assume fetch_and_process returns the composite data

                // Trigger Outreach
                $this->outreach_manager->process_listing_outreach(0, $listing_data);
            }
        }

        return $results_log;
    }

    private function log($message)
    {
        $file = DIRECTORY_CORE_PATH . 'data/orchestrator_log.txt';
        file_put_contents($file, $message . PHP_EOL, FILE_APPEND);
    }
}

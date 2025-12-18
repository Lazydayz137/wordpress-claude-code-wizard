<?php
/**
 * Class Outreach_Manager
 * 
 * "The Salesman"
 * Handles finding contact info (simulated) and sending outreach emails.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Outreach_Manager
{
    private $aic_engine;

    public function __construct()
    {
        // Dependency Injection of AI Engine if available
        if (class_exists('AIC_Engine')) {
            $this->aic_engine = new AIC_Engine();
        }
    }

    /**
     * Process a listing for outreach
     * 1. Find Contact Info
     * 2. Draft Email
     * 3. Send (Simulate)
     */
    public function process_listing_outreach($post_id, $data)
    {
        $business_name = $data['basics']['name'];
        $industry = $data['use_cases'][0]['industry'];
        $location = $data['basics']['headquarters'];

        // 1. Find Contact (Simulation)
        $email = $this->find_contact_email($business_name, $location);

        if (!$email) {
            $this->log("No email found for $business_name. Skipping.");
            return false;
        }

        // 2. Draft Email
        $subject = "Question about $business_name services";
        $body = $this->draft_email($business_name, $industry, $location);

        // 3. Send (Simulate)
        return $this->send_email_simulation($email, $subject, $body, $business_name);
    }

    /**
     * Simulate scraping a website for an email
     */
    private function find_contact_email($name, $location)
    {
        // In real life, this would use Hunter.io API or scraping
        // Simulation: 80% chance of finding an email
        if (rand(1, 10) > 2) {
            $sanitized_name = sanitize_title($name);
            return "contact@{$sanitized_name}.com";
        }
        return false;
    }

    /**
     * Use AI to draft a personalized email
     */
    private function draft_email($name, $industry, $location)
    {
        if ($this->aic_engine) {
            // Future: Use AIC_Engine->generate_email()
            // For now, simple template
        }

        return "Hi there,\n\nI found $name listed as a top $industry in $location and just created a premium listing for you on our local directory.\n\nIt's completely free to claim. Check it out here: " . home_url() . "\n\nBest,\nDirectory Team";
    }

    /**
     * Simulate sending the email
     */
    private function send_email_simulation($to, $subject, $body, $business_name)
    {
        $log_entry = sprintf(
            "[%s] SENT TO: %s | SUBJ: %s | BODY_LEN: %d",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            strlen($body)
        );

        $this->log($log_entry);

        // Save meta to prevent double sending
        // update_post_meta($post_id, '_outreach_sent', time());

        return true;
    }

    private function log($message)
    {
        $file = DIRECTORY_CORE_PATH . 'data/outreach_log.txt';
        file_put_contents($file, $message . PHP_EOL, FILE_APPEND);
    }
}

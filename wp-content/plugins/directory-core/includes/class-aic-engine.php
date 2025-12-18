<?php
/**
 * Class AIC_Engine
 * 
 * AI Content Engine
 * Handles generation of business descriptions, reviews, and blog content
 * via LLM providers (simulated or real).
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIC_Engine
{
    private $api_key;
    private $provider;

    public function __construct()
    {
        $this->api_key = get_option('directory_ai_api_key', '');
        $this->provider = get_option('directory_ai_provider', 'openai');
    }

    /**
     * Generate a rich business description based on basic data
     * 
     * @param array $data Basic business data (name, industry, location, keywords)
     * @return string Generated HTML description
     */
    public function generate_listing_description($data)
    {
        $name = isset($data['name']) ? $data['name'] : 'The Business';
        $industry = isset($data['industry']) ? $data['industry'] : 'Service';
        $location = isset($data['location']) ? $data['location'] : 'Local Area';
        $raw_summary = isset($data['raw_summary']) ? $data['raw_summary'] : '';

        // Construct Prompt
        $prompt = "Write a compelling, professional, and SEO-friendly 300-word business description for '$name', a $industry company based in $location. ";
        $prompt .= "Use the following existing details as a base: '$raw_summary'. ";
        $prompt .= "Format the output with HTML tags (<h3> for headings, <p> for paragraphs, <ul> for lists). ";
        $prompt .= "Include a section on 'Why Choose Us' and 'Our Services'.";

        return $this->call_llm($prompt);
    }

    /**
     * Enhance a short user review to be more descriptive (Simulation)
     */
    public function enhance_review($short_text)
    {
        $prompt = "Rewrite the following short review to be more detailed and professional, while keeping the same sentiment: '$short_text'";
        return $this->call_llm($prompt);
    }

    /**
     * Internal method to call the LLM API
     */
    private function call_llm($prompt)
    {
        // 1. Check for API Key
        if (empty($this->api_key)) {
            return $this->simulation_fallback($prompt);
        }

        // 2. Real API Call (Placeholder for OpenAI implementation)
        // In a real scenario, this would use wp_remote_post to https://api.openai.com/v1/chat/completions

        // For now, even with a key, we'll return a simulated "Success" message to avoid burning credits during dev
        // unless explicitly implemented.
        return $this->simulation_fallback($prompt, true);
    }

    /**
     * Simulation / Fallback Generator
     */
    private function simulation_fallback($prompt, $has_key = false)
    {
        // Extract business name if possible for better simulation
        preg_match("/description for '([^']+)'/", $prompt, $matches);
        $name = isset($matches[1]) ? $matches[1] : 'this business';

        $output = "<!-- AIC_Engine Generated Content (" . ($has_key ? 'Authenticated' : 'Simulation') . ") -->";
        $output .= "<h3>About $name</h3>";
        $output .= "<p>$name is a premier provider of top-quality services. We are dedicated to excellence and ensuring customer satisfaction in every interaction. With years of experience, our team brings professionalism and expertise to every project.</p>";

        $output .= "<h3>Why Choose Us?</h3>";
        $output .= "<ul>";
        $output .= "<li><strong>Experienced Team:</strong> Our professionals are highly trained and skilled.</li>";
        $output .= "<li><strong>Quality Guaranteed:</strong> We never compromise on the quality of our work.</li>";
        $output .= "<li><strong>Customer Focus:</strong> Your needs are our top priority.</li>";
        $output .= "</ul>";

        $output .= "<h3>Our Services</h3>";
        $output .= "<p>We offer a wide range of services tailored to meet your specific needs. Contact us today to learn more about how we can help you achieve your goals.</p>";

        return $output;
    }
}

<?php get_header(); ?>

<main class="site-main">
    <div class="company-single-container">
        <?php while (have_posts()):
            the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="company-header">
                    <h1><?php the_title(); ?></h1>

                    <?php
                    $logo = get_post_meta(get_the_ID(), 'logo', true);
                    if ($logo): ?>
                        <div class="company-logo">
                            <img src="<?php echo esc_url($logo); ?>" alt="<?php the_title(); ?> Logo">
                        </div>
                    <?php endif; ?>
                </header>

                <div class="company-content">
                    <?php the_content(); ?>
                </div>

                <!-- Custom Fields Display (Basics) -->
                <div class="company-details">
                    <?php
                    $fields = ['founded', 'headquarters', 'employees', 'funding'];
                    foreach ($fields as $field) {
                        $value = get_post_meta(get_the_ID(), $field, true);
                        if ($value) {
                            echo '<p><strong>' . ucfirst($field) . ':</strong> ' . esc_html($value) . '</p>';
                        }
                    }
                    ?>
                </div>

                <?php
                // Prepare FAQ Schema
                $faq_schema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => []
                ];

                $faqs = [];

                // Q1: What is it?
                $description = get_the_content();
                if ($description) {
                    $faqs[] = [
                        'question' => sprintf(__('What is %s?', 'my-custom-theme'), get_the_title()),
                        'answer' => wp_trim_words($description, 50)
                    ];
                }

                // Q2: Pricing
                $pricing_model = get_post_meta(get_the_ID(), 'pricing_model', true);
                $starter_price = get_post_meta(get_the_ID(), 'pricing_starter_price', true);
                if ($pricing_model) {
                    $answer = sprintf(__('The pricing model is %s.', 'my-custom-theme'), $pricing_model);
                    if ($starter_price) {
                        $answer .= sprintf(__(' Pricing starts at %s.', 'my-custom-theme'), $starter_price);
                    }
                    $faqs[] = [
                        'question' => sprintf(__('How much does %s cost?', 'my-custom-theme'), get_the_title()),
                        'answer' => $answer
                    ];
                }

                // Q3: Location
                $headquarters = get_post_meta(get_the_ID(), 'headquarters', true);
                if ($headquarters) {
                    $faqs[] = [
                        'question' => sprintf(__('Where is %s located?', 'my-custom-theme'), get_the_title()),
                        'answer' => sprintf(__('%s is headquartered in %s.', 'my-custom-theme'), get_the_title(), $headquarters)
                    ];
                }

                // Convert to Schema
                foreach ($faqs as $faq) {
                    $faq_schema['mainEntity'][] = [
                        '@type' => 'Question',
                        'name' => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['answer']
                        ]
                    ];
                }
                ?>

                <!-- FAQ Schema -->
                <script type="application/ld+json">
                        <?php echo json_encode($faq_schema); ?>
                    </script>

                <div class="company-content glass-panel p-6 mb-8 reveal-item">
                    <h2><?php _e('About', 'my-custom-theme'); ?>     <?php the_title(); ?></h2>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>

                <?php if (!empty($faqs)): ?>
                    <div class="company-faq glass-panel p-6 mb-8 reveal-item">
                        <h2><?php _e('Frequently Asked Questions', 'my-custom-theme'); ?></h2>
                        <div class="faq-list">
                            <?php foreach ($faqs as $faq): ?>
                                <div class="faq-item">
                                    <h3 class="faq-question"><?php echo esc_html($faq['question']); ?></h3>
                                    <p class="faq-answer"><?php echo esc_html($faq['answer']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Reviews Section -->
                <div class="company-reviews glass-panel p-6 reveal-item" id="reviews">
                    <h2><?php _e('Reviews', 'directory-core'); ?></h2>

                    <?php
                    // Display Reviews
                    $reviews = Review_Manager::get_reviews_for_listing(get_the_ID());

                    if ($reviews): ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <span class="review-rating">
                                            <?php echo str_repeat('&#9733;', intval($review->rating)); ?>
                                            <?php echo str_repeat('&#9734;', 5 - intval($review->rating)); ?>
                                        </span>
                                        <span class="review-title"><?php echo esc_html($review->post_title); ?></span>
                                    </div>
                                    <div class="review-content">
                                        <?php echo wpautop(esc_html($review->post_content)); ?>
                                    </div>
                                    <?php if ($review->photo_id): ?>
                                        <div class="review-photo">
                                            <?php echo wp_get_attachment_image($review->photo_id, 'thumbnail'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p><?php _e('No reviews yet. Be the first to review!', 'directory-core'); ?></p>
                    <?php endif; ?>

                    <!-- Review Form -->
                    <?php echo Review_Manager::render_review_form(get_the_ID()); ?>
                </div>

            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
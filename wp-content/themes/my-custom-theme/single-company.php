<?php get_header(); ?>

<main class="site-main">
    <div class="company-single-container">
        <?php while (have_posts()):
            the_post();
            $is_verified = Monetization_Manager::is_verified(get_the_ID());
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="company-header glass-panel p-6 mb-8 reveal-item">
                    <div class="header-top">
                        <h1><?php the_title(); ?></h1>
                        <?php if ($is_verified): ?>
                            <div class="verified-badge">
                                &#10003; <?php _e('Verified Listing', 'my-custom-theme'); ?>
                            </div>
                        <?php else: ?>
                            <a href="#claim-form" class="unclaimed-link">
                                &#9888; <?php _e('Unclaimed', 'my-custom-theme'); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php
                    $logo = get_post_meta(get_the_ID(), 'logo', true);
                    if ($logo): ?>
                        <div class="company-logo">
                            <img src="<?php echo esc_url($logo); ?>" alt="<?php the_title(); ?> Logo">
                        </div>
                    <?php endif; ?>
                </header>

                <!-- Ad Slot: Top -->
                <?php Monetization_Manager::render_ad_slot('header'); ?>

                <div class="company-content-wrapper" style="display:grid; grid-template-columns: 2fr 1fr; gap:30px;">

                    <!-- Main Column -->
                    <div class="main-column">
                        <div class="company-content glass-panel p-6 mb-8 reveal-item">
                            <h2><?php _e('About', 'my-custom-theme'); ?>     <?php the_title(); ?></h2>
                            <div class="entry-content">
                                <?php the_content(); ?>
                            </div>
                        </div>

                        <!-- FAQ Section -->
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
                            <!-- Reviews Loop (Existing Logic) -->
                            <?php
                            $reviews = Review_Manager::get_reviews_for_listing(get_the_ID());
                            if ($reviews): ?>
                                <div class="reviews-list">
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-item">
                                            <div class="review-header">
                                                <span
                                                    class="review-rating"><?php echo str_repeat('&#9733;', intval($review->rating)); ?></span>
                                                <span class="review-title"><?php echo esc_html($review->post_title); ?></span>
                                            </div>
                                            <div class="review-content"><?php echo wpautop(esc_html($review->post_content)); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p><?php _e('No reviews yet.', 'directory-core'); ?></p>
                            <?php endif; ?>
                            <?php echo Review_Manager::render_review_form(get_the_ID()); ?>
                        </div>
                    </div>

                    <!-- Sidebar Column (Monetization & Details) -->
                    <div class="sidebar-column">

                        <!-- Company Details -->
                        <div class="company-details glass-panel p-6 mb-8 reveal-item">
                            <h3><?php _e('At a Glance', 'my-custom-theme'); ?></h3>
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

                        <!-- Ad Slot: Sidebar -->
                        <?php Monetization_Manager::render_ad_slot('sidebar'); ?>

                        <!-- Claim / Contact Actions -->
                        <?php if (!$is_verified): ?>
                            <div class="claim-cta glass-panel p-6 mb-8 reveal-item" id="claim-form">
                                <?php get_template_part('template-parts/components/claim-form'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Lead Gen Form (Contact) -->
                        <div class="contact-widget glass-panel p-6 reveal-item">
                            <h3><?php echo $is_verified ? __('Contact Business', 'my-custom-theme') : __('Request a Quote', 'my-custom-theme'); ?>
                            </h3>
                            <?php echo do_shortcode('[directory_contact_form]'); ?>
                        </div>

                    </div>
                </div>

            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
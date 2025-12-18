<?php
/**
 * Template Name: Directory Homepage
 * 
 * Premium homepage template for the business directory
 * Features: Hero, search, featured categories, top listings, CTA sections
 */

get_header();

// Get niche settings for dynamic content
$niche = get_option('directory_niche', 'local businesses');
$city = get_option('directory_city', 'your city');
?>

<main class="site-main homepage">

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">
                <?php printf(__('Find the Best %s', 'my-custom-theme'), ucwords(esc_html($niche))); ?>
            </h1>
            <p class="hero-subtitle">
                <?php printf(__('Compare prices, read reviews, and find trusted professionals in %s', 'my-custom-theme'), esc_html($city)); ?>
            </p>

            <!-- Search Bar -->
            <div class="hero-search glass-panel">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
                    class="hero-search-form">
                    <input type="search" name="s"
                        placeholder="<?php _e('Search businesses, services...', 'my-custom-theme'); ?>"
                        class="hero-search-input">
                    <input type="hidden" name="post_type" value="company">
                    <button type="submit"
                        class="btn btn-primary hero-search-btn"><?php _e('Search', 'my-custom-theme'); ?></button>
                </form>
            </div>
        </div>
    </section>

    <!-- Browse by Category -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title"><?php _e('Browse by Category', 'my-custom-theme'); ?></h2>

            <div class="category-grid">
                <?php
                $industries = get_terms(array(
                    'taxonomy' => 'industry',
                    'hide_empty' => false,
                    'number' => 8,
                ));

                if (!empty($industries) && !is_wp_error($industries)):
                    foreach ($industries as $industry):
                        $count = $industry->count;
                        ?>
                        <a href="<?php echo esc_url(get_term_link($industry)); ?>" class="category-card glass-panel">
                            <h3 class="category-title">
                                <?php echo esc_html($industry->name); ?>
                            </h3>
                            <span class="category-count">
                                <?php printf(_n('%s listing', '%s listings', $count, 'my-custom-theme'), $count); ?>
                            </span>
                        </a>
                    <?php endforeach;
                else: ?>
                    <p><?php _e('No categories found. Add industries to get started.', 'my-custom-theme'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Listings -->
    <section class="featured-section">
        <div class="container">
            <h2 class="section-title"><?php _e('Top Rated Businesses', 'my-custom-theme'); ?></h2>
            <p class="section-subtitle">
                <?php _e('Verified listings with the highest customer ratings', 'my-custom-theme'); ?>
            </p>

            <?php
            $featured = new WP_Query(array(
                'post_type' => 'company',
                'posts_per_page' => 6,
                'meta_key' => '_listing_average_rating',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            ));

            if ($featured->have_posts()): ?>
                <div class="directory-grid">
                    <?php while ($featured->have_posts()):
                        $featured->the_post(); ?>
                        <?php get_template_part('template-parts/components/card', 'listing'); ?>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <?php
                // Fallback: Show any companies
                $any_companies = new WP_Query(array(
                    'post_type' => 'company',
                    'posts_per_page' => 6,
                ));
                if ($any_companies->have_posts()): ?>
                    <div class="directory-grid">
                        <?php while ($any_companies->have_posts()):
                            $any_companies->the_post(); ?>
                            <?php get_template_part('template-parts/components/card', 'listing'); ?>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center"><?php _e('Import listings to see them here!', 'my-custom-theme'); ?></p>
                <?php endif;
                wp_reset_postdata(); ?>
            <?php endif;
            wp_reset_postdata(); ?>

            <div class="text-center mt-2">
                <a href="<?php echo home_url('/companies/'); ?>"
                    class="btn btn-outline"><?php _e('View All Listings', 'my-custom-theme'); ?></a>
            </div>
        </div>
    </section>

    <!-- Browse by Location -->
    <section class="locations-section">
        <div class="container">
            <h2 class="section-title"><?php _e('Browse by Location', 'my-custom-theme'); ?></h2>

            <div class="locations-grid">
                <?php
                $locations = get_terms(array(
                    'taxonomy' => 'location',
                    'hide_empty' => false,
                    'number' => 12,
                    'orderby' => 'count',
                    'order' => 'DESC',
                ));

                if (!empty($locations) && !is_wp_error($locations)):
                    foreach ($locations as $location): ?>
                        <a href="<?php echo esc_url(get_term_link($location)); ?>" class="location-card">
                            <strong class="location-name"><?php echo esc_html($location->name); ?></strong>
                            <span class="location-count">
                                <?php echo $location->count; ?>         <?php _e('businesses', 'my-custom-theme'); ?>
                            </span>
                        </a>
                    <?php endforeach;
                else: ?>
                    <p><?php _e('No locations found. Add locations to get started.', 'my-custom-theme'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Business Owner CTA -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title"><?php _e('Are You a Business Owner?', 'my-custom-theme'); ?></h2>
            <p class="cta-text">
                <?php _e('Claim your free listing, respond to reviews, and reach thousands of potential customers searching for your services.', 'my-custom-theme'); ?>
            </p>
            <a href="<?php echo home_url('/companies/'); ?>" class="btn cta-btn">
                <?php _e('Claim Your Listing for Free', 'my-custom-theme'); ?>
            </a>
        </div>
    </section>

    <!-- Ad Slot -->
    <?php Monetization_Manager::render_ad_slot('homepage'); ?>

</main>

<?php get_footer(); ?>
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
    <section class="hero-section"
        style="background: var(--gradient-primary); padding: 4rem 0; text-align: center; color: white;">
        <div class="container">
            <h1 style="color: white; font-size: 2.5rem; margin-bottom: 1rem;">
                <?php printf(__('Find the Best %s', 'my-custom-theme'), ucwords(esc_html($niche))); ?>
            </h1>
            <p style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 2rem;">
                <?php printf(__('Compare prices, read reviews, and find trusted professionals in %s', 'my-custom-theme'), esc_html($city)); ?>
            </p>

            <!-- Search Bar -->
            <div class="hero-search glass-panel"
                style="max-width: 600px; margin: 0 auto; padding: 1.5rem; background: rgba(255,255,255,0.95); border-radius: var(--radius-xl);">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
                    style="display: flex; gap: 1rem;">
                    <input type="search" name="s"
                        placeholder="<?php _e('Search businesses, services...', 'my-custom-theme'); ?>"
                        style="flex: 1; padding: 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 1rem;">
                    <input type="hidden" name="post_type" value="company">
                    <button type="submit" class="btn btn-primary"
                        style="padding: 1rem 2rem;"><?php _e('Search', 'my-custom-theme'); ?></button>
                </form>
            </div>
        </div>
    </section>

    <!-- Browse by Category -->
    <section class="categories-section" style="padding: 4rem 0;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 2rem;"><?php _e('Browse by Category', 'my-custom-theme'); ?>
            </h2>

            <div class="category-grid"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">
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
                        <a href="<?php echo esc_url(get_term_link($industry)); ?>" class="category-card glass-panel"
                            style="display: block; padding: 1.5rem; text-align: center; text-decoration: none; transition: var(--transition-smooth);">
                            <h3 style="margin-bottom: 0.5rem; font-size: 1.1rem; color: var(--color-text-main);">
                                <?php echo esc_html($industry->name); ?>
                            </h3>
                            <span style="color: var(--color-text-muted); font-size: 0.9rem;">
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
    <section class="featured-section" style="padding: 4rem 0; background: var(--color-bg-body);">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 0.5rem;">
                <?php _e('Top Rated Businesses', 'my-custom-theme'); ?></h2>
            <p style="text-align: center; color: var(--color-text-muted); margin-bottom: 2rem;">
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
                    <p style="text-align: center;"><?php _e('Import listings to see them here!', 'my-custom-theme'); ?></p>
                <?php endif;
                wp_reset_postdata(); ?>
            <?php endif;
            wp_reset_postdata(); ?>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="<?php echo home_url('/companies/'); ?>"
                    class="btn btn-outline"><?php _e('View All Listings', 'my-custom-theme'); ?></a>
            </div>
        </div>
    </section>

    <!-- Browse by Location -->
    <section class="locations-section" style="padding: 4rem 0;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 2rem;"><?php _e('Browse by Location', 'my-custom-theme'); ?>
            </h2>

            <div class="locations-grid"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
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
                        <a href="<?php echo esc_url(get_term_link($location)); ?>"
                            style="display: block; padding: 1rem; background: white; border: 1px solid var(--color-border); border-radius: var(--radius-md); text-decoration: none; transition: var(--transition-fast);">
                            <strong style="color: var(--color-text-main);"><?php echo esc_html($location->name); ?></strong>
                            <span style="color: var(--color-text-muted); display: block; font-size: 0.85rem;">
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
    <section class="cta-section"
        style="padding: 4rem 0; background: var(--gradient-primary); text-align: center; color: white;">
        <div class="container">
            <h2 style="color: white; margin-bottom: 1rem;"><?php _e('Are You a Business Owner?', 'my-custom-theme'); ?>
            </h2>
            <p
                style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                <?php _e('Claim your free listing, respond to reviews, and reach thousands of potential customers searching for your services.', 'my-custom-theme'); ?>
            </p>
            <a href="<?php echo home_url('/companies/'); ?>" class="btn"
                style="background: white; color: var(--color-primary); padding: 1rem 2rem; font-size: 1.1rem;">
                <?php _e('Claim Your Listing for Free', 'my-custom-theme'); ?>
            </a>
        </div>
    </section>

    <!-- Ad Slot -->
    <?php Monetization_Manager::render_ad_slot('homepage'); ?>

</main>

<?php get_footer(); ?>
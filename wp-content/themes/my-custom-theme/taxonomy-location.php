<?php
/**
 * Template: Location Taxonomy
 * 
 * Handles /location/new-york/ style pages.
 * Implements "Best [Category] in [Location]" logic.
 */

get_header();

$location = get_queried_object();
$location_name = $location->name;

// Check for 'industry' filter (future feature: /location/new-york/?industry=software)
$industry_filter = get_query_var('industry');
$industry_name = $industry_filter ? ucfirst($industry_filter) : 'Companies';

// SEO Title Logic
$page_title = sprintf(__('Best %s in %s', 'my-custom-theme'), $industry_name, $location_name);
?>

<main class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php echo esc_html($page_title); ?></h1>
            <div class="taxonomy-description">
                <?php echo wp_kses_post(term_description()); ?>
            </div>
        </header>

        <?php if (have_posts()): ?>
            <div class="directory-grid">
                <?php while (have_posts()):
                    the_post(); ?>
                    <?php get_template_part('template-parts/components/card', 'listing'); ?>
                <?php endwhile; ?>
            </div>

            <?php the_posts_navigation(); ?>
        <?php else: ?>
            <p><?php printf(__('No %s found in %s.', 'my-custom-theme'), strtolower($industry_name), $location_name); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
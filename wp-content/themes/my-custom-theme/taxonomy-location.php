<?php
/**
 * Template: Location Taxonomy
 * 
 * Handles /location/new-york/ style pages.
 * Implements "Best [Category] in [Location]" logic.
 */

$location = get_queried_object();
$location_name = $location->name;

// Check for 'industry' filter (future feature: /location/new-york/?industry=software)
$industry_filter = get_query_var('industry');
$industry_name = $industry_filter ? ucfirst($industry_filter) : 'Companies';

// SEO Logic
add_filter('document_title_parts', function ($title) use ($location_name, $industry_name) {
    $title['title'] = sprintf(__('Best %s in %s - Reviews & Pricing', 'my-custom-theme'), $industry_name, $location_name);
    return $title;
});



// SEO Title Logic (for H1)
$page_title = sprintf(__('Best %s in %s', 'my-custom-theme'), $industry_name, $location_name);

get_header();
?>

<main class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php echo esc_html($page_title); ?></h1>
            <div class="taxonomy-description">
                <?php echo wp_kses_post(term_description()); ?>
            </div>
        </header>

        <div class="directory-toolbar" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
            <form class="directory-sort" method="get">
                <select name="orderby" onchange="this.form.submit()">
                    <option value="date" <?php selected(get_query_var('orderby'), 'date'); ?>>
                        <?php _e('Newest', 'my-custom-theme'); ?>
                    </option>
                    <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>>
                        <?php _e('Name (A-Z)', 'my-custom-theme'); ?>
                    </option>
                    <option value="rating" <?php selected(get_query_var('orderby'), 'meta_value_num'); ?>>
                        <?php _e('Highest Rated', 'my-custom-theme'); ?>
                    </option>
                </select>
            </form>
        </div>

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
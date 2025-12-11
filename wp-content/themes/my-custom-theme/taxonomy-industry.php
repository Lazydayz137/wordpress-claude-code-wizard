<?php
/**
 * Template: Industry Taxonomy
 * 
 * Handles /industry/software/ style pages.
 */

$industry = get_queried_object();
$industry_name = $industry->name;

// SEO Logic
add_filter('document_title_parts', function ($title) use ($industry_name) {
    $title['title'] = sprintf(__('Best %s Companies & Reviews', 'my-custom-theme'), $industry_name);
    return $title;
});



get_header();
?>

<main class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php printf(__('Top %s Companies', 'my-custom-theme'), $industry_name); ?></h1>
            <div class="taxonomy-description">
                <?php echo wp_kses_post(term_description()); ?>
            </div>
        </header>

        <div class="directory-toolbar"
            style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
            <div class="layout-switcher">
                <a href="?layout=grid"
                    class="btn btn-outline <?php echo (!isset($_GET['layout']) || $_GET['layout'] === 'grid') ? 'active' : ''; ?>"><span
                        class="dashicons dashicons-grid-view"></span> Grid</a>
                <a href="?layout=list"
                    class="btn btn-outline <?php echo (isset($_GET['layout']) && $_GET['layout'] === 'list') ? 'active' : ''; ?>"><span
                        class="dashicons dashicons-list-view"></span> List</a>
                <a href="?layout=compact"
                    class="btn btn-outline <?php echo (isset($_GET['layout']) && $_GET['layout'] === 'compact') ? 'active' : ''; ?>"><span
                        class="dashicons dashicons-menu"></span> Compact</a>
            </div>

            <form class="directory-sort" method="get" style="display: flex; align-items: center; gap: 10px;">
                <?php if (isset($_GET['layout'])): ?>
                    <input type="hidden" name="layout" value="<?php echo esc_attr($_GET['layout']); ?>">
                <?php endif; ?>
                <label><?php _e('Sort by:', 'my-custom-theme'); ?></label>
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

        <?php
        $layout = isset($_GET['layout']) ? $_GET['layout'] : 'grid';
        $grid_class = 'directory-grid';
        if ($layout === 'list')
            $grid_class = 'directory-list';
        if ($layout === 'compact')
            $grid_class = 'directory-compact';
        ?>

        <?php if (have_posts()): ?>
            <div class="<?php echo esc_attr($grid_class); ?>">
                <?php while (have_posts()):
                    the_post(); ?>
                    <?php get_template_part('template-parts/components/card', $layout); ?>
                <?php endwhile; ?>
            </div>

            <?php the_posts_navigation(); ?>
        <?php else: ?>
            <p><?php printf(__('No companies found in %s.', 'my-custom-theme'), $industry_name); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
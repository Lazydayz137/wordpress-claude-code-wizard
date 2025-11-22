<?php
/**
 * Template: Industry Taxonomy
 * 
 * Handles /industry/software/ style pages.
 */

get_header();

$industry = get_queried_object();
$industry_name = $industry->name;

?>

<main class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php printf(__('Top %s Companies', 'my-custom-theme'), $industry_name); ?></h1>
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
            <p><?php printf(__('No companies found in %s.', 'my-custom-theme'), $industry_name); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
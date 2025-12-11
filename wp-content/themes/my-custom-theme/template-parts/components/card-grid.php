<?php
/**
 * Component: Listing Card (Grid View)
 */

$rating = get_post_meta(get_the_ID(), '_listing_average_rating', true) ?: 0;
$review_count = get_post_meta(get_the_ID(), '_listing_review_count', true) ?: 0;
$headquarters = get_post_meta(get_the_ID(), 'headquarters', true);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('directory-card card-grid'); ?>>
    <?php if (has_post_thumbnail()): ?>
        <div class="card-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium_large'); ?>
            </a>
            <div class="card-badge"><?php echo esc_html(get_the_term_list(get_the_ID(), 'industry', '', ', ')); ?></div>
        </div>
    <?php endif; ?>

    <div class="card-content">
        <h2 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <div class="card-rating">
            <span
                class="stars"><?php echo str_repeat('★', round($rating)); ?><?php echo str_repeat('☆', 5 - round($rating)); ?></span>
            <span class="count">(<?php echo esc_html($review_count); ?> reviews)</span>
        </div>

        <?php if ($headquarters): ?>
            <div class="card-meta">
                <span class="dashicons dashicons-location"></span> <?php echo esc_html($headquarters); ?>
            </div>
        <?php endif; ?>

        <div class="card-excerpt">
            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php _e('View Details', 'my-custom-theme'); ?></a>
    </div>
</article>
<?php
/**
 * Component: Listing Card
 */

$rating = get_post_meta(get_the_ID(), '_listing_average_rating', true) ?: 0;
$review_count = get_post_meta(get_the_ID(), '_listing_review_count', true) ?: 0;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('directory-card'); ?>>
    <?php if (has_post_thumbnail()): ?>
        <div class="card-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('card-thumbnail'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="card-content">
        <h2 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <div class="card-rating">
            <span
                class="stars"><?php echo str_repeat('★', round($rating)); ?><?php echo str_repeat('☆', 5 - round($rating)); ?></span>
            <span class="count">(<?php echo esc_html($review_count); ?>)</span>
        </div>

        <div class="card-excerpt">
            <?php the_excerpt(); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="btn btn-outline"><?php _e('View Details', 'my-custom-theme'); ?></a>
    </div>
</article>
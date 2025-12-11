<?php
/**
 * Component: Listing Card (Compact View)
 */

$rating = get_post_meta(get_the_ID(), '_listing_average_rating', true) ?: 0;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('directory-card card-compact'); ?>
    style="flex-direction: row; align-items: center; padding: 10px; gap: 15px;">
    <?php if (has_post_thumbnail()): ?>
        <div class="card-image"
            style="width: 60px; height: 60px; flex-shrink: 0; border-radius: 4px; overflow: hidden; position: relative; padding-top: 0;">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('thumbnail', array('style' => 'width: 100%; height: 100%; object-fit: cover; position: absolute; top:0; left:0;')); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="card-content"
        style="padding: 0; flex-grow: 1; display: flex; align-items: center; justify-content: space-between;">
        <h3 class="card-title" style="font-size: 1rem; margin: 0;">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="card-rating" style="margin: 0;">
            <span class="stars"
                style="color: var(--color-warning);"><?php echo str_repeat('★', round($rating)); ?></span>
            <span style="font-weight: 700; margin-left: 5px;"><?php echo number_format($rating, 1); ?></span>
        </div>
    </div>

    <a href="<?php the_permalink(); ?>" class="btn btn-outline"
        style="padding: 5px 15px; font-size: 0.8rem;"><?php _e('View', 'my-custom-theme'); ?></a>
</article>
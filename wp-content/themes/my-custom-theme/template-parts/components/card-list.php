<?php
/**
 * Component: Listing Card (List View)
 */

$rating = get_post_meta(get_the_ID(), '_listing_average_rating', true) ?: 0;
$review_count = get_post_meta(get_the_ID(), '_listing_review_count', true) ?: 0;
$pricing_model = get_post_meta(get_the_ID(), 'pricing_model', true);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('directory-card card-list'); ?>
    style="flex-direction: row; align-items: center; gap: 20px;">
    <?php if (has_post_thumbnail()): ?>
        <div class="card-image" style="width: 250px; flex-shrink: 0; padding-top: 0; height: 180px; position: relative;">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover; position: absolute;')); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="card-content" style="flex-grow: 1;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title" style="margin-bottom: 5px;">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <div class="pricing-badge"
                style="background: var(--light-gray); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                <?php echo esc_html($pricing_model); ?>
            </div>
        </div>

        <div class="card-rating" style="margin-bottom: 10px;">
            <span
                class="stars"><?php echo str_repeat('★', round($rating)); ?><?php echo str_repeat('☆', 5 - round($rating)); ?></span>
            <span class="count">(<?php echo esc_html($review_count); ?> reviews)</span>
        </div>

        <div class="card-excerpt">
            <?php the_excerpt(); ?>
        </div>
    </div>

    <div class="card-actions"
        style="padding: 20px; border-left: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 10px; min-width: 180px;">
        <a href="<?php the_permalink(); ?>" class="btn btn-primary"
            style="width: 100%;"><?php _e('Visit Website', 'my-custom-theme'); ?></a>
        <a href="<?php the_permalink(); ?>#reviews" class="btn btn-outline"
            style="width: 100%; text-align: center;"><?php _e('Read Reviews', 'my-custom-theme'); ?></a>
    </div>
</article>
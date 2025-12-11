<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Simple SEO Implementation
    $meta_desc = get_bloginfo('description');
    if (is_singular('company')) {
        $meta_desc = get_the_excerpt();
        if (empty($meta_desc)) {
            $meta_desc = wp_trim_words(get_the_content(), 25);
        }
    } elseif (is_tax()) {
        $term_desc = term_description();
        if (!empty($term_desc)) {
            $meta_desc = wp_strip_all_tags($term_desc);
        } else {
            $term = get_queried_object();
            if ($term && !is_wp_error($term)) {
                if (is_tax('location')) {
                    $meta_desc = sprintf(__('Find the top rated companies in %s. Compare features, pricing, and read verified reviews.', 'my-custom-theme'), $term->name);
                } elseif (is_tax('industry')) {
                    $meta_desc = sprintf(__('Find and compare the best %s companies. Read reviews, check pricing, and find the perfect solution.', 'my-custom-theme'), $term->name);
                } else {
                    $meta_desc = sprintf(__('Explore %s in our directory.', 'my-custom-theme'), $term->name);
                }
            }
        }
    }
    ?>
    <meta name="description" content="<?php echo esc_attr(substr($meta_desc, 0, 160)); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header class="site-header">
        <div class="container">
            <h1 class="site-title">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php bloginfo('name'); ?>
                </a>
            </h1>
            <p class="site-description"><?php bloginfo('description'); ?></p>

            <button class="mobile-menu-toggle" aria-label="Toggle Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="header-actions">
                <div class="mega-menu-item dropdown">
                    <button class="dropdown-toggle"><?php _e('Browse Categories', 'my-custom-theme'); ?></button>
                    <div class="dropdown-menu mega-menu">
                        <div class="mega-menu-grid">
                            <?php
                            $industries = get_terms(array('taxonomy' => 'industry', 'hide_empty' => false, 'number' => 10));
                            if (!empty($industries) && !is_wp_error($industries)):
                                foreach ($industries as $industry): ?>
                                    <a href="<?php echo esc_url(get_term_link($industry)); ?>" class="mega-menu-link">
                                        <?php echo esc_html($industry->name); ?>
                                    </a>
                                <?php endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mega-menu-item dropdown">
                    <button class="dropdown-toggle"><?php _e('Locations', 'my-custom-theme'); ?></button>
                    <div class="dropdown-menu mega-menu">
                        <div class="mega-menu-grid">
                            <?php
                            $locations = get_terms(array('taxonomy' => 'location', 'hide_empty' => false, 'number' => 10));
                            if (!empty($locations) && !is_wp_error($locations)):
                                foreach ($locations as $location): ?>
                                    <a href="<?php echo esc_url(get_term_link($location)); ?>" class="mega-menu-link">
                                        <?php echo esc_html($location->name); ?>
                                    </a>
                                <?php endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>

                <div class="header-search">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="search" class="search-field"
                            placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'my-custom-theme'); ?>"
                            value="<?php echo get_search_query(); ?>" name="s" />
                        <button type="submit" class="search-submit"><?php _e('Search', 'my-custom-theme'); ?></button>
                    </form>
                </div>

                <?php if (has_nav_menu('primary')): ?>
                    <nav class="main-navigation">
                        <?php wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_class' => 'primary-menu',
                        )); ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </header>
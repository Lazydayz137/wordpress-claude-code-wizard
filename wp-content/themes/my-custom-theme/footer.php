<?php
/**
 * Footer Template
 * SEO-optimized footer with monetization touchpoints
 */
?>
<footer class="site-footer">
    <div class="container">

        <!-- Footer Widgets Area -->
        <div class="footer-widgets"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">

            <!-- About Column -->
            <div class="footer-widget">
                <h4><?php bloginfo('name'); ?></h4>
                <p><?php echo esc_html(get_bloginfo('description')); ?></p>
                <?php
                // Niche branding from settings
                $niche = get_option('directory_niche', 'local businesses');
                $city = get_option('directory_city', 'your area');
                ?>
                <p>Your trusted source for finding the best <?php echo esc_html($niche); ?> in
                    <?php echo esc_html($city); ?>.</p>
            </div>

            <!-- Quick Links Column -->
            <div class="footer-widget">
                <h4><?php _e('Quick Links', 'my-custom-theme'); ?></h4>
                <?php if (has_nav_menu('footer')): ?>
                    <?php wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class' => 'footer-menu',
                        'depth' => 1,
                    )); ?>
                <?php else: ?>
                    <ul class="footer-menu">
                        <li><a href="<?php echo home_url('/'); ?>"><?php _e('Home', 'my-custom-theme'); ?></a></li>
                        <li><a
                                href="<?php echo home_url('/companies/'); ?>"><?php _e('All Listings', 'my-custom-theme'); ?></a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Popular Categories -->
            <div class="footer-widget">
                <h4><?php _e('Popular Categories', 'my-custom-theme'); ?></h4>
                <ul class="footer-categories">
                    <?php
                    $industries = get_terms(array(
                        'taxonomy' => 'industry',
                        'hide_empty' => false,
                        'number' => 6,
                        'orderby' => 'count',
                        'order' => 'DESC'
                    ));
                    if (!empty($industries) && !is_wp_error($industries)):
                        foreach ($industries as $industry): ?>
                            <li><a
                                    href="<?php echo esc_url(get_term_link($industry)); ?>"><?php echo esc_html($industry->name); ?></a>
                            </li>
                        <?php endforeach;
                    endif;
                    ?>
                </ul>
            </div>

            <!-- Top Locations -->
            <div class="footer-widget">
                <h4><?php _e('Top Locations', 'my-custom-theme'); ?></h4>
                <ul class="footer-locations">
                    <?php
                    $locations = get_terms(array(
                        'taxonomy' => 'location',
                        'hide_empty' => false,
                        'number' => 6,
                        'orderby' => 'count',
                        'order' => 'DESC'
                    ));
                    if (!empty($locations) && !is_wp_error($locations)):
                        foreach ($locations as $location): ?>
                            <li><a
                                    href="<?php echo esc_url(get_term_link($location)); ?>"><?php echo esc_html($location->name); ?></a>
                            </li>
                        <?php endforeach;
                    endif;
                    ?>
                </ul>
            </div>
        </div>

        <!-- Business CTA -->
        <div class="footer-cta glass-panel"
            style="text-align: center; padding: 1.5rem; margin-bottom: 2rem; background: var(--gradient-primary); color: white; border-radius: var(--radius-lg);">
            <h4 style="color: white; margin-bottom: 0.5rem;"><?php _e('Own a Business?', 'my-custom-theme'); ?></h4>
            <p style="margin-bottom: 1rem; opacity: 0.9;">
                <?php _e('Claim your listing for free and reach more customers!', 'my-custom-theme'); ?></p>
            <a href="<?php echo home_url('/companies/'); ?>" class="btn btn-outline"
                style="border-color: white; color: white;"><?php _e('Find Your Business', 'my-custom-theme'); ?></a>
        </div>

        <!-- Copyright & Legal -->
        <div class="footer-bottom"
            style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--color-border); flex-wrap: wrap; gap: 1rem;">
            <p style="margin: 0;">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.
                <?php _e('All rights reserved.', 'my-custom-theme'); ?></p>
            <nav class="legal-links" style="display: flex; gap: 1.5rem;">
                <a
                    href="<?php echo home_url('/privacy-policy/'); ?>"><?php _e('Privacy Policy', 'my-custom-theme'); ?></a>
                <a
                    href="<?php echo home_url('/terms-of-service/'); ?>"><?php _e('Terms of Service', 'my-custom-theme'); ?></a>
            </nav>
        </div>

    </div>
</footer>

<!-- Footer AdSense Slot -->
<?php Monetization_Manager::render_ad_slot('footer'); ?>

<?php wp_footer(); ?>
</body>

</html>
<?php
/**
 * Template Part: Mega Menu
 */
?>

<nav id="site-navigation" class="main-navigation">
    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
        <?php esc_html_e('Primary Menu', 'my-custom-theme'); ?>
    </button>

    <?php
    wp_nav_menu(array(
        'theme_location' => 'primary',
        'menu_id' => 'primary-menu',
        'container' => false,
        'menu_class' => 'menu',
    ));
    ?>
</nav>
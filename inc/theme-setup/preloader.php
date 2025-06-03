<?php

function cob_print_preloader() {
    ?>
    <div id="cob-preloader">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/preloader.gif" alt="Loading...">
    </div>
    <?php
}
add_action( 'wp_body_open', 'cob_print_preloader', 5 );

// 2) Enqueue the script that will hide the preloader once the page has fully loaded
function cob_enqueue_preloader_script() {
    wp_enqueue_script(
        'cob-preloader',
        get_template_directory_uri() . '/assets/js/preloader.js',
        array(),
        '1.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'cob_enqueue_preloader_script' );

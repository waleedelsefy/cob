<?php
/**
 * Template Name: Services Page
 *
 * Template for the services.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();

$theme_dir = get_template_directory_uri();
             get_template_part('template-parts/services/head-services');
             get_template_part('template-parts/services/all-services');
             get_template_part('template-parts/services/experts');
             get_template_part('template-parts/contact-section');
             get_template_part('template-parts/home/articles');
             get_template_part('template-parts/top-articles');


?>
<script src=" https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="<?php echo $theme_dir ?>/assets/js/services.js"></script>
<?php get_footer(); ?>

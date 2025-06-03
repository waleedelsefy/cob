<?php
/**
 * Template Name: About Us Page
 *
 * Template for the About Us  Page .
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();

$theme_dir = get_template_directory_uri();
             get_template_part('template-parts/about-us/head');
             get_template_part('template-parts/about-us/landing');
             get_template_part('template-parts/services/experts');
             get_template_part('template-parts/about-us/services');
             get_template_part('template-parts/home/articles');
             get_template_part('template-parts/contact-section');

?>
<script src=" https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="<?php echo $theme_dir ?>/assets/js/about-us.js"></script>
<?php get_footer(); ?>

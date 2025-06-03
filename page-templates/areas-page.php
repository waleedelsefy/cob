<?php
/**
 * Template Name: Areas Page
 *
 * Template for the Areas Page .
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();

$theme_dir = get_template_directory_uri();
             get_template_part('template-parts/areas/head-areas');
             get_template_part('template-parts/areas/pagination-section');
             get_template_part('template-parts/contact-section');
             get_template_part('template-parts/top-articles');

?>
<script src=" https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="<?php echo $theme_dir ?>/assets/js/areas.js"></script>
<script src="<?php echo $theme_dir ?>/assets/js/home.js"></script>
<?php get_footer(); ?>

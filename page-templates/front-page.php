<?php
/**
 * Template Name: Homepage Page
 *
 * Template for the homepage.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();

$theme_dir = get_template_directory_uri();
             get_template_part('template-parts/home/landing');
            get_template_part('template-parts/home/areas');
            get_template_part('template-parts/home/compounds');
             get_template_part('template-parts/home/projects');
             get_template_part('template-parts/home/properties');
             get_template_part('template-parts/contact-section');
            get_template_part('template-parts/home/developers');
            get_template_part('template-parts/home/services');
             get_template_part('template-parts/home/factories');
             get_template_part('template-parts/home/articles');

?>
<script src=" https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="<?php echo $theme_dir ?>/assets/js/home.js"></script>
<?php get_footer(); ?>

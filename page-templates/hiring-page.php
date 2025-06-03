<?php
/**
 * Template Name: Hiring Page
 *
 * Template for the Hiring  Page .
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();

$theme_dir = get_template_directory_uri();
             get_template_part('template-parts/hiring/head');
             get_template_part('template-parts/hiring/hiring-landing');
             get_template_part('template-parts/hiring/hiring-video');
             get_template_part('template-parts/hiring/jobs-posts');
             get_template_part('template-parts/services/experts');
             get_template_part('template-parts/contact-section');
?>
<script src=" https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="<?php echo $theme_dir ?>/assets/js/hiring.js"></script>
<?php get_footer(); ?>

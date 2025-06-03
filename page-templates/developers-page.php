<?php
/**
 * Template Name: Developers Page
 *
 * Template for the Developers  Page .
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();

$theme_dir = get_template_directory_uri();
             get_template_part('template-parts/developers/head');
             get_template_part('template-parts/developers/landing');
             ?>
<div class="articles news w-full">
<?php
             get_template_part('template-parts/home/articles');

?>


</div>
<?php
get_template_part('template-parts/top-articles');

?>
<script src=" https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="<?php echo $theme_dir ?>/assets/js/developers.js"></script>
<?php get_footer(); ?>

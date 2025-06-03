<?php
/**
 * Template Name: Projects Page
 *
 * Template for the Projects.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();

$theme_dir = get_template_directory_uri();
get_template_part('template-parts/projects/head');
get_template_part('template-parts/projects/projects');
get_template_part('template-parts/contact-section');
?>
<div class="articles news w-full">
    <?php
    get_template_part('template-parts/home/articles');

    ?>
</div>
<?php
get_template_part('template-parts/top-articles');

?>
<script src="<?php echo $theme_dir ?>/assets/js/projects.js"></script>
<?php get_footer(); ?>

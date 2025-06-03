<?php
/**
 * Template Name: single post
 *
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();

$theme_dir = get_template_directory_uri();
get_template_part( 'template-parts/single/head-areas' );
get_template_part( 'template-parts/single/landing-art-name' );
get_template_part( 'template-parts/single/first-writer' );
get_template_part( 'template-parts/single/article-content' );
get_template_part( 'template-parts/single/articles-news' );

?>
<script src=" https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="<?php echo $theme_dir ?>/assets/js/single.js"></script>
<?php get_footer(); ?>

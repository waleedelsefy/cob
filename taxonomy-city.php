<?php
/**
 * Main taxonomy-city.php
 *
 * @package Capital_of_Business
 */

get_header(); ?>

<?php
$current_term = get_queried_object();

$theme_dir = get_template_directory_uri();
get_template_part('template-parts/taxonomy-city/head');
get_template_part('template-parts/taxonomy-city/featured');
get_template_part('template-parts/taxonomy-city/top-trend');
get_template_part('template-parts/taxonomy-city/top-Compounds');
get_template_part('template-parts/contact-section');
get_template_part('template-parts/taxonomy-city/sup-taxonomy');

?>
<script src="<?php echo $theme_dir ?>/assets/js/city.js"></script>
<?php get_footer(); ?>

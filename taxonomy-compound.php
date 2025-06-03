<?php
/**
 * Main taxonomy-compound.php
 * This template handles the display of compound taxonomy archives
 *
 * @package Capital_of_Business
 */

// Set posts per page for this taxonomy
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && is_tax('compound')) {
        $query->set('posts_per_page', 6);
    }
});

get_header();

// Get the current term
$current_term = get_queried_object();
$theme_dir = get_template_directory_uri();
?>

<?php get_template_part('template-parts/taxonomy-compound/head'); ?>
<?php get_template_part('template-parts/taxonomy-compound/featured'); ?>
<?php get_template_part('template-parts/contact-section'); ?>
<?php get_template_part('template-parts/taxonomy-compound/sup-taxonomy'); ?>

<script src="<?php echo $theme_dir ?>/assets/js/city.js"></script>
<?php get_footer(); ?>

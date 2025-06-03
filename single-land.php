<?php
/*
Template Name: Single Project
Template Post Type: projects
*/

$post_id = get_the_ID();
get_header();


get_the_title();
$theme_dir = get_template_directory_uri();

?>
<div class="head-services head-flat">
    <div class="container">
        <div class="breadcrumb">
            <?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) : rank_math_the_breadcrumbs(); endif; ?>
        </div>
    </div>
</div>
<?php
get_template_part( 'template-parts/lands-post/main-flat' );
get_template_part( 'template-parts/lands-post/landing-flat' );

get_template_part( 'template-parts/lands-post/lands-related' );
get_template_part( 'template-parts/contact-section' );
?>
<script src="<?php echo $theme_dir ?>/assets/js/single-properties.js"></script>

<?php
get_footer();
?>

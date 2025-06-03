<div class="head-projects">
    <div class="container">
        <div class="breadcrumb">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
        <div class="main-section">
            <h2><?php echo esc_html( get_the_title() );?></h2>
            <?php
            the_content();
            ?>
        </div>

    </div>
</div>
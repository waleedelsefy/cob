<div class="landing-we">
    <div class="container">
        <div class="right-we">
            <h5><?php echo esc_html( get_the_title() );?></h5>
            <div class="underline"></div>
           <?php
           the_content();
           ?>

        </div>
        <div class="left-we">
            <div class="red-div"></div>
            <div class="black-div">
                <img data-src="<?php
                $large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
                if (!empty($large_image_url)) {
                    echo esc_url($large_image_url[0]);
                }
                ?>" alt="Capital of Business Logo" class="logo lazyload" />
            </div>
        </div>
    </div>
</div>
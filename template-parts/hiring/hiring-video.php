<div class="hiring-video">
    <div class="contanier">
        <div class="head-video w-full">
            <?php
            the_content();
            ?>
        </div>
        <div class="video-content">
            <div class="video-div"></div>
            <div class="video-icon"><svg width="28" height="33" viewBox="0 0 28 33" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                    <path d="M27.4375 16.3159L0.217089 32.0316L0.217091 0.600173L27.4375 16.3159Z" fill="white" />
                </svg>
            </div>
            <video width="600" controls poster="<?php echo get_template_directory_uri(); ?>/assets/imgs/video.jpg">
                <source src="mov_bbb.mp4" type="video/mp4">
                <source src="mov_bbb.ogg" type="video/ogg">
            </video>
        </div>
    </div>
</div>
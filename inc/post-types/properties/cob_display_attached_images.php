<?php
/**
 * عرض جميع الصور المرفقة بالمنشور الحالي
 */
function cob_display_attached_images( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    // Get all image attachments for this post
    $attachments = get_attached_media( 'image', $post_id );

    if ( ! empty( $attachments ) ) {
        echo '<div class="cob-gallery-wrapper">';
        foreach ( $attachments as $attachment ) {
            // output the <img> tag for each attachment (size: medium)
            echo wp_get_attachment_image( $attachment->ID, 'medium', false, array(
                'class' => 'cob-gallery-image',
                'alt'   => esc_attr( get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ) ),
            ) );
        }
        echo '</div>';
    }
}

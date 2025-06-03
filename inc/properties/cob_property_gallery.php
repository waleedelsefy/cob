<?php
function my_add_attached_images_meta_box() {
    $screens = array( 'post', 'properties', 'lands', 'factory', 'page' );
    add_meta_box(
        'cob_gallery_images_meta_box',
        __( 'Gallery Images', 'cob_theme' ),
        'cob_gallery_images_meta_box_callback',
        $screens,
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'my_add_attached_images_meta_box' );

function cob_gallery_images_meta_box_callback( $post ) {
    wp_nonce_field( 'cob_gallery_images_nonce_action', 'cob_gallery_images_nonce' );
    $image_ids = get_post_meta( $post->ID, '_cob_gallery_images', true );
    $image_ids = is_array($image_ids) ? $image_ids : array_filter( explode(',', $image_ids ) );
    ?>
    <div id="attached-images-wrapper">
        <ul id="attached-images">
            <?php foreach ( $image_ids as $id ) :
                $thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
                if ( ! $thumb ) continue;
                ?>
                <li data-attachment-id="<?php echo esc_attr( $id ); ?>">
                    <img src="<?php echo esc_url( $thumb ); ?>" alt="">
                    <button type="button" class="detach-image-button" data-attachment-id="<?php echo esc_attr( $id ); ?>">×</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <button id="add-attached-images" class="button"><?php esc_html_e( 'Add Images', 'cob_theme' ); ?></button>
        <input type="hidden" name="cob_gallery_images" id="cob_gallery_images" value="<?php echo esc_attr( implode(',', $image_ids) ); ?>">
    </div>
    <style>
        #attached-images { list-style: none; margin: 0; padding: 0; overflow: auto; }
        #attached-images li { float: left; margin: 5px; position: relative; cursor: move; }
        #attached-images li img { max-width: 100px; height: auto; display: block; }
        .detach-image-button {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #9f0303;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            padding: 2px 5px;
        }
    </style>
    <script>
        jQuery(document).ready(function($) {
            var frame;

            function updateGalleryField() {
                var ids = $('#attached-images li').map(function() {
                    return $(this).data('attachment-id');
                }).get().join(',');
                $('#cob_gallery_images').val(ids);
            }

            $("#attached-images").sortable({
                update: function() {
                    updateGalleryField();
                }
            });

            $('#add-attached-images').on('click', function(e) {
                e.preventDefault();
                if ( frame ) {
                    frame.open();
                    return;
                }
                frame = wp.media({
                    title: '<?php echo esc_js( __( 'Select Images', 'cob_theme' ) ); ?>',
                    button: { text: '<?php echo esc_js( __( 'Attach Images', 'cob_theme' ) ); ?>' },
                    multiple: true
                });
                frame.on('select', function() {
                    var selection = frame.state().get('selection').map(function(attachment) {
                        return attachment.toJSON();
                    });
                    selection.forEach(function(attachment) {
                        var thumb = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
                        $('#attached-images').append(
                            '<li data-attachment-id="' + attachment.id + '"><img src="' + thumb + '" alt=""><button type="button" class="detach-image-button" data-attachment-id="' + attachment.id + '">×</button></li>'
                        );
                    });
                    updateGalleryField();
                });
                frame.open();
            });

            $('#attached-images').on('click', '.detach-image-button', function(e) {
                e.preventDefault();
                $(this).closest('li').remove();
                updateGalleryField();
            });
        });
    </script>
    <?php
}
function cob_save_gallery_images_meta( $post_id ) {
    if ( ! isset( $_POST['cob_gallery_images_nonce'] ) || ! wp_verify_nonce( $_POST['cob_gallery_images_nonce'], 'cob_gallery_images_nonce_action' ) ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset($_POST['cob_gallery_images']) ) {
        $ids = array_filter( array_map( 'intval', explode(',', $_POST['cob_gallery_images']) ) );
        update_post_meta( $post_id, '_cob_gallery_images', $ids );
    }
}
add_action( 'save_post', 'cob_save_gallery_images_meta' );

add_action( 'save_post', 'cob_import_gallery_from_urls', 20, 2 );
function cob_import_gallery_from_urls( $post_id, $post ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $image_urls = get_post_meta( $post_id, '_cob_gallery_image_urls', true );
    if ( empty( $image_urls ) ) return;

    if ( is_string($image_urls) ) {
        $image_urls = array_map( 'trim', explode( ',', $image_urls ) );
    }

    $attachment_ids = [];
    foreach ( $image_urls as $image_url ) {
        $attachment_id = cob_import_external_image( $image_url, $post_id );
        if ( $attachment_id ) {
            $attachment_ids[] = $attachment_id;
        }
    }

    if ( ! empty( $attachment_ids ) ) {
        update_post_meta( $post_id, '_cob_gallery_images', $attachment_ids );
    }
}

function cob_import_external_image( $url, $post_id = 0 ) {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $tmp = download_url( $url );
    if ( is_wp_error( $tmp ) ) return false;

    $file = [
        'name'     => basename( parse_url( $url, PHP_URL_PATH ) ),
        'type'     => mime_content_type( $tmp ),
        'tmp_name' => $tmp,
        'error'    => 0,
        'size'     => filesize( $tmp ),
    ];

    $sideload = media_handle_sideload( $file, $post_id );
    if ( is_wp_error( $sideload ) ) return false;

    return $sideload;
}

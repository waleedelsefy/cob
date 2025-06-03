jQuery(document).ready(function($){
    $('.toggle-featured').on('click', function(){
        var span = $(this);
        var post_id = span.data('postid');
        $.ajax({
            url: cobFeatured.ajax_url,
            type: 'POST',
            data: {
                action: 'toggle_featured_project',
                post_id: post_id,
                nonce: cobFeatured.nonce
            },
            success: function(response) {
                if ( response.success ) {
                    if ( response.data.new_status === 'yes' ) {
                        span.find('span')
                            .removeClass('dashicons-star-empty not-featured')
                            .addClass('dashicons-star-filled featured');
                    } else {
                        span.find('span')
                            .removeClass('dashicons-star-filled featured')
                            .addClass('dashicons-star-empty not-featured');
                    }
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});

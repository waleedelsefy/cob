jQuery(function($){
    var container  = $('.properties-cards');
    var pagination = $('.pagination');
    var city       = cobFactory.city;
    var ajaxUrl    = cobFactory.ajax_url;
    var nonce      = cobFactory.nonce;

    pagination.on('click', 'a', function(e){
        e.preventDefault();

        var href  = $(this).attr('href');
        var match = href.match(/paged=(\d+)/);
        var paged = match ? match[1] : 1;

        $.post( ajaxUrl, {
            action: 'cob_load_properties',
            nonce:  nonce,
            paged:  paged,
            city:   city,
        }, function(response){
            if ( response.success ) {
                container.html( response.data.cards );
                pagination.html( response.data.pagination );
                // Scroll to top of the listings
                $('html, body').animate({
                    scrollTop: container.offset().top - 100
                }, 400);
            }
        });
    });
});

jQuery(function($){
    var cardsContainer = $('.properties-cards');
    var pagination     = $('.similar-units-pagination');
    var data           = cobSimilar;
    pagination.on('click', 'a', function(e){
        e.preventDefault();
        var href  = $(this).attr('href');
        var match = href.match(/paged=(\d+)/);
        var paged = match ? match[1] : 1;

        $.post( data.ajax_url, {
            action:  'cob_load_similar_units',
            nonce:   data.nonce,
            paged:   paged,
            unit_id: data.unit_id,
            city:    data.city
        }, function(res){
            if ( res.success ) {
                cardsContainer.html( res.data.cards );
                pagination.html( res.data.pagination );
                $('html,body').animate({
                    scrollTop: cardsContainer.offset().top - 80
                }, 300);
            }
        });
    });
});

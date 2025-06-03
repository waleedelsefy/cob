jQuery(function($){
    var container  = $('.factorys-cards');
    var pagination = $('.pagination');
    var data       = cobProjects;

    pagination.on('click', 'a', function(e){
        e.preventDefault();
        var href  = $(this).attr('href');
        var match = href.match(/paged=(\d+)/);
        var paged = match ? match[1] : 1;

        $.post(data.ajax_url, {
            action: 'cob_load_projects',
            nonce:  data.nonce,
            paged:  paged
        }, function(res){
            if (res.success) {
                container.html(res.data.cards);
                pagination.html(res.data.pagination);
                $('html,body').animate({
                    scrollTop: container.offset().top - 100
                }, 400);
            }
        });
    });
});

window.addEventListener('load', function() {
    var preloader = document.getElementById('cob-preloader');
    if ( preloader ) {
        // Fade out the overlay
        preloader.style.transition = 'opacity 0.5s ease';
        preloader.style.opacity = 0;
        // Remove it from the DOM after fading
        setTimeout(function(){
            preloader.remove();
        }, 600);
    }
});

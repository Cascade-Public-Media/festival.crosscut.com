(function($) {
    console.log('working');

    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();

        var hash = this.hash;

        $('html, body').animate({
            scrollTop: $(hash).offset().top
        }, 1000, function() {
            // add hash to URL
            window.location.hash = hash;
        })
    })
})(jQuery);
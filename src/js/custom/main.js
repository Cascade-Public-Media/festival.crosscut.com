$(document).ready(function() {
    console.log('working');

    $('#panelists-carousel').owlCarousel();

    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();

        var hash = this.hash;

        $('html, body').animate({
            scrollTop: $(hash).offset().top
        }, 1000, function() {
            // add hash to URL
            window.location.hash = hash;
        });

        $('a.nav-link[href^="#"]').removeClass('active');
        $(this).addClass('active');
    });

    $('#session-carousel').owlCarousel({
        stagePadding:500,
        loop: true,
        center: true,
        items: 1,
        margin: 10,
        nav:true,
        responsive: {
            0:{
                stagePadding: 30
            },
            768:{
                stagePadding: 110
            },
            900:{
                stagePadding: 170
            },
            1024:{
                stagePadding: 200
            },
            1400:{
                stagePadding: 400
            }
        }
    })
});
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
        nav:true,
        margin: 10,
        responsiveClass: true,
        responsive: {
            0: {
                stagePadding: 70
            },
            450: {
                stagePadding: 110
            },
            576: {
                stagePadding: 100
            },
            768: {
                stagePadding: 110
            },
            900: {
                stagePadding: 170
            },
            1024: {
                stagePadding: 280,
                margin: 40
            },
            1250: {
                items: 2,
                stagePadding: 50,
                margin: 40
            },
            1500: {
                items: 2,
                stagePadding: 100,
                margin: 40
            },
            1700: {
                items:3,
                stagePadding: 20,
                margin:40
            }
        }
    })
});
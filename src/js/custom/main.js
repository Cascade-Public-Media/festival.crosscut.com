$(document).ready(function() {

    var development = true;
    var domain;

    if (development) {
        domain = 'https://crosscut.dev.dd:8443';
    } else {
        domain = 'https://crosscut.com';
    }


    // Smooth Scroll
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


    // News section: get data from festival news REST export view on crosscut.com
    function renderNews(data) {
        var image_path = domain + data['image'];
        var date = data['created'].slice(0, -8); // remove time from long format date

        var html = '<article id="festival-news" class="news"><h3 class="section-header text-left d-inline-block">In the News</h3><a class="view-all pl-3" href="https://crosscut.com/crosscut-festival/">View All<div class="inline-arrow-right"></div></a><div class="row no-gutters"><div class="col-sm-6 col-md-3"><div class="img-container"><img class="newsImage" alt="Crosscut Festival News Article" src="' + image_path + '"/></div></div><span class="col-sm-6 col-md-9 article-teaser"><h4>' + data['title'] + '</h4><p>' + data['excerpt'] + '</p><span class="byline">by ' + data['author'] + ' / ' + date + '</span></div></div></article>';
        console.log(html);
        $('.news-container').append(html);
    }

    var url = domain + '/json/festival-news?_format=json';

    $.ajax({
        url: url,
        method: 'GET',
        crossDomain: true,
        success: function(response) {
            renderNews(response[0]);
        }
    });


    // Initialize Carousels
    $('#panelists-carousel').owlCarousel({
        loop: true,
        dots: true,
        margin: 5,
        responsive: {
            0: {
                items: 2
            },
            576: {
                items: 3
            },
            768: {
                items: 4
            }
        }
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
    });
});
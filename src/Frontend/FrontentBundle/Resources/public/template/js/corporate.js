var navbar = $('.navbar-header').outerHeight(true);
var mins = 22;
$('.bs-example-form').css({
    'margin-top':(navbar/2 - mins)
});

$('.header-inner-menu').css({
    'margin-top':(navbar - 54)
});
$('#breadcrumb').css({
    'margin-top':(navbar+20)
});


var stickyOffset = $('.sticky').offset().top;
$(window).scroll(function(){
    var sticky = $('.sticky'),
        scroll = $(window).scrollTop();
    if (scroll >= stickyOffset) sticky.addClass('fixed');
    else sticky.removeClass('fixed');
});



$(".carousel").carousel({
    interval: 10000,
    wrap:true
});
$(".carousel").on("slid", function() {
    var to_slide;
    to_slide = $(".carousel-item.active").attr("data-slide-no");
    $(".myCarousel-target.active").removeClass("active");
    $(".carousel-indicators [data-slide-to=" + to_slide + "]").addClass("active");
});
$(".myCarousel-target").on("click", function() {
    $(this).preventDefault();
    $(".carousel").carousel(parseInt($(this).attr("data-slide-to")));
    $(".myCarousel-target.active").removeClass("active");
    $(this).addClass("active");
});

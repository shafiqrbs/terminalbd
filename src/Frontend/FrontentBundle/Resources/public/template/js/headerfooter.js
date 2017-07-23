/*======================== Page Slider ============================*/


var owlFeaturePageSLider = $("#feature-page-slider");
owlFeaturePageSLider.owlCarousel({
    items:columnItem,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".feature-page-next").click(function(){
    owlFeaturePageSLider.trigger('owl.next');
});
$(".feature-page-prev").click(function(){
    owlFeaturePageSLider.trigger('owl.prev');
});

/*======================== End Page Slider =============================*/


/*======================== Blog Slider =============================*/

var owlHeaderFooterBlog = $("#blog-header-footer-slider");
owlHeaderFooterBlog.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,
});

// Custom Navigation Events
$(".blog-header-footer-next").click(function(){
    owlHeaderFooterBlog.trigger('owl.next');
});
$(".blog-header-footer-prev").click(function(){
    owlHeaderFooterBlog.trigger('owl.prev');
});

/*======================== End Blog Slider =============================*/

/*======================== Branch Slider =============================*/

var owlHeaderFooterBranch = $("#branch-header-footer-slider");
owlHeaderFooterBranch.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,
});

// Custom Navigation Events
$(".branch-header-footer-next").click(function(){
    owlHeaderFooterBranch.trigger('owl.next');
});
$(".branch-header-footer-prev").click(function(){
    owlHeaderFooterBranch.trigger('owl.prev');
});

/*======================== End Branch Slider =============================*/

/*======================== Client Slider =============================*/

var owlHeaderFooterClient = $("#client-header-footer-slider");
owlHeaderFooterClient.owlCarousel({
    items:6,
    itemsDesktop:[1199,2],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,2],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});

// Custom Navigation Events
$(".client-header-footer-next").click(function(){
    owlHeaderFooterClient.trigger('owl.next');
});
$(".client-header-footer-prev").click(function(){
    owlHeaderFooterClient.trigger('owl.prev');
});

/*======================== End Client Slider =============================*/


/*======================== Event Slider =============================*/

var owlHeaderFooterEvent = $("#event-header-footer-slider");
owlHeaderFooterEvent.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,
});
// Custom Navigation Events
$(".event-header-footer-next").click(function(){
    owlHeaderFooterEvent.trigger('owl.next');
});
$(".event-header-footer-prev").click(function(){
    owlHeaderFooterEvent.trigger('owl.prev');
});

/*======================== Notice Slider =============================*/

var owlHeaderFooterNotice = $("#notice-header-footer-slider");
owlHeaderFooterNotice.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,
});
// Custom Navigation Events
$(".notice-header-footer-next").click(function(){
    owlHeaderFooterNotice.trigger('owl.next');
});
$(".notice-header-footer-prev").click(function(){
    owlHeaderFooterNotice.trigger('owl.prev');
});


/*======================== Portfolio Slider =============================*/

var owlHeaderFooterPortfolio = $("#portfolio-header-footer-slider");
owlHeaderFooterPortfolio.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,

});
// Custom Navigation Events
$(".portfolio-header-footer-next").click(function(){
    owlHeaderFooterPortfolio.trigger('owl.next');
});
$(".portfolio-header-footer-prev").click(function(){
    owlHeaderFooterPortfolio.trigger('owl.prev');
});


/*======================== Service Slider =============================*/

var owlHeaderFooterService = $("#service-header-footer-slider");
owlHeaderFooterService.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,

});
// Custom Navigation Events
$(".service-header-footer-next").click(function(){
    owlHeaderFooterService.trigger('owl.next');
});
$(".service-header-footer-prev").click(function(){
    owlHeaderFooterService.trigger('owl.prev');
});


/*======================== Sponsor Slider =============================*/

var owlHeaderFooterSponsor = $("#sponsor-header-footer-slider");
owlHeaderFooterService.owlCarousel({
    items:6,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,

});
// Custom Navigation Events
$(".sponsor-header-footer-next").click(function(){
    owlHeaderFooterService.trigger('owl.next');
});
$(".sponsor-header-footer-prev").click(function(){
    owlHeaderFooterService.trigger('owl.prev');
});


/*======================== Team Slider =============================*/

var owlHeaderFooterTeam = $("#team-header-footer-slider");

owlHeaderFooterTeam.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false,

});
// Custom Navigation Events
$(".team-header-footer-next").click(function(){
    owlHeaderFooterTeam.trigger('owl.next');
});
$(".team-header-footer-prev").click(function(){
    owlHeaderFooterTeam.trigger('owl.prev');
});


/*======================== Testimonial Slider =============================*/

var owlHeaderFooterTestimonial = $("#testimonial-header-footer-slider");

owlHeaderFooterTestimonial.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:false,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"backSlide",
    navigationText:["",""],
    autoPlay:false,
    rewindNav : false,

});
// Custom Navigation Events
$(".testimonial-header-footer-next").click(function(){
    owlHeaderFooterTestimonial.trigger('owl.next');
});
$(".testimonial-header-footer-prev").click(function(){
    owlHeaderFooterTestimonial.trigger('owl.prev');
});



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

var owlHeaderFooterBlog = $("#blog-page-slider");
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
$(".blog-page-next").click(function(){
    owlHeaderFooterBlog.trigger('owl.next');
});
$(".blog-page-prev").click(function(){
    owlHeaderFooterBlog.trigger('owl.prev');
});

/*======================== End Blog Slider =============================*/

/*======================== Branch Slider =============================*/

var owlHeaderFooterBranch = $("#branch-page-slider");
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
$(".branch-page-next").click(function(){
    owlHeaderFooterBranch.trigger('owl.next');
});
$(".branch-page-prev").click(function(){
    owlHeaderFooterBranch.trigger('owl.prev');
});

/*======================== End Branch Slider =============================*/

/*======================== Client Slider =============================*/

var owlHeaderFooterClient = $("#client-page-slider");
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
$(".client-page-next").click(function(){
    owlHeaderFooterClient.trigger('owl.next');
});
$(".client-page-prev").click(function(){
    owlHeaderFooterClient.trigger('owl.prev');
});

/*======================== End Client Slider =============================*/


/*======================== Event Slider =============================*/

var owlHeaderFooterEvent = $("#event-page-slider");
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
$(".event-page-next").click(function(){
    owlHeaderFooterEvent.trigger('owl.next');
});
$(".event-page-prev").click(function(){
    owlHeaderFooterEvent.trigger('owl.prev');
});

/*======================== Notice Slider =============================*/

var owlHeaderFooterNotice = $("#notice-page-slider");
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
$(".notice-page-next").click(function(){
    owlHeaderFooterNotice.trigger('owl.next');
});
$(".notice-page-prev").click(function(){
    owlHeaderFooterNotice.trigger('owl.prev');
});


/*======================== Portfolio Slider =============================*/

var owlHeaderFooterPortfolio = $("#portfolio-page-slider");
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
$(".portfolio-page-next").click(function(){
    owlHeaderFooterPortfolio.trigger('owl.next');
});
$(".portfolio-page-prev").click(function(){
    owlHeaderFooterPortfolio.trigger('owl.prev');
});


/*======================== Service Slider =============================*/

var owlHeaderFooterService = $("#service-page-slider");
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
$(".service-page-next").click(function(){
    owlHeaderFooterService.trigger('owl.next');
});
$(".service-page-prev").click(function(){
    owlHeaderFooterService.trigger('owl.prev');
});


/*======================== Sponsor Slider =============================*/

var owlHeaderFooterSponsor = $("#sponsor-page-slider");
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
$(".sponsor-page-next").click(function(){
    owlHeaderFooterService.trigger('owl.next');
});
$(".sponsor-page-prev").click(function(){
    owlHeaderFooterService.trigger('owl.prev');
});


/*======================== Team Slider =============================*/

var owlHeaderFooterTeam = $("#team-page-slider");

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
$(".team-page-next").click(function(){
    owlHeaderFooterTeam.trigger('owl.next');
});
$(".team-page-prev").click(function(){
    owlHeaderFooterTeam.trigger('owl.prev');
});


/*======================== Testimonial Slider =============================*/

var owlHeaderFooterTestimonial = $("#testimonial-page-slider");

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
$(".testimonial-page-next").click(function(){
    owlHeaderFooterTestimonial.trigger('owl.next');
});
$(".testimonial-page-prev").click(function(){
    owlHeaderFooterTestimonial.trigger('owl.prev');
});



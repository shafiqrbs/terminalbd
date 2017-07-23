/*======================== Page Slider ============================*/


var owlFeatureSidebarSLider = $("#feature-sidebar-slider");
owlFeatureSidebarSLider.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:false,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    rewindNav : false,
    autoPlay:false
});
// Custom Navigation Events
$(".feature-page-next").click(function(){
    owlFeatureSidebarSLider.trigger('owl.next');
});
$(".feature-page-prev").click(function(){
    owlFeatureSidebarSLider.trigger('owl.prev');
});

/*======================== End Page Slider =============================*/


/*======================== Blog Slider =============================*/

var owlSidebarBlog = $("#blog-sidebar-slider");
owlSidebarBlog.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:false,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    rewindNav : false,
    autoPlay:false
});

// Custom Navigation Events
$(".blog-sidebar-next").click(function(){
    owlSidebarBlog.trigger('owl.next');
});
$(".blog-sidebar-prev").click(function(){
    owlSidebarBlog.trigger('owl.prev');
});

/*======================== End Blog Slider =============================*/

/*======================== Branch Slider =============================*/

var owlSidebarBranch = $("#branch-sidebar-slider");
owlSidebarBranch.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:false,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    rewindNav : false,
    autoPlay:false
});

// Custom Navigation Events
$(".branch-sidebar-next").click(function(){
    owlSidebarBranch.trigger('owl.next');
});
$(".branch-sidebar-prev").click(function(){
    owlSidebarBranch.trigger('owl.prev');
});

/*======================== End Branch Slider =============================*/

/*======================== Client Slider =============================*/

var owlSidebarClient = $("#client-sidebar-slider");
owlSidebarClient.owlCarousel({
    items:2,
    itemsDesktop:[1199,2],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,2],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});

// Custom Navigation Events
$(".client-sidebar-next").click(function(){
    owlSidebarClient.trigger('owl.next');
});
$(".client-sidebar-prev").click(function(){
    owlSidebarClient.trigger('owl.prev');
});

/*======================== End Client Slider =============================*/


/*======================== Event Slider =============================*/

var owlSidebarEvent = $("#event-sidebar-slider");
owlSidebarEvent.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:false,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    navigationText:["",""],
    autoPlay:false
});
// Custom Navigation Events
$(".event-sidebar-next").click(function(){
    owlSidebarEvent.trigger('owl.next');
});
$(".event-sidebar-prev").click(function(){
    owlSidebarEvent.trigger('owl.prev');
});

/*======================== News Slider =============================*/

var owlSidebarNews = $("#news-sidebar-slider");
owlSidebarNews.owlCarousel({
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
$(".news-sidebar-next").click(function(){
    owlSidebarNews.trigger('owl.next');
});
$(".news-sidebar-prev").click(function(){
    owlSidebarNews.trigger('owl.prev');
});


/*======================== Notice Slider =============================*/

var owlSidebarNotice = $("#notice-sidebar-slider");
owlSidebarNotice.owlCarousel({
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
$(".notice-sidebar-next").click(function(){
    owlSidebarNotice.trigger('owl.next');
});
$(".notice-sidebar-prev").click(function(){
    owlSidebarNotice.trigger('owl.prev');
});


/*======================== Portfolio Slider =============================*/

var owlSidebarPortfolio = $("#portfolio-sidebar-slider");
owlSidebarPortfolio.owlCarousel({
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
$(".portfolio-sidebar-next").click(function(){
    owlSidebarPortfolio.trigger('owl.next');
});
$(".portfolio-sidebar-prev").click(function(){
    owlSidebarPortfolio.trigger('owl.prev');
});


/*======================== Service Slider =============================*/

var owlSidebarService = $("#service-sidebar-slider");
owlSidebarService.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:false,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    navigationText:["",""],
    autoPlay:false,
    rewindNav : false,

});
// Custom Navigation Events
$(".service-sidebar-next").click(function(){
    owlSidebarService.trigger('owl.next');
});
$(".service-sidebar-prev").click(function(){
    owlSidebarService.trigger('owl.prev');
});


/*======================== Sponsor Slider =============================*/

var owlSidebarSponsor = $("#sponsor-sidebar-slider");
owlSidebarSponsor.owlCarousel({
    items:2,
    itemsDesktop:[1199,2],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,2],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false

});
// Custom Navigation Events
$(".sponsor-sidebar-next").click(function(){
    owlSidebarSponsor.trigger('owl.next');
});
$(".sponsor-sidebar-prev").click(function(){
    owlSidebarSponsor.trigger('owl.prev');
});


/*======================== Team Slider =============================*/

var owlSidebarTeam = $("#team-sidebar-slider");

owlSidebarTeam.owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:false,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    navigationText:["",""],
    autoPlay:false,
    rewindNav : false,

});
// Custom Navigation Events
$(".team-sidebar-next").click(function(){
    owlSidebarTeam.trigger('owl.next');
});
$(".team-sidebar-prev").click(function(){
    owlSidebarTeam.trigger('owl.prev');
});


/*======================== Testimonial Slider =============================*/

var owlSidebarTestimonial = $("#testimonial-sidebar-slider");

owlSidebarTestimonial.owlCarousel({
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
$(".testimonial-sidebar-next").click(function(){
    owlSidebarTestimonial.trigger('owl.next');
});
$(".testimonial-sidebar-prev").click(function(){
    owlSidebarTestimonial.trigger('owl.prev');
});



$("#sidebar-blog-slider").owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:true,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    navigationText:["",""],
    autoPlay:false
});


var owlBlog = $("#page-blog-slider");
owlBlog.owlCarousel({
    items:2,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".blog-next").click(function(){
    owlBlog.trigger('owl.next');
});
$(".blog-prev").click(function(){
    owlBlog.trigger('owl.prev');
});

$("#sidebar-branch-slider").owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:true,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    navigationText:["",""],
    autoPlay:false
});

var owlBranch = $("#page-branch-slider");
owlBranch.owlCarousel({
    items:2,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".branch-next").click(function(){
    owlBranch.trigger('owl.next');
});
$(".branch-prev").click(function(){
    owlBranch.trigger('owl.prev');
});

$("#sidebar-client-slider").owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:true,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    navigationText:["",""],
    autoPlay:false
});

var owlClient = $("#page-client-slider");
owlClient.owlCarousel({
    items:3,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".client-next").click(function(){
    owlClient.trigger('owl.next');
});
$(".client-prev").click(function(){
    owlClient.trigger('owl.prev');
});


$("#sidebar-event-slider").owlCarousel({
    items:1,
    itemsDesktop:[1000,2],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:true,
    slideSpeed:1000,
    singleItem:true,
    transitionStyle:"goDown",
    navigationText:["",""],
    autoPlay:false
});

var owlEvent = $("#page-event-slider");
owlEvent.owlCarousel({
    items:3,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".event-next").click(function(){
    owlEvent.trigger('owl.next');
});
$(".event-prev").click(function(){
    owlEvent.trigger('owl.prev');
});



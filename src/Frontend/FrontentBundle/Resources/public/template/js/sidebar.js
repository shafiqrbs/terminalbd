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


var owlPageBlog = $("#blog-page-slider");
owlPageBlog.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".blog-page-next").click(function(){
    owlPageBlog.trigger('owl.next');
});
$(".blog-page-prev").click(function(){
    owlPageBlog.trigger('owl.prev');
});

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
    navigationText:["",""],
    autoPlay:false
});

// Custom Navigation Events
$(".branch-sidebar-next").click(function(){
    owlSidebarBranch.trigger('owl.next');
});
$(".branch-sidebar-prev").click(function(){
    owlSidebarBranch.trigger('owl.prev');
});

var owlPageBranch = $("#branch-page-slider");
owlPageBranch.owlCarousel({
    items:4,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".branch-page-next").click(function(){
    owlPageBranch.trigger('owl.next');
});
$(".branch-page-prev").click(function(){
    owlPageBranch.trigger('owl.prev');
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


var owlNotice = $("#page-notice-slider");
owlNotice.owlCarousel({
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
$(".notice-next").click(function(){
    owlNotice.trigger('owl.next');
});
$(".notice-prev").click(function(){
    owlNotice.trigger('owl.prev');
});



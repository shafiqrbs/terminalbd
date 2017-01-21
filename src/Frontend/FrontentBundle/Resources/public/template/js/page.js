var owlProduct = $(".product-slider");
owlProduct.owlCarousel({
    items: 6,
    itemsDesktop: [1199, 3],
    itemsDesktopSmall: [979, 2],
    itemsTablet: [768, 1],
    pagination: false,
    paginationNumbers: false,
    autoPlay: false,
    rewindNav: false
});

// Custom Navigation Events
$(".next").click(function(){
    owlProduct.trigger('owl.next');
})

$(".prev").click(function(){
    owlProduct.trigger('owl.prev');
})

var owlProduct = $(".viewed-product-slider");
owlProduct.owlCarousel({
    items:6,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".viewed-next").click(function(){
    owlProduct.trigger('owl.next');
})

$(".viewed-prev").click(function(){
    owlProduct.trigger('owl.prev');
})

// Custom Navigation Events
$(".next-client").click(function(){
    owlClient.trigger('owl.next');
})

$(".prev-client").click(function(){
    owlClient.trigger('owl.prev');
})

$("#testimonial-single-slider").owlCarousel({
    items:1,
    itemsDesktop:[1000,1],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination: true,
    autoPlay:false
});

$("#testimonial-slider").owlCarousel({
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

$("#event-slider").owlCarousel({
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

$("#service-slider").owlCarousel({
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

$("#sponsor-slider").owlCarousel({
    items:3,
    itemsDesktop:[1000,4],
    itemsDesktopSmall:[979,1],
    itemsTablet:[768,1],
    pagination:false,
    navigation:true,
    slideSpeed:1000,
    transitionStyle:"goDown",
    navigationText:["",""],
    autoPlay:false
});

$("#blog-slider").owlCarousel({
    items : 1,
    itemsDesktop:[1199,2],
    itemsDesktopSmall:[980,2],
    itemsTablet:[650,1],
    pagination:false,
    navigation:true,
    navigationText:["",""]
});



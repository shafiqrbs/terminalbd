$(document).on("click", "#kitchenBtn", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                jsPostPrint(response);
                setTimeout(pageRedirect(),3000);
            });
        }
    });
});

$(document).on("click", ".paymentReceive", function() {
    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('#paymentDone-'+id).remove();
            $.get(url, function( response ) {
               jsPostPrint(response);
            });
        }
    });
});

function pageRedirect() {
    window.location.href = "/restaurant/invoice/new";
}

function jsPostPrint(data) {

    if(typeof EasyPOSPrinter == 'undefined') {
        alert("Printer library not found");
        return;
    }
    EasyPOSPrinter.raw(data);
    EasyPOSPrinter.cut();
    EasyPOSPrinter.print(function(r, x){
        console.log(r)
    });
}

// hide all contents accept from the first div
$('.tabContent div:not(:first)').toggle();

// hide the previous button
$('.previous').hide();

$('.tabs li').click(function () {

    if ($(this).is(':last-child')) {
        $('.next').hide();
    } else {
        $('.next').show();
    }

    if ($(this).is(':first-child')) {
        $('.previous').hide();
    } else {
        $('.previous').show();
    }

    var position = $(this).position();
    var corresponding = $(this).data("id");

    // scroll to clicked tab with a little gap left to show previous tabs
    scroll = $('.tabs').scrollLeft();
    $('.tabs').animate({
        'scrollLeft': scroll + position.left - 30
    }, 200);

    // hide all content divs
    $('.tabContent div').hide();

    // show content of corresponding tab
    $('div.' + corresponding).toggle();

    // remove active class from currently not active tabs
    $('.tabs li').removeClass('active');

    // add active class to clicked tab
    $(this).addClass('active');
});

$('div a').click(function(e){
    e.preventDefault();
    $('li.active').next('li').trigger('click');
});
$('.next').click(function(e){
    e.preventDefault();
    $('li.active').next('li').trigger('click');
});
$('.previous').click(function(e){
    e.preventDefault();
    $('li.active').prev('li').trigger('click');
});


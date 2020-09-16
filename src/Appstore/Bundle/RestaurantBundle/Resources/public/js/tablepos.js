// hide all contents accept from the first div
$('.tabContent div:not(:first)').toggle();

// hide the previous button
$('.previous').hide();

$('.table-tab li').click(function () {
    var id = $(this).data("id");
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
    $('div.' + corresponding).toggle(
        function(e){
          alert(id);
        }
    );

    // remove active class from currently not active tabs
    $('.tabs li').removeClass('active');

    // add active class to clicked tab
    $(this).addClass('active');
});


$('.next').click(function(e){
    e.preventDefault();
    $('ul.table-tab li.active').next('li').trigger('click');
});
$('.previous').click(function(e){
    e.preventDefault();
    $('ul.table-tab li.active').prev('li').trigger('click');
});

$(document).on('click', '#temporaryParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = parseInt($('#quantity').val());
    var price = parseInt($('#price').val());
    var url = $('#temporaryParticular').attr('data-url');
    if(particularId == ''){
        $("#restaurant_particular_particular").select2('open');
        return false;
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
        success: function (response) {
            setTimeout(jsonResult(response),100);
        }
    })
});

$(document).on('click', '.addProduct', function() {

    var url = $(this).attr('data-action');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                setTimeout(jsonResult(response),100);
            });
        }
    });
});

$(document).on('change', '#restaurant_invoice_discountType , #restaurant_invoice_discountCalculation', function() {

    var discountType = $('#restaurant_invoice_discountType').val();
    var discount = parseInt($('#restaurant_invoice_discountCalculation').val());
    if(discount === "NaN"){
        return false;
    }
    $.ajax({
        url: Routing.generate('restaurant_temporary_discount_update'),
        type: 'POST',
        data:'discount=' + discount +'&discountType='+ discountType,
        success: function(response) {
            setTimeout(jsonResult(response),100);
        }
    })
});

$(document).on('change', '#restaurant_invoice_discountCoupon', function() {

    var discount = $('#restaurant_invoice_discountCoupon').val();
    if(discount === "NaN"){
        return false;
    }
    $.ajax({
        url: Routing.generate('restaurant_temporary_discount_coupon'),
        type: 'POST',
        data:'discount=' + discount,
        success: function(response) {
            setTimeout(jsonResult(response),100);
        }
    })
});

$(document).on("click", ".initialParticularDelete , .particularDelete", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                setTimeout(jsonResult(response),100);
                $("#remove-"+id).remove();
            });
        }
    });
});


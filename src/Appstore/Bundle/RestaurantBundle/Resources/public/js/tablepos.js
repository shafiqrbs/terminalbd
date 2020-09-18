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
    var url = $(this).attr("data-action");
    // scroll to clicked tab with a little gap left to show previous tabs
    scroll = $('.tabs').scrollLeft();
    $('.tabs').animate({
        'scrollLeft': scroll + position.left - 30
    }, 200);

    // hide all content divs
    $('.tabContent div').hide();

    // show content of corresponding tab
    $('div.' + corresponding).toggle(
        $.get(url)
            .done(function( response ) {
                $('#tableInvoice').val(corresponding);
                $('#transaction-box').html(response);
            })
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

function jsonResult(response) {

    obj = JSON.parse(response);
    $('#invoiceParticulars').html(obj['invoiceParticulars']).show();
    $('#subTotal').html(obj['subTotal']);
    $('.total').html(obj['total']);
    $('#total').val(obj['total']);
    $('.vat').html(obj['vat']);
    $('.due').html(obj['total']);
    $('#process-'+obj['entity']).removeClass().addClass(obj['process']).html(obj['process']);
    $('#restaurant_invoice_discount').val(obj['discount']);
    if(obj['total'] > 0 ){
        $('.receiveBtn').attr("disabled", false);
    }else{
        $('.receiveBtn').attr("disabled", true);
    }
}


$(document).on('click', '.addProduct', function() {

    var invoice = $('#tableInvoice').val();
    var url = $(this).attr('data-action');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url,{'invoice':invoice}, function( response ) {
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

$(document).on('click', '.invoice-input', function(e) {

    $.ajax({
        url         : Routing.generate('restaurant_tableinvoice_update'),
        type        : 'POST',
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        success     : function(response){
            setTimeout(jsonResult(response),100);
        }
    });
    e.preventDefault();
});

$(document).on('click', '#posKitchen', function(e) {
    url = $(this).attr('data-action');
    var atLeastOneIsChecked =$('input[name="isPrint[]"]:checked').length > 0;
    var searchIDs = $('input[name="isPrint[]"]:checked').map(function(){
        return $(this).val();
    }).get();
    if(atLeastOneIsChecked === true){
        $.get(url,{'isPrint':searchIDs});
    }
    e.preventDefault();

});

$(document).on('click', '#saveButton', function() {
    $('#buttonType').val('saveBtn');
    $.ajax({
        url         : $('form#invoiceForm').attr( 'action' ),
        type        : 'POST',
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        beforeSend  : function() {
            $('#saveButton').html("Please Wait...").attr('disabled', 'disabled');
        },
        success     : function(response){
            $('form#invoiceForm')[0].reset();
            $('.initialVat').html('');
            $('#subTotal').html('');
            $('#restaurant_invoice_vat').val(0);
            $('#restaurant_invoice_payment').val('');
            $('#saveButton').html("<i class='icon-save'></i> Save").attr('disabled','disabled');
            $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
            $('#invoiceParticulars').hide();
        }
    });
});

$(document).on('click', '#posButton', function() {
    $('#buttonType').val('posBtn');
    $.ajax({
        url         : $('form#invoiceForm').attr( 'action' ),
        type        : $('form#invoiceForm').attr( 'method' ),
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        beforeSend  : function() {
            $('#posButton').html("Please Wait...").attr('disabled', 'disabled');
        },
        success     : function(response){
            $('form#invoiceForm')[0].reset();
            $('.initialVat').html('');
            $('#subTotal').html('');
            $('#restaurant_invoice_vat').val(0);
            $('#restaurant_invoice_payment').val(0);
            $('#posButton').html("<i class='icon-print'></i> POS Print").attr('disabled','disabled');
            $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
            $('#invoiceParticulars').hide();
            jsPostPrint(response);
        }
    });
});



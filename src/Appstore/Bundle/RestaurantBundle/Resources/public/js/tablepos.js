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
                obj = JSON.parse(response);
                $('#tableInvoice').val(corresponding);
                $('#invoiceEntity').val(corresponding);
                $('#transaction-box').html(obj['body']);
                $('.due').html(obj['total']);
            })
    );

    // remove active class from currently not active tabs
    $('.tabs li').removeClass('active');

    // add active class to clicked tab
    $(this).addClass('active');
});

$('#search').keyup(function(){

    // Search text
    var text = $(this).val();

    // Hide all content class element
    $('.product-content').hide();

    // Search
    $('.product-content .cta-title').each(function(){

        if($(this).text().toLowerCase().indexOf(""+text+"") != -1 ){
            $(this).closest('.product-content').show();
        }
    });

});

$(document).on('click', '.js-accordion-title', function() {
    $(this).next().slideToggle(200);
    $(this).toggleClass('open', 200);
});

$('.next').click(function(e){
    e.preventDefault();
    $('ul.table-tab li.active').next('li').trigger('click');
});

$('.previous').click(function(e){
    e.preventDefault();
    $('ul.table-tab li.active').prev('li').trigger('click');
});

$('#btn-refresh').click(function(e){
    $('.payment').val('');
    e.preventDefault();
});

$(document).on('click', '.invoice-process', function() {
    var process = $(this).val();
    var entity = $(this).attr('data-id');
    var url = $(this).attr('data-action');
    if(process ===  "Payment"){
        $('.hide-payment').hide();
    }else{
        $('.hide-payment').show();
    }
    $.get(url,{'process':process}, function( response ) {
        $('#process-'+entity).removeClass().addClass(process).html(process);

    });
});

$(document).on('click', '.method-process', function() {
    var method = $(this).val();
    if(method === 'Bank' ){
        $('.bankHide').show(500);
        $('.mobileBankHide').hide(500);
    }else if(method === 'Mobile'){
        $('.bankHide').hide(500);
        $('.mobileBankHide').show(500);
    }else{
        $('.bankHide').hide(500);
        $('.mobileBankHide').hide(500);
    }
});

function jsonResult(response) {

    obj = JSON.parse(response);
    $('#invoiceParticulars').html(obj['invoiceParticulars']).show();
    $('#subTotal').html(financial(obj['subTotal']));
    $('.total').html(financial(obj['total']));
    $('#total').val(obj['total']);
    $('.vat').html(obj['vat']);
    $('.due').html(financial(obj['total']));
    $('#due').val(obj['total']);
    $('.discount').html(obj['discount']);
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

$(document).on('change', '.invoice-change', function(e) {

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

function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}


$(document).on('keyup', '.payment', function() {

    var payment  = parseInt($('#restaurant_invoice_payment').val()  != '' ? $('#restaurant_invoice_payment').val() : 0 );
    var due  = parseInt($('#due').val()  != '' ? $('#due').val() : 0 );
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due '+financial(dueAmount));
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return '+financial(balance));
    }

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
        success : function(response){
            $('form#invoiceForm')[0].reset();
            $('.initialVat').html('');
            $('#subTotal').html('');
            $('.vat').html(0);
            $('.sd').html(0);
            $('.discount').html(0);
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
            $('.vat').html(0);
            $('.sd').html(0);
            $('.discount').html(0);
            $('#restaurant_invoice_vat').val(0);
            $('#restaurant_invoice_payment').val(0);
            $('#posButton').html("<i class='icon-print'></i> POS Print").attr('disabled','disabled');
            $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
            $('#invoiceParticulars').hide();
            jsPostPrint(response);
        }
    });
});


function calcNumbers(result){
    restaurant_invoice.restaurant_invoice_payment.value=restaurant_invoice.restaurant_invoice_payment.value+result;
    paymentBalance();
}
function paymentBalance() {
    var payment  = parseInt($('#restaurant_invoice_payment').val()  != '' ? $('#restaurant_invoice_payment').val() : 0 );
    var due  = parseInt($('#due').val()  != '' ? $('#due').val() : 0 );
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due '+financial(dueAmount));
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return '+financial(balance));
    }
}

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
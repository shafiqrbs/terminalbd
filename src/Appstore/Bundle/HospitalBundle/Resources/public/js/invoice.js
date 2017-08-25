/**
 * Created by rbs on 5/1/17.
 */

$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});
// Getter
var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

// Setter
$( ".date-picker" ).datepicker( "option", "dateFormat", "dd-mm-yy" );


$( "#name" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('domain_customer_auto_name_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {}

});

$( "#mobile" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('domain_customer_auto_mobile_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {}

});


$(".addReferred").click(function(){

    if ($(this).attr("id") == 'show') {

        $( ".referred-add" ).slideDown( "slow" );
        $( ".referred-search" ).slideUp( "slow" );

    }else {

        $( ".referred-add" ).slideUp( "slow" );
        $( ".referred-search" ).slideDown( "slow" );

    }

});

$(document).on('change', '.transactionMethod', function() {

    var paymentMethod = $(this).val();

    if( paymentMethod == 2){
        $('#cartMethod').show();
        $('#bkashMethod').hide();
    }else if( paymentMethod == 3){
        $('#bkashMethod').show();
        $('#cartMethod').hide();
    }else{
        $('#cartMethod').hide();
        $('#bkashMethod').hide();
    }

});

$(".receivePayment").click(function(){
    $("#showPayment").slideToggle(1000);
});

$(document).on('change', '#particular', function() {

    var url = $(this).val();
    if(url == ''){
        alert('You have to add particulars from drop down');
        return false;
    }
    alert(url);
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#particularId').val(obj['particularId']);
            $('#quantity').val(obj['quantity']);
            $('#price').val(obj['price']);
            $('#instruction').html(obj['instruction']);
            $('#addParticular').attr("disabled", false);
        }
    })
});

$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = $('#quantity').val();
    var price = $('#price').val();
    var url = $('#addParticular').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
        success: function (response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').val(obj['vat']);
            $('.due').html(obj['due']);
            $('.discountAmount').html(obj['discount']);
            $('.discount').val('').attr( "placeholder", obj['discount'] );
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#invoiceTransaction').html(obj['invoiceTransaction']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
            $('#addParticular').attr("disabled", true);
        }
    })
});


$(document).on('change', '.discount', function() {

    var discount = parseInt($('.discount').val());
    var invoice = parseInt($('#invoiceId').val());

    $.ajax({
        url: Routing.generate('hms_invoice_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&invoice='+ invoice,
        success: function(response) {

            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
            $('.discount').val('').attr("placeholder", obj['discount']);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#invoiceTransaction').html(obj['invoiceTransaction']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
        }

    })
});

$(document).on("click", ".removeDiscount", function() {

    var url = $(this).attr("data-url");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {

            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
            $('.discount').val('').attr( "placeholder", obj['discount'] );
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#invoiceTransaction').html(obj['invoiceTransaction']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
        }
    })
});

$('#invoiceParticulars').on("click", ".delete", function() {

    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#remove-'+id).hide();
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
            $('.discount').val('').attr( "placeholder", obj['discount'] );
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#invoiceTransaction').html(obj['invoiceTransaction']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
        }
    })
});

$(document).on('click', '#addPayment', function() {

    var payment = $('#payment').val();
    var discount = $('#discount').val();
    var process = $('#process').val();
    var url = $('#addPayment').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'payment='+ payment +'&discount='+ discount +'&process='+ process,
        success: function (response) {
            location.reload();
        }
    })
});

$(document).on('change', '#appstore_bundle_hospitalbundle_invoice_payment', function() {

    var payment  = parseInt($('#appstore_bundle_hospitalbundle_invoice_payment').val()  != '' ? $('#appstore_bundle_hospitalbundle_invoice_payment').val() : 0 );
    var due =  parseInt($('#due').val());
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.due').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.due').html(balance);
    }
});

$('form.horizontal-form').on('keypress', 'input', function (e) {

    if (e.which == 13) {
        e.preventDefault();

        switch (this.id) {

            case 'discount':
                $('#paymentAmount').focus();
                break;

            case 'paymentAmount':
                $('#receiveBtn').focus();
                break;
        }
    }
});

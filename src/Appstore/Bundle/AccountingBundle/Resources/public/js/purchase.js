/**
 * Created by rbs on 5/1/17.
 */
$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
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


var form = $("#purchaseItem").validate({

    rules: {

        "particular": {required: true},
        "price": {required: true},
        "quantity": {required: true},
    },

    messages: {

        "particular":"Enter particular name",
        "price":"Enter purchase price",
        "quantity":"Enter purchase quantity",
    },
    tooltip_options: {

        "particular": {placement:'top',html:true},
        "price": {placement:'top',html:true},
        "quantity": {placement:'top',html:true},
    },

    submitHandler: function(form) {

        $.ajax({
            url         : $('form#purchaseItem').attr( 'action' ),
            type        : $('form#purchaseItem').attr( 'method' ),
            data        : new FormData($('form#purchaseItem')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('#subTotal').html(obj['subTotal']);
                $('#purchase_totalAmount').val(obj['subTotal']);
                $('#payment').val(obj['payment']);
                $('form#purchaseItem')[0].reset();
                $('#quantity').val('1');

            }
        });
    }
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
            $('#subTotal').html(obj['subTotal']);
            $('#purchase_totalAmount').val(obj['subTotal']);
            $('#paymentTotal').val(obj['netTotal']);

        }
    })
});

$(document).on('keyup', '#purchase_payment', function() {

    var payment     = parseInt($('#purchase_payment').val()  != '' ? $('#purchase_payment').val() : 0 );
    var due =  parseInt($('#paymentTotal').val());
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

$('form#purchase').on('keypress', 'input', function (e) {

    if (e.which == 13) {
        e.preventDefault();

        switch (this.id) {

            case 'purchase_discountCalculation':
                $('#purchase_payment').focus();
                break;

            case 'purchase_payment':
                $('#receiveBtn').focus();
                break;
        }
    }
});

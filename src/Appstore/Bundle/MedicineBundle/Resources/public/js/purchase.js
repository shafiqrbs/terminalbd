/**
 * Created by rbs on 5/1/17.
 */

$(document).on("click", ".approve", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                location.reload();
            });
        }
    });
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

$(document).on('change', '#purchaseItem_stockName', function() {

    var medicine = $('#purchaseItem_stockName').val();
    $.ajax({
        url: Routing.generate('medicine_purchase_particular_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#purchaseItem_salesPrice').val(obj['salesPrice']);
            $('#purchaseItem_purchasePrice').val(obj['purchasePrice']);
        }
    })

});

$('#purchaseItem_stockName').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#purchaseItem_stockName').focus();
    }, 2000)
});


$('form#purchaseItemForm').on('keypress', '.input', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'purchaseItem_quantity':
                $('#addParticular').focus();
                break;
            case 'addParticular':
                $('#purchaseItem_stockName').select2('open');
                break;
        }
        return false;
    }
});

var form = $("#purchaseItemForm").validate({

    rules: {

        "purchaseItem[stockName]": {required: true},
        "purchaseItem[purchasePrice]": {required: true},
        "purchaseItem[salesPrice]": {required: true},
        "purchaseItem[quantity]": {required: true},
        "purchaseItem[expirationDate]": {required: true},
    },

    messages: {

        "purchaseItem[stockName]":"Enter medicine name",
        "purchaseItem[purchasePrice]":"Enter purchase price",
        "purchaseItem[salesPrice]":"Enter sales price",
        "purchaseItem[quantity]":"Enter medicine quantity",
        "purchaseItem[expirationDate]": "Enter medicine expiration date",
    },
    tooltip_options: {
        "purchaseItem[stockName]": {placement:'top',html:true},
        "purchaseItem[purchasePrice]": {placement:'top',html:true},
        "purchaseItem[salesPrice]": {placement:'top',html:true},
        "purchaseItem[quantity]": {placement:'top',html:true},
        "purchaseItem[expirationDate]": {placement:'top',html:true},
    },

    submitHandler: function(form) {

        $.ajax({
            url         : $('form#purchaseItemForm').attr( 'action' ),
            type        : $('form#purchaseItemForm').attr( 'method' ),
            data        : new FormData($('form#purchaseItemForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('#subTotal').html(obj['subTotal']);
                $('#vat').val(obj['vat']);
                $('.grandTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['netTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#msg').html(obj['msg']);
                $('#purchaseItem_salesPrice').val('');
                $('#purchaseItem_purchasePrice').val('');
                $('#purchaseItem_expirationDate').val('');
                $('#purchaseItem_quantity').val('');
                $("#purchaseItem_medicineStock").select2().select2("val","");
                $('#purchaseItemForm')[0].reset();
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
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#msg').html(obj['msg']);
        }
    })
});


$(document).on('change', '#medicinepurchase_discount', function() {

    var discountType = $('#discountType').val();
    var discount = parseInt($('#medicinepurchase_discount').val());
    $.ajax({
        url: Routing.generate('hms_invoice_temporary_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType,
        success: function(response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.initialGrandTotal').html(obj['initialGrandTotal']);
            $('.initialDiscount').html(obj['initialDiscount']);
            $('#initialDiscount').val(obj['initialDiscount']);
        }

    })

});

$(document).on('change', '#medicinepurchase_payment , #medicinepurchase_discount', function() {

    var payment     = parseInt($('#medicinepurchase_payment').val()  != '' ? $('#medicinepurchase_payment').val() : 0 );
    var discount     = parseInt($('#medicinepurchase_discount').val()  != '' ? $('#medicinepurchase_discount').val() : 0 );
    var due =  parseInt($('#due').val());
    var dueAmount = (due-discount-payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.dueAmount').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.dueAmount').html(balance);
    }
});



$('form#purchaseForm').on('keypress', '.inputs', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);

        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {
                case 'medicinepurchase_payment':
                $('#receiveBtn').focus();
                break;
        }
        return false;
    }
});


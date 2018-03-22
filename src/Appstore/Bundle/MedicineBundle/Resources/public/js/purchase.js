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

$(document).on('change', '#appstore_bundle_dmspurchase_medicineStock', function() {

    var medicine = $('#appstore_bundle_dmspurchase_medicineStock').val();
    $.ajax({
        url: Routing.generate('medicine_purchase_particular_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#appstore_bundle_dmspurchase_salesPrice').val(obj['salesPrice']);
            $('#appstore_bundle_dmspurchase_purchasePrice').val(obj['purchasePrice']);
        }
    })

});

$('#appstore_bundle_dmspurchase_medicineStock').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#appstore_bundle_dmspurchase_purchasePrice').focus();
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

            case 'appstore_bundle_dmspurchase_quantity':
                $('#addParticular').focus();
                break;
            case 'addParticular':
                $('#appstore_bundle_dmspurchase_medicineStock').select2('open');
                break;
        }
        return false;
    }
});

var form = $("#purchaseItemForm").validate({

    rules: {

        "appstore_bundle_dmspurchase[medicineStock]": {required: true},
        "appstore_bundle_dmspurchase[purchasePrice]": {required: true},
        "appstore_bundle_dmspurchase[salesPrice]": {required: true},
        "appstore_bundle_dmspurchase[quantity]": {required: true},
        "appstore_bundle_dmspurchase[expirationDate]": {required: true},
    },

    messages: {

        "appstore_bundle_dmspurchase[medicineStock]":"Enter medicine name",
        "appstore_bundle_dmspurchase[purchasePrice]":"Enter purchase price",
        "appstore_bundle_dmspurchase[salesPrice]":"Enter sales price",
        "appstore_bundle_dmspurchase[quantity]":"Enter medicine quantity",
        "appstore_bundle_dmspurchase[expirationDate]": "Enter medicine expiration date",
    },
    tooltip_options: {
        "appstore_bundle_dmspurchase[medicineStock]": {placement:'top',html:true},
        "appstore_bundle_dmspurchase[purchasePrice]": {placement:'top',html:true},
        "appstore_bundle_dmspurchase[salesPrice]": {placement:'top',html:true},
        "appstore_bundle_dmspurchase[quantity]": {placement:'top',html:true},
        "appstore_bundle_dmspurchase[expirationDate]": {placement:'top',html:true},
    },

    submitHandler: function(form) {

        $.ajax({
            url         : $('form#purchaseItemForm').attr( 'action' ),
            type        : $('form#purchaseItemForm').attr( 'method' ),
            data        : new FormData($('form#purchaseItemForm')[0]),
            processData : false,
            contentType : false,
            beforeSend: function() {
                $('#savePatientButton').show().addClass('btn-ajax-loading').fadeIn(3000);
                $('.btn-ajax-loading').attr("disabled", true);
            },
            complete: function(){
                $('.btn-ajax-loading').attr("disabled", false);
                $('#savePatientButton').removeClass('btn-ajax-loading');
            },
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('#subTotal').html(obj['subTotal']);
                $('#vat').val(obj['vat']);
                $('.grandTotal').html(obj['grandTotal']);
                $('#paymentTotal').val(obj['grandTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#msg').html(obj['msg']);
                $('#appstore_bundle_dmspurchase_salesPrice').val('');
                $('#appstore_bundle_dmspurchase_purchasePrice').val('');
                $('#appstore_bundle_dmspurchase_expirationDate').val('');
                $('#appstore_bundle_dmspurchase_quantity').val('');
                $("#appstore_bundle_dmspurchase_medicineStock").select2().select2("val","");
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


$(document).on('change', '#appstore_bundle_medicine_sales_discount', function() {

    var discountType = $('#discountType').val();
    var discount = parseInt($('#appstore_bundle_hospitalbundle_invoice_discount').val());
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

$(document).on('change', '#appstore_bundle_medicinepurchase_payment , #appstore_bundle_medicinepurchase_discount', function() {

    var payment     = parseInt($('#appstore_bundle_medicinepurchase_payment').val()  != '' ? $('#appstore_bundle_medicinepurchase_payment').val() : 0 );
    var discount     = parseInt($('#appstore_bundle_medicinepurchase_discount').val()  != '' ? $('#appstore_bundle_medicinepurchase_discount').val() : 0 );
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
                case 'appstore_bundle_medicinepurchase_payment':
                $('#receiveBtn').focus();
                break;
        }
        return false;
    }
});


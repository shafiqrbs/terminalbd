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
            $('#salesPrice').val(obj['salesPrice']);
            $('#purchasePrice').val(obj['purchasePrice']);
        }
    })

});

var form = $("#purchaseItemForm").validate({

    rules: {

        "appstore_bundle_dmspurchase[medicineStock]": {required: true},
        "purchasePrice": {required: true},
        "salesPrice": {required: true},
        "quantity": {required: true},
        "appstore_bundle_dmspurchase[expirationDate]": {required: true},
    },

    messages: {

        "appstore_bundle_dmspurchase[medicineStock]":"Enter medicine name",
        "purchasePrice":"Enter purchase price",
        "salesPrice":"Enter sales price",
        "quantity":"Enter medicine quantity",
        "appstore_bundle_dmspurchase[expirationDate]": "Enter medicine expiration date",
    },
    tooltip_options: {
        "appstore_bundle_dmspurchase[medicineStock]": {placement:'top',html:true},
        "purchasePrice": {placement:'top',html:true},
        "salesPrice": {placement:'top',html:true},
        "quantity": {placement:'top',html:true},
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
                $('#due').val(obj['dueAmount']);
                $('.dueAmount').html(obj['dueAmount']);
                $('.msg-hidden').show();
                $('#msg').html(obj['msg']);
                $('#purchasePrice').val('');
                $("#particular").select2().select2("val","");
                $('#price').val('');
                $('#quantity').val('1');
            }
        });
    }
});

$(document).on('click', '#addParticularx', function() {

    var particularId = $('#appstore_bundle_dmspurchase_medicineStock').val();
    var quantity = $('#quantity').val();
    var purchasePrice = $('#purchasePrice').val();
    var salesPrice = $('#salesPrice').val();
    var expirationDate = $('#expirationDate').val();
    var url = $('#addParticular').attr('data-url');
    if(particularId == ''){
        $('.msg-hidden').show();
        $('input[name=particular]').focus();
        $('#msg').html('Please select medicine or accessories name');
        return false;
    }
    if(price == ''){
        $('.msg-hidden').show();
        $('#msg').html('Please enter purchase price');
        $('input[name=purchasePrice]').focus();
        return false;
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&purchasePrice='+purchasePrice+'&price='+price,
        success: function (response) {
            obj = JSON.parse(response);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['grandTotal']);
            $('#paymentTotal').val(obj['grandTotal']);
            $('#due').val(obj['dueAmount']);
            $('.dueAmount').html(obj['dueAmount']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
            $('#purchasePrice').val('');
            $("#particular").select2().select2("val","");
            $('#price').val('');
            $('#quantity').val('1');
        }
    })
});
$(document).on('change', '#discount', function() {

    var discount = parseInt($('#discount').val());
    var purchaseId = parseInt($('#purchaseId').val());
    $.ajax({
        url: Routing.generate('hms_purchase_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&invoice='+ purchaseId,
        success: function(response) {
            obj = JSON.parse(response);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['grandTotal']);
            $('#paymentTotal').val(obj['grandTotal']);
            $('#due').val(obj['dueAmount']);
            $('.dueAmount').html(obj['dueAmount']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
        },

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
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['grandTotal']);
            $('#due').val(obj['dueAmount']);
            $('.dueAmount').html(obj['dueAmount']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
        }
    })
});

$(document).on('change', '#appstore_bundle_hospitalbundle_hmspurchase_payment', function() {

    var payment     = parseInt($('#appstore_bundle_hospitalbundle_hmspurchase_payment').val()  != '' ? $('#appstore_bundle_hospitalbundle_hmspurchase_payment').val() : 0 );
    var due =  parseInt($('#due').val());
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.dueAmount').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.dueAmount').html(balance);
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

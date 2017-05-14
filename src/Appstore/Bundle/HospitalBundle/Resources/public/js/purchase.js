/**
 * Created by rbs on 5/1/17.
 */
$(document).on("click", ".approve", function() {
    alert('approve');
    $(this).removeClass('approve');
    var url = $(this).attr("data-url");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            location.reload();
        },
    })
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

$(document).on('change', '#particular', function() {

    var url = $('#particular').val();
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#particularId').val(obj['particularId']);
            $('#quantity').val(obj['quantity']);
            $('#price').val(obj['price']);
            $('#instruction').html(obj['instruction']);
        }
    })
});

$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = $('#quantity').val();
    var price = $('#price').val();
    var url = $('#addParticular').attr('data-url');
    alert(particularId);
    if(particularId == ''){
        $('#msg').html('Please select medicine or accessories name');
        return false;

    }
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
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

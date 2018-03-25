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

$(".addCustomer").click(function(){
    $( ".customer" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
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

$(document).on('change', '#salesitem_medicineStock', function() {

    var medicine = $('#salesitem_medicineStock').val();
    $.ajax({
        url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#salesitem_barcode').html(obj['purchaseItems']);
            $('#salesitem_salesPrice').val(obj['salesPrice']);
        }
    })

});

$('#salesitem_medicineStock').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#salesitem_barcode').focus();
    }, 2000)
});


$('form#salesItemForm').on('keypress', '.input', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'salesitem_quantity':
                $('#addParticular').focus();
                break;
            case 'addParticular':
                $('#salesitem_medicineStock').select2('open');
                break;
        }
        return false;
    }
});

var form = $("#salesItemForm").validate({

    rules: {

        "salesitem[medicineStock]": {required: true},
        "salesitem[barcode]": {required: true},
        "salesitem[salesPrice]": {required: true},
        "salesitem[quantity]": {required: true},
    },

    messages: {

        "salesitem[medicineStock]":"Enter medicine name",
        "salesitem[barcode]":"Select barcode",
        "salesitem[salesPrice]":"Enter sales price",
        "salesitem[quantity]":"Enter medicine quantity",
    },
    tooltip_options: {
        "salesitem[medicineStock]": {placement:'top',html:true},
        "salesitem[barcode]": {placement:'top',html:true},
        "salesitem[salesPrice]": {placement:'top',html:true},
        "salesitem[quantity]": {placement:'top',html:true},
    },

    submitHandler: function(form) {

        $.ajax({
            url         : $('form#salesItemForm').attr( 'action' ),
            type        : $('form#salesItemForm').attr( 'method' ),
            data        : new FormData($('form#salesItemForm')[0]),
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
                $('#invoiceParticulars').html(obj['salesItems']);
                $('#subTotal').html(obj['subTotal']);
                $('#vat').val(obj['vat']);
                $('.grandTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['netTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#msg').html(obj['msg']);
                $('#salesitem_salesPrice').val('');
                $('#salesitem_quantity').val('');
                $('#salesItemForm')[0].reset();
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

$(document).on('change', '#sales_discount', function() {

    var discountType = $('#discountType').val();
    var discount = parseInt($('#sales_discount').val());
    var invoice = parseInt($('#invoiceId').val());
    var total =  parseInt($('#dueAmount').val());
    if( discount >= total ){
        $('#sales_discount').val(0)
        return false;
    }
    $.ajax({
        url: Routing.generate('medicine_sales_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType+'&invoice='+invoice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#sales_discount').val(obj['discount']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
        }

    })

});

$(document).on('change', '#sales_payment , #sales_discount', function() {

    var payment     = parseInt($('#sales_payment').val()  != '' ? $('#sales_payment').val() : 0 );
    var discount     = parseInt($('#sales_discount').val()  != '' ? $('#sales_discount').val() : 0 );
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



$('form#salesForm').on('keypress', '.inputs', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);

        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {
            case 'sales_payment':
                $('#receiveBtn').focus();
                break;
        }
        return false;
    }
});


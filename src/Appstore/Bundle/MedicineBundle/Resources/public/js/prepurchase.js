/**
 * Created by rbs on 5/1/17.
 */
function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}

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




$(document).on('change', '#purchaseItem_stockName', function() {

    var medicine = $('#purchaseItem_stockName').val();
    $.ajax({
        url: Routing.generate('medicine_purchase_particular_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#purchaseItem_expirationStartDate').focus();
            $('#purchaseItem_salesPrice').val(obj['salesPrice']);
            $('#purchaseItem_purchasePrice').val(obj['purchasePrice']);
            $('#unit').html(obj['unit']);
        }
    })

});

$('form#stockItemForm').on('keyup', '#medicineStock_purchasePrice', function (e) {
    var mrp = $('#medicineStock_purchasePrice').val();
    $('#medicineStock_salesPrice').val(mrp);
});

$('#purchaseItem_stockName').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#purchaseItem_stockName').focus();
    }, 2000)
});


$('form#purchaseItemForm').on('keyup', '#purchaseItem_purchasePrice', function (e) {
    var mrp = $('#purchaseItem_purchasePrice').val();
    $('#purchaseItem_salesPrice').val(mrp);
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



$('form#stockItemForm').on('keypress', '.stockInput', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'medicineStock_rackNo':
                $('#medicineStock_purchaseQuantity').focus();
                break;
            case 'medicineStock_purchaseQuantity':
                $('#medicineStock_purchasePrice').focus();
                break;
            case 'medicineStock_purchasePrice':
                $('#medicineStock_unit').focus();
                break;
            case 'medicineStock_unit':
                $('#stockItemCreate').focus();
                break;

        }
        return false;
    }
});

var formStock = $("#stockItemForm").validate({
    rules: {

        "medicineStock[name]": {required: true},
        "medicineStock[rackNo]": {required: false},
        "medicineStock[unit]": {required: false},
        "medicineStock[purchasePrice]": {required: false},
        "medicineStock[purchaseQuantity]": {required: true}
    },

    messages: {

        "medicineStock[name]":"Enter medicine name",
        "medicineStock[rackNo]":"Enter medicine rack no",
        "medicineStock[unit]":"Enter medicine unit",
        "medicineStock[purchasePrice]":"Enter purchase price",
        "medicineStock[purchaseQuantity]":"Enter purchase quantity",

    },
    tooltip_options: {
        "medicineStock[name]": {placement:'top',html:true},
        "medicineStock[rackNo]": {placement:'top',html:true},
        "medicineStock[unit]": {placement:'top',html:true},
        "medicineStock[purchasePrice]": {placement:'top',html:true},
        "medicineStock[purchaseQuantity]": {placement:'top',html:true},
    },

    submitHandler: function(formStock) {

        $.ajax({
            url         : $('form#stockItemForm').attr( 'action' ),
            type        : $('form#stockItemForm').attr( 'method' ),
            data        : new FormData($('form#stockItemForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                if(obj['success'] === 'invalid'){
                    alert('This item already exist in stock item');
                    return false;
                }
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('#subTotal').html(obj['subTotal']);
                $('#vat').val(obj['vat']);
                $('.grandTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['netTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#discount').html(obj['discount']);
                $('#msg').html(obj['msg']);
                $("#medicineStock_name").select2("val", "");
                $("#medicineId").val();
                $('#stockItemForm')[0].reset();
                EditableInit();
            }
        });
    }
});

$('#invoiceParticulars').on("click", ".delete", function() {

    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('#remove-'+id).hide();
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#discount').html(obj['discount']);
            $('#msg').html(obj['msg']);
        }
    })
});


$(document).on('change', '.quantity , .purchasePrice ,.salesPrice', function() {

    var id = $(this).attr('data-id');
    var quantity = parseFloat($('#quantity-'+id).val());
    var salesPrice = parseFloat($('#salesPrice-'+id).val());
    var subTotal  = (quantity * salesPrice);
    $("#subTotal-"+id).html(financial(subTotal));
    $.ajax({
        url: Routing.generate('medicine_prepurchase_item_update'),
        type: 'POST',
        data:'purchaseItemId='+ id +'&quantity='+ quantity +'&salesPrice='+ salesPrice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#total').html(obj['netTotal']);
            $('#distcount').val(obj['discount']);
        },

    })
});


$(document).on('change', '#medicinepurchase_discountCalculation', function() {

    var discount = parseFloat($('#medicinepurchase_discountCalculation').val());
    var purchase = parseInt($('#purchaseId').val());
    $.ajax({
        url: Routing.generate('medicine_prepurchase_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&purchase='+purchase,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#total').html(obj['netTotal']);
            $('#discount').html(obj['discount']);
        }

    })

});


$(document).on('change', '#medicinepurchase_payment , #medicinepurchase_discount', function() {

    var payment     = parseInt($('#medicinepurchase_payment').val()  != '' ? $('#medicinepurchase_payment').val() : 0 );
    var due =  parseInt($('#paymentTotal').val());
    var dueAmount = (due-payment);
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

            case 'medicinepurchase_discountCalculation':
            $('#medicinepurchase_payment').focus();
            break;

            case 'medicinepurchase_payment':
            $('#receiveBtn').focus();
            break;
        }
        return false;
    }
});

$(document).on("click", ".confirmSubmit", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('form#purchaseForm').submit();
        }
    });

});


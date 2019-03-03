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
            $('#purchaseItem_expirationStartDate').focus();
            $('#purchaseItem_salesPrice').val(obj['salesPrice']);
            $('#purchaseItem_purchasePrice').val(obj['purchasePrice']);
            $('#unit').html(obj['unit']);
        }
    })

});


$('#purchaseItem_stockName').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#purchaseItem_stockName').focus();
    }, 500)
});

$('form#purchaseItemForm').on('keyup', '#purchaseItem_purchasePrice', function (e) {
    var mrp = $('#purchaseItem_purchasePrice').val();
    $('#purchaseItem_salesPrice').val(mrp);
});

$(document).on( "click", "#stockShow", function(e){
    $('#hide').slideToggle(2000);
    $("i", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
    $('#medicineStock_name').focus();
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

            case 'purchaseItem_stockName':
                $('#purchaseItem_quantity').focus();
                break;

            case 'purchaseItem_quantity':
                $('#purchaseItem_purchasePrice').focus();
                break;

            /*case 'purchaseItem_purchasePrice':
                $('#purchaseItem_expirationEndDate').focus();
                break;*/

            case 'purchaseItem_purchasePrice':
                $('#addPurchaseItem').click();
                $('#purchaseItem_stockName').select2('open');
                break;
        }
        return false;
    }
});

/*
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



        }
        return false;
    }
});
*/

$('form#medicineStock').on('keypress', '.stockInput', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);

        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'medicineStock_name':
                $('#medicineStock_purchaseQuantity').focus();
                break;

            case 'medicineStock_purchaseQuantity':
                $('#medicineStock_purchasePrice').focus();
                break;

/*            case 'medicineStock_purchasePrice':
                $('#medicineStock_unit').focus();
                break;

            case 'medicineStock_unit':
                $('#medicineStock_rackNo').focus();
                break;*/

            case 'medicineStock_purchasePrice':
                $('#stockItemCreate').click();
                $('#medicineStock_name').focus();
                break;
        }
        return false;
    }
});


$('form#medicineStock').on('keyup', '#medicineStock_purchasePrice', function (e) {
    var mrp = $('#medicineStock_purchasePrice').val();
    $('#medicineStock_salesPrice').val(mrp);
});

var formStock = $("#medicineStock").validate({
    rules: {
        "medicineStock[name]": {required: true},
        "medicineStock[accessoriesBrand]": {required: false},
        "medicineStock[rackNo]": {required: false},
        "medicineStock[purchaseQuantity]": {required: true},
        "medicineStock[unit]": {required: false},
        "medicineStock[purchasePrice]": {required: true},
        "medicineStock[salesPrice]": {required: false}
    },
    messages: {
        "medicineStock[name]": "Enter medicine name",
        "medicineStock[rackNo]": "Enter medicine rack no",
        "medicineStock[unit]": "Enter medicine unit",
        "medicineStock[purchasePrice]": "Enter purchase price",
        "medicineStock[purchaseQuantity]": "Enter purchase quantity",
    },
    tooltip_options: {
        "medicineStock[name]": {placement: 'top', html: true},
        "medicineStock[rackNo]": {placement: 'top', html: true},
        "medicineStock[unit]": {placement: 'top', html: true},
        "medicineStock[purchasePrice]": {placement: 'top', html: true},
        "medicineStock[purchaseQuantity]": {placement: 'top', html: true},
    },

    submitHandler: function (formStock) {
        $.ajax({
            url: $('form#medicineStock').attr('action'),
            type: $('form#medicineStock').attr('method'),
            data: new FormData($('form#medicineStock')[0]),
            processData: false,
            contentType: false,
            success: function (response) {
                obj = JSON.parse(response);
                if (obj['success'] === 'invalid') {
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
                $("#medicineStock_rackNo").select2("val", "");
                $("#medicineStock_unit").select2("val", "");
                $("#medicineId").val();
                $('#medicineStock')[0].reset();
            }
        });
    }
});

var form = $("#purchaseItemForm").validate({
    rules: {

        "purchaseItem[stockName]": {required: true},
        "purchaseItem[purchasePrice]": {required: false},
        "purchaseItem[salesPrice]": {required: false},
        "purchaseItem[quantity]": {required: true},
        "purchaseItem[expirationEndDate]": {required: false}
    },

    messages: {
        "purchaseItem[stockName]":"Enter medicine name",
        "purchaseItem[purchasePrice]":"Enter purchase price",
        "purchaseItem[salesPrice]":"Enter sales price",
        "purchaseItem[quantity]":"Enter medicine quantity"
    },
    tooltip_options: {
        "purchaseItem[stockName]": {placement:'top',html:true},
        "purchaseItem[purchasePrice]": {placement:'top',html:true},
        "purchaseItem[salesPrice]": {placement:'top',html:true},
        "purchaseItem[quantity]": {placement:'top',html:true}
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
                $('#discount').html(obj['discount']);
                $('#msg').html(obj['msg']);
                $("#purchaseItem_stockName").select2("val", "");
                $('#purchaseItemForm')[0].reset();
                $('#addPurchaseItem').html('<i class="icon-save"></i> Add').attr("disabled", false);
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
    var salesQuantity = parseFloat($('#salesQuantity-'+id).val());
    var purchasePrice = parseFloat($('#purchasePrice-'+id).val());
    var salesPrice = parseFloat($('#salesPrice-'+id).val());
    if(salesQuantity > quantity){
        $('#quantity-'+id).val($('purchaseQuantity-'+id).val());
        alert("Purchase quantity must be more then sales quantity.");
        return false;
    }
    var subTotal  = (quantity * purchasePrice);
    $("#subTotal-"+id).html(subTotal);
    $.ajax({
        url: Routing.generate('medicine_purchase_item_update'),
        type: 'POST',
        data:'purchaseItemId='+ id +'&quantity='+ quantity +'&purchasePrice='+ purchasePrice+'&salesPrice='+ salesPrice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#discount').html(obj['discount']);
        },

    })
});


$(document).on('change', '#medicinepurchase_discountCalculation , #medicinepurchase_discountType', function() {

    var discountType = $('#medicinepurchase_discountType').val();
    var discount = parseFloat($('#medicinepurchase_discountCalculation').val());
    var purchase = parseInt($('#purchaseId').val());
    $.ajax({
        url: Routing.generate('medicine_purchase_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType+'&purchase='+purchase,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#msg').html(obj['msg']);
            $('#discount').html(obj['discount']);
            $('#medicinepurchase_discount').val(obj['discount']);

        }

    })

});

$('#medicinepurchase_payment').click(function() {
    $('#medicinepurchase_payment').attr('value', '');
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


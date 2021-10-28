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

    if( paymentMethod === 2){
        $('#cartMethod').show();
        $('#bkashMethod').hide();
    }else if( paymentMethod === 3){
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
            $('#purchaseItem_quantity').focus();
            $('#pack').val(obj['pack']);
            $('#minQuantity').val(obj['minQuantity']);
            $('#purchaseItem_salesPrice').val(obj['salesPrice']);
            $('#purchaseItem_purchasePrice').val(obj['purchasePrice']);
            $('#unit').html(obj['unit']);
            if(obj['openingStatus'] === 'valid' ){
                $('#opening-box').show();
                $('#currentSalesQty').html(obj['salesQty']);
                $('#salesQty').val(obj['salesQty']);
                $('#openingQuantity').val(obj['salesQty']);
                $('#totalQty').html(obj['salesQty']);
            }
            $('.expirationDate').editable();
        }
    })
});

$('form#purchaseItemForm').on('keyup', '#currentQty', function (e) {

    var salesQty =  parseInt($('#salesQty').val());
    var currentQty =  parseInt($(this).val());
    var total = (salesQty + currentQty);
    $('#totalQty').html(total);
    $('#openingQuantity').val(total);
});

$('form#purchaseItemForm').on('keyup', '#pack , #purchaseItem_quantity,#purchaseItem_bonusQuantity', function (e) {
    var bonus     = parseInt($('#purchaseItem_bonusQuantity').val()  != '' ? $('#purchaseItem_bonusQuantity').val() : 0 );
    var pack     = parseInt($('#pack').val()  != '' ? $('#pack').val() : 0 );
    var qnt     = parseInt($('#purchaseItem_quantity').val()  != '' ? $('#purchaseItem_quantity').val() : 0 );
    if(qnt === "NaN" || qnt === ""){
        $('#totalQnt').html(0);
    }
    if(qnt > 0){
        var totalQnt = ((pack * qnt)+bonus);
        $('#totalQnt').html(totalQnt);
    }
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
                var qnt = $('#medicineStock_purchaseQuantity').val();
                if(qnt == "NaN" || qnt =="" ){
                    $('#medicineStock_purchaseQuantity').focus();
                }else{
                    $('#medicineStock_purchasePrice').focus();
                }
                break;
            case 'medicineStock_purchasePrice':
                var price = $('#medicineStock_purchasePrice').val();
                if(price == "NaN" || price =="" ){
                    $('#medicineStock_purchasePrice').focus();
                }else {
                    $('#stockItemCreate').click();
                    $('#medicineStock_name').focus();
                }
                break;
            case 'medicineStock_unit':
                $('#stockItemCreate').click();
                $('#medicineStock_name').focus();
                break;

        }
        return false;
    }
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
                var qnt = $('#purchaseItem_quantity').val();
                if(qnt == "NaN" || qnt =="" ){
                    $('#purchaseItem_quantity').focus();
                }else{
                    $('#addPurchaseItem').click();
                    $('#purchaseItem_stockName').select2('open');
                }
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

$('form#medicineStock').on('keyup', '#medicineStock_purchasePrice', function (e) {
    var mrp = $('#medicineStock_purchasePrice').val();
    $('#medicineStock_salesPrice').val(mrp);
});

$('form#medicineStock').on('keyup', ' #medicineStock_pack , #medicineStock_purchaseQuantity', function (e) {
    var pack = parseInt($('#medicineStock_pack').val());
    var qnt = parseInt($('#medicineStock_purchaseQuantity').val());
    var totalQnt = (pack * qnt);
    $('#stockTotalQnt').html(totalQnt);
});

var formStock = $("#medicineStock").validate({
    rules: {
        "medicineStock[name]": {required: true},
        "medicineStock[purchaseQuantity]": {required: true},
        "medicineStock[unit]": {required: false},
        "medicineStock[salesPrice]": {required: true},
    },
    messages: {
        "medicineStock[name]": "Enter medicine name",
        "medicineStock[unit]": "Enter medicine unit",
        "medicineStock[salesPrice]": "Enter mrp price",
        "medicineStock[purchaseQuantity]": "Enter purchase quantity",
    },
    tooltip_options: {
        "medicineStock[name]": {placement: 'top', html: true},
        "medicineStock[unit]": {placement: 'top', html: true},
        "medicineStock[salesPrice]": {placement: 'top', html: true},
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
                $('#paymentTotal').val(obj['subTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#discount').html(obj['discount']);
                $('#msg').html(obj['msg']);
                $("#medicineStock_name").select2("val", "");
                $("#medicineStock_rackNo").select2("val", "");
                $("#medicineStock_unit").select2("val", "");
                $("#medicineId").val();
                $('#medicineStock')[0].reset();
                $('#opening-box').hide();

            }
        });
    }
});

var form = $("#purchaseItemForm").validate({
    rules: {
        "purchaseItem[stockName]": {required: true},
        "purchaseItem[salesPrice]": {required: false},
        "purchaseItem[quantity]": {required: true},
        "purchaseItem[expirationEndDate]": {required: false},
        "purchaseItem[bonusQuantity]": {required: false}
    },

    messages: {
        "purchaseItem[stockName]":"Enter medicine name",
        "purchaseItem[salesPrice]":"Enter sales price",
        "purchaseItem[quantity]":"Enter medicine quantity"
    },
    tooltip_options: {
        "purchaseItem[stockName]": {placement:'top',html:true},
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
                $('#paymentTotal').val(obj['subTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#discount').html(obj['discount']);
                $('#msg').html(obj['msg']);
                $("#purchaseItem_stockName").select2("val", "");
                $('#purchaseItemForm')[0].reset();
                $('#addPurchaseItem').html('<i class="icon-save"></i> Add').attr("disabled", false);
                $('#opening-box').hide();
                $('.expirationDate').editable();
            }
        });
    }
});


$('#invoiceParticulars').on("click", ".deleteParticular", function() {

    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                $('#remove-'+id).hide();
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('.grandTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['subTotal']);
                $('#due').val(obj['due']);
                $('.dueAmount').html(obj['due']);
                $('#discount').html(obj['discount']);
                $('#msg').html(obj['msg']);
            });
        }
    });
});


$(document).on('change', '.quantity ,.salesPrice,.bonusQuantity', function() {

    var id = $(this).attr('data-id');
    var quantity = parseFloat($('#quantity-'+id).val());
    var salesQuantity = parseFloat($('#salesQuantity-'+id).val());
    var salesPrice = parseFloat($('#salesPrice-'+id).val());
    var bonusQuantity = parseFloat($('#bonusQuantity-'+id).val());
    if(salesQuantity > quantity){
        $('#quantity-'+id).val($('purchaseQuantity-'+id).val());
        alert("Purchase quantity must be more then sales quantity.");
        return false;
    }
    var subTotal  = (quantity * salesPrice);
    $("#subTotal-"+id).html(subTotal);
    $.ajax({
        url: Routing.generate('medicine_purchase_item_update'),
        type: 'POST',
        data:'purchaseItemId='+ id +'&quantity='+ quantity+'&salesPrice='+ salesPrice+'&bonusQuantity='+ bonusQuantity,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['subTotal']);
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
            $('#paymentTotal').val(obj['subTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
            $('#msg').html(obj['msg']);
            $('#discount').html(obj['discount']);
            $('#medicinepurchase_discount').val(obj['discount']);
            $('#discountPercentage').html(discount+'%');
        }

    })

});


$('.remove-value').click(function() {
    $(this).attr('value', '');
});

$('.invoice-mode').change(function() {
    var mode = $(this).val();
    if(mode === 'invoice'){
        $('.invoiceMode').hide();
        $('#due-input').toggle();
    }else{
        $(".invoiceMode").toggle();
        $('#due-input').hide();
    }
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

$(document).on('change', '#medicinepurchase_payment , #invoiceDue', function() {
    var invoiceMode = $('#medicinepurchase_invoiceMode').val();
    if(invoiceMode === "invoice") {
        var payment = parseInt($('#medicinepurchase_payment').val() != '' ? $('#medicinepurchase_payment').val() : 0);
        var invoiceDue = parseInt($('#invoiceDue').val() != '' ? $('#invoiceDue').val() : 0);
        var paymentTotal = parseInt($('#paymentTotal').val() != '' ? $('#paymentTotal').val() : 0);
        var discount = (paymentTotal - (payment + invoiceDue));
        var percentage = ((discount * 100) / paymentTotal).toFixed(2);
        $('#discountPercentage').html(percentage + '%');
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


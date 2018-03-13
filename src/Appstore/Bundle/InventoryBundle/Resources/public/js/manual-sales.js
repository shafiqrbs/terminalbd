$('#addItem').attr("disabled", true);
$(".addCustomer").click(function(){
    $( ".customer" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
});

$(".select2CustomerName").select2({

    ajax: {

        url: Routing.generate('domain_customer_search'),
        dataType: 'json',
        delay: 250,
        data: function (params, page) {
            return {
                q: params,
                page_limit: 100
            };
        },
        results: function (data, page) {
            return {
                results: data
            };
        },
        cache: true
    },
    escapeMarkup: function (m) {
        return m;
    },
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var customer = $(element).val();
        $.ajax(Routing.generate('domain_customer_name', { customer : customer}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 3
});

$(document).on("click", ".remove", function() {

    var url = $(this).attr('data-url');
    var id = $(this).attr('id');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                $('#remove'+id).hide();
                obj = JSON.parse(response);
                $('#subTotal').val(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('#netTotal').val(obj['netTotal']);
                $('#salesItem').html(obj['salesItems']);
                $('#vat').val(obj['vat']);
                $('#dueAmount').val(obj['due']);
            });
        }
    });

});

$(document).on('change', '.itemSearch', function() {
    var item = $(this).val();
    if(item == ''){
        alert('You have to select item from drop down and this not item');
        return false;
    }
    $.ajax({
        url: Routing.generate('inventory_salesmanual_item_search'),
        type: 'POST',
        data: 'item='+item,
        success: function (response) {
            obj = JSON.parse(response);
            $('#itemId').val(obj['itemId']);
            $('#salesPrice').val(obj['price']);
            $('#purchasePrice').val(obj['purchasePrice']);
            if(obj['unit']){
                $('.unit').html(obj['unit']);
            }
            $('#addItem').attr("disabled", false);
        }
    })
});

$(document).on('click', '#addItem', function(e) {

    var url = $(this).attr('data-url');
    var item = $('#appstore_bundle_inventorybundle_salesitem_item').val();
    var salesPrice = $('#salesPrice').val();
    var quantity = $('#quantity').val();
    var purchasePrice = $('#purchasePrice').val();
    if(quantity == ''){
        alert('Add Product Price & Quantity , Please try again.');
        return false;
    }

    $.ajax({
        url: url,
        type: 'POST',
        data:'item=' + item + '&quantity=' + quantity + '&salesPrice=' + salesPrice+'&purchasePrice='+purchasePrice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').val(obj['subTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('#salesItem').html(obj['salesItems']);
            $('#vat').val(obj['vat']);
            $('#dueAmount').val(obj['due']);
            $('#addItem').attr("disabled", true);
            $("#appstore_bundle_inventorybundle_salesitem_item").select2().select2("val","");
            $('#salesPrice').val('');
            $('#quantity').val('1');
        },
    })
    e.preventDefault();

});

$(document).on('change', '#paymentAmount', function() {

    var payment     = parseInt($('#paymentAmount').val()  != '' ? $('#paymentAmount').val() : 0 );
    var total =  parseInt($('#netTotal').val());
    if( payment >= total ){

        var returnAmount = ( payment - total );
        $('#returnAmount').val(returnAmount).addClass('payment-yellow');
        $('.returnAmount').html(returnAmount).addClass('payment-yellow');
        $('#dueAmount').val('').removeClass('payment-red');
        $('.dueAmount').html('').removeClass('payment-red');

    }else{

        var dueAmount = (total-payment);
        if(dueAmount > 0){
            $('#returnAmount').val('').removeClass('payment-yellow');
            $('.returnAmount').html('').removeClass('payment-yellow');
            $('#dueAmount').val(dueAmount).addClass('payment-red');
            $('.dueAmount').html(dueAmount).addClass('payment-red');
        }
    }
    if(payment > 0 && total > 0  ){
        $(".paymentBtn").attr("disabled", false);
    }else{
        $(".paymentBtn").attr("disabled", true);
    }

});


$(document).on( "click", ".btn-qnt-particular", function(e){

    e.preventDefault();
    var productId   = $(this).attr('data-text');
    var price       = $(this).attr('data-title');
    fieldId         = $(this).attr('data-id');
    fieldName       = $(this).attr('data-field');
    type            = $(this).attr('data-type');
    var input       = $('#quantity');
    var currentVal  = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }

        }
    } else {
        input.val(1);
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
                $('#sales_general_process').focus();
                break;

            case 'sales_general_process':
                $('#receiveBtn').focus();
                break;
        }
    }
});

$(document).on("click", ".paymentBtn", function(e) {

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('form').submit();
        }
    });
    e.preventDefault();
});


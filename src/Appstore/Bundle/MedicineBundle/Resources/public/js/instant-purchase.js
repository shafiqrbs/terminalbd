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

$(document).on("click", "#instantPopup", function() {

    var url = $(this).attr('data-url');
    $.ajax({
        url : url,
        beforeSend: function(){
            $('.loader-double').fadeIn(1000).addClass('is-active');
        },
        complete: function(){
            $('.loader-double').fadeIn(1000).removeClass('is-active');
        },
        success:  function (data) {
            $("#instantPurchaseLoad").html(data);
            jqueryLoad();
        }
    });

});

$(document).on('click', '#instantPopupx', function() {

    $('.dialogModal_header').html('Instant Purchase Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('medicine_instant_purchase_load'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    jqueryLoad();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});


$(document).on('change', '#medicineName', function() {

    var medicine = $('#medicineName').val();
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

$('#medicineName').on("select2-selecting", function (e) {
    setTimeout(function () {
        $('#medicineName').focus();
    }, 2000)
});


$('form#instantPurchase').on('keypress', '.input', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'quantity':
                $('#addParticular').focus();
                break;
            case 'addParticular':
                $('#salesitem_medicineStock').select2('open');
                break;
        }
        return false;
    }
});

$(document).on('click', '#addInstantPurchase', function() {

    var form = $("#instantPurchase").validate({

        rules: {

            "medicineName": {required: true},
            "vendor": {required: true},
            "receiveUser": {required: true},
            "purchasePrice": {required: true},
            "salesPrice": {required: true},
            "expirationDate": {required: false},
            "quantity": {required: true}
        },

        messages: {

            "medicineName": "Enter medicine name",
            "vendor": "Select vendor",
            "purchasePrice": "Enter sales price",
            "salesPrice": "Enter sales price",
            "quantity": "Enter medicine quantity",
        },
        tooltip_options: {
            "medicineName": {placement: 'top', html: true},
            "vendor": {placement: 'top', html: true},
            "receiveUser": {placement: 'top', html: true},
            "purchasePrice": {placement: 'top', html: true},
            "salesPrice": {placement: 'top', html: true},
            "quantity": {placement: 'top', html: true},
        },

        submitHandler: function (form) {
            $.ajax({
                url: $('form#instantPurchase').attr('action'),
                type: $('form#instantPurchase').attr('method'),
                data: new FormData($('form#instantPurchase')[0]),
                processData: false,
                contentType: false,
                success: function (response) {
                    obj = JSON.parse(response);
                    $('#instantPurchaseItem').html(obj['instantPurchaseItem']);
                    $('#instantPurchase')[0].reset();
                }
            });
        }
    });
});

$(document).on('click', '.instantSales', function() {

    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    var quantity = parseInt($('#quantity-'+id).val());
    $.ajax({
        url:url,
        type: 'POST',
        data:'quantity='+ quantity,
        success: function(response){
            obj = JSON.parse(response);
            $('#invoiceParticulars').html(obj['salesItems']);
            $('#subTotal').html(obj['subTotal']);
            $('#vat').val(obj['vat']);
            $('.grandTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
        }
    })

});




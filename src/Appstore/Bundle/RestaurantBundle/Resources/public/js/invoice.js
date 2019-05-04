$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});


$( "#name" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('domain_customer_auto_name_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {}

});

$( "#mobile" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('domain_customer_auto_mobile_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {}

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

$(document).on('change', '.particular', function() {
    var id = $(this).val();
    if(id == ''){
        alert('You have to add product from drop down and this not service item');
        return false;
    }
    $.ajax({
        url: Routing.generate('restaurant_temporary_particular_search',{'id':id}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#particularId').val(obj['particularId']);
            $('#quantity').val(obj['quantity']).focus();
            $('#price').val(obj['price']);
            $('#instruction').html(obj['instruction']);
            $('#addParticular').attr("disabled", false);
        }
    })
});

$('form#particularForm').on('keypress', 'input,select,textarea', function (e) {
    if (e.which == 13) {
        e.preventDefault();
        switch (this.id) {
            case 'quantity':
                $('#addParticular').trigger('click');
                break;
        }
    }
});

$(document).on('click', '#addParticular', function() {

    var particularId = $('#restaurant_item_particular').val();
    if(particularId == ''){
        $("#restaurant_item_particular").select2('open');
        return false;
    }
    var quantity = parseInt($('#quantity').val());
    var url = $('#addParticular').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&process=create',
        success: function (response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').val(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
            $('.discount').val(obj['discount']).attr( "placeholder", obj['discount'] );
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#quantity').val('1');
            $("#restaurant_item_particular").select2().select2("val","").select2('open');
            $('.receiveBtn').attr("disabled", false);
        }
    })
});

$(document).on('click', '.addCart', function() {

    var id = $(this).attr('data-text');
    var price = parseInt($(this).attr('data-title'));
    var particularId = $(this).attr('data-id');
    var quantity = parseInt($('#quantity-'+id).val());
    var subTotal = (price * quantity);
    $('#subTotal-'+id).html('= '+subTotal);
    var url = $(this).attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&process=update',
        success: function (response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').val(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
        }
    })
});

$(document).on("click", ".removeDiscount", function() {

    var url = $(this).attr("data-url");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);

        }
    })
});

$(document).on("click", ".particularDelete", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                obj = JSON.parse(data);
                $('.subTotal').html(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('.due').html(obj['due']);
                $('.discountAmount').html(obj['discount']);
                $('.discount').val('').attr( "placeholder", obj['discount'] );
                $('.total'+id).html(obj['total']);
                $('#msg').html(obj['msg']);
                $('#remove-'+id).hide();
            });
        }
    });
});

$(document).on('change', '#restaurant_invoice_discountType , #restaurant_invoice_discountCalculation', function() {

    var invoice = $('#invoiceId').val();
    var discountType = $('#restaurant_invoice_discountType').val();
    var discount = parseInt($('#restaurant_invoice_discountCalculation').val());
    if(discount === "NaN"){
        return false;
    }
    $.ajax({
        url: Routing.generate('restaurant_invoice_discount_update'),
        type: 'POST',
        data:'invoice=' + invoice+'&discount=' + discount+'&discountType='+discountType,
        success: function(response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.discount').html(obj['discount']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
        }
    })
});

$(document).on('change', '#restaurant_invoice_discountCoupon', function() {

    var discount = $('#restaurant_invoice_discountCoupon').val();
    if(discount === "NaN"){
        return false;
    }
    $.ajax({
        url: Routing.generate('restaurant_temporary_discount_coupon'),
        type: 'POST',
        data:'discount=' + discount,
        success: function(response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
        }
    })
});



/*$(document).on("change", "#invoiceForm", function() {

    var url = $(this).attr("action");
    $.ajax({
        url: url,
        type: 'POST',
        data: new FormData($('#invoiceForm')[0]),
        processData: false,
        contentType: false,
        success: function (response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('#discount').val(obj['discount']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);

        }
    })

});*/


$(document).on('keyup', '#restaurant_invoice_payment', function() {

    var payment  = parseInt($('#restaurant_invoice_payment').val()  != '' ? $('#restaurant_invoice_payment').val() : 0 );
    var netTotal  = parseInt($('#netTotal').val()  != '' ? $('#netTotal').val() : 0 );
    var dueAmount = (netTotal - payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('#dueable').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('#dueable').html(balance);
    }
});

/*$('#invoiceParticular').DataTable( {
    scrollY:        '25vh',
    scrollCollapse: true,
    paging:         false,
    bInfo : false,
    sScrollX: '100%',
    bSort: false,
    bFilter: false,
});*/

$('#salesList').DataTable( {
    scrollY:        '50vh',
    scrollCollapse: true,
    paging:         false,
    bInfo : false,
    bSort: false
});

/*
$(document).on('change', '#restaurant_invoice_tokenNo', function(e) {

    var invoice = $('#invoiceId').val();
    var tokenNo = $('#restaurant_invoice_tokenNo').val();
    if(tokenNo == ''){
        return false;
    }
    $.post( Routing.generate('restaurant_invoice_token_check') ,{ invoice:invoice , tokenNo:tokenNo} )
        .done(function( data ) {
            if(data == 'invalid'){
                $("#restaurant_invoice_tokenNo").select2().select2("val","");
                $('#cabinInvalid').notifyModal({
                    duration : 5000,
                    placement : 'center',
                    overlay : true,
                    type : 'notify',
                    icon : false,
                });

            }else{

                $("#receiveBtn").attr("disabled", false);
                $("#kitchenBtn").attr("disabled", false);
            }
        });

});*/

$("input:text:visible:first").focus();
$('input[name=particular]').focus();

$('.inputs').keydown(function (e) {
    if (e.which === 13) {
        var index = $('.inputs').index(this) + 1;
        $('.inputs').eq(index).focus();
    }
});
$('.input2').keydown(function (e) {
    if (e.which === 13) {
        var index = $('.input2').index(this) + 1;
        $('.input2').eq(index).focus();
    }
});

$('form#invoiceForm').on('keypress', 'input,select,textarea', function (e) {

    if (e.which == 13) {
        e.preventDefault();
        switch (this.id) {
            case 'restaurant_invoice_slipNo':
                $('#restaurant_invoice_tokenNo').focus();
                break;
            case 'restaurant_invoice_tokenNo':
                $('#restaurant_invoice_salesBy').focus();
                break;
            case 'restaurant_invoice_salesBy':
                $('#restaurant_invoice_discountCalculation').focus();
                break;
            case 'restaurant_invoice_discountCalculation':
                $('#restaurant_invoice_discountCoupon').focus();
                break;
            case 'restaurant_invoice_discountCoupon':
                $('#restaurant_invoice_payment').focus();
                break;
            case 'restaurant_invoice_payment':
                $('#restaurant_invoice_process').focus();
                break;
            case 'restaurant_invoice_process':
                $('#saveButton').trigger('click');
                break;
        }
    }
});

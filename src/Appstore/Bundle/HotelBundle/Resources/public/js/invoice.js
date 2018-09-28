$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

var bindDatePicker = function(element) {
    $(element).datetimepicker({
        showOn: "button",
        buttonImage: "/img/calendar_icon.png",
        buttonImageOnly: true,
        dateFormat: 'mm/dd/yy',
        timeFormat: 'hh:mm tt',
        stepMinute: 1,
        onClose: datePickerClose
    });
};

function datePickerReload() {
    $( ".date-picker" ).datepicker({
        dateFormat: "dd-mm-yy"
    });
}

$("[id^=startPicker]").each(function() {
    bindDatePicker(this);
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
        $('#cartMethod').css({ 'display': "block" });
        $('#bkashMethod').hide();
    }else if( paymentMethod == 3){
        $('#bkashMethod').css({ 'display': "block" });
        $('#cartMethod').hide();
    }else{
        $('#cartMethod').hide();
        $('#bkashMethod').hide();
    }

});

$(".addCustomer").click(function(){
    $( ".customer" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
});

$(document).on('change', '#particular', function() {

    var particular = $('#particular').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    $.ajax({
        url: Routing.generate('hotel_particular_search',{'id':particular,'startDate':startDate,'endDate':endDate}),
        type: 'POST',
        success: function (response) {
            obj = JSON.parse(response);
            if(obj['msg'] === 'valid'){
                $('#unit').html(obj['unit']);
                $('#salesPrice').val(obj['salesPrice']);
                $('#subTotal').html(obj['salesPrice']);
            }else{
                $("#particular").select2().select2("val","");
                alert(obj['msg']);
            }


        }
    })

});


var form = $("#stockInvoice").validate({

    rules: {

        "particular": {required: true},
        "startDate": {required: true},
        "endDate": {required: true},
        "adult": {required: true},
        "child": {required: false},
        "guestName": {required: true},
        "guestMobile": {required: true}
    },

    messages: {

        "particular":"Enter select room name/no",
        "startDate": "Enter start date",
        "endDate":"Enter end date",
        "adult": "Select no of adult",
        "guestName": "Enter specific room gust name",
        "guestMobile": "Enter specific room gust mobile no"
    },
    tooltip_options: {
        "particular": {placement:'top',html:true},
        "startDate": {placement:'top',html:true},
        "endDate": {placement:'top',html:true},
        "adult": {placement:'top',html:true},
        "guestName": {placement:'top',html:true},
        "guestMobile": {placement:'top',html:true},
    },

    submitHandler: function(form) {

        $.ajax({
            url         : $('form#stockInvoice').attr( 'action' ),
            type        : $('form#stockInvoice').attr( 'method' ),
            data        : new FormData($('form#stockInvoice')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('.subTotal').html(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('.due').html(obj['due']);
                $('.payment').html(obj['payment']);
                $('.discount').html(obj['discount']);
                $('#paymentTotal').val(obj['due']);
                $("#particular").select2().select2("val","");
                $('#stockInvoice')[0].reset();
            }
        });
    }
});

$(document).on('change', '.quantity , .salesPrice', function() {

    var id = $(this).attr('data-id');

    var quantity = parseFloat($('#quantity-'+id).val());
    var price = parseFloat($('#salesPrice-'+id).val());
    var subTotal  = (quantity * price);
    $("#subTotal-"+id).html(subTotal);
    $.ajax({
        url: Routing.generate('business_invoice_item_update'),
        type: 'POST',
        data:'itemId='+ id +'&quantity='+ quantity +'&salesPrice='+ price,
        success: function(response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('.due').html(obj['due']);
            $('#paymentTotal').val(obj['due']);
            $('.payment').html(obj['payment']);
            $('.discount').html(obj['discount']);
        },

    })
});


$(document).on("click", ".particularDelete", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
       $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                obj = JSON.parse(response);
                $('#remove-'+id).remove();
                $('.subTotal').html(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('.due').html(obj['due']);
                $('#paymentTotal').val(obj['due']);
                $('.payment').html(obj['payment']);
                $('.discount').html(obj['discount']);

            });
        }
    });
});


$(document).on("click", ".approve", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                $('#approved-'+id).hide();
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('.payment').html(obj['payment']);
                $('#paymentTotal').val(obj['due']);
                $('#sales_discount').val(obj['discount']);
                $('.discount').html(obj['discount']);
                $('#due').val(obj['due']);
                $('.due').html(obj['due']);
            });
        }
    });
});



$(document).on('keyup', '#quantity , #salesPrice ', function() {

    var width = parseFloat($('#width').val());
    var height = parseFloat($('#height').val());
    var salesPrice = parseFloat($('#salesPrice').val());
    var quantity = parseInt($('#quantity').val());

    if(isNaN(width) && !jQuery.isNumeric(width)) {
        $('#subTotal').html(quantity * salesPrice);
    }else{
        $('#subQuantity').html(width * height);
        var total = (width * height) * quantity;
        $('#subTotal').html(total * salesPrice);
    }

});



$(document).on('keyup', '#businessInvoice_discountCalculation', function(e) {

    var discountType = $('#businessInvoice_discountType').val();
    var discount = parseInt($('#businessInvoice_discountCalculation').val() !="" ? $('#businessInvoice_discountCalculation').val() : 0);
    var invoice = $('#invoiceId').val();
    var total =  parseInt($('#dueAmount').val());
    if( discount >= total ){
        $('#sales_discount').val(0);
        return false;
    }
    $.ajax({
        url: Routing.generate('hotel_invoice_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType+'&invoice='+invoice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['due']);
            $('#sales_discount').val(obj['discount']);
            $('.discount').html(obj['discount']);
            $('#due').val(obj['due']);
            $('.due').html(obj['due']);
        }

    })

});

$(document).on('keyup', '#businessInvoice_received', function() {

    var payment     = parseInt($('#businessInvoice_received').val()  != '' ? $('#businessInvoice_received').val() : 0 );
    var paymentTotal =  parseInt($('#paymentTotal').val());
    var dueAmount = (paymentTotal-payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.due').html(dueAmount);
    }else{
        var balance =  payment - paymentTotal ;
        $('#balance').html('Return Tk.');
        $('.due').html(balance);
    }
});

$('form#salesForm').on('keypress', '.salesInput', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {
            case 'sales_transactionMethod':
                $('#sales_salesBy').focus();
                break;
            case 'sales_salesBy':
                $('#sales_received').focus();
                break;
            case 'sales_received':
                $('#receiveBtn').focus();
                break;
        }
        return false;
    }
});

var invoiceForm = $("#invoiceForm").validate({

    rules: {

        "firstName": {required: true},
        "lastName": {required: true},
        "mobile": {required: true},
        "email": {required: false},
        "profession": {required: false},
        "organization": {required: false},
        "address": {required: true},
        "location": {required: false},
        "postalCode": {required: false},
        "businessInvoice[discountCalculation]": {required: false},
        "businessInvoice[received]": {required: false},
        "remark": {required: false}
    },

    messages: {

        "firstName":"Enter first name",
        "lastName": "Enter last name",
        "mobile":"Enter guest mobile no",
        "address":"Enter guest address"

    },
    tooltip_options: {
        "firstName": {placement:'top',html:true},
        "lastName": {placement:'top',html:true},
        "mobile": {placement:'top',html:true}
    },

    submitHandler: function(invoiceForm) {
        $('form#invoiceForm').submit();
    }
});

var invoicePaymentForm = $("#invoicePaymentForm").validate({

    rules: {
        "received": {required: true},
        "businessInvoice[discountCalculation]": {required: false},
        "businessInvoice[cardNo]": {required: false},
        "businessInvoice[paymentMobile]": {required: false},
        "businessInvoice[transactionId]": {required: false}
    },

    messages: {
        "received":"Enter receive amount"
    },
    tooltip_options: {
        "received": {placement:'top',html:true},
    },

    submitHandler: function(invoicePaymentForm) {

        $.ajax({
            url         : $('form#invoicePaymentForm').attr( 'action' ),
            type        : $('form#invoicePaymentForm').attr( 'method' ),
            data        : new FormData($('form#invoicePaymentForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#invoiceTransaction').html(obj['invoiceTransactions']);
                $('#subTotal').html(obj['subTotal']);
                $('#netTotal').html(obj['netTotal']);
                $('#paymentTotal').val(obj['due']);
                $('#discount').html(obj['discount']);
                $('businessInvoice[received]').val('');
            }
        });
    }
});



$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$("form#invoiceForm").on('click', '.addCustomer', function() {
    $( ".customer" ).slideToggle( "slow" );
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

/*$( "#mobile" ).autocomplete({

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
});*/

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

$(document).on( "click", ".patientShow", function(e){
    $('#updatePatient').slideToggle(2000);
    $("span", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
});

$(document).on( "click", ".receivePayment", function(e){
    $("#showPayment").slideToggle(1000);
    $("span", this).toggleClass("fa-minus fa-money");
});

$(document).on('change', '.particular', function() {

    var particular = $(this).val();
    $.get( Routing.generate('hms_invoice_admission_particular_search') ,{id:particular } )
        .done(function( response ) {
            obj = JSON.parse(response);
            $('#particularId').val(obj['particularId']);
            $('#quantity').val(obj['quantity']).focus();
            $('.price').val(obj['price']);
            $('#instruction').html(obj['instruction']);
            $('#addParticular').attr("disabled", false);
        });

});


$(document).on('click', '#addPatientParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = parseInt($('#admitted_particular_quantity').val());
    var price = parseInt($('#admitted_particular_salesPrice').val());
    var url = $('#addPatientParticular').attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function (event, el){
            $.ajax({
                url: url,
                type: 'POST',
                data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
                success: function (response) {
                    obj = JSON.parse(response);
                    $('#invoiceParticulars').html(obj['invoiceParticulars']);
                    $('.total').html(obj['total']);
                    $('.msg-hidden').show();
                    $('#msg').html(obj['msg']);
                    $("#particular").select2().select2("val","");
                    $('#price').val('');
                    $('#quantity').val('1');
                    $('#addPatientParticular').attr("disabled", false);
                }
            })
        }
    });


});


$(document).on('change', '#particular', function() {

    var url = $(this).val();
    if(url == ''){
        alert('You have to add particulars from drop down and this not service item');
        return false;
    }
    $.ajax({
        url: url,
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

$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    var quantity = parseInt($('#quantity').val());
    var price = parseInt($('#price').val());
    var url = $('#addParticular').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
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
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
            $("#particular").select2().select2("val","");
            $('#price').val('');
            $('#quantity').val('1');
            $('#addParticular').attr("disabled", true);
            $('#addPatientParticular').attr("disabled", true);

        }
    })
});

var form = $("#invoiceForm").validate({

    rules: {

        "invoice[receive]": {required: true, digits: true},
        "invoice[discountCalculation]": {required: false},
        "invoice[comment]": {required: false},
    },

    messages: {
        "invoice[invoice_receive]": "Enter payment amount",
    },
    tooltip_options: {
        "invoice[invoice_receive]": {placement: 'top', html: true},
    },
    submitHandler: function (form) {
        $.ajax({
            url         : $('form#invoiceForm').attr( 'action' ),
            type        : $('form#invoiceForm').attr( 'method' ),
            data        : new FormData($('form#invoiceForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                $('form#invoiceForm')[0].reset();
                obj = JSON.parse(response);
                $('.subTotal').html(obj['subTotal']);
                $('.total').html(obj['total']);
                $('.payment').html(obj['payment']);
                $('.due').html(obj['due']);
                $('.discount').html(obj['discount']);
                $('#invoiceTransaction').html(obj['invoiceTransaction']);
            }
        });

    }
});

$(document).on('change', '#discountType , #invoice_discountCalculation', function() {

    var discount = parseInt($('#invoice_discountCalculation').val());
    var invoice = parseInt($('#invoiceId').val());
    var discountType = $('#discountType').val();
    if(discount === "NaN"){
        return false;
    }
    $.ajax({
        url: Routing.generate('hms_invoice_admission_discount'),
        type: 'POST',
        data:'discount=' + discount+'&invoice='+ invoice+'&discountType='+discountType,
        success: function(response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.total').html(obj['total']);
            $('.payment').html(obj['payment']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.discount').html(obj['discount']);
        }

    })
});

$(document).on("click", ".removeDiscount", function() {
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {location.reload();});
        }
    });
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

$(document).on("click", ".transactionDelete", function(event) {
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(el) {
            $.get(url, function( data ) {
                obj = JSON.parse(data);
                $('#invoiceTransaction').html(obj['invoiceTransaction']);
                $('.payment').html(obj['payment']);
            });
        }
    });
});

$(document).on("click", ".paymentConfirm", function(event) {
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(el) {
            $.get(url, function( data ) {
                obj = JSON.parse(data);
                $('.subTotal').html(obj['subTotal']);
                $('.total').html(obj['total']);
                $('.discount').html(obj['discount']);
                $('.payment').html(obj['payment']);
                $('.due').html(obj['due']);
                $('#invoiceTransaction').html(obj['invoiceTransaction']);
            });
        }
    });
});


$(document).on('click', '#addPayment', function() {

    var payment = $('#payment').val();
    var discount = $('#discount').val();
    var process = $('#process').val();
    var url = $('#addPayment').attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function (event, el) {
            $.ajax({
                url: url,
                type: 'POST',
                data: 'payment=' + payment + '&discount=' + discount + '&process=' + process,
                success: function (response){
                    location.reload();
                }
            })
        }
    });

});

$(document).on("click", "#diagnosticReceiveBtn", function() {

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('#invoiceForm').submit();
        }
    });

});

$(document).on("click", "#receiveBtn", function() {

    $('#invoice_cabin, #invoice_customer_alternativeContactPerson, #invoice_customer_alternativeRelation, #invoice_customer_alternativeContactMobile').each(function() {
        if ($(this).val() == '') {
            $('#invoice_customer_alternativeContactPerson').addClass('input-error').focus;
            $('#invoice_customer_alternativeRelation').addClass('input-error').focus;
            $('#invoice_customer_alternativeContactMobile').addClass('input-error').focus;
            $('#invoice_cabin').addClass('input-error').focus;
            $('#invoice_disease').addClass('input-error').focus;
            $('#updatePatient').show();
            return false;

        }else{

            $('#confirm-content').confirmModal({
                topOffset: 0,
                top: '25%',
                onOkBut: function(event, el) {
                    $('#invoiceForm').submit();
                }
            });
        }

    });
});


$(document).on('keyup', '#invoice_payment', function() {

    var payment  = parseInt($('#invoice_payment').val()  != '' ? $('#invoice_payment').val() : 0 );
    var due  = parseInt($('#due').val()  != '' ? $('#due').val() : 0 );
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.due').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.due').html(balance);
    }
});

$('.particular-info').on('keypress', 'input', function (e) {
    if (e.which == 13) {
        e.preventDefault();
        switch (this.id) {

            case 'quantity':
                $('#price').focus();
                break;

            case 'price':
                $('#addParticular').trigger('click');
                $('#particular').focus();
                break;
        }
    }
});

$('form.horizontal-form').on('keypress', 'input', function (e) {

    if (e.which == 13) {
        e.preventDefault();

        switch (this.id) {
            case 'invoice_discount':
                $('#invoice_payment').focus();
                break;

            case 'paymentAmount':
                $('#receiveBtn').focus();
                break;
        }
    }
});

$(document).on('change', '.invoiceCabinBooking', function(e) {

    var url ="{{ path('hms_invoice_admission_newpatint_cabin') }}";
    var invoice = $('#invoiceId').val();
    var cabin = $(this).val();
    if(cabin === ''){
        return false;
    }
    $.post( url,{ invoice:invoice , cabin:cabin } )
        .done(function( data ) {
            if(data === 'invalid'){
                $("#invoice_cabin").select2().select2("val","");
                $('#cabinInvalid').notifyModal({
                    duration : 5000,
                    placement : 'center',
                    overlay : true,
                    type : 'notify',
                    icon : false,
                });
            }
        });

});



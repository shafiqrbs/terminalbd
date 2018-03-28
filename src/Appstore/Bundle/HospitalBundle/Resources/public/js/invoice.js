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


$(".addReferred").click(function(){

    if ($(this).attr("id") == 'show') {

        $('#hide').addClass('btn-show');
        $( ".referred-add" ).slideDown( "slow" );
        $( ".referred-search" ).slideUp( "slow" );

    }else {
        $( ".referred-add" ).slideUp( "slow" );
        $( ".referred-search" ).slideDown( "slow" );

    }

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

$(document).on( "click", ".patientShow", function(e){
    $('#updatePatient').slideToggle(2000);
    $("span", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
});

$(document).on( "click", ".receivePayment", function(e){
    $("#showPayment").slideToggle(1000);
    $("span", this).toggleClass("fa-minus fa-money");
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
            $('#invoiceTransaction').html(obj['invoiceTransaction']);
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


$(document).on('change', '.discount', function() {

    var discount = parseInt($('.discount').val());
    var invoice = parseInt($('#invoiceId').val());
    var payment  = parseInt($('#appstore_bundle_hospitalbundle_invoice_payment').val()  != '' ? $('#appstore_bundle_hospitalbundle_invoice_payment').val() : 0 );

    $.ajax({
        url: Routing.generate('hms_invoice_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&invoice='+ invoice,
        success: function(response) {

            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']-payment);
            $('#due').val(obj['due']);
            $('.discountAmount').html(obj['discount']);
            $('.discount').val(obj['discount']).attr("placeholder", obj['discount']);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#invoiceTransaction').html(obj['invoiceTransaction']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
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
            $('.discount').val(obj['discount']).attr("placeholder", obj['discount']);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('#invoiceTransaction').html(obj['invoiceTransaction']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
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

    $('#appstore_bundle_hospitalbundle_invoice_cabin, #appstore_bundle_hospitalbundle_invoice_customer_alternativeContactPerson, #appstore_bundle_hospitalbundle_invoice_customer_alternativeRelation, #appstore_bundle_hospitalbundle_invoice_customer_alternativeContactMobile').each(function() {
        if ($(this).val() == '') {
            $('#appstore_bundle_hospitalbundle_invoice_customer_alternativeContactPerson').addClass('input-error').focus;
            $('#appstore_bundle_hospitalbundle_invoice_customer_alternativeRelation').addClass('input-error').focus;
            $('#appstore_bundle_hospitalbundle_invoice_customer_alternativeContactMobile').addClass('input-error').focus;
            $('#appstore_bundle_hospitalbundle_invoice_cabin').addClass('input-error').focus;
            $('#appstore_bundle_hospitalbundle_invoice_disease').addClass('input-error').focus;
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


$(document).on('change', '#appstore_bundle_hospitalbundle_invoice_payment', function() {

    var payment  = parseInt($('#appstore_bundle_hospitalbundle_invoice_payment').val()  != '' ? $('#appstore_bundle_hospitalbundle_invoice_payment').val() : 0 );
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

$('form#invoiceForm').on('keypress', '.admissionInput', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);

        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {
            case 'id="appstore_bundle_hospitalbundle_invoice_discount"':
                $('#appstore_bundle_hospitalbundle_transactionMethod').focus();
                break;
            case 'appstore_bundle_hospitalbundle_invoice_transactionMethod':
                $('#appstore_bundle_hospitalbundle_invoice_payment').focus();
                break;
            case 'appstore_bundle_hospitalbundle_invoice_payment':
                $('#receiveBtn').focus();
                break;
        }
        return false;
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
            case 'appstore_bundle_hospitalbundle_invoice_discount':
                $('#appstore_bundle_hospitalbundle_invoice_payment').focus();
                break;

            case 'paymentAmount':
                $('#receiveBtn').focus();
                break;
        }
    }
});

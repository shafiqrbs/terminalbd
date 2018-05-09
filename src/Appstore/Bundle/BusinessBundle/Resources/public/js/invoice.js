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

$(document).on("click", ".sms-confirm", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url);
        }
    });
});


$('.checkboxes').checkradios({
    checkbox: {
        iconClass:'fa fa-window-close'
    }

});
$(document).on("click", "#patientOverview", function() {
    var url = $(this).attr('data-url');
    $.ajax({
        url :url,
        beforeSend: function(){
            $('.loader-double').fadeIn(1000).addClass('is-active');
        },
        complete: function(){
            $('.loader-double').fadeIn(1000).removeClass('is-active');
        },
        success:  function (data) {
            $("#patientLoad").html(data);
        }
    });
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

$(document).on( "click", ".patientShow", function(e){
    $('#updatePatient').slideToggle(2000);
    $("span", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
});

$(document).on( "click", ".receivePayment", function(e){
    $("#showPayment").slideToggle(1000);
    $("span", this).toggleClass("fa-minus fa-money");
});

$(document).on( "change", "#invoiceParticular", function(e){

    var price = $(this).val();
    $('#appstore_bundle_dmsbundle_invoice_payment').val(price);
});


$(document).on('click', '#addParticular', function() {


    var particular = $('#particular').val();
    var price = $('#price').val();
    var quantity = $('#quantity').val();
    var unit = $('#unit').val();
    var url = $(this).attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particular='+particular+'&price='+price+'&quantity='+quantity+'&unit='+unit,
        success: function (response) {
            obj = JSON.parse(response);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('.due').html(obj['due']);
            $('.payment').html(obj['payment']);
            $('.discount').html(obj['discount']);
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
            $.get(url, function( response ) {
                obj = JSON.parse(response);
                $('#remove-'+id).remove();
                $('.subTotal').html(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('.due').html(obj['due']);
                $('.payment').html(obj['payment']);
                $('.discount').html(obj['discount']);

            });
        }
    });
});

$(document).on("click", ".deleteMedicine", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $('#medicine-'+id).hide();
            });
        }
    });
});

$(document).on("click", ".approve", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#treatment-approved-'+id).hide();
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                if(data == 'success'){
                    location.reload();
                }
            });
        }
    });
});

$(document).on('click', '#addAccessories', function() {

    var accessories = $('#accessories').val();
    if (accessories == '') {
        $('#accessories').focus();
        $('#accessories').addClass('input-error');
        alert('Please select accessories name');
        return false;
    }
    var quantity = parseInt($('#quantity').val());
    var url = $(this).attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'accessories='+accessories+'&quantity='+quantity,
        success: function (response) {
            $("#accessories").select2().select2("val","");
            $('#quantity').val('1');
            $('#invoiceAccessories').html(response);
            $(".editable").editable();

        }
    })
});

$(document).on("click", ".deleteAccessories", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $('#accessories-'+id).hide();
            });
        }
    });
});

$(document).on("click", ".approveAccessories", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $('#approved-'+id).hide();
            });
        }
    });
});

$(document).on('click', '.appointmentSchedule', function() {

    var url = $(this).attr('data-url');
    var dataTitle = $(this).attr('data-title');
    $('.dialogModal_header').html(dataTitle);
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: url,
                type: 'POST',
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on('click', '#searchAppointment', function() {

    var url = $('form#appointmentForm').attr('action');
    $.ajax({
        url: url,
        type: 'POST',
        data:new FormData($('#appointmentForm')[0]),
        processData: false,
        contentType: false,
        success: function (response) {
            $('#appointmentSchedule').html(response);
            setTimeout(datePickerReload(),1000);
            EditableInit();
        }
    })

});

var form = $("#invoiceForm").validate({

    rules: {

        "appstore_bundle_dmsbundle_invoice[customer][name]": {required: true},
        "appstore_bundle_dmsbundle_invoice[customer][mobile]": {required: true},
        "appstore_bundle_dmsbundle_invoice[customer][age]": {required: true},
        "appstore_bundle_dmsbundle_invoice[customer][address]": {required: false},
    },

    messages: {

        "appstore_bundle_dmsbundle_invoice[customer][name]":"Enter patient name",
        "appstore_bundle_dmsbundle_invoice[customer][mobile]":"Enter patient mobile no",
        "appstore_bundle_dmsbundle_invoice[customer][age]": "Enter patient age",
    },
    tooltip_options: {
        "appstore_bundle_dmsbundle_invoice[customer][name]": {placement:'top',html:true},
        "appstore_bundle_dmsbundle_invoice[customer][mobile]": {placement:'top',html:true},
        "appstore_bundle_dmsbundle_invoice[customer][age]": {placement:'top',html:true},
    },

    submitHandler: function(form) {

        $.ajax({

            url         : $('form#invoiceForm').attr( 'action' ),
            type        : $('form#invoiceForm').attr( 'method' ),
            data        : new FormData($('form#invoiceForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                location.reload();
            }
        });
    }
});

$('#appstore_bundle_dmsbundle_invoice_customer_name').on('click', function(){
    form.element($(this));
});
$('#appstore_bundle_dmsbundle_invoice_customer_mobile').on('click', function(){
    form.element($(this));
});
$('#appstore_bundle_dmsbundle_invoice_customer_age').on('click', function(){
    form.element($(this));
});


$(document).on("click", ".saveButton", function() {

    var formData = new FormData($('form#invoiceForm')[0]); // Create an arbitrary FormData instance
    var url = $('form#invoiceForm').attr('action'); // Create an arbitrary FormData instance
    $.ajax({
        url:url ,
        type: 'POST',
        processData: false,
        contentType: false,
        data:formData,
        success: function(response){

        }
    });

});

$(document).on("change", ".invoiceProcess", function() {

    var formData = new FormData($('form#invoiceForm')[0]); // Create an arbitrary FormData instance
    var url = $('form#invoiceForm').attr('action'); // Create an arbitrary FormData instance
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.ajax(url,{
                processData: false,
                contentType: false,
                type: 'POST',
                data: formData,
                success: function (response){}
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

/*
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
*/

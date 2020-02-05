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

$( ".autoProcedure" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('dms_invoice_procedure_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 1,
    select: function( event, ui ) {
    }
});

$( ".investigation" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('dms_invoice_investigation_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 1,
    select: function( event, ui ) {
    }
});

$( ".autoMetaValue" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('dms_invoice_auto_particular_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 1,
    select: function( event, ui ) {
    }
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

$(document).on('click', '.addProcedure', function() {

    var dataTab    = $(this).attr('data-tab');
    var procedure =  $('#'+dataTab).find('#procedure').val();
    if(procedure == ''){
        alert('You have to add procedure text');
        $('#'+dataTab).find('#procedure').focus();
        return false;
    }
    var checked = []
    $('#'+dataTab).find("input[name='teethNo[]']:checked").each(function ()
    {
       checked.push(parseInt($(this).val()));
    });

    var url     = $(this).attr('data-url');
    var showDiv    = $(this).attr('data-id');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'procedure='+procedure+'&teethNo='+checked,
        success: function (response) {
            $('#'+dataTab).find('#procedure-'+showDiv).html(response);
            $('#'+dataTab).find('#procedure').val('');
            $('#'+dataTab).find('.checkradios-checkbox').prop('checked', false);
            $('#'+dataTab).find('.checked').removeClass('fa fa-window-close');
           }
    });
});

$(document).on('click', '.addInvestigation', function() {

    var dataTab    = $(this).attr('data-tab');
    var procedure =  $('#'+dataTab).find('#investigation').val();
    if(procedure == ''){
        alert('You have to add procedure text');
        $('#'+dataTab).find('#investigation').focus();
        return false;
    }

    var file =  $('#'+dataTab).find('#file').val();
    if(file == ''){
        alert('You have to add file');
        $('#'+dataTab).find('#file').focus();
        return false;
    }

    var url = $('form#invoiceForm').attr('action');
    var showDiv    = $(this).attr('data-id');
    var formData = new FormData($('form#invoiceForm')[0]);
    $.ajax({
        url:url ,
        type: 'POST',
        beforeSend: function() {
            $('.addInvestigation').show().addClass('btn-ajax-loading').fadeIn(3000);
            $('.btn-ajax-loading').attr("disabled", true);
        },
        processData: false,
        contentType: false,
        data:formData,
        success: function(response){
            $('#'+dataTab).find('#procedure-'+showDiv).html(response);
            $('#'+dataTab).find('#investigation').val('');
            $('#'+dataTab).find('#file').val('');
            $('.btn-ajax-loading').attr("disabled", false);
            $('.addInvestigation').removeClass('btn-ajax-loading');
        }
    });
});

$(document).on("click", ".particularDelete", function() {
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    var dataTab    = $(this).attr('data-tab');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $('#procedure-'+dataTab).find('tr#remove-'+id).remove();
            });
        }
    });
});

$(document).on('click', '#addPrescriptionParticular', function() {


    var medicine = $('#medicine').val();
    var medicineId = $('#medicineId').val();
    var generic = $('#generic').val();
    var medicineQuantity = parseInt($('#medicineQuantity').val());
    var medicineDose = $('#medicineDose').val();
    var medicineDoseTime = $('#medicineDoseTime').val();
    var medicineDuration = $('#medicineDuration').val();
    var medicineDurationType = $('#medicineDurationType').val();
    var url = $('#addPrescriptionParticular').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'medicine='+medicine+'&medicineId='+medicineId+'&medicineQuantity='+medicineQuantity+'&medicineDose='+medicineDose+'&medicineDoseTime='+medicineDoseTime+'&medicineDuration='+medicineDuration+'&medicineDurationType='+medicineDurationType,
        success: function (response) {
            $('#invoiceMedicine').html(response);
            $('#medicine').val('');
            $('#generic').val('');
            $('#medicineId').val('');
        }
    })
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

$(document).on('click', '.prescription', function() {

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
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
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
            $('#price').val(obj['price']);
            $('#instruction').html(obj['instruction']);
            $('#addParticular').attr("disabled", false);
        }
    })
});

$(document).on('change', '#appointmentDate', function() {

    var appointmentDate = $('#appointmentDate').val();
    if(appointmentDate == ''){
        return false;
    }
    var assignDoctor = $('#appstore_bundle_dmsbundle_invoice_assignDoctor').val();
    $.get(Routing.generate('dms_invoice_appointment_schedule_time',{assignDoctor:assignDoctor,appointmentDate:appointmentDate}),
            function(data){
               $('#appointmentTime').html(data);
            }
        );
});

$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    if (particularId == '') {

        $('#particularId').addClass('input-error');
        $('#particularId').focus();
        alert('Please select treatment particular');
        return false;
    }

    var appointmentDate = $('#appointmentDate').val();
    if (appointmentDate == '') {

        $('#appointmentDate').addClass('input-error');
        $('#appointmentDate').focus();
        alert('Please select appointment date');
        return false;
    }

    var price = $('#price').val();
    var appointmentTime = $('#appointmentTime').val();

    var url = $('#addParticular').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&price='+price+'&appointmentDate='+appointmentDate+'&appointmentTime='+appointmentTime,
        success: function (response) {
            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('.due').html(obj['due']);
            $('.payment').html(obj['payment']);
            $('.discount').html(obj['discount']);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $("#particular").select2().select2("val","");
            $('#price').val('');
            $('#addParticular').attr("disabled", true);
            $('#addPatientParticular').attr("disabled", true);
            $(".editable").editable();

        }
    })
});

$(document).on("click", ".treatmentDelete", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                obj = JSON.parse(response);
                $('#remove-'+id).hide();
                $('.subTotal').html(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('.due').html(obj['due']);
                $('.payment').html(obj['payment']);
                $('.discount').html(obj['discount']);
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

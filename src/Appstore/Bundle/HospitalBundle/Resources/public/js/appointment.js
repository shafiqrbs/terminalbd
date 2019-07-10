$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$(document).on('click', '.addAppointment', function() {

    $('.dialogModal_header').html('Patient Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('hms_doctor_visit_new'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    formSubmit();
                    $('.select2').select2();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

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

$(document).on('change', '#appointment_invoice_assignDoctor', function() {
    var id = $(this).val();
    $.get(Routing.generate('hms_doctor_visit_amount',{id:id}), function( data ){
        $('#appointment_invoice_assignDoctor').val(data);
    });
});


function formSubmit() {

    $('#appointment_invoice_customer_name').focus().keypress(function () {
        $('#appointment_invoice_customer_name').css('textTransform', 'capitalize');
    });
    $('form#invoicePatientForm').on('keypress', 'input,select,textarea', function (e) {

        if (e.which == 13) {

            e.preventDefault();
            switch (this.id) {

                case 'appointment_invoice_customer_name':
                    $('#appointment_invoice_customer_mobile').focus();
                    break;

                case 'appointment_invoice_customer_mobile':
                    $('#appointment_invoice_customer_age').focus();
                    break;

                case 'appointment_invoice_customer_age':
                    $('#appointment_invoice_customer_height').focus();
                    break;

                case 'appointment_invoice_customer_height':
                    $('#appointment_invoice_customer_weight').focus();
                    break;

                case 'appointment_invoice_customer_weight':
                    $('#appointment_invoice_customer_bloodPressure').focus();
                    break;

                case 'appointment_invoice_customer_bloodPressure':
                    $("#appointment_invoice_assignDoctor").select2().select2("val","").select2('open');
                    break;

                case 'appointment_invoice_assignDoctor':
                    $('#appointment_invoice_customer_payment').focus();
                    break;


                case 'appointment_invoice_customer_payment':
                    $('#saveDiagnosticButton').trigger('click');
                    break;
            }
        }
    });

    $(document).on('keyup', '.payment', function() {

        var payment  = parseInt($('#appointment_invoice_payment').val()  != '' ? $('#appointment_invoice_payment').val() : 0 );
        var due  = parseInt($('#initialDue').val()  != '' ? $('#initialDue').val() : 0 );
        var dueAmount = (due - payment);
        if(dueAmount > 0){
            $('#balance').html('Due Tk.');
            $('#dueable').html(dueAmount);
        }else{
            var balance =  payment - due ;
            $('#balance').html('Return Tk.');
            $('#dueable').html(balance);
        }

    });

    var form = $("#invoicePatientForm").validate({

        rules: {

            "appointment_invoice[customer][name]": {required: true},
            "appointment_invoice[customer][mobile]": {required: true, digits: true},
            "appointment_invoice[customer][age]": {required: true, digits: true},
            "appointment_invoice[customer][weight]": {required: false, digits: true},
            "appointment_invoice[customer][height]": {required: false, digits: true},
            "appointment_invoice[customer][bloodPressure]": {required: false},
            "appointment_invoice[payment]": {required: false, digits: true},
            "appointment_invoice[comment]": {required: false},
        },

        messages: {
            "appointment_invoice[customer][name]": "Enter patient name",
            "appointment_invoice[customer][mobile]": "Enter patient mobile no",
            "appointment_invoice[customer][age]": "Enter patient age",
            "appointment_invoice[payment]": "Enter payment amount, if payment are due input zero",
        },
        tooltip_options: {
            "appointment_invoice[customer][name]": {placement: 'top', html: true},
            "appointment_invoice[customer][mobile]": {placement: 'top', html: true},
            "appointment_invoice[customer][age]": {placement: 'top', html: true},
            "appointment_invoice[payment]": {placement: 'top', html: true},
        },
        submitHandler: function (form) {
            $.ajax({
                url         : $('form#invoicePatientForm').attr( 'action' ),
                type        : $('form#invoicePatientForm').attr( 'method' ),
                data        : new FormData($('form#invoicePatientForm')[0]),
                processData : false,
                contentType : false,
                beforeSend: function() {
                    $('#saveDiagnosticButton').html("Please Wait...").attr('disabled', 'disabled');
                },
                success: function(response){
                    $('form#invoicePatientForm')[0].reset();
                    $('#saveDiagnosticButton').html("<i class='icon-save'></i> Save").attr("disabled", false);
                    $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
                    $('#appointment_invoice_discount').val(0);
                    $('#invoiceParticulars').hide();
                    $("#appointment_invoice_assignDoctor").select2().select2("val","");
                    $("#referredId").select2().select2("val","");
                    window.open('/hms/invoice/'+response+'/appointment-print', '_blank');
                }
            });

        }
    });
}




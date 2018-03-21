$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});


$(document).on('click', '.addPatient', function() {

    $('.dialogModal_header').html('Patient Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('dms_invoice_new'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    formSubmit();
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



function formSubmit() {

    $("#invoicePatientForm").validate({

        rules: {

            "appstore_bundle_dmsinvoice[customer][name]": {required: true},
            "appstore_bundle_dmsinvoice[customer][mobile]": {required: true},
            "appstore_bundle_dmsinvoice[customer][age]": {required: true},
            "appstore_bundle_dmsinvoice[customer][address]": {required: false},
            "appstore_bundle_dmsinvoice[customer][weight]": {required: false},
        },

        messages: {

            "appstore_bundle_dmsinvoice[customer][name]": "Enter patient name",
            "appstore_bundle_dmsinvoice[customer][mobile]": "Enter patient mobile no",
            "appstore_bundle_dmsinvoice[customer][age]": "Enter patient age",
        },
        tooltip_options: {
            "appstore_bundle_dmsinvoice[customer][name]": {placement: 'top', html: true},
            "appstore_bundle_dmsinvoice[customer][mobile]": {placement: 'top', html: true},
            "appstore_bundle_dmsinvoice[customer][age]": {placement: 'top', html: true},
        },

        submitHandler: function (form) {

            $.ajax({
                url         : $('form#invoicePatientForm').attr( 'action' ),
                type        : $('form#invoicePatientForm').attr( 'method' ),
                data        : new FormData($('form#invoicePatientForm')[0]),
                processData : false,
                contentType : false,
                beforeSend: function() {
                    $('#saveNewPatientButton').show().addClass('btn-ajax-loading').fadeIn(3000);
                    $('.btn-ajax-loading').attr("disabled", true);
                },
                success: function(response){
                    $('.btn-ajax-loading').attr("disabled", false);
                    $('#saveNewPatientButton').removeClass('btn-ajax-loading').fadeOut(3000);
                    window.location.href = '/dms/invoice/'+response+'/edit';
                }
            });
        }
    });
}


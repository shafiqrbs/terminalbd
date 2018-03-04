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
                url: Routing.generate('dps_invoice_new'),
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

function formSubmit() {
    $("#invoicePatientForm").validate({

        rules: {

            "appstore_bundle_dpsinvoice[customer][name]": {required: true},
            "appstore_bundle_dpsinvoice[customer][mobile]": {required: true},
            "appstore_bundle_dpsinvoice[customer][age]": {required: true},
            "appstore_bundle_dpsinvoice[customer][address]": {required: false},
        },

        messages: {

            "appstore_bundle_dpsinvoice[customer][name]": "Enter patient name",
            "appstore_bundle_dpsinvoice[customer][mobile]": "Enter patient mobile no",
            "appstore_bundle_dpsinvoice[customer][age]": "Enter patient age",
        },
        tooltip_options: {
            "appstore_bundle_dpsinvoice[customer][name]": {placement: 'top', html: true},
            "appstore_bundle_dpsinvoice[customer][mobile]": {placement: 'top', html: true},
            "appstore_bundle_dpsinvoice[customer][age]": {placement: 'top', html: true},
        },

        submitHandler: function (form) {
            $('#savePatientButton').attr("disabled", true);
            $("#invoicePatientForm").submit();
        }
    });
}


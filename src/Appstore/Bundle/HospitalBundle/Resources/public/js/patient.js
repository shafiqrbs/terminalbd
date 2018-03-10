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
                url: Routing.generate('hms_invoice_temporary_new'),
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

function formSubmit() {

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

    $(document).on('click', '#temporaryParticular', function() {

        var particularId = $('#particularId').val();
        var quantity = parseInt($('#quantity').val());
        var price = parseInt($('#price').val());
        var url = $('#temporaryParticular').attr('data-url');
        $.ajax({
            url: url,
            type: 'POST',
            data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
            success: function (response) {
                obj = JSON.parse(response);
                $('.subTotal').html(obj['subTotal']);
                $('#initialDue').val(obj['subTotal']);
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

    $(document).on('change', '.initialDiscount', function() {

        var discountType = $('#discountType').val();
        var discount = parseInt($('#appstore_bundle_hospitalbundle_invoice_discount').val());
        $.ajax({
            url: Routing.generate('hms_invoice_temporary_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType,
            success: function(response) {
                obj = JSON.parse(response);
                $('.subTotal').html(obj['subTotal']);
                $('.initialGrandTotal').html(obj['initialGrandTotal']);
                $('.initialDiscount').html(obj['initialDiscount']);
                $('#initialDiscount').val(obj['initialDiscount']);

            }

        })
    });


    $(document).on("click", ".initialParticularDelete", function() {

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

    $(document).on('change', '.payment', function() {

        var payment  = parseInt($('#appstore_bundle_hospitalbundle_invoice_payment').val()  != '' ? $('#appstore_bundle_hospitalbundle_invoice_payment').val() : 0 );
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


    $("#invoicePatientForm").validate({

        rules: {

            "appstore_bundle_hospitalbundle_invoice[customer][name]": {required: true},
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": {required: true},
            "appstore_bundle_hospitalbundle_invoice[customer][age]": {required: true},
            "appstore_bundle_hospitalbundle_invoice[discount]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[payment]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[customer][address]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[customer][location]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[referredDoctor][name]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[referredDoctor][address]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[comment]": {required: false},
        },

        messages: {

            "appstore_bundle_hospitalbundle_invoice[customer][name]": "Enter patient name",
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": "Enter patient mobile no",
            "appstore_bundle_hospitalbundle_invoice[customer][age]": "Enter patient age",
        },
        tooltip_options: {
            "appstore_bundle_hospitalbundle_invoice[customer][name]": {placement: 'top', html: true},
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": {placement: 'top', html: true},
            "appstore_bundle_hospitalbundle_invoice[customer][age]": {placement: 'top', html: true},
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
                    window.location.href = '/hms/invoice/'+response+'/invoice-confirm';
                }
            });
        }
    });
}




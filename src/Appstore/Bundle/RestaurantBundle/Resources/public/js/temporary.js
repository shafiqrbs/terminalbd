$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$(document).on('click', '.addInvoice', function() {

    $('.dialogModal_header').html('Order Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('restaurant_temporary_new'),
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

$(document).on("click", ".saveButtonxxx", function() {

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

    $("form#invoicePatientForm").on('click', '.addCustomer', function() {
        $( ".customer" ).slideToggle( "slow" );
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

    $(document).on('click', '#temporaryParticular', function() {

        var particularId = $('#particularId').val();
        var quantity = parseInt($('#quantity').val());
        var price = parseInt($('#price').val());
        var url = $('#temporaryParticular').attr('data-url');
        if(particularId == ''){
            $("#particular").select2('open');
            return false;
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
            success: function (response) {
                obj = JSON.parse(response);
                $('#invoiceParticulars').show();
                $('.subTotal').html(obj['subTotal']);
                $('.initialGrandTotal').html(obj['initialGrandTotal']);
                $('.initialVat').html(obj['initialVat']);
                $('.vat').val(obj['initialVat']);
                $('.payment').val(obj['initialGrandTotal']);
                $('#initialDue').val(obj['initialGrandTotal']);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $("#particular").select2().select2("val","").select2('open');
                $('#price').val('');
                $('#quantity').val('1');
                $('#particularId').val('');
                $('#addParticular').attr("disabled", true);
                if(obj['initialGrandTotal'] > 0 ){
                    $('.receiveBtn').attr("disabled", false);
                }else{
                    $('.receiveBtn').attr("disabled", true);
                }
            }
        })
    });

    $(document).on('change', '#restaurant_invoice_discountType , #restaurant_invoice_discountCalculation', function() {

        var discountType = $('#restaurant_invoice_discountType').val();
        var discount = parseInt($('#restaurant_invoice_discountCalculation').val());
        if(discount === "NaN"){
            return false;
        }
        $.ajax({
            url: Routing.generate('restaurant_temporary_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType,
            success: function(response) {
                obj = JSON.parse(response);
                $('.subTotal').html(obj['subTotal']);
                $('.initialGrandTotal').html(obj['initialGrandTotal']);
                $('.initialVat').html(obj['initialVat']);
                $('.vat').val(obj['initialVat']);
                $('.payment').val(obj['initialGrandTotal']);
                $('.initialDiscount').html(obj['initialDiscount']);
                 $('#restaurant_invoice_discount').val(obj['initialDiscount']);
                $('#initialDue').val(obj['initialGrandTotal']);
            }
        })
    });

    $(document).on("click", ".initialParticularDelete , .particularDelete", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    obj = JSON.parse(data);
                    $('.subTotal').html(obj['subTotal']);
                    $('.initialGrandTotal').html(obj['initialGrandTotal']);
                    $('#initialDue').val(obj['initialGrandTotal']);
                    $('.initialVat').html(obj['initialVat']);
                    $('.vat').val(obj['initialVat']);
                    $('.payment').val(obj['initialGrandTotal']);
                    $('.due').html(obj['due']);
                    $('.discountAmount').html(obj['discount']);
                    $('#restaurant_invoice_discount').val(obj['discount']);
                    $('.total'+id).html(obj['total']);
                    $('#remove-'+id).hide();
                    if(obj['initialGrandTotal'] > 0 ){
                        $('.receiveBtn').attr("disabled", false);
                    }else{
                        $('.receiveBtn').attr("disabled", true);
                    }
                });
            }
        });
    });

    $(document).on('keyup', '.payment', function() {

        var payment  = parseInt($('#restaurant_invoice_payment').val()  != '' ? $('#restaurant_invoice_payment').val() : 0 );
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
    $(document).on('click', '#saveButton', function() {

        $.ajax({
        url         : $('form#invoiceForm').attr( 'action' ),
        type        : 'POST',
        data        : new FormData($('form#invoiceForm')[0]),
        processData : false,
        contentType : false,
        beforeSend  : function() {
            $('#saveButton').html("Please Wait...").attr('disabled', 'disabled');
        },
        success     : function(response){
            $('form#invoiceForm')[0].reset();
            $('#saveButton').html("<i class='icon-save'></i> Save").attr('disabled','disabled');
            $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
            $('#invoiceParticulars').hide();
        }
    });
    });

    $(document).on('click', '#posButton', function() {
        $.ajax({
            url         : $('form#invoiceForm').attr( 'action' ),
            type        : $('form#invoiceForm').attr( 'method' ),
            data        : new FormData($('form#invoiceForm')[0]),
            processData : false,
            contentType : false,
            beforeSend  : function() {
                $('#savePosButton').html("Please Wait...").attr('disabled', 'disabled');
            },
            success     : function(response){
                $('form#invoiceForm')[0].reset();
                $('#savePosButton').html("<i class='icon-save'></i> Save").attr('disabled','disabled');
                $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
                $('#invoiceParticulars').hide();
             //   jsPostPrint(response);

            }
        });
    });

    $(document).on( "click", ".btn-number", function(e){

        e.preventDefault();
        url = $(this).attr('data-url');
        var productId = $(this).attr('data-text');
        var price = $(this).attr('data-title');
        fieldId = $(this).attr('data-id');
        fieldName = $(this).attr('data-field');
        type      = $(this).attr('data-type');
        var input = $('#quantity-'+fieldId);
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if(type == 'minus') {
                if(currentVal > input.attr('min')) {
                    var existVal = (currentVal - 1);
                    input.val(existVal).change();
                }
                if(parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if(type == 'plus') {

                if(currentVal < input.attr('max')) {
                    var existVal = (currentVal + 1);
                    input.val(existVal).change();
                }
                if(parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(1);
        }
    });

    function jsPostPrint(data) {

        if(typeof EasyPOSPrinter == 'undefined') {
            alert("Printer library not found");
            return;
        }
        EasyPOSPrinter.raw(data);
        EasyPOSPrinter.cut();
        EasyPOSPrinter.print(function(r, x){
            console.log(r)
        });
    }
}






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

$(document).on('keyup', '#businessInvoice_discountCalculation', function() {

    var discountType = $('#businessInvoice_discountType').val();
    var discount = parseInt($('#businessInvoice_discountCalculation').val());
    var invoice = $('#invoiceId').val();
    var total =  parseInt($('#dueAmount').val());
    if( discount >= total ){
        $('#sales_discount').val(0);
        return false;
    }
    $.ajax({
        url: Routing.generate('business_invoice_discount_update'),
        type: 'POST',
        data:'discount=' + discount+'&discountType='+discountType+'&invoice='+invoice,
        success: function(response) {
            obj = JSON.parse(response);
            $('#subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#paymentTotal').val(obj['netTotal']);
            $('#sales_discount').val(obj['discount']);
            $('.discount').html(obj['discount']);
            $('#due').val(obj['due']);
            $('.dueAmount').html(obj['due']);
        }

    })

});

$(document).on('keyup', '#businessInvoice_received', function() {

    var payment     = parseInt($('#businessInvoice_received').val()  != '' ? $('#businessInvoice_received').val() : 0 );
    var due =  parseInt($('#due').val());
    var dueAmount = (due-payment);
    if(dueAmount > 0){
        $('#balance').html('Due Tk.');
        $('.dueAmount').html(dueAmount);
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return Tk.');
        $('.dueAmount').html(balance);
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

$(document).on("click", "#receiveBtn", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('form#invoiceForm').submit();
        }
    });
});
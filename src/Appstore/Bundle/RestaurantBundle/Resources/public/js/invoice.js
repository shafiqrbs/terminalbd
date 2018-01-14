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

$(document).on('click', '.addCart', function() {

    var id = $(this).attr('data-id');
    var particularId = $(this).attr('data-id');
    var quantity = parseInt($('#quantity-'+id).val());
    var price = parseInt($('#price-'+id).val());
    var url = $(this).attr('data-url');
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

$(document).on("change", "#invoiceForm", function() {

    var url = $(this).attr("action");
    var payment  = parseInt($('#appstore_bundle_restaurant_invoice_payment').val()  != '' ? $('#appstore_bundle_hospitalbundle_invoice_payment').val() : 0 );

    $.ajax({
        url: url,
        type: 'POST',
        data: new FormData($('form')[0]),
        processData: false,
        contentType: false,
        success: function (response) {

            obj = JSON.parse(response);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('#netTotal').val(obj['netTotal']);
            $('.paymentAmount').html(obj['payment']);
            $('.vat').html(obj['vat']);
            $('.due').html(obj['due']);
            $('#due').val(obj['due']);
            $('.totalDiscount').html(obj['totalDiscount']);
            $('.discount').val(obj['discount']).attr("placeholder", obj['discount']);
            $('.msg-hidden').show();
            $('#msg').html(obj['msg']);
            if(obj['netTotal'] > obj['payment'] ){
                $("#receiveBtn").attr("disabled", true);
            }
        }
    })

});


$(document).on('change', '#appstore_bundle_restaurant_invoice_payment', function() {

    var payment  = parseInt($('#appstore_bundle_restaurant_invoice_payment').val()  != '' ? $('#appstore_bundle_restaurant_invoice_payment').val() : 0 );
    var netTotal  = parseInt($('#netTotal').val()  != '' ? $('#netTotal').val() : 0 );
    if(netTotal > payment ){
        $('#balance').html('Due Tk.');
    }else{
        $('#balance').html('Return Tk.');
        $("#receiveBtn").attr("disabled", false);
    }

});

$('#product').DataTable( {
    scrollY:        '110vh',
    scrollCollapse: true,
    paging:         false,
    bInfo : false,
    orderable: false,
    bSort: false,
    aoColumnDefs: [
        {
            bSortable: false,
            aTargets: [ -1 ]
        }
    ]
});

/*$('#invoiceParticular').DataTable( {
    scrollY:        '25vh',
    scrollCollapse: true,
    paging:         false,
    bInfo : false,
    sScrollX: '100%',
    bSort: false,
    bFilter: false,
});*/

$('#salesList').DataTable( {
    scrollY:        '50vh',
    scrollCollapse: true,
    paging:         false,
    bInfo : false,
    bSort: false
});



/*

$(window).scroll(function ()
{
    if($(document).height() <= $(window).scrollTop() + $(window).height())
    {
        loadmore();
    }
});

$(window).scroll(function (){
   if($(document).height() <= $(window).scrollTop() + $(window).height()){loadmore();}
});
function loadmore()
{
    var val = document.getElementById("row_no").value;
        $.ajax({
        type: 'post',
        url: 'get_results.php',
        data: {
            getresult:val
        },
        success: function (response) {
            var content = document.getElementById("all_rows");
            content.innerHTML = content.innerHTML+response;
            // We increase the value by 10 because we limit the results by 10
            document.getElementById("row_no").value = Number(val)+10;
        }
    });
}
*/


/*
$(document).on('change', '#appstore_bundle_restaurant_invoice_tokenNo', function(e) {

    var invoice = $('#invoiceId').val();
    var tokenNo = $('#appstore_bundle_restaurant_invoice_tokenNo').val();
    if(tokenNo == ''){
        return false;
    }
    $.post( Routing.generate('restaurant_invoice_token_check') ,{ invoice:invoice , tokenNo:tokenNo} )
        .done(function( data ) {
            if(data == 'invalid'){
                $("#appstore_bundle_restaurant_invoice_tokenNo").select2().select2("val","");
                $('#cabinInvalid').notifyModal({
                    duration : 5000,
                    placement : 'center',
                    overlay : true,
                    type : 'notify',
                    icon : false,
                });

            }else{

                $("#receiveBtn").attr("disabled", false);
                $("#kitchenBtn").attr("disabled", false);
            }
        });

});*/

$("input:text:visible:first").focus();
$('input[name=particular]').focus();

$('.inputs').keydown(function (e) {
    if (e.which === 13) {
        var index = $('.inputs').index(this) + 1;
        $('.inputs').eq(index).focus();
    }
});
$('.input2').keydown(function (e) {
    if (e.which === 13) {
        var index = $('.input2').index(this) + 1;
        $('.input2').eq(index).focus();
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

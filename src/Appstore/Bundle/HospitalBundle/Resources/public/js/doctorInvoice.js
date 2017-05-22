/**
 * Created by rbs on 5/1/17.
 */

$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});
// Getter
var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

// Setter
$( ".date-picker" ).datepicker( "option", "dateFormat", "dd-mm-yy" );

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

$(document).on('click', '.addPay', function() {

    var url = $(this).attr('data-url');
    $('.btn').removeClass('addPay');
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            location.reload();
        }
    })
});

$(document).on('click', '.delete', function() {

    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#remove-'+id).hide();
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            location.reload();
        }
    })
});


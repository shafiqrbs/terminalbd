$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true
});

$( ".datePicker" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-10:+0"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0"
});


function CommonJs(){


    $( ".date-picker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
    // Getter

    var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

    // Setter
    $( ".date-picker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );


    $(document).on('change', '.transactionMethod', function() {

        var transactionMethod = $(this).val();
        if(transactionMethod == 2 ){
            $('.bankHide').show();
            $('.bkashHide').hide();
        }else if(transactionMethod == 3 ){
            $('.bankHide').hide();
            $('.bkashHide').show();
        }else{
            $('.bankHide').hide();
            $('.bkashHide').hide();
        }

    });


    $(document).on("change", ".customer-ledger", function() {

        var customer = $(this).val();
        $.get( Routing.generate('domain_customer_ledger'),{ customer:customer} )
            .done(function( data ) {
                $('#outstanding').html(data);
        });

    });

    $(document).on("change", ".vendor-ledger-business", function() {

        var vendor = $(this).val();
        $.get( Routing.generate('account_single_vendor_ledger'),{ vendor:vendor,'type':'business'} )
            .done(function( data ) {
                $('#outstanding').html(data);
            });
    });


    $(document).on("click", ".delete", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    $('#remove-' + id).remove();
                }
            },
        })

    })


}


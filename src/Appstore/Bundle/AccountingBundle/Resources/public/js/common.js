$( ".date-picker" ).datepicker({
    dateFormat: "yy-mm-dd"
});
// Getter

var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

// Setter
$( ".date-picker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

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

    })

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


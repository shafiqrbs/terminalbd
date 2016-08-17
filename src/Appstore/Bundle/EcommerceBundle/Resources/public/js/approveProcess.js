function ApproveProcess(){

    $('#address').hide();

    $( ".date-picker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
    // Getter
    var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

    // Setter
    $( ".date-picker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );


    var delivery = $('select[name=delivery]').val();
    if( delivery == 'delivery'){
        $('#address').show();
    }
    $(document).on("click", "#delivery", function() {
        $('#address').show();
    });


    $(document).on("click", "#submitProcess", function() {

        var serialized = $('form#process').serialize();
        var url = $(this).attr('data-url');
        $.ajax({
            url: url,
            type: "GET",
            data: serialized
        }).done(function(data){
           location.reload();
        });

    });


    $(document).on("click", ".remove-tr", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    $('#remove-tr-' + id).remove();
                    location.reload();
                }
            },
        })

    })

     $(document).on("click", ".remove", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    $('#remove-' + id).remove();
                    location.reload();
                }
            },
        })

    })

    $(document).on("click", ".approve, .confirm", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    location.reload();
                }
            },
        })

    })
    $(document).on("click", ".process", function() {

        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    location.reload();
                }
            },
        })

    })

    $('#wfc').submit( function( e ) {

        var url = $('#confirm').attr("data-url");
        $.ajax({
            url: url,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (response) {
                if ('success' == response ) {
                    location.reload();
                }
            },
        })
        e.preventDefault();

    })

    $('#bankbox').hide();
    $('#bkashbox').hide();
    var paymentType = $('#paymentType').val();

    if(paymentType == 'cash-on-bank'){
        $('#bankbox').show();
        $('#bkashbox').hide();
    }
    if(paymentType == 'cash-on-bkash'){
        $('#bankbox').hide();
        $('#bkashbox').show();
    }

    $(document).on("change", "#paymentType", function() {

        var paymentType = $('#paymentType').val();

        alert(paymentType);

        if(paymentType == 'cash-on-bank'){

            $('#bankbox').show();
            $('#bkashbox').hide();
        }

        if(paymentType == 'cash-on-bkash'){
            $('#bankbox').hide();
            $('#bkashbox').show();
        }


    })

    $('#payment').submit( function( e ) {

        var url = $('#submitted').attr("data-url");
        var paymentMethod = $('#paymentType').val();
        var bank = $('#bank').val();
        var bkash = $('#bkash').val();
        if( paymentMethod == "" ){
            alert( "Please select payment type!" );
            return false;
        }
        if( paymentMethod == 'cash-on-bank' && bank == ''){
            alert( "Please select payment bank account no" );
            return false;
        }
        if( paymentMethod == 'cash-on-bkash' && bkash == ''){
            alert( "Please select payment bkash account" );
            return false;
        }
        $.ajax({
            url:url,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response) {
                location.reload();
            }
        });

        e.preventDefault();

    });



}


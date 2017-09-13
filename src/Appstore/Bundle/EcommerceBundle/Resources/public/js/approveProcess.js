function ApproveProcess(){

    $( ".date-picker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
    // Getter
    var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

    // Setter
    $( ".date-picker" ).datepicker( "option", "dateFormat", "dd-mm-yy" );

    $(document).on("click", "#submitProcess", function() {

        var serialized = $('form#process').serialize();
        $.ajax({
            url: url,
            type: "GET",
            data: serialized
        }).done(function(data){
           /*location.reload();*/
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
                   location.reload();
                }
            },
        })

    })

    $(document).on("click", ".remove", function() {
        var url = $(this).attr('data-url');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    console.log(data);
                    //location.reload();
                });
            }
        });
    });

   $(document).on("click", ".item-disable", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                  location.reload();
            },
        })

    });




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

    });
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
        });

    });

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
        });
        e.preventDefault();

    });

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


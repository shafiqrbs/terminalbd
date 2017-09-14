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
        var subDomain = $(this).attr('data-value');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    if(data == 'itemDelete'){
                        location.reload();
                    }else{
                        top.location.href="/customer/"+subDomain+"/order";//redirection
                    }
                });
            }
        });
    });

    $('.btn-number').click(function(e){

        e.preventDefault();

        url = $(this).attr('data-url');
        productId = $(this).attr('data-text');
        price = $(this).attr('data-title');
        fieldId = $(this).attr('data-id');
        fieldName = $(this).attr('data-field');
        type      = $(this).attr('data-type');
        var input = $('#quantity-'+$(this).attr('data-id'));
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if(type == 'minus') {
                if(currentVal > input.attr('min')) {
                    var existVal = (currentVal - 1);
                    input.val(existVal).change();
                    $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                        .done(function( data ) {
                            location.reload();
                        });
                }
                if(parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if(type == 'plus') {

                if(currentVal < input.attr('max')) {
                    var existVal = (currentVal + 1);
                    input.val(existVal).change();
                    $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                        .done(function(data){
                            if(data == 'success'){
                                location.reload();
                            }else{
                                input.val(existVal-1).change();
                                alert('There is not enough product in stock at this moment')
                            }
                        });
                }
                if(parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(0);
        }
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


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


    $(document).on("click", " .approve, .confirm, .remove , .process , .remove-tr , .item-disable", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    location.reload();
                });
            }
        });

    });


    $(document).on("click", ".item-remove", function() {
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
        var productId = $(this).attr('data-text');
        var price = $(this).attr('data-title');
        fieldId = $(this).attr('data-id');
        fieldName = $(this).attr('data-field');
        type      = $(this).attr('data-type');
        var size = $('#size-'+fieldId);
        var color = $('#color-'+fieldId);
        var input = $('#quantity-'+$(this).attr('data-id'));
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if(type == 'minus') {
                if(currentVal > input.attr('min')) {
                    var existVal = (currentVal - 1);
                    input.val(existVal).change();
                    $.get( url,{ quantity:existVal,'productId':productId,'price':price,'size':size,'color':color})
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
                    $.get( url,{ quantity:existVal,'productId':productId,'price':price,'size':size,'color':color})
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




    $('.itemProcess').click(function(e){
        
        var rel = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        var quantity = $('#quantity-'+rel).val() != '' ? $('#quantity-'+rel).val() : 1;
        var convertRate = $('#convertRate-'+rel).val() != '' ? parseFloat($('#convertRate-'+rel).val()) : 0;
        var shippingCharge = $('#shippingCharge-'+rel).val() != '' ? $('#shippingCharge-'+rel).val() : 0;
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get( url,{quantity:quantity,convertRate:convertRate,shippingCharge:shippingCharge})
                    .done(function(data){
                        if(data == 'success'){
                            location.reload();
                        }
                    });
            }
        });
        e.preventDefault();
    });

    $('.orderItemProcess').click(function(e){
        
        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        var quantity = $('#quantity-'+id).val() != '' ? $('#quantity-'+id).val() :1;
        var size = $('#size-'+id).val() != '' ? $('#size-'+id).val() : '';
        var color = $('#color-'+id).val() != '' ? $('#color-'+id).val() : '';

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get( url,'quantity='+quantity+'&size='+size+'&color='+color)
                    .done(function(data){
                        location.reload();
                    });
            }
        });
        e.preventDefault();
    });

    $('#submitPayment').click( function( e ) {

        var url = $('#ecommerce-payment').attr("action");

        var amount = $('#ecommerce_payment_amount').val();
        if( amount == "" ){
            alert( "Please add payment amount" );
            return false;
        }
        var transactionType = $('#ecommerce_payment_transactionType').val();
        if( transactionType == "" ){
            alert( "Please select transaction type" );
            return false;
        }
        var accountMobileBank = $('#ecommerce_payment_accountMobileBank').val();
        if( accountMobileBank == "" ){
            alert( "Please payment mobile account" );
            return false;
        }
        var mobileAccount = $('#ecommerce_payment_mobileAccount').val();
        if( mobileAccount == "" ){
            alert( "Please add payment mobile no" );
            return false;
        }
        var transaction = $('#ecommerce_payment_transaction').val();
        if( transaction == "" ){
            alert( "Please add payment transaction no" );
            return false;
        }
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.post( url,{amount:amount,transactionType:transactionType,'accountMobileBank':accountMobileBank,'mobileAccount':mobileAccount,'transaction':transaction})
                    .done(function(data){
                         location.reload();
                });
            }
        });
        e.preventDefault();

    });

    $('#addAddress').click(function(e){
        var url = $(this).attr("data-url");
        var delivery = $('#delivery').val()
        var address = $('#address').val()
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get( url,{delivery:delivery,'address':address})
                    .done(function(data){
                        if(data == 'success'){
                            location.reload();
                        }
                    });
            }
        });
        e.preventDefault();
    });

    $('#orderProcess').submit( function( e ) {

        var url = $('#orderProcess').attr("action");
        var comment = $('#ecommerce_order_comment').val();
        var address = $('#ecommerce_order_address').val();
        var location = $('#ecommerce_order_location').val();
        var process = $('#ecommerce_order_process').val();
        var deliveryDate = $('#ecommerce_order_deliveryDate').val();
        var cashOnDelivery = $('#ecommerce_order_cashOnDelivery').val();


        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.post( url,{comment:comment,process:process,deliveryDate:deliveryDate,address:address,location:location,cashOnDelivery:cashOnDelivery})
                    .done(function(data){
                        location.reload();
                });
            }
        });
        e.preventDefault();

    });

}


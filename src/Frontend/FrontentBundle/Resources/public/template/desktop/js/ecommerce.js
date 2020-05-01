$(document).on( "change", ".modalChange", function( e ) {

    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        success: function(response) {
            $('.modal-content').html(response);
        },

    })

});

$(document).on( "change", ".changeSize", function( e ) {

    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        beforeSend: function() {
            $('#subItemDetails').show().addClass('loading').fadeIn(3000);
        },
        success: function(response) {
            $('#subItemDetails').html(response);
            $('#subItemDetails').removeClass('loading');
        },

    })

});


$('.addCart').submit( function(e) {

    var url = $('.cartSubmit').attr("data-url");
    $.ajax({
        url:url ,
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response){
            obj = JSON.parse(response);
            if(obj['process'] == 'invalid'){
                alert('There is not enough product in stock at this moment');
            }else{
                $('.totalItem').html(obj['totalItem']);
                $('.totalAmount').html(obj['cartTotal']);
                $('.vsidebar .txt').html(obj['cartResult']);
            }
        }
    });
    e.preventDefault();

});


$(document).on( "click", ".cartSubmit", function(e){

    var url = $('.cartSubmit').attr("data-url");
    var data = $('.addCart').serialize();
    var qnt = $('#quantity').val();

    $.ajax({
        url:url ,
        type: 'POST',
        data:data,
        beforeSend: function () {
            $('.loader-double').fadeIn(5000).addClass('is-active');
            $('.cartSubmit').attr("disabled", true).html('<i class="fa fa-shopping-cart"></i> '+qnt+' in Basket');
        },
        success: function(response){
            $('.loader-double').fadeOut(5000).removeClass('is-active');
            obj = JSON.parse(response);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.dropdown-cart').html(obj['salesItem']);
            $('.vsidebar .txt').html(obj['cartResult']);

        }
    });
    e.preventDefault();

});


$(document).on( "click", ".hunger-remove-cart", function(e){
    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#item-remove-'+id).hide();
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            obj = JSON.parse(response);
            $('#cart-item-list-box').html(obj['cartItem']);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.vsidebar .txt').html(obj['cartResult']);

        }
    });
    e.preventDefault();
});



$('.remove-cart').click( function(e) {

    var url = $(this).attr("data-url");
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            location.reload();
        }
    });
    e.preventDefault();

});

$('.product-preview').click(function () {

    var url = $(this).attr("data-url");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('.product-modal-content').html(response);
            $('#product-modal').modal('toggle');
        }
    })
});

$(document).on( "click", ".preview", function(e){
    var url = $(this).attr("data-url");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('.product-modal-content').html(response);
            $('#product-modal').modal('toggle');
        }
    })
});

$(document).on( "click", ".btn-number-cart", function(e){

    e.preventDefault();

    var url         = $(this).attr('data-url');
    var productId   = $(this).attr('data-text');
    var price       = $(this).attr('data-title');
    var fieldId     = $(this).attr('data-id');
    var fieldName   = $(this).attr('data-field');
    var type        = $(this).attr('data-type');
    var input       = $('#quantity-'+ $(this).attr('data-id'));
    var currentVal  = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        var subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['totalItem']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $('#'+input).attr('disabled', true);
            }else {
                $('#'+input).attr('disabled', false);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] == 'success'){
                            var subTotal = (existVal * parseInt(price));
                            $('#btn-total-'+fieldId).html(subTotal);
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['totalItem']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $('#'+input).attr('disabled', true);
            }else {
                $('#'+input).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-new-cart-item", function(e){

    e.preventDefault();
    productId      = $(this).attr('data-id');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $("input[name='"+fieldName+"']");
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);

            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", "#productBuy", function(e){


    var url = $('#productBuy').attr("data-url");
    var data = $('.addCart').serialize();
    var qnt = $('#quantity').val();

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('.loader-curtain').fadeIn(5000).addClass('is-active');
            $.post(url,data).done(function(response) {
                obj = JSON.parse(response);
                $('#productBuy').addClass('shopping-cart').attr("disabled", true).html('<i class="fa fa-shopping-cart"></i> '+qnt+' in Basket');
                $('.totalItem').html(obj['totalItem']);
                $('.totalAmount').html(obj['cartTotal']);
                $('.dropdown-cart').html(obj['salesItem']);
                $('.vsidebar .txt').html(obj['cartResult']);
                $('.loader-curtain').fadeOut(1000);
            }).always(function() {
                $('#product-confirm').notifyModal({
                    duration : 10000,
                    placement : 'center',
                    overlay : true,
                    type : 'notify',
                    icon : false
                });
            });
        }
    });
});

$(document).on( "click", ".product-inline-buy", function(e){

    var dataId = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    var quantity = $('#quant-'+dataId).val() !== '' ? $('#quant-'+dataId).val() : '';
    var subItem = $('#size-'+dataId).val() !== '' ? $('#size-'+dataId).val() : '';
    var color = $('#color-'+dataId).val() !== '' ? $('#color-'+dataId).val() : '';
    var productImg = $('#productImg-'+dataId).val() !== '' ? $('#productImg-'+dataId).val() : '';
    $.get(url,{ quantity:quantity,subItem:subItem, color: color,productImg:productImg})
        .done(function(response) {
            obj = JSON.parse(response);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.dropdown-cart').html(obj['salesItem']);
            $('.vsidebar .txt').html(obj['cartResult']);
        });
});


$(document).on( "change", ".inlineSizeChange", function( e ) {

    var url = $(this).attr("data-url");
    var subItem = $(this).val();
    var product = $(this).attr("data-id");
    $.get(url,{'subItem':subItem}).done(function(response) {
        obj = JSON.parse(response);
        $('#inlineLoading-'+product).html(obj['subItem']);
        $('#currency-'+product).html(obj['salesPrice']);
    });
});

$(document).on( "click", "#spec", function(e){
    $('#showSpec').slideToggle('2000');
    $("span", this).toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
});


$(document).on( "click", ".btn-number", function(e){

    e.preventDefault();

    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {

            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }
        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }
        }
    } else {
        input.val(0);
    }
});


$("div.list-group > a").click(function(e) {
    e.preventDefault();
    $(this).siblings('a.active').removeClass("active");
    $(this).addClass("active");
    var index = $(this).index();
    $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
    $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
});


var searchSubItemConfig = function (subdomain,product) {

}

var categoryProductRes = $('#categoryProductRight').outerHeight(true);
$('.categoryProductLeft').css({
    'height':(categoryProductRes-30)
});

$('.categoryProductLeft .img-card-large').css({
    'height':(categoryProductRes-73)
});

$('.categoryProductLeft .img-card-large img').css({
    'height':(categoryProductRes-73)
});


var subCategoryProductRes = $('#subCategoryProductRight').outerHeight(true);
$('.subCategoryProductLeft').css({
    'height':(subCategoryProductRes-30)
});
$('.subCategoryProductLeft .img-card-cat').css({
    'height':(subCategoryProductRes-73)
});

$('.subCategoryProductLeft .img-card-cat img').css({
    'height':(subCategoryProductRes-73)
});


var categorySubCategoryRes = $('#categorySubCategoryRight').outerHeight(true);
$('#categorySubCategoryLeft').css({
    'height':(categorySubCategoryRes-30)
});

var brandHeight = $('#brandProductRight').outerHeight(true);
$('.brandProductLeft').css({
    'height':(brandHeight-30)
});

$('.brandProductLeft .img-card-large').css({
    'height':(brandHeight-73)
});

$('.brandProductLeft .img-card-large img').css({
    'height':(brandHeight-73)
});


var promotionHeight = $('#promotionProductRight').outerHeight(true);
$('.promotionProductLeft').css({
    'height':(promotionHeight-30)
});

$('.promotionProductLeft .img-card-large').css({
    'height':(promotionHeight-73)
});

$('.promotionProductLeft .img-card-large img').css({
    'height':(promotionHeight-73)
});

var tagHeight = $('#tagProductRight').outerHeight(true);
$('.tagProductLeft').css({
    'height':(tagHeight-30)
});

$('.tagProductLeft .img-card-large').css({
    'height':(tagHeight-73)
});

$('.tagProductLeft .img-card-large img').css({
    'height':(tagHeight-73)
});

var discountHeight = $('#discountProductRight').outerHeight(true);
$('.discountProductLeft').css({
    'height':(discountHeight-30)
});

$('.discountProductLeft .img-card-large').css({
    'height':(discountHeight-73)
});

$('.discountProductLeft .img-card-large img').css({
    'height':(discountHeight-73)
});


$(document).on( "click", "#filter", function(e){

    $('#productFilter').submit();

});

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


/*$('body').on('click', '.btn-cookie', function(el) {
    var val = $(this).attr('id');
    var url = $(this).attr('data-url');
    $.cookie('productList', val, {path: '/'});
    setTimeout(location.reload(), 1000);
});*/

$(document).on('click', '.btn-sorted', function(el) {
    $("#showFilter").slideToggle(200);
});

$(document).on( "click", "#filter", function(e){
    $('#productFilter').slideToggle('2000');
    $("span", this).toggleClass("fa-close fa-filter");
});


$(document).on( "click", ".showCartItem", function(e){
    $.ajax({url:'/cart/product-details'}).done(function(content){
        $("#showCartItem").html(content).slideDown("slow");
        $('html, body').animate({
            'scrollTop' : $("#showCartItem").position().top
        }, 1000);
    });
});


$(document).on( "click", ".hideCartItem", function(e){
    $("#showCartItem").slideUp("slow");
});

$(document).on( "click", ".productToCart", function(e){

    var cartForm = $(this).closest("form");
    var url = $(this).attr("data-url");
    var dataId = $(this).attr("data-id");
    var quantity = $('#quant-'+dataId , cartForm).val() != '' ? $('#quant-'+dataId).val() : '';
    var productImg = $('#productImg-'+dataId, cartForm).val() != '' ? $('#productImg-'+dataId).val() : '';
    $.get(url,{ quantity:quantity,productImg:productImg } ).done(function(response) {
        obj = JSON.parse(response);
        $('.totalItem').html(obj['items']);
        $('.totalAmount').html(obj['cartTotal']);
        $('.dropdown-cart').html(obj['salesItem']);
        $('.vsidebar .txt').html(obj['cartResult']);
    });
    e.preventDefault();
});


    $(document).on( "click", ".btn-number-stock", function(e){

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

    $( "#prescriptionUpload" ).click(function() {

        $.ajax({
            url: "/prescription-stock-item" ,
            type: 'POST',
            data:'',
            success: function(response) {
                $('.product-modal-content').html(response);
                $('#product-modal').modal('toggle');
                jqueryTemporaryLoad();
            },
        })
    });

$('.dropzone-mobile').inputFileZone({
    message: 'UPLOAD YOUR PRESCRIPTION',
    messageFile: 'Ready to upload',
    zIndex: 9999999,
    previewImages: true
});


function jqueryTemporaryLoad() {

    $('#itemName').click(function() {
        $(this).attr('value', '').focus();
    });

    $('.dropzone').inputFileZone({
        message: 'UPLOAD YOUR PRESCRIPTION',
        messageFile: 'Ready to upload',
        zIndex: 9999999,
        previewImages: true
    });

    var searchRequest = null;
    var minlength = 1;

    $(document).on( "keyup", ".select2StockMedicine", function(e){

        var that = this,
            value = $(this).val();
        if (value.length >= minlength ) {
            if (searchRequest != null)
                searchRequest.abort();
            searchRequest = $.ajax({
                type: "GET",
                url: "/product-stock",
                data: {
                    'q' : value
                },
                dataType: 'json',
                success: function(response){
                    var len = response.length;
                    $("#searchResult").empty();
                    for( var i = 0; i<len; i++){
                        var id = response[i]['id'];
                        var fname = response[i]['name'];
                        $("#searchResult").append("<li value='"+id+"'>"+fname+"</li>");
                    }
                    // binding click event to li
                    $("#searchResult li").bind("click",function(){
                        setText(this);
                    });

                }
            });
        }
    });

    function setText(element){

        var value = $(element).text();
        var stockId = $(element).val();
        $('#stockId').val(stockId);
        $("#itemName").val(value);
        $("#searchResult").empty();
        $.get( "/product-stock-details", { stockId:stockId } )
            .done(function( response ) {
                obj = JSON.parse(response);
                $('#salesPrice').html("৳"+obj['price']);
                $('#unit').html(obj['unit']);
                $('#quantity').focus();
            });
    }

    $("#stockItemForm").validate({
        rules: {
            "itemName": {required: true},
            "itemQuantity": {required: true},
            "salesPrice": {required: false},
        },

        messages: {
            "itemName": "Search a item name",
            "itemQuantity": "Enter item quantity",
        },
        tooltip_options: {
            "itemName": {placement: 'top', html: true},
            "itemQuantity": {placement: 'top', html: true},
        },

        submitHandler: function (form) {

            $.ajax({
                url: $('form#stockItemForm').attr('action'),
                type: $('form#stockItemForm').attr('method'),
                data: new FormData($('form#stockItemForm')[0]),
                processData: false,
                contentType: false,
                success: function (response) {
                    $('form#stockItemForm')[0].reset();
                    $("#stockCart").html(response);
                }
            });
        }
    });
}


$('.cartItem').click(function(){
    $('.cartItem').popModal({
        html : function(callback) {
            $.ajax({url:'/cart/product-details'}).done(function(content){
                callback(content);
            });
        }
    });
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
            $('.totalItem').html(obj['items']);
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
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            obj = JSON.parse(response);
            $('.totalItem').html(obj['items']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.vsidebar .txt').html(obj['cartResult']);
            $(e.target).closest('tr').hide();

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



$(document).on( "click", "#productBuy", function(e){


    var url = $('#productBuy').attr("data-url");
    var data = $('.addCart').serialize();
    var qnt = $('#quantity').val();

    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.post(url,data).done(function(response) {
                obj = JSON.parse(response);
                $('#productBuy').addClass('shopping-cart').attr("disabled", true).html('<i class="fa fa-shopping-cart"></i> '+qnt+' in Basket');
                $('.totalItem').html(obj['items']);
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


$(document).on( "click", "#spec", function(e){
    $('#showSpec').slideToggle('2000');
    $("span", this).toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
});


$('.input-number').focusin(function(){
    $(this).data('oldValue', $(this).val());
});


$(document).on( "click", ".btn-number", function(e){

    e.preventDefault();

    dataId = $(this).attr('data-id');
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type === 'minus') {

            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(this).attr('disabled', true);
                $('#btn-left-'+dataId).hide();
            }
        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
                $('#btn-left-'+dataId).show();

            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(this).attr('disabled', true);
            }
        }
    } else {
        input.val(0);
    }
});



$(document).on( "click", ".btn-number-cart", function(e){

    e.preventDefault();
    url         = $(this).attr('data-url');
    price       = $(this).attr('data-title');
    fieldId     = $(this).attr('data-id');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $('#quantity-'+$(this).attr('data-id'));
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function( data ) {
                        subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        obj = JSON.parse(data);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['items']);
                        $('.cartTotal').html(obj['cartTotal']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
                            subTotal = (existVal * parseInt(price));
                            $('#btn-total-'+fieldId).html(subTotal);
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['items']);
                            $('.cartTotal').html(obj['cartTotal']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-new-cart-item", function(e){

    e.preventDefault();
    productId      = $(this).attr('data-id');
    url         = $(this).attr('data-url');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $("input[name='"+fieldName+"']");
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:-1})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['items']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(input).attr('disabled', true);
                $('#btn-left-'+productId).hide();
            }else {
                $(input).attr('disabled', false);

            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:1})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['items']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);
                $('#btn-left-'+productId).show();
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-new-cart-item-modal", function(e){

    e.preventDefault();
    productId      = $(this).attr('data-id');
    url         = $(this).attr('data-url');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $("input[name='"+fieldName+"']");
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['items']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(input).attr('disabled', true);
                $('#btn-left-modal').hide();
            }else {
                $(input).attr('disabled', false);

            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['items']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);
                $('#btn-left-modal').show();
            }

        }
    } else {
        input.val(0);
    }
});

/*

$(document).on( "click", ".btn-number-cart", function(e){

    e.preventDefault();
    url         = $(this).attr('data-url');
    price       = $(this).attr('data-title');
    fieldId     = $(this).attr('data-id');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $('#quantity-'+$(this).attr('data-id'));
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function( data ) {
                        subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        obj = JSON.parse(data);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['items']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                alert(input);
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
                            subTotal = (existVal * parseInt(price));
                            $('#btn-total-'+fieldId).html(subTotal);
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['items']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-cart-item", function(e){

    e.preventDefault();

    url         = $(this).attr('data-url');
    productId   = $(this).attr('data-text');
    price       = $(this).attr('data-title');
    fieldId     = $(this).attr('data-id');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input       = $('#quantity-'+$(this).attr('data-id'));
    currentVal  = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                var existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['items']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                var existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal,'productId':productId,'price':price})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
                            var subTotal = (existVal * parseInt(price));
                            $('#btn-total-'+fieldId).html(subTotal);
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['items']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-cart-mobile", function(e){

    e.preventDefault();
    productId      = $(this).attr('data-id');
    url         = $(this).attr('data-url');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $("input[name='"+fieldName+"']");
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function( data ) {
                        obj = JSON.parse(data);
                        $('#cart-item-list-box').html(obj['cartItem']);
                        $('.totalItem').html(obj['items']);
                        $('.totalAmount').html(obj['cartTotal']);
                        $('.vsidebar .txt').html(obj['cartResult']);
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $(input).attr('disabled', true);
                $('#btn-left-'+productId).hide();
            }else {
                $(input).attr('disabled', false);

            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function(data){
                        obj = JSON.parse(data);
                        if(obj['process'] === 'success'){
                            $('#cart-item-list-box').html(obj['cartItem']);
                            $('.totalItem').html(obj['items']);
                            $('.totalAmount').html(obj['cartTotal']);
                            $('.vsidebar .txt').html(obj['cartResult']);
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $(input).attr('disabled', true);
            }else {
                $(input).attr('disabled', false);
                $('#btn-left-'+productId).show();
            }

        }
    } else {
        input.val(0);
    }
});

*/



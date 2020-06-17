$(document).on('click', ".cartUpload, #prescriptionUpload", function(el) {

    $.ajax({
        url: "/cart-stock-item" ,
        type: 'POST',
        data:'',
        success: function(response) {
            $('.product-modal-content').html(response);
            $('#product-modal').modal('toggle');
            jqueryTemporaryLoad();
        },
    })
});


function cartInfo(response,quantity) {
    obj = JSON.parse(response);
    var qnt = quantity === "" ? 1 : quantity;
    $('.cartSubmit').attr("disabled", true).html(qnt+' in Basket');
    setTimeout(function(){$('.cartSubmit').html('<i class="fa fa-shopping-cart"></i> ADD')}, 3000);
    $('.totalItem').html(obj['totalItems']);
    $('.cartQuantity').html(obj['totalQuantity']);
    $('.totalAmount').html(obj['cartTotal']);
    $('.cartTotal').html(obj['cartTotal']);
    $('.vsidebar .txt').html(obj['cartResult']).show().fadeOut(3000);
}

$(document).on( "click", ".hunger-remove-cart", function(e){
    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#item-remove-'+id).hide();
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            cartInfo(response,1);
        }
    });
    e.preventDefault();
});


$(document).on('click', '.btn-sorted', function(el) {
    $("#showFilter").slideToggle(200);
});

$(document).on( "click", "#filter", function(e){
    $('#productFilter').slideToggle('2000');
    $("span", this).toggleClass("fa-close fa-filter");
});

$(document).on( "click", ".upload-pres", function(e){
    $('#uploadPrescription').slideToggle('2000');
    $("span", this).toggleClass("fa-close fa-camera");
});

$(document).on( "click", ".showCartItem", function(e){
    $.ajax({
        url: "/cart/product-details",
        type: 'GET',
        success: function (response) {
            $('.product-modal-content').html(response);
            $('#product-modal').modal('toggle');
            jqueryTemporaryLoad();
        }
    })
});



$(document).on( "click", ".showCartItemxx", function(e){
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


$('.productSingleCart').click( function(e) {

    var url = $(this).attr("data-url");
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            cartInfo(response,1);
        }
    });
    e.preventDefault();

});

$(document).on( "click", ".productToCart", function(e){

    var cartForm = $(this).closest("form");
    var url = $(this).attr("data-url");
    var dataId = $(this).attr("data-id");
    var quantity = $('#quant-'+dataId , cartForm).val() != '' ? $('#quant-'+dataId).val() : '';
    var productImg = $('#productImg-'+dataId, cartForm).val() != '' ? $('#productImg-'+dataId).val() : '';
    $.get(url,{ quantity:quantity,productImg:productImg } )
        .done(function(response) {
        cartInfo(response,quantity)
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
                    .done(function( response ) {
                        subTotal = (existVal * parseInt(price));
                        $('#btn-total-'+fieldId).html(subTotal);
                        cartInfo(response,existVal)
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
                    .done(function(response){
                        obj = JSON.parse(response);
                        if(obj['process'] === 'success'){
                            subTotal = (existVal * parseInt(price));
                            $('#btn-total-'+fieldId).html(subTotal);
                            cartInfo(response,existVal)
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

function jqueryTemporaryLoad() {

    $('#itemName').click(function() {
        $(this).attr('value', '').focus();
    });


    $('.dropzone').inputFileZone({
        message: 'UPLOAD YOUR SHOPPING IMAGE',
        previewImages: false,
    });

    jQuery.validator.addMethod("maxFileSize", function(value, element, param) {

        var isOptional = this.optional(element),
            file;
        if(isOptional) {
            return isOptional;
        }

        if ($(element).attr("type") === "file") {

            if (element.files && element.files.length) {

                file = element.files[0];
                return ( file.size && file.size <= param );
            }
        }
        return false;
    }, "File size is too large.");

    $(document).on("#cartForm").validate({
        rules: {
            uploadFile: {
                required: false,
                extension:'jpe?g,png,pdf',
                maxFileSize:5242880
            }
        },messages: {
            uploadFile:{
                extension:"Upload file must be jpeg,jpg,png,pdf",
                maxFileSize:"File size must be less than 5 MB.",
            }
        },
        submitHandler: function(form,e) {
            form.submit();
        }
    });


    var searchRequest = null;
    var minlength = 1;

    $(document).on( "keyup", ".select2Stock", function(e){

        var that = this,
            value = $(this).val();
        if (value.length >= minlength ) {
            if (searchRequest != null)
                searchRequest.abort();
            searchRequest = $.ajax({
                type: "GET",
                url: "/product-stock-search",
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
                $('#salesPrice').html(obj['price']);
                $('#unit').html(obj['unit']);
                $('#cart-subitem').html(obj['subItems']);
                $('#quantity').focus();
            });
    }

    $(document).on( "change", ".changeSize", function( e ) {

        var subItem = $(this).val();
        var url = $(this).attr("data-url");
        $.ajax({
            url: url ,
            type: 'GET',
            data:'subItem='+subItem,
            success: function(response) {
                $('#subItemDetails').html(response);
            },

        })

    });

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

function fileUpload() {
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

$(document).on('click',"#cart-print", function (event) {

    var url = $(this).attr('data-action');

    $.MessageBox({

        buttonDone      : "OK",
        buttonFail      : "Cancel",
        message         : "<b>PRINT CONFIRM</b>",
        input           : {
            mobile : {
                type    : "text",
                label   : "Mobile:",
                title   : "Mobile no"
            }
        },
        filterDone: function (data) {
            if (data['mobile'] === "") return "Please input mobile no";
            return $.ajax({
                url: url,
                type: "get",
                data: data
            }).done(function (data) {
                if (data === 'invalid') {
                    $.MessageBox("This item name already exist, Please try another item name");
                } else {
                    $.MessageBox("Invalid");
                }
            })
        }
    });
});

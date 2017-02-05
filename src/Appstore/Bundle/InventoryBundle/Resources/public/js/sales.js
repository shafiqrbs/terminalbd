/**
 * Created by rbs on 2/9/16.
 */

var InventorySales = function(sales) {

    $('input[name=barcode]').focus();
    $(document).on('change', '#barcode', function() {
        var barcode = $('#barcode').val();
        if(barcode == ''){
            $('#wrongBarcode').html('<strong>Error!: </strong>Invalid barcode, Please try again.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response){
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#purchaseItem').html(obj['purchaseItem']);
                $('#salesItem').html(obj['salesItem']);
                $('.salesTotal').html(obj['salesTotal']);
                $('#dueAmount').val(obj['salesTotal']);
                $('#subTotal').val(obj['salesSubTotal']);
                $('#vat').val(obj['salesVat']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                $('#wrongBarcode').html(obj['msg']);
                FormComponents.init();
            },

        })
    })

    $(document).on('change', '#barcodeNo', function() {

        var barcode = $('#barcodeNo').val();
        if(barcode == ''){
            $('#wrongBarcode').html('Using wrong barcode, Please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response) {
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#purchaseItem').html(obj['purchaseItem']);
                $('#salesItem').html(obj['salesItem']);
                $('.salesTotal').html(obj['salesTotal']);
                $('#subTotal').val(obj['salesSubTotal']);
                $('#vat').val(obj['salesVat']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                $('#dueAmount').val(obj['salesTotal']);
                $('#wrongBarcode').html(obj['msg']);
                FormComponents.init();
            },
        })
    });

    $(document).on('click', '.addSales', function() {

        var barcode = $(this).attr('id');
        if(barcode == ''){
            $('#wrongBarcode').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response) {
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#purchaseItem').html(obj['purchaseItem']);
                $('#salesItem').html(obj['salesItem']);
                $('.salesTotal').html(obj['salesTotal']);
                $('#subTotal').val(obj['salesSubTotal']);
                $('#vat').val(obj['salesVat']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                $('#dueAmount').val(obj['salesTotal']);
                $('#wrongBarcode').html(obj['msg']);
                FormComponents.init();
            },
        })
    });

    $(document).on('change', '#item', function() {

        var item = $('#item').val();
        if(item == ''){
            $('#stockItemDetails').hide();
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_purchase'),
            type: 'POST',
            data:'item='+ item +'&sales=' + sales,
            success: function(response) {
                $('#stockItemDetails').show();
                $('#itemDetails').html(response);
                $(".editable").editable();
            },
        })
    })



    $(document).on("click", ".customPrice", function() {

        var rel = $(this).attr('rel');
        var estimatePrice = parseInt($('#estimatePrice-'+rel).val());
        var quantity = parseInt($('#quantity-'+rel).val());
        if($(this).is(':checked'))
        {
            $("#salesPrice-"+rel).attr("readonly", false).focus().val(estimatePrice);

        }else{

            var customPrice = 0;
            $("#salesPrice-"+rel).attr("readonly", true).focus().val(estimatePrice);
            var subTotal  = (quantity * estimatePrice);
            $("#subTotalShow-"+rel).html(subTotal);
            $.ajax({
                url: Routing.generate('inventory_sales_item_update'),
                type: 'POST',
                data:'salesItemId='+rel+'&quantity='+quantity+'&salesPrice='+estimatePrice+'&customPrice='+customPrice,
                success: function(response) {
                    obj = JSON.parse(response);
                    $('.salesTotal').html(obj['salesTotal']);
                    $('#subTotal').val(obj['salesSubTotal']);
                    $('#vat').val(obj['salesVat']);
                    $('#paymentTotal').val(obj['salesTotal']);
                    $('#paymentSubTotal').val(obj['salesTotal']);
                    $('#dueAmount').html(obj['salesTotal']);
                },

            })
        }
    });



    $(document).on('change', '.quantity , .salesPrice', function() {

        var rel = $(this).attr('rel');

        var quantity = parseFloat($('#quantity-'+rel).val());
        var price = parseFloat($('#salesPrice-'+rel).val());
        if($('#customPrice-'+rel).is(':checked')){
            var customPrice = 1;
        }else{
            var customPrice = 0;
        }
        var subTotal  = (quantity * price);
        $("#subTotalShow-"+rel).html(subTotal);

        $.ajax({
            url: Routing.generate('inventory_sales_item_update'),
            type: 'POST',
            data:'salesItemId='+rel+'&quantity='+ quantity +'&salesPrice='+price+'&customPrice='+customPrice,
            success: function(response) {
                obj = JSON.parse(response);
                $('.salesTotal').html(obj['salesTotal']);
                $('#subTotal').val(obj['salesSubTotal']);
                $('#vat').val(obj['salesVat']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                $('#dueAmount').val(obj['salesTotal']);
                $('#wrongBarcode').html(obj['msg']);
            },

        })
    })

    $('#salesItem').on("click", ".delete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                if ('success' == obj['success']) {
                    $('#remove-' + id).hide();
                    $('.salesTotal').html(obj['salesTotal']);
                    $('#subTotal').val(obj['salesSubTotal']);
                    $('#vat').val(obj['salesVat']);
                    $('#paymentTotal').val(obj['salesTotal']);
                    $('#paymentSubTotal').val(obj['salesTotal']);
                    $('#dueAmount').val(obj['salesTotal']);
                }


            },
        })
    });

    $(document).on('change', '#discount', function() {

        var discount = parseInt($('#discount').val());
        $.ajax({
            url: Routing.generate('inventory_sales_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&sales='+ sales,
            success: function(response) {
                obj = JSON.parse(response);
                $('.salesTotal').html(obj['salesTotal']);
                $('#subTotal').val(obj['salesSubTotal']);
                $('#vat').val(obj['salesVat']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                $('#dueAmount').val(obj['salesTotal']);
            },

        })
    })

    $(document).on('change', '#paymentAmount', function() {

        var payment     = parseInt($('#paymentAmount').val()  != '' ? $('#paymentAmount').val() : 0 );

        var total =  parseInt($('#paymentTotal').val());
        if( payment >= total ){

            var returnAmount = ( payment - total );
            $('#returnAmount').val(returnAmount).addClass('payment-yellow');
            $('#dueAmount').val('').removeClass('payment-red');

        }else{

            var dueAmount = (total-payment);
            if(dueAmount > 0){
                $('#returnAmount').val('').removeClass('payment-yellow');
                $('#dueAmount').val(dueAmount).addClass('payment-red');
            }
        }
        if(payment > 0 && total > 0  ){
            $(".paymentBtn").attr("disabled", false);
        }else{
            $(".paymentBtn").attr("disabled", true);
        }

    })


    $(document).on('change', '#sales_transactionMethod', function() {

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

    })

    $('#sales').on("click", ".delete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                if ('success' == obj['success']) {
                    $('#salesRemove-' + id).hide();
                }
            }
        })
    })

    $(".select2Item").select2({

        placeholder: "Search item, color, size & brand name",
        allowClear: true,
        ajax: {
            url: Routing.generate('item_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (m) {
            return m;
        },
        formatResult: function(item){

            return item.name +' => '+ (item.remainingQuantity)

        }, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.name + ' / ' + item.sku}, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });

    $("#barcodeNo").select2({
        placeholder: "Enter specific barcode",
        allowClear: true,
        ajax: {

            url: Routing.generate('inventory_purchaseitem_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (m) {
            return m;
        },
        formatResult: function(item){ return item.text +'(' +item.item_name+')'}, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.text +'(' +item.item_name+')' }, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });


}


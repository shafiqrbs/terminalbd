$(".select2StockMedicine").select2({

    placeholder: "Search stock medicine name",
    ajax: {
        url: Routing.generate('order_medicine_stock_search'),
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
    formatResult: function (item) { return item.text}, // omitted for brevity, see the source of this page
    formatSelection: function (item) { return item.text }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('medicine_stock_name', { stock : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});


$(document).on('change', '#orderItem_itemName', function() {

    var medicine = $(this).val();
    $.ajax({
        url: Routing.generate('medicine_order_item_stock_details',{'id': medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#orderItem_price').val(obj['price']);
            $('#unit').html(obj['unit']);
        }
    })

});

$(document).on('click', '#addOrderItem', function() {
    if($("#orderItem").valid()){
        $.ajax({
            url         : $('form#orderItem').attr( 'action' ),
            type        : $('form#orderItem').attr( 'method' ),
            data        : new FormData($('form#orderItem')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                location.reload();
            }
        });
    }
});

var formTemporary = $("#orderItem").validate({

    rules: {

        "orderItem[itemName]": {required: true},
        "orderItem[price]": {required: true},
        "orderItem[quantity]": {required: true},
    },

    messages: {

        "orderItem[itemName]":"Enter medicine name",
        "orderItem[price]":"Enter sales price",
        "orderItem[quantity]":"Enter medicine quantity",
    },
    tooltip_options: {
        "orderItem[itemName]": {placement:'top',html:true},
        "orderItem[price]": {placement:'top',html:true},
        "orderItem[quantity]": {placement:'top',html:true},
    }
});


/**
 * Created by rbs on 5/1/17.
 */

$(document).on("click", "#submitBtn", function() {
    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('#purchaseReturn').submit();
        }
    });
});

$(document).on('keyup', '.quantity , .price', function() {

    var returnSubTotal     = parseFloat($('#returnTotal').val()  != '' ? $('#returnTotal').val() : 0 );
    var id = $(this).attr('data-id');
    var quantity = parseFloat($('#quantity-'+id).val());
    var price = parseFloat($('#price-'+id).val());
    var subTotal  = (quantity * price);
    $("#subTotal-"+id).html(subTotal);
    var total = (returnSubTotal + subTotal)
    $('.returnSubTotal').html(total);
    $('.returnSubTotal').val(total);

});

$(document).on("click", ".approve", function() {
    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                if(data ==='success'){
                    window.location.replace("/medicine/purchase-return/"+id+"/show");
                }
            });
        }
    });
});

$(".select2StockMedicine").select2({

    placeholder: "Search stock medicine name",
    ajax: {
        url: Routing.generate('medicine_stock_search'),
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
        $.ajax(Routing.generate('medicine_stock_name', { vendor : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});

$(document).on('change', '#damage_medicineStock', function() {

    var medicine = $('#damage_medicineStock').val();
    $.ajax({
        url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#medicinePurchaseItem').html(obj['purchaseItems']).focus();
        }
    })

});

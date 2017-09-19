$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});
// Getter
var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

// Setter
$( ".date-picker" ).datepicker( "option", "dateFormat", "dd-mm-yy" );

$(document).on("click", ".editable-submit", function() {
    setTimeout(pageReload, 3000);
});
function pageReload() {
    location.reload();
}

$(document).on("click", ".confirm", function() {
    var url = $(this).attr('data-url');
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



$(document).on("click", ".delete", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#action-'+id).hide();
    $('#delete-'+id).hide();

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


$(document).on("click", ".approve", function() {

    $(this).removeClass('approve');
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#action-'+id).hide();
    $('#delete-'+id).hide();

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

$(document).on("click", ".approvex", function() {

    $(this).removeClass('approve');
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#action-'+id).hide();
    $('#delete-'+id).hide();
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function() {
            $('.tabbable').show().addClass('ajax-loading').fadeIn(3000);
        },
        success: function (response) {
            location.reload();
        },
    })
});

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

        //return item.name +' => '+ (item.remainingQuantity)
        return item.name

    }, // omitted for brevity, see the source of this page
    formatSelection: function(item){return item.name + ' / ' + item.sku}, // omitted for brevity, see the source of this page
    initSelection: function(element, callback) {
        var id = $(element).val();
    },
    allowClear: true,
    minimumInputLength:1
});

$(".branchSales2Item").select2({

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

        //return item.name +' => '+ (item.remainingQuantity)
        return item.name

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

$(".select2Barcode").select2({

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
    formatResult: function(item){ return item.text}, // omitted for brevity, see the source of this page
    formatSelection: function(item){return item.text}, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_barcode_name', { barcode : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });


    },
    allowClear: true,
    minimumInputLength:1

});


$(".select2Grn").select2({

    placeholder: "Search purchase grn",
    ajax: {

        url: Routing.generate('inventory_grn_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_grn_name', { grn : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });


    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Product").select2({

    placeholder: "Search product name",
    ajax: {

        url: Routing.generate('inventory_product_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_product_name', { product : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });


    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Category").select2({

    placeholder: "Search by product category",
    ajax: {
        url: Routing.generate('inventory_category_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {

        var id = $(element).val();
        $.ajax(Routing.generate('inventory_category_name', { category : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });


    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Unit").select2({

    placeholder: "Search product unit",
    ajax: {
        url: Routing.generate('inventory_unit_search'),
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
        $.ajax(Routing.generate('inventory_unit_name', { vendor : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });


    },
    allowClear: true,
    minimumInputLength: 1

});

$(".select2Color").select2({

    placeholder: "Search color name",
    ajax: {

        url: Routing.generate('inventory_itemcolor_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_itemcolor_name', { color : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Size").select2({

    placeholder: "Search size name",
    ajax: {

        url: Routing.generate('inventory_itemsize_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_itemsize_name', { size : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });


    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Vendor").select2({

    placeholder: "Search vendor name",
    ajax: {
        url: Routing.generate('inventory_vendor_search'),
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
        $.ajax(Routing.generate('inventory_vendor_name', { vendor : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });


    },
    allowClear: true,
    minimumInputLength: 1

});

$(".select2Brand").select2({

    placeholder: "Search brand name",
    ajax: {

        url: Routing.generate('inventory_itembrand_search'),
        dataType: 'json',
        delay: 250,
        data: function (params, page) {
            return {
                q: params,
                page_limit: 100,

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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_itembrand_name', { brand : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});

$(".select2User").select2({

    ajax: {

        url: Routing.generate('domain_user_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('domain_user_name', { user : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Customer").select2({

    ajax: {

        url: Routing.generate('domain_customer_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var customer = $(element).val();
        $.ajax(Routing.generate('domain_customer_name', { customer : customer}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Location").select2({

    ajax: {

        url: Routing.generate('domain_location_search'),
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
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var location = $(element).val();
        $.ajax(Routing.generate('domain_location_name', { location : location}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

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

$(document).on('change', '#item', function() {

    var item = $('#item').val();
    if(item == ''){
        $('#stockItemDetails').hide();
        return false;
    }
    $.ajax({
        url: Routing.generate('inventory_sales_item_purchase',{'customer':'customer'}),
        type: 'POST',
        data:'item='+ item,
        success: function(response) {
            $('#stockItemDetails').show();
            $('#itemDetails').html(response);
            $(".editable").editable();
        },
    })
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

$("#sku").select2({

    placeholder: "Enter product sku",
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


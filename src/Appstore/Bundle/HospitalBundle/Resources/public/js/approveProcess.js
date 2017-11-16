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
              /*  location.reload();*/
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


$(".select2CustomerCode" ).autocomplete({

    source: function( request, response ) {
        $.ajax({
            url: Routing.generate('domain_customer_code_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        });
    },
    minLength: 8,
    select: function( event, ui){
        var customerId = ui.item.id;
        var invoice = $('#invoice').val();
        $.ajax({
            url: Routing.generate('hms_invoice_customer_details'),
            type: 'POST',
            data:'customer='+customerId +'&invoice='+invoice,
            success: function(response) {
                obj = JSON.parse(response);
                if(obj['status'] == 'valid'){
                    $('.select2CustomerId').val(obj['customerId']);
                    $('.select2mobile').val(obj['mobile']);
                    $('.patientNme').val(obj['name']);
                    $('.patientAge').val(obj['age']);
                    $('.address').val(obj['address']);
                    $('.location').val(obj['location']).find("option[value=" + obj['location'] +"]").attr('selected', true);
                    $('.gender').val(obj['gender']).find("option[value=" + obj['gender'] +"]").attr('selected', true);
                    $('.ageType').val(obj['ageType']).find("option[value=" + obj['ageType'] +"]").attr('selected', true);

                }else{
                    alert("Exit patient information does not exist");
                }
            },
        })
    }

});

$( ".select2mobile" ).autocomplete({

    source: function( request, response ) {
        $.ajax({
            url: Routing.generate('domain_customer_auto_mobile_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        });
    },
    minLength: 11,
    select: function( event, ui){
        var customerId = ui.item.id;
        var invoice = $('#invoice').val();
        $.ajax({
            url: Routing.generate('hms_invoice_customer_details'),
            type: 'POST',
            data:'customer='+customerId+'&invoice='+invoice,
            success: function(response) {
                obj = JSON.parse(response);
                if(obj['status'] == 'valid'){
                    $('.select2CustomerId').val(obj['customerId']);
                    $('.select2mobile').val(obj['mobile']);
                    $('.patientNme').val(obj['name']);
                    $('.patientAge').val(obj['age']);
                    $('.address').val(obj['address']);
                    $('.location').val(obj['location']).find("option[value=" + obj['location'] +"]").attr('selected', true);
                    $('.gender').val(obj['gender']).find("option[value=" + obj['gender'] +"]").attr('selected', true);
                    $('.ageType').val(obj['ageType']).find("option[value=" + obj['ageType'] +"]").attr('selected', true);

                }else{
                    alert("Exit patient information does not exist");
                }
            },
        })
    }

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
    minimumInputLength:1

});


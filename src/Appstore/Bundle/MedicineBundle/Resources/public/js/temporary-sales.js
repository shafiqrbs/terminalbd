/**
 * Created by rbs on 5/1/17.
 */

$(document).on('click', '#temporarySales', function() {

    $('.dialogModal_header').html('Sales Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('medicine_sales_temporary_new'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    jqueryTemporaryLoad();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on("click", "#instantPopup", function() {

    var url = $(this).attr('data-url');
    $.ajax({
        url : url,
        beforeSend: function(){
            $('.loader-double').fadeIn(1000).addClass('is-active');
        },
        complete: function(){
            $('.loader-double').fadeIn(1000).removeClass('is-active');
        },
        success:  function (data) {
            $("#instantPurchaseLoad").html(data);
            jqueryInstantTemporaryLoad();
        }
    });
});

function jqueryTemporaryLoad() {

    $(document).on('change', '#salesTemporaryItem_stockName', function() {

        var medicine = $('#salesTemporaryItem_stockName').val();
        $.ajax({
            url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#salesTemporaryItem_barcode').html(obj['purchaseItems']).focus();
                $('#salesTemporaryItem_salesPrice').val(obj['salesPrice']);
            }
        })

    });

    $('form#salesTemporaryItemForm').on('keypress', '.input', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {

                case 'salesTemporaryItem_barcode':
                    $('#salesTemporaryItem_quantity').focus();
                    break;

                case 'salesTemporaryItem_quantity':
                    $('#addTemporaryItem').focus();
                    break;

                case 'addTemporaryItem':
                    $('#salesTemporaryItem_stockName').select2('open');
                    break;
            }
            return false;
        }
    });

    var formTemporary = $("#salesTemporaryItemForm").validate({

        rules: {

            "salesTemporaryItem[stockName]": {required: true},
            "salesTemporaryItem[barcode]": {required: true},
            "salesTemporaryItem[salesPrice]": {required: true},
            "salesTemporaryItem[quantity]": {required: true},
        },

        messages: {

            "salesTemporaryItem[medicineStock]":"Enter medicine name",
            "salesTemporaryItem[barcode]":"Select barcode",
            "salesTemporaryItem[salesPrice]":"Enter sales price",
            "salesTemporaryItem[quantity]":"Enter medicine quantity",
        },
        tooltip_options: {
            "salesTemporaryItem[medicineStock]": {placement:'top',html:true},
            "salesTemporaryItem[barcode]": {placement:'top',html:true},
            "salesTemporaryItem[salesPrice]": {placement:'top',html:true},
            "salesTemporaryItem[quantity]": {placement:'top',html:true},
        },

        submitHandler: function(formTemporary) {

            $.ajax({
                url         : $('form#salesTemporaryItemForm').attr( 'action' ),
                type        : $('form#salesTemporaryItemForm').attr( 'method' ),
                data        : new FormData($('form#salesTemporaryItemForm')[0]),
                processData : false,
                contentType : false,
                success: function(response){
                    obj = JSON.parse(response);
                    $('#invoiceParticulars').html(obj['salesItems']);
                    $('#subTotal').html(obj['subTotal']);
                    $('#grandTotal').html(obj['initialGrandTotal']);
                    $('.discount').html(obj['initialDiscount']);
                    $('.dueAmount').html(obj['initialGrandTotal']);
                    $('#salesNetTotal').val(obj['initialGrandTotal']);
                    $('#salesTemporary_discount').val(obj['initialDiscount']);
                    $('#salesTemporary_due').val(obj['initialGrandTotal']);
                    $("#salesTemporaryItem_stockName").select2("val", "");
                    $('#salesTemporaryItemForm')[0].reset();
                }
            });
        }
    });

    $(document).on('change', '.quantity , .salesPrice', function() {

        var id = $(this).attr('data-id');

        var quantity = parseFloat($('#quantity-'+id).val());
        var price = parseFloat($('#salesPrice-'+id).val());
        var subTotal  = (quantity * price);
        $("#subTotal-"+id).html(subTotal);
        $.ajax({
            url: Routing.generate('medicine_sales_temporary_item_update'),
            type: 'POST',
            data:'salesItemId='+ id +'&quantity='+ quantity +'&salesPrice='+ price,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
            },

        })
    });

    $(document).on('click', '.itemUpdate', function() {

        var id = $(this).attr('data-id');
        var quantity = parseFloat($('#quantity-'+id).val());
        var price = parseFloat($('#salesPrice-'+id).val());
        var subTotal  = (quantity * price);
        $("#subTotal-"+id).html(subTotal);
        $.ajax({
            url: Routing.generate('medicine_sales_temporary_item_update'),
            type: 'POST',
            data:'salesItemId='+ id +'&quantity='+ quantity +'&salesPrice='+ price,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
            },

        })
    });


    $(document).on('change', '#salesTemporary_discountCalculation , #salesTemporary_discountType', function() {

        var discountType = $('#salesTemporary_discountType').val();
        var discount = parseInt($('#salesTemporary_discountCalculation').val());
        $.ajax({
            url: Routing.generate('medicine_sales_temporary_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
            }

        })

    });

    $('#invoiceParticulars').on("click", ".temporaryDelete", function() {

        var url = $(this).attr("data-url");
        var id = $(this).attr("id");
        $('#remove-'+id).hide();
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
            }
        })
    });


    $(document).on('change', '#salesTemporary_received', function() {

        var payment     = parseInt($('#salesTemporary_received').val()  != '' ? $('#salesTemporary_received').val() : 0 );
        var discount     = parseInt($('#salesTemporary_discount').val()  != '' ? $('#salesTemporary_discount').val() : 0 );
        var netTotal =  parseInt($('#salesNetTotal').val());
        var dueAmount = (netTotal-payment);
        if(dueAmount > 0){
            $('#balance').html('Due Tk.');
            $('.dueAmount').html(dueAmount);
            $('#salesTemporary_due').val(dueAmount);
        }else{
            var balance =  payment - netTotal ;
            $('#balance').html('Return Tk.');
            $('.dueAmount').html(balance);
            $('#salesTemporary_due').val(0);
        }
    });

    $('form#salesTemporaryForm').on('keypress', '.salesInput', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {

                case 'salesTemporary_discountCalculation':
                    $('#salesTemporary_received').focus();
                    break;

                case 'salesTemporary_received':
                    $('#receiveBtn').focus();
                    break;
            }
            return false;
        }
    });

    $(document).on("click", ".confirmTemporarySubmit", function() {

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.ajax({
                    url         : $('form#salesTemporaryForm').attr( 'action' ),
                    type        : $('form#salesTemporaryForm').attr( 'method' ),
                    data        : new FormData($('form#salesTemporaryForm')[0]),
                    processData : false,
                    contentType : false,
                    success: function(response){
                        obj = JSON.parse(response);
                        $('#salesTemporaryForm')[0].reset();
                        if( obj['process'] === 'print'){
                            window.location.replace("/medicine/sales/"+obj['sales']+"/show");
                        }
                        $('#invoiceParticulars').html('');
                        $('#subTotal').html('');
                        $('#grandTotal').html('');
                        $('.discount').html('');
                        $('.dueAmount').html('');
                        $('#salesNetTotal').val('');
                        $('#salesTemporary_discount').val('');
                        $('#salesTemporary_due').val('');
                   }
                });
            }
        });

    });
    $(".select2StockMedicine").select2({

        placeholder: "Search vendor name",
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
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        initSelection: function (element, callback) {
            var id = $(element).val();
            $.ajax(Routing.generate('medicine_stock_name', {vendor: id}), {
                dataType: "json"
            }).done(function (data) {
                return callback(data);
            });
        },
        allowClear: true,
        minimumInputLength:2

    });
}

function jqueryInstantTemporaryLoad(){


    $(document).on('change', '#medicineName', function() {

        var medicine = $('#medicineName').val();
        $.ajax({
            url: Routing.generate('medicine_purchase_particular_search',{'id':medicine}),
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#salesPrice').val(obj['salesPrice']);
                $('#purchasePrice').val(obj['purchasePrice']);
            }
        })

    });

    $('#medicineName').on("select2-selecting", function (e) {
        setTimeout(function () {
            $('#medicineName').focus();
        }, 2000)
    });

    $('form#instantPurchase').on('keypress', '.input', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {

                case 'quantity':
                    $('#addInstantPurchase').focus();
                    break;
                case 'addInstantPurchase':
                    $('#vendor').select2('open');
                    break;
            }
            return false;
        }
    });

    $(document).on('click', '#addInstantPurchase', function() {

        var form = $("#instantPurchase").validate({

            rules: {

                "medicineName": {required: true},
                "vendor": {required: true},
                "purchasesBy": {required: true},
                "purchasePrice": {required: true},
                "expirationStartDate": {required: false},
                "expirationEndDate": {required: false},
                "quantity": {required: true}
            },

            messages: {

                "medicineName": "Enter medicine name",
                "vendor": "Select vendor name",
                "purchasesBy": "Enter purchase by medicine",
                "purchasePrice": "Enter purchase price",
                "quantity": "Enter medicine quantity"
            },
            tooltip_options: {
                "medicineName": {placement: 'top', html: true},
                "vendor": {placement: 'top', html: true},
                "purchasesBy": {placement: 'top', html: true},
                "purchasePrice": {placement: 'top', html: true},
                "quantity": {placement: 'top', html: true}
            },

            submitHandler: function (form) {
                $.ajax({
                    url: $('form#instantPurchase').attr('action'),
                    type: $('form#instantPurchase').attr('method'),
                    data: new FormData($('form#instantPurchase')[0]),
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        obj = JSON.parse(response);
                        $('#invoiceParticulars').html(obj['salesItems']);
                        $('#subTotal').html(obj['subTotal']);
                        $('#grandTotal').html(obj['initialGrandTotal']);
                        $('.discount').html(obj['initialDiscount']);
                        $('.dueAmount').html(obj['initialGrandTotal']);
                        $('#salesNetTotal').val(obj['initialGrandTotal']);
                        $('#salesTemporary_discount').val(obj['initialDiscount']);
                        $('#salesTemporary_due').val(obj['initialGrandTotal']);
                        $('#instantPurchase')[0].reset();
                        $("#medicineName").select2("val", "");
                        $("#purchasesBy").select2("val", "");
                    }
                });
            }
        });
    });

    $(document).on("click", ".instantDelete", function() {

        var url = $(this).attr("data-url");
        var id = $(this).attr("id");
        $.get(url, function(data, status){
            $('#removeInstantItem-'+id).hide();
        });

    });

    $(".select2InstantMedicine").select2({

        placeholder: "Search medicine name",
        ajax: {

            url: Routing.generate('medicine_select_search'),
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

        },
        allowClear: true,
        minimumInputLength: 2
    });

    $(".select2StockMedicine").select2({

        placeholder: "Search medicine stock name",
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

    $( ".select2Vendor" ).autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: Routing.generate('medicine_vendor_search'),
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response(data);
                }
            });
        },
        minLength: 1,
        select: function( event, ui ) {
            $("#vendor").val(ui.item.id); // save selected id to hidden input

        }
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
        },
        allowClear: true,
        minimumInputLength: 1
    });

    $( "#expirationStartDate" ).datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
    });

    $( "#expirationEndDate" ).datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
    });


}



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
                    $('#salesTemporaryItem_stockName').select2('open');
                    $('#salesTemporary_received').val('');
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on('change', '#salesitem_stockName', function() {

    var medicine = $('#salesitem_stockName').val();
    $.ajax({
        url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#salesitem_barcode').html(obj['purchaseItems']).focus();
            $('#salesitem_salesPrice').val(obj['salesPrice']);
        }
    })

});


$(document).on("click", ".instantPopup", function() {

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
            $("#instantPurchaseLoad").html(data).show();
            $('#instantPurchasePopup').removeClass("instantPopup").addClass("removePopup");
            jqueryInstantTemporaryLoad();
        }
    });
});
$(document).on("click", ".removePopup", function() {
    $("#instantPurchaseLoad").slideToggle();
    $('#instantPurchasePopup').removeClass("removePopup").addClass("instantPopup");
});

$(document).on("change", "#barcode", function() {
    var barcode = $('#barcode').val();
    if(barcode === ''){
        $('#wrongBarcode').html('<strong>Error!: </strong>Invalid barcode, Please try again.');
        return false;
    }
    url = Routing.generate('medicine_sales_barcode_search');
    $.get(url, {barcode: barcode} , function(response){
        obj = JSON.parse(response);
        $("#addTemporaryItem").attr("disabled", true);
        $('#invoiceParticulars').html(obj['salesItems']);
        $('#subTotal').html(obj['subTotal']);
        $('#grandTotal').html(obj['initialGrandTotal']);
        $('.discount').html(obj['initialDiscount']);
        $('.dueAmount').html(obj['initialGrandTotal']);
        $('#salesSubTotal').val(obj['subTotal']);
        $('#salesNetTotal').val(obj['initialGrandTotal']);
        $('#profit').html(obj['profit']);
        $('#salesTemporary_discount').val(obj['initialDiscount']);
        $('#salesTemporary_due').val(obj['initialGrandTotal']);
        $('#barcode').focus().val('');
    });
});





function jqueryTemporaryLoad() {


    $('#salesTemporary_received').click(function() {
        $('#salesTemporary_received').attr('value', '');
    });

    $(document).on("change", "#genericStock", function() {
        var id = $(this).val();
        if(id === ''){
            return false;
        }
        $.ajax(Routing.generate('medicine_sales_generic_stock', { id : id}), {
            type: 'GET',
        }).done(function (response) {
            obj = JSON.parse(response);
            alert(obj['subTotal']);
            $("#addTemporaryItem").attr("disabled", true);
            $('#invoiceParticulars').html(obj['salesItems']);
            $('#subTotal').html(obj['subTotal']);
            $('#grandTotal').html(obj['initialGrandTotal']);
            $('.discount').html(obj['initialDiscount']);
            $('.dueAmount').html(obj['initialGrandTotal']);
            $('#salesSubTotal').val(obj['subTotal']);
            $('#salesNetTotal').val(obj['initialGrandTotal']);
            $('#profit').html(obj['profit']);
            $('#salesTemporary_discount').val(obj['initialDiscount']);
            $('#salesTemporary_due').val(obj['initialGrandTotal']);
            $('#generic-stock-hide').hide();
            $("#genericStock").select2("val", "");
        });
    });

    $(".selectStock2Generic").select2({

        placeholder: "Search generic by stock medicine",
        ajax: {
            url: Routing.generate('medicine_generic_stock_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    pram: params,
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
        },
        allowClear: true,
        minimumInputLength:2

    });


    $(".addTemporaryCustomer").click(function(){
        $( ".customer" ).slideToggle( "slow" );
    }).toggle( function() {
        $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
    }, function() {
        $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
    });

    $(document).on('change', '#salesTemporaryItem_stockName', function() {

        var medicine = $('#salesTemporaryItem_stockName').val();
        $.ajax({
            url: Routing.generate('medicine_sales_stock_search',{'id':medicine}),
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#salesTemporaryItem_quantity').focus();
                $('#salesTemporaryItem_barcode').html(obj['purchaseItems']).focus();
                $('#addTemporaryItem').html('<i class="fa fa-shopping-cart"></i> Add').attr("disabled", false);
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
                case 'salesTemporaryItem_stockName':
                    $('#salesTemporaryItem_quantity').focus();
                    break;

                case 'salesTemporaryItem_quantity':
                    $('#addTemporaryItem').click();
                    $('#salesTemporaryItem_stockName').select2('open');
                    break;
            }
            return false;
        }
    });

    var formTemporary = $("#salesTemporaryItemForm").validate({

        rules: {

            "salesTemporaryItem[stockName]": {required: true},
            "salesTemporaryItem[purchaseItem]": {required: false},
            "salesTemporaryItem[itemPercent]": {required: false},
            "salesTemporaryItem[salesPrice]": {required: false},
            "salesTemporaryItem[quantity]": {required: false},
        },

        messages: {

            "salesTemporaryItem[medicineStock]":"Enter medicine name",
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
                    $("#addTemporaryItem").attr("disabled", true);
                    $('#invoiceParticulars').html(obj['salesItems']);
                    $('#subTotal').html(obj['subTotal']);
                    $('#grandTotal').html(obj['initialGrandTotal']);
                    $('.discount').html(obj['initialDiscount']);
                    $('.dueAmount').html(obj['initialGrandTotal']);
                    $('#salesSubTotal').val(obj['subTotal']);
                    $('#salesNetTotal').val(obj['initialGrandTotal']);
                    $('#profit').html(obj['profit']);
                    $('#salesTemporary_discount').val(obj['initialDiscount']);
                    $('#salesTemporary_due').val(obj['initialGrandTotal']);
                    $("#salesTemporaryItem_stockName").select2("val", "");
                    $("input#isShort:not(:checked)");
                    $('#salesTemporaryItemForm')[0].reset();
                    $('#addTemporaryItem').html('<i class="fa fa-shopping-cart"></i> Add').attr("disabled", true);
                    $('.salesBtn').prop("disabled", false);
                }
            });
        }
    });

    $(document).on('change', '.quantity , .salesPrice, .itemPercent', function() {

        var id = $(this).attr('data-id');
        var quantity = parseFloat($('#quantity-'+id).val());
        var price = parseFloat($('#salesPrice-'+id).val());
        var estimatePrice = parseFloat($('#estimatePrice-'+id).val());
        var itemPercent = parseFloat($('#itemPercent-'+id).val());
        var amount = (estimatePrice-(estimatePrice*itemPercent/100));
        var subTotal  = (quantity * amount);
        $("#subTotal-"+id).html(subTotal);

        $.ajax({
            url: Routing.generate('medicine_sales_temporary_item_update'),
            type: 'POST',
            data:'salesItemId='+ id +'&quantity='+ quantity +'&salesPrice='+price+'&itemPercent='+itemPercent,
            success: function(response) {
                obj = JSON.parse(response);
                $('#subTotal').html(obj['subTotal']);
                $('#invoiceParticulars').html(obj['salesItems']);
                $('#grandTotal').html(obj['initialGrandTotal']);
                $('.discount').html(obj['initialDiscount']);
                $('.dueAmount').html(obj['initialGrandTotal']);
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
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
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
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
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
                if(obj['initialDiscount'] > obj['subTotal']){
                    $('.salesBtn').prop("disabled", true);
                }
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
                $('#salesSubTotal').val(obj['subTotal']);
                $('#salesNetTotal').val(obj['initialGrandTotal']);
                $('#profit').html(obj['profit']);
                $('#salesTemporary_discount').val(obj['initialDiscount']);
                $('#salesTemporary_due').val(obj['initialGrandTotal']);
                if(obj['subTotal'] === 0){
                    $('.salesBtn').prop("disabled", true);
                }
            }
        })
    });


    $(document).on('keyup', '#salesTemporary_received', function() {

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

    $(document).on("click", "#receiveBtn", function() {

        $('#buttonType').val('receiveBtn');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('.salesBtn').prop("disabled", true);
                $.ajax({
                    url         : $('form#salesTemporaryForm').attr( 'action' ),
                    type        : $('form#salesTemporaryForm').attr( 'method' ),
                    data        : new FormData($('form#salesTemporaryForm')[0]),
                    processData : false,
                    contentType : false,
                    success: function(response){
                        $('#salesTemporaryForm')[0].reset();
                        $('#invoiceParticulars').html('');
                        $('#subTotal').html('');
                        $('#grandTotal').html('');
                        $('.discount').html('');
                        $('.dueAmount').html('');
                        $('#profit').html('');
                        $('#salesNetTotal').val('');
                        $('#salesSubTotal').val('');
                        $('#salesTemporary_discount').val('');
                        $('#salesTemporary_due').val('');
                        $(".select2TemporaryCustomer").select2("val", "");
                        $(".customer").hide();
                        $('#cartMethod , #bkashMethod').css("display","none");

                    }
                });
            }
        });

    });

    $(document).on("click", "#regularPrint", function() {

        $('#buttonType').val('regularBtn');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('.salesBtn').prop("disabled", true);
                $.ajax({
                    url         : $('form#salesTemporaryForm').attr( 'action' ),
                    type        : $('form#salesTemporaryForm').attr( 'method' ),
                    data        : new FormData($('form#salesTemporaryForm')[0]),
                    processData : false,
                    contentType : false,
                    success: function(response){
                        $('#salesTemporaryForm')[0].reset();
                        $('#invoiceParticulars').html('');
                        $('#subTotal').html('');
                        $('#grandTotal').html('');
                        $('.discount').html('');
                        $('.dueAmount').html('');
                        $('#profit').html('');
                        $('#salesNetTotal').val('');
                        $('#salesSubTotal').val('');
                        $('#salesTemporary_discount').val('');
                        $('#salesTemporary_due').val('');
                        $(".select2TemporaryCustomer").select2("val", "");
                        $(".customer").hide();
                        $('#cartMethod , #bkashMethod').css("display","none");
                        window.open('/medicine/sales/'+response+'/print', '_blank');

                    }
                });
            }
        });

    });

    $(document).on("click", "#posBtn", function() {

        $('#buttonType').val('posBtn');
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('.salesBtn').prop("disabled", true);
                $.ajax({
                    url         : $('form#salesTemporaryForm').attr( 'action' ),
                    type        : $('form#salesTemporaryForm').attr( 'method' ),
                    data        : new FormData($('form#salesTemporaryForm')[0]),
                    processData : false,
                    contentType : false,
                    success: function(response){
                        $('#salesTemporaryForm')[0].reset();
                        $('#invoiceParticulars').html('');
                        $('#subTotal').html('');
                        $('#grandTotal').html('');
                        $('.discount').html('');
                        $('.dueAmount').html('');
                        $('#profit').html('');
                        $('#salesNetTotal').val('');
                        $('#salesSubTotal').val('');
                        $('#salesTemporary_discount').val('');
                        $('#salesTemporary_due').val('');
                        $('#cartMethod , #bkashMethod').css("display","none");
                        $(".select2TemporaryCustomer").select2("val", "");
                        $(".customer").hide();
                        jsPostPrint(response);
                    }
                });
            }
        });

    });

    function jsPostPrint(data) {

        if(typeof EasyPOSPrinter == 'undefined') {
            alert("Printer library not found");
            return;
        }
        EasyPOSPrinter.raw(data);
        EasyPOSPrinter.cut();
        EasyPOSPrinter.print(function(r, x){
            console.log(r);
        });
    }

    $(".select2StockMedicine").select2({

        placeholder: "Search medicine name",
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

    $(".select2TemporaryCustomer").select2({

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
}

function jqueryInstantTemporaryLoad(){

    $('#vendor').focus();
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

    $('form#instantPurchase').on('keyup', '#purchaseQuantity', function (e) {
        var mrp = $('#purchaseQuantity').val();
        $('#salesQuantity').val(mrp);
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

                case 'vendor':
                    $('#purchasesBy').select2('open');
                    break;
                case 'purchasesBy':
                    $('#purchaseQuantity').focus();
                    break;
                case 'purchaseQuantity':
                    $('#salesPrice').focus();
                    break;
                case 'salesPrice':
                    $('#addInstantPurchase').click();
                    $('#vendor').focus();
                    break;
            }
            return false;
        }
    });

    $(document).on('click', '#addInstantPurchase', function() {

        var form = $("#instantPurchase").validate({
            rules: {

                "vendor": {required: true},
                "medicineName": {required: true},
                "purchasesBy": {required: false},
                "purchaseQuantity": {required: true},
                "salesPrice": {required: true},
                "expirationStartDate": {required: false},
                "expirationEndDate": {required: false}
            },

            messages: {

                "medicineName": "Enter medicine name",
                "vendor": "Select vendor name",
                "purchasesBy": "Enter purchase by medicine",
                "purchaseQuantity": "Enter medicine quantity",
                "salesPrice": "Enter sales price"
            },
            tooltip_options: {

                "medicineName": {placement: 'top', html: true},
                "vendor": {placement: 'top', html: true},
                "purchasesBy": {placement: 'top', html: true},
                "purchaseQuantity": {placement: 'top', html: true},
                "salesPrice": {placement: 'top', html: true}
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
                        $("#medicineId").val('');
                        $('.salesBtn').prop("disabled", false);
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

    $(".select2InstantMedicine").autocomplete({

        source: function( request, response ) {
            $.ajax( {
                url: Routing.generate('medicine_search'),
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response( data );
                }
            } );
        },
        minLength: 2,
        select: function( event, ui ) {
            $("#medicineId").val(ui.item.id); // save selected id to hidden input

        }

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







function InventoryPurchasePage(){

    $( "#masterItem" ).autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: Routing.generate('inventory_product_masteritem_search'),
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
        }
    });

    $('#addMasterItem').click(function(e) {

        var url =  $('#masterProduct').attr("action");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data : $('#masterProduct').serialize(),
                    success: function (response) {
                        location.reload();
                    },
                });
            }
        });
        e.preventDefault();
    });

    $('#purchaseitem_item').on("select2-selecting", function (e) {
        setTimeout(function () {
            $('#purchaseitem_purchasePrice').focus();
        }, 100)
    });

    $('form#purchaseItemForm').on('keypress', '.itemInput', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {
                case 'purchaseitem_quantity':
                    $('#addPurchaseForm').focus();
                    break;
            }
            return false;
        }
    });



    var validator =  $('form#purchaseItemForm').validate({

        rules: {

            "purchaseitem[item]": {required: true},
            "purchaseitem[purchasePrice]": {required: true},
            "purchaseitem[salesPrice]": {required: true},
            "purchaseitem[quantity]": {required: true},
        },

        messages: {

            "purchaseitem[item]":"Select purchase item name",
            "purchaseitem[purchasePrice]":"Enter purchase price",
            "purchaseitem[salesPrice]":"Enter sales price",
            "purchaseitem[quantity]":"Enter product qnt",
        },

        tooltip_options: {

            "purchaseitem[item]": {placement:'top',html:true},
            "purchaseitem[purchasePrice]": {placement:'top',html:true},
            "purchaseitem[salesPrice]": {placement:'top',html:true},
            "purchaseitem[quantity]": {placement:'top',html:true},

        },
        submitHandler: function() {
            $('#purchaseItemForm').submit();
        }

    });

    $('#addInventory').click(function(e) {
        $( "#inventoryItem" ).fadeToggle();
    });


    $(document).on("click", ".vendorItemDelete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response) {
                   location.reload();
                }
            },
        })
    });

    $('#addStockItem').click(function(e) {

        var url =  $('#inventoryItem').attr("action");

        var masterItem = $('#masterItem').val();
        if( masterItem == "" ){
            alert( "Please add  item name" );
            $('#masterItem').focus();
            return false;
        }
        var color = $('#color').val();
        if( color == "" ){
            alert( "Please add color name" );
            $('#color').focus();
            return false;
        }
        var size = $('#size').val();
        if( size == "" ){
            alert( "Please add size value" );
            $('#size').focus();
            return false;
        }

        var brand = $('#brand').val();
        if( brand == "" ){
            alert( "Please add brand name" );
            $('#brand').focus();
            return false;
        }

        var vendor = $('#vendor').val();
        if( vendor == "" ){
            alert( "Please add vendor name" );
            $('#vendor').focus();
            return false;
        }

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data : $('#inventoryItem').serialize(),
                    success: function (response) {
                        obj = JSON.parse(response);
                        if(obj['status'] == 'valid'){
                            location.reload();
                        }else{
                            alert(obj['message']);
                        }
                    },
                });
            }
        });
        e.preventDefault();
    });

    $(document).on("click", ".purchaseItemDelete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response) {
                   location.reload();

                }
            },
        })
    });

    $('#purchase_totalItem').attr("readonly", true);
    $('#purchase_totalQnt').attr("readonly", true);
    $('#purchase_dueAmount').attr("readonly", true);
    $('#purchase_totalAmount').attr("readonly", true);

    $('form#purchaseForm').on('keypress', '.purchaseInput', function (e) {

        if (e.which === 13) {
            var inputs = $(this).parents("form").eq(0).find("input,select");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus(); //  handles submit buttons
            }
            switch (this.id) {
                case 'purchase_purchaseTo':
                    $('#purchase_memo').focus();
                    break;

                case 'purchase_paymentAmount':
                    $('#purchase_process').focus();
                    break;

                case 'purchase_process':
                    $('#actionButton').focus();
                    break;
            }
            return false;
        }
    });


    $("form#purchaseForm").validate({

        rules: {

            "purchase[vendor]": {required: true},
            "purchase[memo]": {required: true},
            "purchase[totalItem]": {required: false},
            "purchase[totalQnt]": {required: false},
            "purchase[totalAmount]": {required: true},
            "purchase[dueAmount]": {required: false},
            "purchase[paymentAmount]": {required: true},
            "purchase[accountBank]": {required: false},
            "purchase[accountMobileBank]": {required: false},
            "purchase[file]": {required: false},
        },

        messages: {

            "purchase[vendor]":"Enter vendor name",
            "purchase[memo]":"Enter memo or invoice no",
            "purchase[receiveDate]":"Enter receive date",
            "purchase[paymentAmount]":"Enter payment amount",
        },

        tooltip_options: {

            "purchase[vendor]": {placement:'top',html:true},
            "purchase[memo]": {placement:'top',html:true},
            "purchase[totalItem]": {placement:'top',html:true},
            "purchase[totalQnt]": {placement:'top',html:true},
            "purchase[totalAmount]": {placement:'top',html:true},
            "purchase[paymentAmount]": {placement:'top',html:true}

        },
        submitHandler: function() {
            $("#purchaseForm").submit();
        }

    });

    $('#purchase_totalAmount , #purchase_paymentAmount ,#purchase_commissionAmount ').change(function() {

        var totalAmount = ($('#purchase_totalAmount').val());
        total = (totalAmount != '') ? parseInt(totalAmount) : 0;
        var paymentAmount = $('#purchase_paymentAmount').val();
        payment = ( paymentAmount != '') ? parseInt(paymentAmount) : 0;
        var commissionPayment = ($('#purchase_commissionAmount').val());
        commission = (commissionPayment != '') ? parseInt(commissionPayment) : 0;
        //var due = (total -  ( payment + commission));
        var due = (total - payment);
        $('#purchase_dueAmount').val(due);
        if (paymentAmount = ""){
            alert(total);
            $('#purchase_paymentAmount').val(total);
        }

    });


}


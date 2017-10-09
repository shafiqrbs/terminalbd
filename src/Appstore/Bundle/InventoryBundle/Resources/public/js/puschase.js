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

    $('#addStockItem').click(function(e) {

        var url =  $('#inventoryItem').attr("action");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data : $('#inventoryItem').serialize(),
                    success: function (response) {
                        location.reload();
                    },
                });
            }
        });
        e.preventDefault();
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


    $('#addPurchaseForm').click(function(e) {

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('#purchaseItemForm').submit()
            }
        });
        e.preventDefault();
    });

    $(' #action-button').click(function(e) {

        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $('#purchaseForm').submit()
            }
        });
        e.preventDefault();
    });

    $('#addInventory').click(function(e) {
        $( "#inventoryItem" ).fadeToggle( "slow", function() {
            // Animation complete.
        });
    });

    $('#purchase').on("click", ".delete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response) {
                    $('#remove-' + id).hide();
                }
            },
        })
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

    

    $('#appstore_bundle_inventorybundle_purchase_memo').attr("required", true);
    $('#appstore_bundle_inventorybundle_purchase_dueAmount').attr("disabled", true);



    $('#appstore_bundle_inventorybundle_purchase_totalAmount , #appstore_bundle_inventorybundle_purchase_paymentAmount ,#appstore_bundle_inventorybundle_purchase_commissionAmount ').change(function() {

        var totalAmount = ($('#appstore_bundle_inventorybundle_purchase_totalAmount').val());
        total = (totalAmount != '') ? parseInt(totalAmount) : 0;
        var paymentAmount = $('#appstore_bundle_inventorybundle_purchase_paymentAmount').val();
        payment = ( paymentAmount != '') ? parseInt(paymentAmount) : 0;
        var commissionPayment = ($('#appstore_bundle_inventorybundle_purchase_commissionAmount').val());
        commission = (commissionPayment != '') ? parseInt(commissionPayment) : 0;
        //var due = (total -  ( payment + commission));
        var due = (total - payment);
        $('#appstore_bundle_inventorybundle_purchase_dueAmount').val(due);
        if (paymentAmount = ""){
            alert(total);
            $('#appstore_bundle_inventorybundle_purchase_paymentAmount').val(total);
        }

    })

    $('.clone-block').on("change", ".quantity , .purchasePrice ", function() {

        var purchasePrice = $("input[name='purchasePrice[]']").val();
        purchasePrice = (purchasePrice != '') ?  parseInt(purchasePrice) : 0 ;
        var quantity = $("input[name='quantity[]']").val();
        quantity = (quantity != '') ?  parseInt(quantity) : 0 ;
        var subTotal = ( purchasePrice * quantity );
        alert(subTotal);
        $(this).closest('.subTotalPurchase').val(subTotal);
    });

    var validator =  $("from.purchase").validate({

        rules: {

            "appstore_bundle_inventorybundle_purchase[vendor]": {required: true},
            "appstore_bundle_inventorybundle_purchase[memo]": {required: true},
            "appstore_bundle_inventorybundle_purchase[totalItem]": {required: true},
            "appstore_bundle_inventorybundle_purchase[totalQnt]": {required: true},
            "appstore_bundle_inventorybundle_purchase[totalAmount]": {required: true},
            "appstore_bundle_inventorybundle_purchase[paymentAmount]": {required: true}
        },

        messages: {

            "appstore_bundle_inventorybundle_purchase[vendor]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[memo]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[totalItem]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[totalQnt]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[totalAmount]":"Enter your vendor name",
            "appstore_bundle_inventorybundle_purchase[paymentAmount]":"Enter your vendor name"
        },

        tooltip_options: {

            "appstore_bundle_inventorybundle_purchase[vendor]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[memo]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[totalItem]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[totalQnt]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[totalAmount]": {placement:'top',html:true},
            "appstore_bundle_inventorybundle_purchase[paymentAmount]": {placement:'top',html:true}

        },
        submitHandler: function() {

            $(this).submit();

        }

    });

   /* $('#action-buttonxx').click( function( e ) {

        var name_regex = /^[a-zA-Z]+$/;
        var number_regex = /^[0-9]+$/;

        var vendor =  $('#appstore_bundle_inventorybundle_purchase_vendor').val();
        var totalAmount =  $('#appstore_bundle_inventorybundle_purchase_totalAmount').val();
        var paymentAmount =  $('#appstore_bundle_inventorybundle_purchase_paymentAmount').val();
        var memo =  $('#appstore_bundle_inventorybundle_purchase_memo').val();
        var totalItem =  $('#appstore_bundle_inventorybundle_purchase_totalItem').val();
        var totalQnt =  $('#appstore_bundle_inventorybundle_purchase_totalQnt').val();


        if(vendor == ""){
            $('#error-msg').text("Please add your vendor name"); //this segment displays the validation rule for selection
            $("#appstore_bundle_inventorybundle_purchase_vendor").focus();
            return false;
        }else if(memo ==""){
            $('#error-msg').text("Please add purchase memo no"); //this segment displays the validation rule for selection
            $("#appstore_bundle_inventorybundle_purchase_memo").focus();
            return false;
        }else if(!totalItem.match(number_regex)){
            $('#error-msg').text("Please add purchase total item");
            $("#appstore_bundle_inventorybundle_purchase_totalItem").focus();
            return false;
        }else if(!totalQnt.match(number_regex) || totalQnt.length == 0){
            $('#error-msg').text("Please add purchase total quantity"); //this segment displays the validation rule for selection
            $("#appstore_bundle_inventorybundle_purchase_totalQnt").focus();
            return false;

        }else if(!totalAmount.match(number_regex) || totalAmount.length == 0){
            $('#error-msg').text("Please add purchase total amount"); //this segment displays the validation rule for selection
            $("#appstore_bundle_inventorybundle_purchase_totalAmount").focus();
            return false;
        }else if(!paymentAmount.match(number_regex) || paymentAmount.length == 0){
            $('#error-msg').text("Please add payment amount"); //this segment displays the validation rule for selection
            $("#appstore_bundle_inventorybundle_purchase_paymentAmount").focus();
            return false;
        }else{
            $('.purchase').submit();
        }
        e.preventDefault();

    });*/

    var count = 0;
    $('.addmore').click(function(){
        var $el = $(this);
        $vendor_id = $el.data('ref-id');
        var $cloneBlock = $('#clone-block-'+ $vendor_id);

        var $clone = $cloneBlock.find('.clone:eq(0)').clone();
        $clone.find('[id]').each(function(){this.id+='someotherpart'});
        $clone.find(':text,textarea' ).val("");
        $clone.attr('id', "added"+(++count));
        $clone.find('.remove').removeClass('hidden');
        $cloneBlock.append($clone);
        $('.numeric').numeric();
    });

    $('form.purchase').on('click', '.remove', function(){
        $(this).closest('.clone').remove();
    });
}


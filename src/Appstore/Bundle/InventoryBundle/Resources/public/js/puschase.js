function InventoryPurchasePage(){

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
    })

    $('#purchaseVendorItem').on("click", ".delete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response) {
                    $('#remove-vendor-item-' + id).hide();
                }
            },
        })
    })

    $('#purchaseItem').on("click", ".delete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response) {
                    $('#remove-purchase-item-' + id).hide();
                }
            },
        })
    })

    $(document).on("click", ".approve", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("data-title");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                if ('success' == obj['success']) {
                    $('#action-' + id).append('<a title="Approve" href="javascript:" class="btn green mini" ><i class="icon-check"></i>&nbsp;Approved</a>');
                    $('.approved-' + id).remove();
                }
            },
        })

    })

    $('#appstore_bundle_inventorybundle_purchase_memo').attr("required", true);
    $('#appstore_bundle_inventorybundle_purchase_dueAmount').attr("disabled", true);



    $('#appstore_bundle_inventorybundle_purchase_totalAmount , #appstore_bundle_inventorybundle_purchase_paymentAmount ,#appstore_bundle_inventorybundle_purchase_commissionAmount ').change(function(){

        var totalAmount = ($('#appstore_bundle_inventorybundle_purchase_totalAmount').val());
        total = (totalAmount != '') ?  parseInt(totalAmount) : 0 ;
        var paymentAmount = $('#appstore_bundle_inventorybundle_purchase_paymentAmount').val();
        payment = ( paymentAmount != '') ?  parseInt(paymentAmount) : 0 ;
        var commissionPayment = ($('#appstore_bundle_inventorybundle_purchase_commissionAmount').val());
        commission = (commissionPayment != '') ?  parseInt(commissionPayment) : 0 ;
        //var due = (total -  ( payment + commission));
        var due = (total -  payment);
        $('#appstore_bundle_inventorybundle_purchase_dueAmount').val(due);

    })

    $('.clone-block').on("change", ".quantity , .purchasePrice ", function() {

        var purchasePrice = $("input[name='purchasePrice[]']").val();
        purchasePrice = (purchasePrice != '') ?  parseInt(purchasePrice) : 0 ;
        var quantity = $("input[name='quantity[]']").val();
        quantity = (quantity != '') ?  parseInt(quantity) : 0 ;
        var subTotal = ( purchasePrice * quantity );
        alert(subTotal);
        $(this).closest('.subTotalPurchase').val(subTotal);
    })

    $('#action-button').click( function( e ) {

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
        }else if(!totalAmount.match(number_regex) || totalAmount.length == 0){
            $('#error-msg').text("Please add purchase total amount"); //this segment displays the validation rule for selection
            $("#appstore_bundle_inventorybundle_purchase_totalAmount").focus();
            return false;
        }else if(!paymentAmount.match(number_regex) || paymentAmount.length == 0){
            $('#error-msg').text("Please add payment amount"); //this segment displays the validation rule for selection
            $("#appstore_bundle_inventorybundle_purchase_paymentAmount").focus();
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
        }else{
            $('.purchase').submit();
        }
        e.preventDefault();

    });

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
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
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
}


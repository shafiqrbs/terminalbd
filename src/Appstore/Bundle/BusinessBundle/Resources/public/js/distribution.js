$("#addStore").click(function(){
    $( "#storeLedger" ).slideToggle( "slow" );
}).toggle( function() {
    $(this).removeClass("purple").addClass("red").html('<i class="icon-remove"></i>');
}, function() {
    $(this).removeClass("red").addClass("purple").html('<i class="icon-user"></i>');
});



var storeForm = $("#storeForm").validate({

    rules: {
        "store": {required: true},
        "amount": {required: true},
    },
    tooltip_options: {
        "store": {placement:'top',html:true},
        "amount": {placement:'top',html:true},
    },
    submitHandler: function(storeForm) {

        $.ajax({
            url         : $('form#storeForm').attr( 'action' ),
            type        : $('form#storeForm').attr( 'method' ),
            data        : new FormData($('form#storeForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                $('#storeLedgerItem').html(response);
                document.getElementById('storeLedgerForm').reset();
            }
        });
    }
});

var salesReturnForm = $("#salesReturnForm").validate({

    rules: {
        "returnItem": {required: true},
        "quantity": {required: true},
        "amount": {required: true},
    },
    tooltip_options: {
        "returnItem": {placement:'top',html:true},
        "quantity": {placement:'top',html:true},
        "amount": {placement:'top',html:true},
    },

    submitHandler: function(salesReturnForm) {

        $.ajax({
            url         : $('form#salesReturnForm').attr( 'action' ),
            type        : $('form#salesReturnForm').attr( 'method' ),
            data        : new FormData($('form#salesReturnForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                obj = JSON.parse(response);
                $('#salesReturnItem').html(response);
                $('#subTotal').html(obj['subTotal']);
                $('#salesReturn').html(obj['salesReturn']);
                $('.netTotal').html(obj['netTotal']);
                document.getElementById('salesReturnForm').reset();
            }
        });
    }
});

var formLedger = $("#storeLedgerForm").validate({

    rules: {
        "store": {required: true},
        "storeMobile": {required: true},
        "dsm": {required: true},
        "area": {required: true},
    },
    tooltip_options: {
        "store": {placement:'top',html:true},
        "storeMobile": {placement:'top',html:true},
        "dsm": {placement:'top',html:true},
        "area": {placement:'top',html:true},
    },

    submitHandler: function(formLedger) {

        $.ajax({
            url         : $('form#storeLedgerForm').attr( 'action' ),
            type        : $('form#storeLedgerForm').attr( 'method' ),
            data        : new FormData($('form#storeLedgerForm')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                $('#store').html(response);
                document.getElementById('storeLedgerForm').reset();
            }
        });
    }
});

$(document).on("click", ".returnItemDelete", function(event) {

    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(el) {
            $.get(url, function( response ) {
                $(event.target).closest('tr').remove();
                obj = JSON.parse(response);
                $('#salesReturnItem').html(response);
                $('#subTotal').html(obj['subTotal']);
                $('#salesReturn').html(obj['salesReturn']);
                $('.netTotal').html(obj['netTotal']);
            });
        }
    });
});

$(document).on("click", ".ledgerDelete", function(event) {

    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(el) {
            $.get(url, function( data ) {
                $(event.target).closest('tr').remove();
                $('#storeLedgerItem').html(response);
            });
        }
    });
});




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
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                document.getElementById('storeLedgerForm').reset();
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



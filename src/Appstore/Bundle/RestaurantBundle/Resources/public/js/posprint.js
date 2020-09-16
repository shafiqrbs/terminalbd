$(document).on("click", "#kitchenBtn", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                jsPostPrint(response);
                setTimeout(pageRedirect(),3000);
            });
        }
    });
});

$(document).on("click", ".paymentReceive", function() {
    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('#paymentDone-'+id).remove();
            $.get(url, function( response ) {
               jsPostPrint(response);
            });
        }
    });
});

function pageRedirect() {
    window.location.href = "/restaurant/invoice/new";
}

function jsPostPrint(data) {

    if(typeof EasyPOSPrinter == 'undefined') {
        alert("Printer library not found");
        return;
    }
    EasyPOSPrinter.raw(data);
    EasyPOSPrinter.cut();
    EasyPOSPrinter.print(function(r, x){
        console.log(r)
    });
}



$(document).on("keyup", ".input-number", function() {
    var sum = 0;
    var dataId = $(this).attr("data-id");
    var price = $(this).attr("data-value");
    var quantity = $(this).val();
    var amount = (price * quantity);
    $("#subTotal-"+dataId).html(amount);
    $("#sub-"+dataId).val(amount);
    $(".subTotal").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#total").html(sum);
    $("#grandTotal").val(sum);
    if(sum > 0){
        $("#submitBtn").attr("disabled", false);
    }else{
        $("#submitBtn").attr("disabled", true);
    }

});


$(document).on("click", ".approve", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $("#purchaseReturn").submit();
        }
    });
});
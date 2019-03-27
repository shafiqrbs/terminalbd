function AccountingApproveProcess(){}

$('.horizontal-form').submit(function(){
    $("button[type='submit']", this)
        .html("Please Wait...")
        .attr('disabled', 'disabled');
    return true;
});

$(document).on("click", ".delete", function() {
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url).done(function( data ) {
                location.reload();
            });
        }
    });
});

$(document).on("click", "#cash-reconciliation", function() {
    var particular = $('#particular').val();
    var amount = $('#amount').val();
    var method = $('#method').val();
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url,{particular:particular,amount:amount,method:method}).done(function( data ) {
                location.reload();
            });
        }
    });
});


$('.amount').on('click', function(event) {
    $(this).val('');
});

$(document).on('keyup', ".amount", function() {
    var sum = 0;
    $(".amount").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#total").html(sum);
});
$(document).on('keyup', ".bankAmount", function() {
    var sum = 0;
    $(".bankAmount").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#bankTotal").html(sum);
});
$(document).on('keyup', ".mobileAmount", function() {
    var sum = 0;
    $(".mobileAmount").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#mobileTotal").html(sum);
});

$(document).on("change", ".updateAmount", function() {
    var amount = $(this).val();
    var url = $(this).attr("data-url");
    $.get(url,{amount:amount});
});



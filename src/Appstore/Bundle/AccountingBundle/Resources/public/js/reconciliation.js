function AccountingApproveProcess(){}

$('.horizontal-form').submit(function(){
    $("button[type='submit']", this)
        .html("Please Wait...")
        .attr('disabled', 'disabled');
    return true;
});

$(document).on("click", ".delete", function() {
    var url = $(this).attr('data-url');
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

$(document).on("click", ".approve", function() {

    $(this).removeClass('approve');
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                 location.reload();
            });
        }
    });

});

var count = 0;
$('.addmore').click(function(){

    var $el = $(this);
    var $cloneBlock = $('#clone-block');
    var $clone = $cloneBlock.find('.clone:eq(0)').clone();
    $clone.find('[id]').each(function(){this.id+='someotherpart'});
    $clone.find(':text,textarea' ).val("");
    $clone.attr('id', "added"+(++count));
    $clone.find('.remove').removeClass('hidden');
    $cloneBlock.append($clone);
    $('.numeric').numeric();
});

$('#clone-block').on('click', '.remove', function(){
    $(this).closest('.clone').remove();
});

$('.trash').on("click", ".remove", function() {

    var url = $(this).attr('data-url');
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





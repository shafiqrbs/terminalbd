var searchSubItemConfig = function (subdomain,product) {

}

$(document).on( "change", ".modalChange", function( e ) {
    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        success: function(response) {
            $('.modal-content').html(response);
        },

    })

});

$(document).on( "change", ".changeSize", function( e ) {
    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        beforeSend: function() {
            $('#subItemDetails').show().addClass('loading').fadeIn(3000);
        },
        success: function(response) {
            $('#subItemDetails').html(response);
            $('#subItemDetails').removeClass('loading');
        },

    })

});

$(document).on( "change", ".changeCartSize", function( e ) {
    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        beforeSend: function() {
            $('#subItemDetails').show().addClass('loading').fadeIn(3000);
        },
        success: function(response) {
            $('#subItemDetails').html(response);
            $('#subItemDetails').removeClass('loading');
        },

    })

});

$('.addCart').submit( function( e ) {

    var url = $('.cartSubmit').attr("data-url");
    alert(url);
    $.ajax({
        url:url ,
        type: 'POST',
        data: new FormData( this ),
        processData: false,
        contentType: false,
        success: function(response) {
            alert('success');
        }
    });
    e.preventDefault();

});

$('.preview').click(function () {

    var url = $(this).attr("data-url");
    $('.modal-body').html('');

    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('.modal-content').html(response);
            $('#myModal').modal('toggle')
        }
    })
});

/*
$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0"
});

*/


/*
$(document).on("click", ".editable-submit", function() {
    setTimeout(pageReload, 3000);
});
function pageReload() {
    location.reload();
}
*/


$(document).on('click', '#booking', function() {

    var title = $(this).attr('data-title');
    $('.dialogModal_header').html('');
    $('.overview-title').html(title);
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('hotel_booking'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    jqueryTemporaryLoad();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

function jqueryTemporaryLoad() {

    var dateToday = new Date();
    var dates = $("#bookingStartDate, #bookingEndDate").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 2,
        dateFormat: "dd-mm-yy",
        minDate: dateToday,
        onSelect: function(selectedDate) {
            var option = this.id == "bookingStartDate" ? "minDate" : "maxDate",
                instance = $(this).data("datepicker"),
                date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
            dates.not(this).datepicker("option", option, date);
        }
    });

    $(".booking-roomx").click(function(){
        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $.get(url, function( data ) {
            $('#room-'+id).html(data).slideToggle( "slow" );
        });
    }).toggle( function() {
        $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
    }, function() {
        $(this).removeClass("red").addClass("blue").html('<i class="icon-user"></i>');
    });


    $(document).on("click", ".booking-room", function(e) {

        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $.get(url, function( data ) {
            $('#room-'+id).html(data);
        });
    });

    $(document).on("click", ".booking-form", function(e) {
        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $.get(url, function( data ) {
            $('#form-'+id).html(data);
        });
    });

    $(document).on("click", ".room-cancel", function(e) {
        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        $.get(url, function( data ) {
            $('form#stockInvoice-'+id)[0].reset();
        });
    });

    $(document).on("click", ".room-close", function(e) {
        var id = $(this).attr('data-id');
        $('#room-close-'+id).slideUp();
    });


    $(document).on('change', '.bookingEndDate', function(e) {

        var particular = $(this).attr('data-id');
        var startDate = $('#bookingStartDate-'+particular).val();
        var endDate = $('#bookingEndDate-'+particular).val();
        if(startDate !== '' && endDate  !== '' ){
            $.ajax({
                url: Routing.generate('hotel_particular_search',{'id':particular,'startDate':startDate,'endDate':endDate}),
                type: 'POST',
                success: function (response) {
                    obj = JSON.parse(response);
                    if(obj['msg'] === 'valid'){
                        $('#addRoom-'+particular).prop('disabled', false);
                    }else{
                        alert(obj['msg']);
                        var startDate = $('#bookingStartDate-'+particular).val('');
                        var endDate = $('#bookingEndDate-'+particular).val('');
                        $('#addRoom-'+particular).prop('disabled', true);

                    }
                }
            })
        }

    });

    $(document).on("click", "#bookingSearch", function(e) {
        var url = $(this).attr('data-url');
        var bookingStartDate = $('#bookingStartDate').val();
        var bookingEndDate = $('#bookingEndDate').val();
        var process = $('#processStatus').val();
        var category = $('#category').val();
        if(bookingStartDate === "" || bookingStartDate === "" ){
            return false;
        }
        $.get(url,{'bookingStartDate':bookingStartDate,'bookingEndDate':bookingEndDate,'process':process,'category':category}, function( response ) {
            obj = JSON.parse(response);
            $('#bookingLoad').html(obj['data']);
            $('#date').html(obj['date']);
        });
    });


    $(document).on('click', '.booking-submit', function(e) {

        var particular = $(this).attr('data-id');
        $.ajax({
            url         : $('form#stockInvoice-'+particular).attr( 'action' ),
            type        : $('form#stockInvoice-'+particular).attr( 'method' ),
            data        : new FormData($('form#stockInvoice-'+particular)[0]),
            processData : false,
            contentType : false,
            success: function(response){
               alert('This room is booked temporarily.');
            }
        });
    })

}



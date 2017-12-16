
$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});
var dateToday = new Date();
$( ".datePicker" ).datepicker({
    dateFormat: "dd-mm-yy",
    minDate: dateToday
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$(document).on( "click", "#show", function(e){
    $('#hide').slideToggle(2000);
    $("i", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
});


$(document).on("click", ".confirmSubmit", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('form').submit();
        }
    });

});

$(document).on("click", ".confirm", function() {
    var url = $(this).attr('data-url');
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



$(document).on("click", ".delete", function() {
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



var table = $('#attendance').DataTable( {
    scrollY:        "auto",
    scrollX:        true,
    scrollCollapse: true,
    paging:         false,
    bInfo : false,
    fixedColumns:   {
        leftColumns: 1,
        rightColumns: 1
    }
});

$(document).on("click", ".attendance", function() {

    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    var present = $('#'+id).val();
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get( url,{present:present})
                .done(function(data){
                    $('this').prop('checked',true);
            });
        },
        onClose:function(el){
            $(this).prop( "checked", false ).removeAttr('checked');
        }
    });
});

$(".select2User").select2({

    ajax: {

        url: Routing.generate('domain_user_search'),
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
    },
    allowClear: true,
    minimumInputLength: 1
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
    formatResult: function (item) { return item.text}, // omitted for brevity, see the source of this page
    formatSelection: function (item) { return item.text }, // omitted for brevity, see the source of this page
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

function ApproveProcess(){


    $( ".date-picker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
    // Getter
    var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

    // Setter
    $( ".date-picker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

    $(document).on("click", ".delete", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    $('#remove-' + id).remove();
                }
            },
        })

    })

    $(document).on("click", ".approve", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#action-'+id).hide();
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    location.reload();
                }
            },
        })

    })


    $(".select2Category").select2({

        placeholder: "Search by product category",
        ajax: {
            url: Routing.generate('inventory_category_search'),
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
            $.ajax(Routing.generate('inventory_category_name', { category : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength: 1
    });

}


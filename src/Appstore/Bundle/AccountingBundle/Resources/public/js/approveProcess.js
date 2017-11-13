function AccountingApproveProcess(){


    $( ".date-picker" ).datepicker({dateFormat: "dd-mm-yy"});
    // Getter
    var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );
    // Setter
    $( ".date-picker" ).datepicker( "option", "dateFormat", "dd-mm-yy" );


    $( ".dateCalendar" ).datepicker({ dateFormat: "dd-mm-yy" });
    // Getter
    $( ".dateCalendar" ).datepicker( "option", "dateFormat" );
    // Setter
    $( ".dateCalendar" ).datepicker( "option", "dateFormat", "dd-mm-yy" );

    $(document).on("click", ".editable-submit", function() {
        setTimeout(pageReload, 3000);
    });
    function pageReload() {
        location.reload();
    }

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

        $(this).removeClass('approve');
        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#delete-'+id).hide();
        $('#action-'+id).hide();
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                location.reload();
            },
        })

    });

    $(".approvex").confirm({

        text: "Are you sure you want to approve this operation?",
        title: "Confirmation required",
        confirm: function(button) {

            $(this).removeClass('approve');
            var id = $(this).attr("data-id");
            var url = $(this).attr("data-url");
            $('#action-'+id).hide();
            $('#delete-'+id).hide();
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    location.reload();
                },
            })
        },
        cancel: function(button) {
            // nothing to do
        },
        confirmButton: "Yes I am",
        cancelButton: "No",
        post: true,
        confirmButtonClass: "btn-danger",
        cancelButtonClass: "btn-default",
        dialogClass: "modal-dialog modal-lg" // Bootstrap classes for large modal
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
            $.ajax(Routing.generate('domain_user_name', { user : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
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

    $(".select2Customer").select2({

        ajax: {

            url: Routing.generate('domain_customer_search'),
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
            $.ajax(Routing.generate('domain_customer_name', { user : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 3
    });

    $(".select2Invoice").select2({

        ajax: {

            url: Routing.generate('inventory_sales_invoice_search'),
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
            $.ajax(Routing.generate('inventory_sales_invoice_name', { user : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 1
    });


}


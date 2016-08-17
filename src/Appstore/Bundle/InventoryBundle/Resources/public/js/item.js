var InventoryItemListPage = function () {

    window.prepareBarCode = function () {
        var itemArr = $('input.barcode:checked').map(function () {
            return $(this).val();
        }).get();
        $.cookie('barcodes', itemArr, {path: '/'});
        return true;
    }

    $(".select2Product").select2({

        placeholder: "Search product name",
        ajax: {

            url: Routing.generate('inventory_product_search'),
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
            $.ajax(Routing.generate('inventory_product_name', { product : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength: 1
    });

    $(".select2Color").select2({

        placeholder: "Search color name",
        ajax: {

            url: Routing.generate('inventory_itemcolor_search'),
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
            $.ajax(Routing.generate('inventory_itemcolor_name', { color : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength: 1
    });

    $(".select2Size").select2({

        placeholder: "Search size name",
        ajax: {

            url: Routing.generate('inventory_itemsize_search'),
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
            $.ajax(Routing.generate('inventory_itemsize_name', { size : id}), {
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

    $(".select2Brand").select2({

        placeholder: "Search brand name",
        ajax: {

            url: Routing.generate('branding_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100 ,

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
            $.ajax(Routing.generate('branding_search_name', { brand : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength: 1

    });

    $(".select2Category").select2({

        placeholder: "Search category name",
        ajax: {

            url: Routing.generate('category_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100 ,

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
            $.ajax(Routing.generate('category_search_name', { brand : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });


        },
        allowClear: true,
        minimumInputLength: 1

    });

}

var InventoryItemEditPage = function (item) {
    /**
     * Created by rbs on 2/12/16.
     */
    if (item > 0) {

        $("#pluploadUploader").pluploadQueue({

            // General settings
            runtimes: 'gears,browserplus,html5',
            url: Routing.generate('inventory_vendoritem_gallery', {item: item}),
            max_file_size: '10mb',
            chunk_size: '2mb',
            unique_names: true,
            sortable: true,
            // Resize images on clientside if we can
            resize: {width: 1024, height: 1024, quality: 90},
            // Specify what files to browse for
            filters: [
                {title: "Image files", extensions: "jpeg,jpg,gif,png"},
                {title: "Zip files", extensions: "zip"}
            ],

            // Flash settings
            flash_swf_url: 'theme/scripts/plupload/js/plupload.flash.swf',

            // Silverlight settings
            silverlight_xap_url: 'theme/scripts/plupload/js/plupload.silverlight.xap',
            init : {
                FilesAdded: function(up, files) {
                    var maxfiles = 2;
                    if(up.files.length > maxfiles )
                    {
                        up.splice(maxfiles);
                        alert('no more than '+maxfiles + ' file(s)');
                    }
                    if (up.files.length === maxfiles) {
                        $('#pluploadUploader').hide("slow"); // provided there is only one #uploader_browse on page
                    }
                }
            }

        });

    }

    $(document).on("click", ".barcode", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
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

    var count = 0;

    $('.addmore').click(function(){

        var $el = $(this);
        alert($el);
        var $cloneBlock = $('#clone-block-1');
        var $clone = $cloneBlock.find('.clone:eq(0)').clone();
        $clone.find('[id]').each(function(){this.id+='someotherpart'});
        $clone.find(':text,textarea' ).val("");
        $clone.attr('id', "added"+(++count));
        $clone.find('.remove').removeClass('hidden');
        $cloneBlock.append($clone);
        $('.numeric').numeric();
    });

    $('form.addPurchase').on('click', '.remove', function(){
        $(this).closest('.clone').remove();
    });


}


    $('.modalOpen').click(function(){
        var id = $(this).attr('id');
        var inst = $("[data-remodal-id=modal"+id+"]").remodal();
        inst.open();

    });




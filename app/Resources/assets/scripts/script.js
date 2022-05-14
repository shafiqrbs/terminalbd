/**
 Core script to handle the entire theme and core functions
 **/
var AppScript = function () {
    $('.select2').select2();
    $('.selectbox').selectBox();
    $('.selectbox select').selectBox();
    $(".numeric").numeric();
    $(".mask_number").numeric();
    $(".invoice-no").inputmask("mask", {"mask": "9999-99-99-9999"}); //specifying fn & options
    $(".batch-year").inputmask("mask", {"mask": "9999"}); //specifying fn & options
    $(".mobile").inputmask("mask", {"mask": "99999-999-999"}); //specifying fn & options
    $(".phone").inputmask("mask", {"mask": "99-999999"}); //specifying fn & options
    $("#schedule").inputmask("mask", {"mask":"dd/mm/yyyy"}); //specifying fn & options
    $( ".date-picker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
    $( ".datePicker" ).datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true
    }).inputmask('dd/mm/yyyy');

    $('.multiselect').multiselect();

    $('.toggle').click(function(){
        var id = $(this).attr('id');
        $("#show-"+id).slideToggle(100);
    }).toggle( function() {
        $(this).children("span").text("[-]");
    }, function() {
        $(this).children("span").text("[+]");
    });

    $('.current_ancestor').addClass('active');

    $('.mobileBankHide').hide();
    $('.bankHide').hide();
    $(document).on('change', '.transactionMethod', function() {

        var transactionMethod = $(this).val();
        if(transactionMethod == 2 ){
            $('.bankHide').show(500);
            $('.mobileBankHide').hide(500);
        }else if(transactionMethod == 3 ){
            $('.bankHide').hide(500);
            $('.mobileBankHide').show(500);
        }else{
            $('.bankHide').hide(500);
            $('.mobileBankHide').hide(500);
        }

    });

    /*$('.horizontal-form').submit(function(){
        $("button[type='submit']", this)
            .attr('disabled', 'disabled');
        setTimeout(function(){
            $('.pull-right').find('button').removeAttr('disabled')
        }, 3000);
        return true;
    });

    $('.form-horizontal').submit(function(){
        $("button[type='submit']", this)
            .attr('disabled', true);
            setTimeout(function(){
                $('.pull-right').find('button').removeAttr('disabled')
            }, 3000);
        return true;
    });
*/
    $('.horizontal-form').submit(function(){
        $("button[type='submit']", this)
            .html("Please Wait...")
            .attr('disabled', 'disabled');
        //setTimeout(function(){ location.reload() }, 3000);
        setTimeout(function(){
            $("button[type='submit']", this).removeAttr('disabled')
        }, 3000);
        return true;
    });

    $('.form-horizontal').submit(function(){
        $("button[type='submit']", this)
            .html("Please Wait...")
            .attr('disabled', 'disabled');
        //setTimeout(function(){ location.reload() }, 3000);
        setTimeout(function(){
            $("button[type='submit']", this).removeAttr('disabled')
        }, 3000);
        return true;
    });

    $(document).on('change', '.targetTo', function() {
        var targetTo = $(this).val();
        $('.hide').hide();
        $('#'+targetTo).show();
    });

    $(document).on('click', '#show-messenger', function() {

        $('#messenger-block').show();
        $('#calculator-block').hide();
    });

    $('#fb-msg-close').click(function(){

        $('#messenger-block').hide();
    });
    $( ".select2GenericMedicine" ).autocomplete({

        source: function( request, response ) {
            $.ajax( {
                url: Routing.generate('medicine_search_medicine_generic_complete'),
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response( data );
                }
            } );
        },
        minLength: 2,
        select: function( event, ui ) {
            $("#medicineId").val(ui.item.id); // save selected id to hidden input

        }
    });

    $('.mobile-nav').click(function(){
        $('.nav-collapse').toggleClass("mob-nav-collapse");
    });

    $('.dataTable').DataTable( {
        iDisplayLength: 25,
        scrollY:        '50vh',
        scrollX: false,
        scrollCollapse: true,
        paging:         false,
        bInfo : false,
        orderable: true,
        bSort: true,
        aoColumnDefs: [
            {
                bSortable: false,
                aTargets: [ -1 ]
            }
        ]
    });

}();
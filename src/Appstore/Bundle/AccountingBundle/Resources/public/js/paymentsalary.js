
/**
 * Created by rbs on 2/9/16.
 */

var PaymentSalary = function() {


    $(document).on('change', '#appstore_bundle_accountingBundle_paymentsalary_salarySetting', function() {

        var id = $('#appstore_bundle_accountingBundle_paymentsalary_salarySetting').val();
        if(id === ''){
            return false;
        }
        $.ajax({
            url: Routing.generate('account_salarysetting_salaryAmount'),
            type: 'POST',
            data:'id='+id,
            success: function(response) {
                   $('#salaryAmount').val(response);
                   $('#totalAmount').val(response);
                   $('#appstore_bundle_accountingBundle_paymentsalary_totalAmount').val(response);
            },
        })
    })

    $(document).on('change', ' #appstore_bundle_accountingBundle_paymentsalary_otherAmount', function() {

        var payment     = parseInt($('#salaryAmount').val()  != '' ? $('#salaryAmount').val() : 0 );
        var other    = parseInt($('#appstore_bundle_accountingBundle_paymentsalary_otherAmount').val() != '' ? $('#appstore_bundle_accountingBundle_paymentsalary_otherAmount').val() : 0);
        var netAmount   = (payment + other);
        $('#totalAmount').val(netAmount);
        $('#appstore_bundle_accountingBundle_paymentsalary_totalAmount').val(netAmount);

    })

    $('#payment').on("click", ".delete", function() {

        alert('dfsdf');
        var url = $(this).attr("data-url");
        var id = $(this).attr("data-id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    $('#remove-' + id).hide();
                }
            },
        })
    })



}


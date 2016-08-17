function CommonJs(){

    alert('test');
    $('.checkbox :checkbox').rcSwitcher({

        inputs:false,
        onText: 'Yes',
        offText: 'No',
        theme: 'modern',


    });

    $('#commentform').validate({

        rules: {
            'Core_userbundle_user[profile][mobile]': {
                required: true,
                url: true,
                remote: {
                    url: Routing.generate('bindu_user_check'),
                    type: "post",
                    data:
                    {
                        subDomain: function()
                        {
                            return $('#commentform :input[name="Core_userbundle_user[profile][mobile]"]').val();
                        }
                    }
                }
            },
        },
        messages:{
            'Core_userbundle_user[profile][mobile]':{
                required: "Please enter your choseable sub domain name.",
                remote: jQuery.validator.format("{0} is already taken.")
            }
        },
        submitHandler: function(form) {
            $.ajax({
                url:'',
                type: "POST",
                beforeSend: function() {
                    $('.ajax-loading').show().addClass('loading').fadeIn(3000);
                },
                data: $('form.option').serialize(),
                success: function(response) {
                    $('.ajax-loading').fadeOut(3000);
                    $('.main-setting').fadeOut(3500);
                },
                complete: function(){
                    document.location.reload();
                }
            });
        }

    });


}


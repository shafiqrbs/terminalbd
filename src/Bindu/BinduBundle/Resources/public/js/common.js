function indexFormValidation(){

    $('.checkbox :checkbox').rcSwitcher({

        inputs:false,
        onText: 'Yes',
        offText: 'No',
        theme: 'modern'

    });

    var validator =  $("#signupxx").validate({

        rules: {

            "Core_userbundle_user[profile][name]": {required: true},
            "Core_userbundle_user[profile][mobile]": {
                required: true,
                remote: Routing.generate('bindu_signup_check')
            },
            "Core_userbundle_user[email]": {required: false , email:true,},
            "Core_userbundle_user[profile][location]": {required: true},
            "Core_userbundle_user[profile][address]": {required: true},
            "Core_userbundle_user[profile][termsConditionAccept]": {required: true},
        },

        messages: {

            "Core_userbundle_user[profile][name]":"Enter your full name",
            "Core_userbundle_user[profile][mobile]":{
                required: "Enter valid mobile no",
                remote: "This mobile no is already registered. Please try to login."
            },
            "Core_userbundle_user[email]":{
                required: "Enter valid email address",
                remote: "This username is already taken! Try another."
            },
            "Core_userbundle_user[profile][location]": "Enter your location",
            "Core_userbundle_user[profile][address]": "Enter your present address",
            "Core_userbundle_user[profile][termsConditionAccept]": "Please read terms & condition and agree",
        },

        tooltip_options: {

            "Core_userbundle_user[profile][name]": {placement:'top',html:true},
            "Core_userbundle_user[profile][mobile]": {placement:'top',html:true},
            "Core_userbundle_user[email]": {placement:'top',html:true},
            "Core_userbundle_user[profile][location]": {placement:'top',html:true},
            "Core_userbundle_user[profile][address]": {placement:'top',html:true},
            "Core_userbundle_user[profile][termsConditionAccept]":{placement:'right',html:true},
        },
        submitHandler: function(form) {

            $.ajax({

                url         : $(form).attr( 'action' ),
                type        : $(form).attr( 'method' ),
                data        : new FormData(form),
                processData : false,
                contentType : false,
                success: function(response) {
                    if(response == 'valid'){
                        $('#registerModal').modal('hide');
                        $('#forgetModal').modal('hide');
                        $('#loginModal').modal('toggle');
                    }
                },
                complete: function(){

                }
            });
        }
    });

    $('#Core_userbundle_user_profile_mobile').change(function(){

        var mobile = $(this).val();
        $.ajax({
            url:Routing.generate('bindu_signup_check'),
            type: "POST",
            beforeSend: function() {
                $('#checking').html('');
            },
            data: 'mobile='+ mobile,
            success: function(response) {
                if(response == 'failed'){
                    $('#checking').html('Someone already has that mobile no. Try another?').css({'top':0, 'right':0,'width':'auto'});
                    $('#Core_userbundle_user_profile_mobile').val('');
                    $('#Core_userbundle_user_profile_mobile').css('border','1px solid red');
                }else{
                    $('#checking img').fadeOut(3000);
                    $('#Core_userbundle_user_profile_mobile').css('border','1px solid #2dbae7');
                }

            }
        });
    });

}

function CommonJs(){



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


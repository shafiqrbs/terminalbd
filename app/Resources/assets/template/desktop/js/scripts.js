/**
 * Created by rbs on 8/20/17.
 */
wow = new WOW(
    {
        boxClass:     'wow',
        animateClass: 'animated',
        offset:       100
    }
);
wow.init();

$(document).ready(function(){

        var stickyOffset = $('.sticky').offset().top;
        $(window).scroll(function(){
            var sticky = $('.sticky'),
                scroll = $(window).scrollTop();
            if (scroll > 1) sticky.addClass('fixed-top');
            else sticky.removeClass('fixed-top');
        });

        $('.social-tooltip').tooltip({
            selector: "[data-toggle=tooltip]",
            container: "body"
        });


        $('.select-category').select2({
            placeholder: "Filter by category",
            allowClear: true,
            color: "black"
        });

        $('.select-brand').select2({
            placeholder: "Filter by barnd",
            allowClear: true,
            color: "black"
        });

        $('.select-location').select2({
            placeholder: "Filter by location",
            allowClear: true,
            color: "black"
        });
        
        $(".numeric").numeric();
        $(".mobile").inputmask("mask", {"mask": "99999-999-999"}); //specifying fn & options
        $(".otp").inputmask("mask", {"mask": "9999"}); //specifying fn & options

        $('#searchEvent').click(function(){
            $('#nav-search').slideToggle('slow');
        });

        $(document).on("click", ".searchBtnSelector", function() {
            $('#search-area').slideToggle('slow');
        });

        $('#catlist').children('.news-list').each(function(index) {
            $(this).addClass(index % 2 ? 'odd' : 'even');
        });
        $(".dropdown").hover(
            function() {
                $('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).slideDown("400");
                $(this).toggleClass('open');
            },
            function() {
                $('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).slideUp("400");
                $(this).toggleClass('open');
            }
        );
        
        $('#list').click(function(event){event.preventDefault();$('#product .item').addClass('list-group-item');});
        $('#grid').click(function(event){event.preventDefault();$('#product .item').removeClass('list-group-item');$('#products .item').addClass('grid-group-item');});

        $('body').append('<div id="toTop" class="btn btn-primary color1"><span class="glyphicon glyphicon-chevron-up"></span></div>');
        $(window).scroll(function () {
            if ($(this).scrollTop() != 0) {
                $('#toTop').fadeIn();
            } else {
                $('#toTop').fadeOut();
            }
        });
        $('#toTop').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 700);
            return false;
        });

        $('.custom-menu a[href^="#"], .intro-scroller .inner-link').on('click',function (e) {
        
            e.preventDefault();
            var target = this.hash;
            var $target = $(target);
        
            $('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, 900, 'swing', function () {
                window.location.hash = target;
            });
        });
        
        $('a.page-scroll').bind('click', function(event) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top
            }, 1500, 'easeInOutExpo');
            event.preventDefault();
        });
        
        $(".nav a").on("click", function(){
            $(".nav").find(".active").removeClass("active");
            $(this).parent().addClass("active");
        });

        $("#price-range").slider({
            tooltip: 'always'
        });

        $( "#viewed" ).click(function() {
            $( "#viewed-item" ).slideToggle( "slow" );
        });

        $(document).on("click", ".login-preview", function() {
            $('#registerModal').modal('hide');
            $('#forgetModal').modal('hide');
            $('#loginModal').modal('toggle');

        });

        $('.register-btn').click(function () {

            $('#loginModal').modal('hide');
            $('#forgetModal').modal('hide');
            $('#registerModal').modal('toggle');

        });
        $('#forget-password').click(function () {

            $('#loginModal').modal('hide');
            $('#registerModal').modal('hide');
            $('#forgetModal').modal('toggle');
        });

        $("#mega-menu-carousel").carousel({
            interval: 10000,
            wrap:true
        });

         $(".carousel").carousel({
            interval: 10000,
            wrap:true
        });

        $(".carousel").on("slid", function() {
            var to_slide;
            to_slide = $(".carousel-item.active").attr("data-slide-no");
            $(".myCarousel-target.active").removeClass("active");
            $(".carousel-indicators [data-slide-to=" + to_slide + "]").addClass("active");
        });
        $(".myCarousel-target").on("click", function() {
            $(this).preventDefault();
            $(".carousel").carousel(parseInt($(this).attr("data-slide-to")));
            $(".myCarousel-target.active").removeClass("active");
            $(this).addClass("active");
        });



        $('#topTabCarousel').carousel({
            interval:   4000
        });
    
        var clickEventTop = false;
        $('#topTabCarousel').on('click', '.nav a', function() {
            clickEventTop = true;
            $('.nav li').removeClass('active');
            $(this).parent().addClass('active');
        }).on('slid.bs.carousel', function(e) {
            if(!clickEventTop) {
                var count = $('.nav').children().length -1;
                var current = $('.nav li.active');
                current.removeClass('active').next().addClass('active');
                var id = parseInt(current.data('slide-to'));
                if(count == id) {
                    $('.nav li').first().addClass('active');
                }
            }
            clickEventTop = false;
        });


        $('#bottomTabCarousel').carousel({
            interval:   4000
        });

        var clickEvent = false;
        $('#bottomTabCarousel').on('click', '.nav a', function() {
            clickEvent = true;
            $('.nav li').removeClass('active');
            $(this).parent().addClass('active');
        }).on('slid.bs.carousel', function(e) {
            if(!clickEvent) {
                var count = $('.nav').children().length -1;
                var current = $('.nav li.active');
                current.removeClass('active').next().addClass('active');
                var id = parseInt(current.data('slide-to'));
                if(count == id) {
                    $('.nav li').first().addClass('active');
                }
            }
            clickEvent = false;
        });

        $("#newsLetter").validate({

            ignore: ".ignore",
            rules: {
                email: {required: true,email: true}
            },
            messages: {
                email: "Please enter a valid email address."
            },
            tooltip_options: {
                email: {trigger:'focus',placement:'top',html:true},
            },
            submitHandler: function(form) {
                $.ajax({
                    url         : $('#newsLetter').attr( 'action' ),
                    type        : $('#newsLetter').attr( 'method' ),
                    data        : $('#newsLetter').serialize(),
                    success: function(response) {
                        $("form").trigger("reset");
                        if(response == 'valid'){
                            $('#email-confirm').notifyModal({
                                duration : 10000,
                                placement : 'center',
                                overlay : true,
                                type : 'notify',
                                icon : false
                            });
                        }else{
                            $('#email-invalid').notifyModal({
                                duration : 10000,
                                placement : 'center',
                                overlay : true,
                                type : 'notify',
                                icon : false
                            });
                        }

                    },

                });

            }

        });
        $("#contactUs").validate({

                rules: {
                    name: {required: true},
                    mobile: {required: true,maxlength:13,minlength:13},
                    message: {required: true,maxlength:512},
                    email: {email: true},
                    hiddenRecaptcha: {
                        required: function() {
                            if(grecaptcha.getResponse() == '') {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                },
                messages: {
                    name: "Enter name or company",
                    mobile: "Enter valid mobile no",
                    email: "Please enter a valid email address.",
                    message: "Please enter comments",
                    hiddenRecaptcha: {
                        checkCaptcha: "Your Captcha response was incorrect. Please try again."
                    }
                },

                tooltip_options: {
                    name: {trigger:'focus',placement:'top',html:true},
                    mobile: {trigger:'focus',placement:'top',html:true},
                    email: {trigger:'focus',placement:'top',html:true},
                    message: {trigger:'focus',placement:'top',html:true},
                    hiddenRecaptcha: {trigger:'focus',placement:'top',html:true}
                },

                submitHandler: function(form) {

                    if(grecaptcha.getResponse() == "") {
                        alert("Your Captcha response was incorrect. Please try again.");
                        return false;
                    }
                    $.ajax({

                        url         : $(form).attr( 'action' ),
                        type        : $(form).attr( 'method' ),
                        data        : new FormData(form),
                        processData : false,
                        contentType : false,
                        success: function(response) {
                            $("form").trigger("reset");
                            grecaptcha.reset();
                        },
                        complete: function(){
                            $('#contactMsg').show();
                        }
                    });
                }

        });

        
        $("#signup").validate({
    
            rules: {
    
                "Core_userbundle_user[profile][name]": {required: true},
                "Core_userbundle_user[email]": {
                    required: false,
                    remote:'/checking-user-email'
                },
                "Core_userbundle_user[profile][mobile]": {
                    required: true,
                    remote:'/checking-username'
                },
                "Core_userbundle_user[profile][termsConditionAccept]": {required: true}
            },
    
            messages: {
    
                "Core_userbundle_user[profile][name]":"Enter your full name",
                "Core_userbundle_user[profile][mobile]":{
                    required: "Enter valid mobile no",
                    remote: "This mobile no is already registered. Please try to another no."
                },
                 "Core_userbundle_user[profile][email]":{
                    remote: "This email is already registered. Please try to another email."
                },
                "Core_userbundle_user[profile][location]": "Enter your location",
                "Core_userbundle_user[profile][termsConditionAccept]": "Please read terms & condition and agree"
            },
    
            tooltip_options: {

                "Core_userbundle_user[profile][name]": {placement:'top',html:true},
                "Core_userbundle_user[profile][mobile]": {placement:'top',html:true},
                "Core_userbundle_user[profile][status]":{placement:'right',html:true},
            },
            submitHandler: function(form) {
    
                $.ajax({
    
                    url         : $(form).attr( 'action' ),
                    type        : $(form).attr( 'method' ),
                    data        : new FormData(form),
                    processData : false,
                    contentType : false,
                    success: function(response){
                        if(response === 'success') {
                            $(".panel-body").hide();
                            $("#register-success").show();
                            setTimeout(function(){location.reload();},1000);
                        }
                    },
                    complete: function(response){
                        if(response === 'success'){
                            $(".panel-body").hide();
                            $("#register-success").show();
                            setTimeout(function(){location.reload();},1000);
                        }else if(response === 'invalid'){
                            $('#registerModal').modal('hide');
                            $('#forgetModal').modal('hide');
                            $('#loginModal').modal('toggle');
                            $("form").trigger("reset");
                        }
                    }
                });
            }
        });

        $("#registrationForm").validate({

        rules: {

            "registration_name": {required: true},
            "registration_email": {
                required: false,
                remote:'/checking-member-email'
            },
            "registration_mobile": {
                required: true,
                remote:'/checking-member'
            },
            "registration_facebookId": {required: false},
            "registration_address": {required: false}
        },

        messages: {

            "registration_name":"Enter your full name",
            "registration_mobile":{
                required: "Enter valid mobile no",
                remote: "This mobile no is already registered. Please try to another no."
            },
            "registration_email":{
                remote: "This email is already registered. Please try to another email."
            },

        },

        tooltip_options: {
            "registration_name": {placement:'top',html:true},
            "registration_mobile": {placement:'top',html:true},
        },
        submitHandler: function(form) {

            $.ajax({

                url         : $(form).attr( 'action' ),
                type        : $(form).attr( 'method' ),
                data        : new FormData(form),
                processData : false,
                contentType : false,
                success: function(response){
                    window.open(response+"#modal","_self");
                },
                complete: function(response){

                }
            });
        }
    });

        $("#loginForm").validate({

            rules: {
                "_username": {required: true},
                "_password": {required: true},
            },

            messages: {

                "_username":"Enter your mobile name",
                "_password":"Enter valid OTP",

            },

            tooltip_options: {
                "_username": {placement:'top',html:true},
                "_password": {placement:'top',html:true}
            },
            submitHandler: function(form) {

                $.ajax({

                    url         : $(form).attr( 'action' ),
                    type        : $(form).attr( 'method' ),
                    data        : new FormData(form),
                    processData : false,
                    contentType : false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.has_error) {
                            $('#loginMsg').addClass('alert-danger');
                            $('.alert-danger').html('There was an error with your Mobile no/Password combination. Please try again.');
                        }else {
                            $('#loginMsg').removeClass('alert-danger');
                            $('#loginMsg').addClass('alert-success');
                            $('.alert-success').html('Yor are login success.');
                            setTimeout(function () {
                                location.reload();
                            }, 3000)

                        }
                    }
                });
            }

        });

    var total;
    function getRandom(){return Math.ceil(Math.random()* 20);}
    function createSum(){
        var randomNum1 = getRandom(),
            randomNum2 = getRandom();
        total =randomNum1 + randomNum2;
        $( "#question" ).text( randomNum1 + " + " + randomNum2 + "=" );
        $("#ans").val('');
        checkInput();
    }

    function checkInput(){
        var input = $("#ans").val(),
            slideSpeed = 200,
            hasInput = !!input,
            valid = hasInput && input == total;
        $('#verify').toggle(!hasInput);
        $('#registrationSubmitForm').prop('disabled', !valid);
        $('#success').toggle(valid);
        $('#fail').toggle(hasInput && !valid);
    }

    createSum();
    // On "reset button" click, generate new random sum
 //   $('#registrationSubmitForm').click(createSum);
    // On user input, check value
    $( "#ans" ).keyup(checkInput);

    $(document).on( "change", ".userMobile", function( e ) {

        var mobile = $(this).val();
        var url = $(this).attr("data-action");
        $.get(url,{ mobile:mobile} ).done(function(response) {
            $("#mobile-validate").html(response);
        }).always(function() {
            $('#mobile-confirm').notifyModal({
                duration : 10000,
                placement : 'center',
                overlay : true,
                type : 'notify',
                icon : true
            });
        });

    });
});

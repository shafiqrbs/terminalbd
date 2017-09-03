wow = new WOW(
    {
        boxClass:     'wow',
        animateClass: 'animated',
        offset:       100
    }
);
wow.init();

$(document).ready(function(){

        "use strict";

        // custom scrollbar

        $("html").niceScroll({styler:"fb",cursorcolor:"#2ad2c9", cursorwidth: '4', cursorborderradius: '10px', background: '#FFFFFF', spacebarenabled:false, cursorborder: '0',  zindex: '1000'});

        $(".scrollbar1").niceScroll({styler:"fb",cursorcolor:"#2ad2c9", cursorwidth: '4', cursorborderradius: '0',autohidemode: 'false', background: '#FFFFFF', spacebarenabled:false, cursorborder: '0'});

        $(".scrollbar1").getNiceScroll();
        if ($('body').hasClass('scrollbar1-collapsed')) {
            $(".scrollbar1").getNiceScroll().hide();
        }

        $("#mm-menu").mmenu();
        $(window).load(function() {
            $('#gmap_canvas').show();
        });


        $('.popup-top-anim').magnificPopup({
            type: 'inline',
            fixedContentPos: false,
            fixedBgPos: true,
            overflowY: 'auto',
            closeBtnInside: true,
            preloader: false,
            midClick: true,
            removalDelay: 300,
            mainClass: 'my-mfp-zoom-in'
        });

        $('.scrollToTop').click(function(){
            $('html, body').animate({scrollTop : 0},800);
            return false;
        });

        $(".numeric").numeric();
        $(".mobile").inputmask("mask", {"mask": "99999-999-999"}); //specifying fn & options


        $('.sms-content').keypress(function(e) {

            var tval = $('textarea').val(),
                tlength = tval.length,
                set = 140,
                remain = parseInt(set - tlength);
            $('#limit').text(remain);
            if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                $('textarea').val((tval).substring(0, tlength - 1))
            }

        });

        $("#menu-bar a").click(function () {
            var id = $(this).attr("href").substring(1);
            $("html, body").animate({scrollTop: $("#" + id).offset().top}, 1000, function () {
                $("#menu-bar").slideReveal("hide");
            });
        });

        var slider = $("#side-bar-panel").slideReveal({
            // width: 100,
            push: false,
            position: "right",
            // speed: 600,
            trigger: $(".handle"),
            // autoEscape: false,
            shown: function (obj) {
                obj.find(".handle").html('<span class="glyphicon glyphicon-chevron-right"></span>');
                obj.addClass("left-shadow-overlay");
            },
            hidden: function (obj) {
                obj.find(".handle").html('<span class="glyphicon glyphicon-chevron-left"></span>');
                obj.removeClass("left-shadow-overlay");
                $('#registerModal').hide();
                $('#forgetModal').hide();
                $('#loginModal').hide();
                $('#cart-details').hide();
            }
        });


        $('.social-tooltip').tooltip({
            selector: "[data-toggle=tooltip]",
            container: "body"
        });

        $(".numeric").numeric();
        $(".mobile").inputmask("mask", {"mask": "99999-999-999"}); //specifying fn & options
        $('#searchEvent').click(function(){
            $('#nav-search').slideToggle('slow');
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

        $("#sendSms").validate({
            ignore: ".ignore",
            rules: {
                mobile: {required: true},
                content: {required: true}
            },
            messages: {
                mobile: "Enter valid mobile no",
                content: "Enter your comment"
            },
            tooltip_options: {
                name: {trigger:'focus',placement:'top',html:true},
                mobile: {placement:'top',html:true}
            },
            submitHandler: function(form) {

                $.ajax({

                    url         : $(form).attr( 'action' ),
                    type        : $(form).attr( 'method' ),
                    data        : new FormData(form),
                    processData : false,
                    contentType : false,
                    success: function(response) {
                        $("form").trigger("reset");
                    },
                    complete: function(){

                    }
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

        $("#sendEmail").validate({

            rules: {
                name: {required: true},
                mobile: {required: true,maxlength:13,minlength:13},
                content: {required: true,maxlength:512},
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
                content: "Please enter comments",
                hiddenRecaptcha: {
                    checkCaptcha: "Your Captcha response was incorrect. Please try again."
                }
            },

            tooltip_options: {
                name: {trigger:'focus',placement:'top',html:true},
                mobile: {trigger:'focus',placement:'top',html:true},
                email: {trigger:'focus',placement:'top',html:true},
                content: {trigger:'focus',placement:'top',html:true},
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

        $("#signupx").validate({

            rules: {

                "Core_userbundle_user[globalOption][name]": {required: true},
                "Core_userbundle_user[profile][mobile]": {
                    required: true,
                    remote: Routing.generate('webservice_customer_checking',{'subdomain':subdomain})
                },
                "Core_userbundle_user[globalOption][syndicate]": {required: true},
                "Core_userbundle_user[globalOption][location]": {required: true},
                "Core_userbundle_user[globalOption][status]": {required: true}
            },

            messages: {

                "Core_userbundle_user[globalOption][name]":"Enter your organization name",
                "Core_userbundle_user[profile][mobile]":{
                    required: "Enter valid mobile no",
                    remote: "This mobile no is already registered. Please try to another no."
                },
                "Core_userbundle_user[profile][syndicate]": "Enter your professional",
                "Core_userbundle_user[profile][location]": "Enter your location",
                "Core_userbundle_user[globalOption][status]": "Please read terms & condition and agree"
            },

            tooltip_options: {

                "Core_userbundle_user[globalOption][name]": {placement:'top',html:true},
                "Core_userbundle_user[profile][mobile]": {placement:'top',html:true},
                "Core_userbundle_user[globalOption][syndicate]": {placement:'top',html:true},
                "Core_userbundle_user[globalOption][location]": {placement:'top',html:true},
                "Core_userbundle_user[globalOption][status]":{placement:'right',html:true},
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
});




                     
     
  
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
            if (scroll >= stickyOffset) sticky.addClass('fixed-top');
            else sticky.removeClass('fixed-top');
        });

        var navbar = $('.navbar-header').outerHeight(true);
        var mins = 22;
        $('.bs-example-form').css({
            'margin-top':(navbar/2 - mins)
        });
        
        $('.social-tooltip').tooltip({
            selector: "[data-toggle=tooltip]",
            container: "body"
        });
        
        $('.select2').select2({
            placeholder: "Filter by category",
            allowClear: true,
            color: "black"
        });
        
        $(".numeric").numeric();
        $(".mobile").inputmask("mask", {"mask": "99999-999-999"}); //specifying fn & options

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

        $('.login-preview').click(function () {
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

        $("#contactMsg").validate({

            ignore: ".ignore",
            rules: {
                name: {required: true},
                mobile: {required: true}
            },
            messages: {
                name: "Enter your name or company",
                mobile: "Enter valid mobile no"
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
                       console.log(response);
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

        $("#signup").validate({
    
            rules: {
    
                "Core_userbundle_user[profile][name]": {required: true},
                "Core_userbundle_user[profile][mobile]": {
                    required: true,
                    remote:'/checking-username'
                },
                "Core_userbundle_user[profile][location]": {required: true},
                "Core_userbundle_user[profile][termsConditionAccept]": {required: true}
            },
    
            messages: {
    
                "Core_userbundle_user[profile][name]":"Enter your full name",
                "Core_userbundle_user[profile][mobile]":{
                    required: "Enter valid mobile no",
                    remote: "This mobile no is already registered. Please try to another no."
                },
                "Core_userbundle_user[profile][location]": "Enter your location",
                "Core_userbundle_user[profile][termsConditionAccept]": "Please read terms & condition and agree"
            },
    
            tooltip_options: {

                "Core_userbundle_user[profile][name]": {placement:'top',html:true},
                "Core_userbundle_user[profile][mobile]": {placement:'top',html:true},
                "Core_userbundle_user[profile][location]": {placement:'top',html:true},
                "Core_userbundle_user[profile][status]":{placement:'right',html:true},
            },
            submitHandler: function(form) {
    
                $.ajax({
    
                    url         : $(form).attr( 'action' ),
                    type        : $(form).attr( 'method' ),
                    data        : new FormData(form),
                    processData : false,
                    contentType : false,
                    success: function(response) {
                        if(response == 'success'){
                            $('#registerModal').modal('hide');
                            $('#forgetModal').modal('hide');
                            $('#loginModal').modal('toggle');
                        }
                    },
                    complete: function(){
                        $("form").trigger("reset");
                        $('#error').addClass('alert-success');
                        $('.alert-success').html('Dear Customer, Registration success, User name mobile no & Password is 1234');
                    }
                });
            }
        });

});

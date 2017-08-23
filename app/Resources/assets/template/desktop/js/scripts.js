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

        $("#contactMsg").validate({

            rules: {
                name: {required: true},
                mobile: {required: true}
            },
            messages: {
                name: "Just check the box<h5 class='text-danger'>You aren't going to read the EULA</h5>",
                mobile: "Enter valid mobile no"
            },
            tooltip_options: {
                name: {trigger:'focus'},
                mobile: {placement:'top',html:true}
            },

        });


        $("#price-range").slider({
            tooltip: 'always'
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

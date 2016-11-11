    var owlProduct = $(".product-slider");
    owlProduct.owlCarousel({
        items: 6,
        itemsDesktop: [1199, 3],
        itemsDesktopSmall: [979, 2],
        itemsTablet: [768, 1],
        pagination: false,
        paginationNumbers: false,
        autoPlay: false,
        rewindNav: false
    });

// Custom Navigation Events
$(".next").click(function(){
    owlProduct.trigger('owl.next');
})

$(".prev").click(function(){
    owlProduct.trigger('owl.prev');
})

var owlProduct = $(".viewed-product-slider");
owlProduct.owlCarousel({
    items:6,
    itemsDesktop:[1199,3],
    itemsDesktopSmall:[979,2],
    itemsTablet:[768,1],
    pagination: false,
    paginationNumbers: false,
    autoPlay:false,
    rewindNav:false
});
// Custom Navigation Events
$(".viewed-next").click(function(){
    owlProduct.trigger('owl.next');
})

$(".viewed-prev").click(function(){
    owlProduct.trigger('owl.prev');
})

function commonJs(){


    $('#contactForm').on('submit', function(e){
        e.preventDefault();
        e.stopPropagation();

        // get values from FORM
        var name = $("#name").val();
        var email = $("#email").val();
        var message = $("#message").val();
        var goodToGo = false;
        var messgaeError = 'Request can not be send';
        var pattern = new RegExp(/^(('[\w-\s]+')|([\w-]+(?:\.[\w-]+)*)|('[\w-\s]+')([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);


        if (name && name.length > 0 && $.trim(name) !='' && message && message.length > 0 && $.trim(message) !='' && email && email.length > 0 && $.trim(email) !='') {
            if (pattern.test(email)) {
                goodToGo = true;
            } else {
                messgaeError = 'Please check your email address';
                goodToGo = false;
            }
        } else {
            messgaeError = 'You must fill all the form fields to proceed!';
            goodToGo = false;
        }
        if (goodToGo) {
            $.ajax({
                data: $('#contactForm').serialize(),
                beforeSend: function() {
                    $('#success').html('<div class="col-md-12 text-center"><img src="images/spinner1.gif" alt="spinner" /></div>');
                },
                success:function(response){
                    if (response==1) {
                        $('#success').html('<div class="col-md-12 text-center">Your email was sent successfully</div>');
                        window.location.reload();
                    } else {
                        $('#success').html('<div class="col-md-12 text-center">E-mail was not sent. Please try again!</div>');
                    }
                },
                error:function(e){
                    $('#success').html('<div class="col-md-12 text-center">We could not fetch the data from the server. Please try again!</div>');
                },
                complete: function(done){
                    console.log('Finished');
                },
                type: 'POST',
                url: 'js/send_email.php',
            });
            return true;
        } else {
            $('#success').html('<div class="col-md-12 text-center">'+messgaeError+'</div>');
            return false;
        }
        return false;
    });
}

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

$.validator.setDefaults({

    errorElement: "span",
    errorClass: "help-block",
    //	validClass: 'stay',
    highlight: function (element, errorClass, validClass) {
        $(element).addClass(errorClass); //.removeClass(errorClass);
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass(errorClass); //.addClass(validClass);
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2')) {
            error.insertAfter(element.next('span'));
        } else {
            error.insertAfter(element);
        }
    }
});

$('.select2').on('change', function () {
    $(this).valid();
});

$("#commentform").submit(function (e) {

    e.preventDefault();
    var $this = $(e.currentTarget),
        inputs = {};

    // Send all form's inputs
    $.each($this.find('input'), function (i, item) {
        var $item = $(item);
        inputs[$item.attr('name')] = $item.val();
    });

    // Send form into ajax
    $.ajax({
        url: $this.attr('action'),
        type: 'POST',
        dataType: 'json',
        data: inputs,
        success: function (data) {
            if (data.has_error) {
                $('#error').html(data.error);
            }else {
                location.reload();
            }
        }
    });
});



var validator =  $("#signup").validate({

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


$("div.list-group>a").click(function(e) {
    e.preventDefault();
    $(this).siblings('a.active').removeClass("active");
    $(this).addClass("active");
    var index = $(this).index();
    $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
    $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
});

$('.modalOpen').click(function(){
    var id = $(this).attr('id');
    var inst = $("[data-remodal-id=modal"+id+"]").remodal();
    inst.open();

});


$('.pgwSlider').pgwSlider({

    mainClassName : 'pgwSlider',
    listPosition : 'left',
    selectionMode : 'click',
    transitionEffect : 'slide',
    autoSlide : false,
    displayList : true,
    displayControls : false,
    touchControls : true,
    verticalCentering : true,
    adaptiveHeight : false,
    minHeight : 400,
    maxHeight : 400,
    beforeSlide : null,
    afterSlide : null,
    adaptiveDuration : 200,
    transitionDuration : 500,
    intervalDuration : 3000,
    nextSlide :true,
    previousSlide :true

});

function sameHeights(selector) {

    var selector = selector || '[data-key="sameHeights"]',
        query = document.querySelectorAll(selector),
        elements = query.length,
        max = 0;
    if (elements) {
        while (elements--) {
            var element = query[elements];
            if (element.clientHeight > max) {
                max = element.clientHeight;
            }
        }
        elements = query.length;
        while (elements--) {
            var element = query[elements];
            element.style.height = max + 'px';
        }
    }
}

if ('addEventListener' in window) {
    // first group
    window.addEventListener('resize', function(){
        sameHeights('[data-key="sameHeights"]');
    });
    window.addEventListener('load', function(){
        sameHeights('[data-key="sameHeights"]');
    });

    // second group
    window.addEventListener('resize', function(){
        sameHeights('[data-key="featureHeights"]');
    });
    window.addEventListener('load', function(){
        sameHeights('[data-key="featureHeights"]');
    });

    // second group
    window.addEventListener('resize', function(){
        sameHeights('[data-key="viewHeights"]');
    });
    window.addEventListener('load', function(){
        sameHeights('[data-key="viewHeights"]');
    });
}


function heightsEqualizer(selector) {
    var elements = document.querySelectorAll(selector),
        max_height = 0,
        len = 0,
        i;

    if ( (elements) && (elements.length > 0) ) {
        len = elements.length;
        for (i = 0; i < len; i++) { // get max height
            elements[i].style.height = ''; // reset height attr
            if (elements[i].clientHeight > max_height) {
                max_height = elements[i].clientHeight;
            }
        }

        for (i = 0; i < len; i++) { // set max height to all elements
            elements[i].style.height = max_height + 'px';
        }
    }
}

if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', function() {
        heightsEqualizer('.js-equal-height');
    });
    window.addEventListener('resize', function(){
        heightsEqualizer('.js-equal-height');
    });
}

setTimeout(function () { // set 1 second timeout for having all fonts loaded
    heightsEqualizer('.js-equal-height');
}, 1000);










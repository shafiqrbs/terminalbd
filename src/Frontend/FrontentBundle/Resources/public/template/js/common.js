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









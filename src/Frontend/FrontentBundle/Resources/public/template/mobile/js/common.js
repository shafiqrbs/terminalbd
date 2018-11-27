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

$('body').on('click', '.btn-cookie', function(el) {
    var val = $(this).attr('id');
    var url = $(this).attr('data-url');
    $.cookie('productList', val, {path: '/'});
    setTimeout(location.reload(), 1000);
});


$(document).on( "click", ".emailSender", function(e){

        $.ajax({
            url         : $('#newsLetter').attr( 'action' ),
            type        : $('#newsLetter').attr( 'method' ),
            data        : $('#newsLetter').serialize(),
            success: function(response) {
                $('#newsLetter').find("input[type=text]").val("");
                $('#email-success').html('Your e-mail is sending successfuly');
            },

        });

    });

    $( "#viewed" ).click(function() {
        $( "#viewed-item" ).slideToggle( "slow" );
    });

    $('.login-preview').click(function () {

        $('#registerModal').hide();
        $('#forgetModal').hide();
        $('#cart-details').hide();
        $('#loginModal').show('toggle');

    });
    $('.register-btn').click(function () {

        $('#loginModal').hide();
        $('#forgetModal').hide();
        $('#cart-details').hide();
        $('#registerModal').show('toggle');
    });

    $('#forget-password').click(function () {

        $('#loginModal').hide();
        $('#registerModal').hide();
        $('#cart-details').hide();
        $('#forgetModal').show('toggle');
    });

    $('.cart-preview').click(function () {

        $('#loginModal').hide();
        $('#registerModal').hide();
        $('#forgetModal').hide();
        $('#cart-details').show('toggle');
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

    $("div.list-group > a").click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
        $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
    });



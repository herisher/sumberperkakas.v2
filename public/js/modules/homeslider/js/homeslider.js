$(document).ready(function(){

    if (typeof(homeslider_speed) == 'undefined')
        homeslider_speed = 500;
    if (typeof(homeslider_pause) == 'undefined')
        homeslider_pause = 3000;
    if (typeof(homeslider_loop) == 'undefined')
        homeslider_loop = true;
    if (typeof(homeslider_width) == 'undefined')
        homeslider_width = 870;


    if (!!$.prototype.bxSlider)
        $('#homeslider').bxSlider({
            mode:'fade',
            useCSS: false,
            maxSlides: 1,
            slideWidth: homeslider_width,
            infiniteLoop: homeslider_loop,
            hideControlOnEnd: true,
            pager: false,
            autoHover: true,
            autoControls: true,
            auto: homeslider_loop,
            speed: homeslider_speed,
            pause: homeslider_pause,
            controls: true,
            startText:'',
            stopText:'',
            pagerCustom: ''
        });

    $('.homeslider-description').click(function () {
        window.location.href = $(this).prev('a').prop('href');
    });
    
    $("#brandslider").jCarouselLite({
        btnPrev : "#prevbtn",
        auto    : 2500,
        speed   : 2500,
        circular: true,
        visible : 8,/**/
        scroll  : 1,
        btnNext : "#nextbtn",
        pauseOnHover: true
    });
});
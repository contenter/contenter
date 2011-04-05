$(function(){
    var params = $(document).data('params');

    var needLeftPos = ($(document).width() - params['top_width'] > 0) ? (($(document).width() - params['top_width']) / 2) : 0;
    
    $('#crop_div').css({
        "left" : needLeftPos,
        "top"  : 40
    });

    $('#crop_div').show();
});
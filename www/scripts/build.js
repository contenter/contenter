jQuery.fn.outerHTML = function() {
    return $('<div>').append( this.eq(0).clone() ).html();
};

$(function(){
    var params = $(document).data('params');
    var needLeftPos = (screen.width - params['top_width']) / 2;
    var leftOffset = (params['left_width'] > needLeftPos) ? -(params['left_width'] - needLeftPos) : (needLeftPos - params['left_width']);
    var topOffset = - params['top_height'] + 40;
    $('#body_div').css({
       "left":  leftOffset,
       "top":   topOffset
    });
    $('#fl').css({
       "width":  needLeftPos
    });
    var fr_left = parseInt(needLeftPos) + parseInt(params['top_width']);
    var fr_width = screen.width - needLeftPos - params['top_width'];
    $('#fr').css({
       "left":  fr_left,
       "width": fr_width
    });

    $('#body_div').show();
});
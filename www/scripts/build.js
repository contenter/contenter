jQuery.fn.outerHTML = function() {
    return $('<div>').append( this.eq(0).clone() ).html();
};

$(function(){
    var params = $(document).data('params');
    var needLeftPos = (screen.width - params['top_width']) / 2;
    var leftOffset = (params['left_width'] > needLeftPos) ? -(params['left_width'] - needLeftPos) : (needLeftPos - params['left_width']);
    var topOffset = - params['top_height'] + 90;
    $('#body_div').css({
       "left":  leftOffset,
       "top":   topOffset
    });
    $('#fl').css({
       "width":  needLeftPos
    });

    $('#ft').css({
       "left":  needLeftPos
    });

    $('#fb').css({
       "left":  needLeftPos
    });

    $('#fr').css({
       "left":  needLeftPos + params['top_width'],
       "width": screen.width - needLeftPos - params['top_width']
    });

    $('#body_div').show();
});
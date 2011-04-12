$(function(){
    var params = $(document).data('params');

    console.log(params['frame_url']);

    interval = setInterval(function(){
        $('iframe').each(function(){
            var src = $(this).attr('src');
            if(src.indexOf(params['frame_url']) > -1) { return; }
            $(this).attr('src', params['frame_url'] + '?url=' + src);
        });
        
        $('#' + params['visibilityDiv']).css('visibility', 'visible');
    }, 1000);
    
});
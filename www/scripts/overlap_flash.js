jQuery.fn.outerHTML = function() {
    return $('<div>').append( this.eq(0).clone() ).html();
};

interval = setInterval(function(){
    if(jQuery.browser.msie) {
        $('object').each(function(){
            var paramWmodeReps = false;
            var embedWmodeReps = false;
            var paramWmodeExist = false;
            var flashVars = "";
            var obj = this;
            var html = this.outerHTML;
            var cp = obj.firstChild;
            if(cp !== null){
                do{
                    if (cp.tagName == 'PARAM' && cp.getAttribute('NAME') == 'wmode') {
                        paramWmodeExist = true;
                        if(cp.getAttribute('VALUE') != 'opaque') {
                            html = html.replace(/<param([^>]*)name=[\'\"]{0,1}wmode[\'\"]{0,1}[^>]*>/gi, '<param name="Wmode" value="opaque">');
                            paramWmodeReps = true;
                        }
                    }
                    if (cp.tagName == 'PARAM' && cp.getAttribute('NAME') == 'flashvars') {
                        flashVars = cp.getAttribute('VALUE');
                    }
                    if (cp.tagName == 'EMBED') {
                        if (!cp.getAttribute('wmode')) {
                            html = html.replace(/<embed/gi, '<embed wmode="opaque"');
                            embedWmodeReps = true;
                        }else{
                            if(cp.getAttribute('wmode') != 'opaque') {
                                html.replace(/<embed([^>]*?)\swmode=[\'\"]{0,1}[A-Za-z]*[\'\"]{0,1}\s([^>]*?)>/gi, '<embed$1 wmode="opaque" $2>');
                                embedWmodeReps = true;
                            }
                        }
                    }
                    cp = cp.nextSibling;
                }while(cp !== null);

                if (!paramWmodeExist) {
                    html = html.replace(/<object([^>]*?)>/gi, '<object$1><param name="wmode" value="opaque">');
                    paramWmodeReps = true;
                }

                if (embedWmodeReps || paramWmodeReps) {
                    if (flashVars != "") {
                        html = html.replace(/<param([^>]*)name=[\'\"]{0,1}flashvars[\'\"]{0,1}[^>]*>/gi, '<param name="FlashVars" value="' + flashVars + '">');
                    }

                    this.outerHTML = html;
                }
            }else{
                html = html.replace(/<object([^>]*?)>/gi, '<object$1><param name="wmode" value="opaque">');
                this.outerHTML = html;
            }
        });
    }else{
        $('object:has(param[name="wmode"][value!="opaque"])').each(function(){
            $(this).css({"z-index": 0});
            $('param[name="wmode"]', this).val("opaque");
            $('embed[wmode!="opaque"]', this).attr('wmode', 'opaque');
            var html = $(this).outerHTML();
            $(this).replaceWith(html);
        } );

        $('object:not(:has(param[name="wmode"]))').each(function(){
            $(this).css({"z-index": 0});
            $('<param name="wmode" value="opaque">').appendTo($(this));
            $('embed[wmode!="opaque"]', this).attr('wmode', 'opaque');
            var html = $(this).outerHTML();
            $(this).replaceWith(html);
        });

        $('embed[wmode!="opaque"]').each(function(){
            console.log('bla');
            $(this).css({"z-index": 0});
            $(this).attr('wmode', 'opaque');
            var html = $(this).outerHTML();
            $(this).replaceWith(html);
        } );
    }
}, 1000);
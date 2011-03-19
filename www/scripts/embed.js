jQuery.fn.outerHTML = function() {
    return $('<div>').append( this.eq(0).clone() ).html();
};

// Boxer plugin
$.widget("ui.boxer", $.extend({}, $.ui.mouse, {

  _init: function() {
    this.dragged = false;
    
    this.offset = this.element.offset();

    this._mouseInit();
    
    this.doc_height = $(document).height() - 84;
    this.doc_width = $(document).width();
    
    this.helpers = this.options.helpers;
  },

  destroy: function() {
    this._mouseDestroy();

    return this;
  },

  _mouseStart: function(event) {
    var self = this;

    this.opos = [event.pageX - this.offset.left, event.pageY - this.offset.top];

    if (this.options.disabled)
      return;

    var options = this.options;

    this._trigger("start", event);

    this.helpers.getFormDiv().hide();
    this.helpers.getLD().css({
        "left": 0,
        "top": 0,
        "width": this.opos[0],
        "height": this.doc_height
    });

    this.helpers.getRD().css({
        "left": this.opos[0],
        "top": 0,
        "width": this.doc_width - this.opos[0],
        "height": this.doc_height
    });
    
    this.helpers.getTD().css({
        "left": this.opos[0],
        "top": 0,
        "width": 0,
        "height": this.opos[1]
    });
    
    this.helpers.getBD().css({
        "left": this.opos[0],
        "top": this.opos[1],
        "width": 0,
        "height": this.doc_height - this.opos[1]
    });    
  },

  _mouseDrag: function(event) {
    var self = this;
    this.dragged = true;

    if (this.options.disabled)
      return;

    var options = this.options;
    var pos = [event.pageX - this.offset.left, event.pageY - this.offset.top];
    
    var x1 = this.opos[0], y1 = this.opos[1], x2 = pos[0], y2 = pos[1];
    if (x1 > x2) {var tmp = x2;x2 = x1;x1 = tmp;}
    if (y1 > y2) {var tmp = y2;y2 = y1;y1 = tmp;}
    
    this.helpers.getLD().css({
        "left": 0,
        "top": 0,
        "width": x1,
        "height": this.doc_height
    });

    this.helpers.getRD().css({
        "left": x2,
        "top": 0,
        "width": this.doc_width - x2,
        "height": this.doc_height
    });

    this.helpers.getTD().css({
        "left": x1,
        "top": 0,
        "width": x2 - x1,
        "height": y1
    });

    this.helpers.getBD().css({
        "left": x1,
        "top": y2,
        "width": x2 - x1,
        "height": this.doc_height - y2
    });

    if(event.clientY + 40 > $(window).height() ){
        $(window).scrollTop($(window).scrollTop() + (event.clientY + 40 - $(window).height()));
    }
    
    if(event.clientY < 40 && $(window).scrollTop() > 0) {
        var delta = $(window).scrollTop() - (40 - event.clientY);
        if (delta < 0) delta = 0;
        $(window).scrollTop(delta);
    }

    if (x2 - x1 >= 100 && y2 - y1 >= 100) {
        this.helpers.getFormDiv().css({
           "left": x2 - 100,
           "top": y2 - 40 + this.helpers.getParentDiv().offset().top
        });
        this.helpers.getFormDiv().show();
    }else{
        this.helpers.getFormDiv().hide();
    }

    this._trigger("drag", event);

    return false;
  },

  _mouseStop: function(event) {
    var self = this;

    this.dragged = false;

    var options = this.options;

    this._trigger("stop", event);

    return false;
  }

}));
$.extend($.ui.boxer, {
  defaults: $.extend({}, $.ui.mouse.defaults, {
    appendTo: 'body',
    distance: 0
  })
});

$(function(){
    var divs = $(document).data('params');

    var self = {
        getParentDiv: function() {
            return $('#' + divs['ld']).parent();
        },
        getLD: function() {
            return $('#' + divs['ld']);
        },
        getRD: function() {
            return $('#' + divs['rd']);
        },
        getTD: function() {
            return $('#' + divs['td']);
        },
        getBD: function() {
            return $('#' + divs['bd']);
        },
        getTransD: function() {
            return $('#' + divs['transd']);
        },
        getFormDiv: function() {
            return $('#' + divs['fd']);
        },
        getForm: function() {
            return this.getFormDiv().find('form');
        },
        getLwi: function() {
            return $('#' + divs['lwi'], this.getForm());
        },
        getTwi: function() {
            return $('#' + divs['twi'], this.getForm());
        },
        getThi: function() {
            return $('#' + divs['thi'], this.getForm());
        },
        getBti: function() {
            return $('#' + divs['bti'], this.getForm());
        },
        getDocumentw: function() {
            return $('#' + divs['documentW'], this.getForm());
        },
        getDocumentyh: function() {
            return $('#' + divs['documentH'], this.getForm());
        },
        getContentDiv: function() {
            return $('#' + divs['contentDiv']);
        }
    };
    var doc_height = $(document).height();
    var clear_height = doc_height - 84;
    var doc_width = $(document).width();
    
    self.getContentDiv().css({height: clear_height, width: doc_width});
    self.getLD().css({height: clear_height});
    self.getRD().css({height: clear_height, width: doc_width});
    self.getTransD().css({height: clear_height, width: doc_width});
    self.getDocumentw().val(doc_width + 'px');
    self.getDocumentyh().val(clear_height + 'px');
    
    self.getParentDiv().boxer({
        appendTo: self.getParentDiv(),
        helpers: self
    });

    self.getForm().find('input[type="button"]').click(function(){
        self.getLwi().val(self.getLD().css('width'));
        self.getThi().val(self.getTD().css('height'));
        self.getTwi().val(self.getTD().css('width'));
        self.getBti().val(self.getBD().css('top'));
        self.getForm().submit();
    } );
    
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
});
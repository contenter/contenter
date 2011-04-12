$.widget("ui.boxer", $.extend({}, $.ui.mouse, {

  _init: function() {
    this.dragged = false;

    this.offset = this.element.offset();

    this._mouseInit();

    this.doc_height = this.options.helpers.getContentDiv().height();
    this.doc_width = this.options.helpers.getContentDiv().width();

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
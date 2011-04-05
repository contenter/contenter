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
        },
        getVisibilityDiv: function() {
            return $('#' + divs['visibilityDiv']);
        }
    };

    self.getContentDiv().css('width', $(document).width() );
    var doc_height = self.getContentDiv().height();
    var doc_width = self.getContentDiv().width();

    self.getLD().css({height: doc_height});
    self.getRD().css({height: doc_height, width: doc_width});
    self.getTransD().css({height: doc_height, width: doc_width});
    self.getDocumentw().val(doc_width + 'px');
    self.getDocumentyh().val(doc_height + 'px');
    
    self.getParentDiv().boxer({
        appendTo: self.getParentDiv(),
        helpers: self
    });
    
    self.getVisibilityDiv().css('visibility', 'visible');

    self.getForm().find('input[type="button"]').click(function(){
        self.getLwi().val(self.getLD().css('width'));
        self.getThi().val(self.getTD().css('height'));
        self.getTwi().val(self.getTD().css('width'));
        self.getBti().val(self.getBD().css('top'));
        self.getForm().submit();
    } );
});
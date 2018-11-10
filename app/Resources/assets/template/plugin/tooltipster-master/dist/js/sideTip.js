/**
 * Sidetip jquery plugin
 * @version 0.0.3
 * @author Brian Liccardo <brianliccardo@gmail.com>
 * works with either hidden elements or ajax content
 */
;(function($) {
    var sidetip = function(element, options) {
        this.hoverelement = element;
        this.options = options;
        this.hovertimeout = false;
        this.leavetimeout = false;
        this.cancelleavertimeout = false;
        this.data_url = false;
        this.data_ele = false;
        this.mode = false;

        this.init = function() {
            var self = this;

            // determin mode
            var data_url = this.hoverelement.attr('data-url');
            var data_ele = this.hoverelement.attr('data-ele');

            if (this.options.url !== false || (typeof data_url !== 'undefined' && data_url !== false)) {
                this.sidetip = $('#sidetip');
                this.mode = 'url';
                this.data_url = (this.options.url === false) ? data_url : this.options.url;
            } else if (this.options.ele !== false || (typeof data_ele !== 'undefined' && data_ele !== false)) {
                this.mode = 'ele';
                this.data_ele = (this.options.ele === false) ? data_ele : this.options.ele;

                // move ele to bottom of the page
                var dele = $(this.data_ele).detach();
                dele.appendTo('body');

                this.sidetip = $('#'+this.data_ele);
            } else if (this.options.html !== false) {
                this.sidetip = $('#sidetip');
                this.mode = 'html';
            }

            // attach events
            this.hoverelement.hover(
                function(){
                    if (self.cancelleavertimeout == false) {
                        self.hovertimeout = setTimeout(function(){
                            self.show();
                        },self.options.delay);
                    }
                },
                function(){
                    clearTimeout(self.hovertimeout);
                    if (self.sidetip.is(':visible')) {
                        this.hovertimeout = setTimeout(function(){
                            if (self.cancelleavertimeout == false) {
                                self.hide(true);
                            }
                        },300);
                    }
                }
            );

            this.sidetip.hover(
                function(){
                    clearTimeout(self.hovertimeout);
                    self.cancelleavertimeout = true;
                },
                function(){
                    self.cancelleavertimeout = false;
                    self.hovertimeout = setTimeout(function(){
                        self.hide(true);
                    },self.options.delay);
                }
            );
        };

        this.show = function() {
            var self = this;
            if (self.hoverelement.data('ajax') == 'cached') {
                self._show(self.hoverelement.data('sidetip'));
            } else {
                switch (this.mode) {
                    case 'url':
                        $.get(this.data_url, function(data){
                            self.hoverelement.data('sidetip', data);
                            self.hoverelement.data('ajax', 'cached');
                            self._show(data);
                        });
                        break;
                    case 'ele':
                        this._showEle(this.sidetip);
                        break;
                    case 'html':
                        this.hoverelement.data('sidetip', this.options.html);
                        this.hoverelement.data('ajax', 'cached');
                        this._show(this.options.html);
                        break;
                }
            }
        };

        this._getPos = function(tipele) {
            var pos = {top:0,left:0};

            var offset = this.hoverelement.offset();

            if (this.options.pos == 'left' || this.options.pos == 'right') {
                // left right
                pos.top = offset.top + parseInt(this.hoverelement.outerHeight() / 2) - parseInt(tipele.outerHeight() / 2);

                if (this.options.pos == 'left') {
                    // try to position left first
                    pos.left = offset.left - this.options.sideOffset - tipele.outerWidth();

                    if (pos.left < 1) {
                        pos.left = offset.left + this.options.sideOffset + this.hoverelement.outerWidth();
                    }
                } else {
                    // try to position left right
                    pos.left = offset.left + this.options.sideOffset + this.hoverelement.outerWidth();

                    if ((pos.left + this.hoverelement.outerWidth()) > $(document).width()) {
                        pos.left = offset.left - this.options.sideOffset - tipele.outerWidth();
                    }
                }
            } else {
                // top buttom
                pos.left = offset.left + parseInt(this.hoverelement.outerWidth() / 2) - parseInt(tipele.outerWidth() / 2);

                if (this.options.pos == 'bottom') {
                    pos.top = offset.top + this.options.sideOffset + this.hoverelement.outerHeight();

                } else {
                    pos.top = offset.top - this.options.sideOffset - tipele.outerHeight();
                }
            }

            return pos;
        };

        this._showEle = function(ele) {
            var pos = this._getPos(ele);
            ele.css({position: "absolute", marginLeft: 0, marginTop: 0,top: pos.top, left: pos.left});
            ele.fadeIn(300)
        };

        this._show = function(data) {
            if (this.sidetip.is(':visible')) this.sidetip.hide();
            this.sidetip.html(data);

            var pos = this._getPos(this.sidetip);

            this.sidetip.css({position: "absolute", marginLeft: 0, marginTop: 0,top: pos.top, left: pos.left, zIndex:1000});

            this.sidetip.fadeIn(300)
        }

        this.hide = function(fade) {
            if (fade) {
                this.sidetip.fadeOut(300)
            } else {
                this.sidetip.hide();
            }
        };

        this.init();
    };

    // plugin
    $.fn.sidetip = function(options) {
        var defaults = {delay:400,sideOffset:10,url:false,ele:false,html:false,pos:'right'};

        var options =  $.extend(defaults, options);

        $('body').append('<div class="sidetip" id="sidetip" style="display:none;"></div>');

        return this.each(function() {
            new sidetip($(this), options);
        });
    };
})(jQuery);
(function($){ 
	var Sollist = function(options, elem){
		var defaults ={
			profiles:{}
			,theme: 'lee-gargano-circle-color'
			,pixelsBetweenItems: 10
			,themeDirPath: ''
			,containerTag: 'ul'
			,elementTag: 'li'
			,size: 32
			,iconFileExtension: 'png'
			,hoverEffect: ''
			,newTab: true
			,showTooltips: false
			,tooltipsterOptions: {
				theme: "sl-tooltip-black"
			}
			,tooltipClass: "sl-tooltip"
            ,showTooltips: false
			,tooltips: {
				'googleplus': 'google+'
			}
			,itemClass: ''
			,itemCss: {}
		}
		var self = this;
		this.elem = elem;
		this.options = $.extend({}, defaults, options);
		this.options.tooltips = $.extend ({}, defaults.tooltips, this.options.tooltips)
		if (
			(!this.options.theme && !this.themeDirPath) ||
			!this.options.profiles ||
			!this.elem
		){
			return;
		}
		
		this.hoverFunctions = {
			myFadeOut: {
				inFunc: function($item) {
					$item.fadeTo(0.2, 0.5);
				}
				,outFunc: function($item) {
					$item.fadeTo(0.2, 1);
				}
			}

		}; 
		this.go();
	}
	$.extend(Sollist.prototype, {
		go: function(){
			this.setHoverFucntions();
			$container = $('<'+this.options.containerTag + '></'+this.options.containerTag+'>');
			var len = this.options.profiles.length; 
			for(var profileKey in this.options.profiles) {
				$container.addClass('sl-container');
				$container.append(this.itemElement(profileKey));
			}
			$container.children().last().css("margin-right", 0);
			this.elem.append($container);
            if(this.options.showTooltips) {
                $('.'+this.options.tooltipClass).tooltipster(this.options.tooltipsterOptions);
            }
			
		}
		,itemElement: function(key) {
			var $elem = $('<'+this.options.elementTag + '></'+this.options.elementTag+'>');
            $elem.addClass('sl-item');
            $elem.addClass(this.options.itemClass);
            if (this.options.hoverInFunction && this.options.hoverOutFunction) {
				$elem.hover(function(){
					self.options.hoverInFunction($(this));
				}, function(){
					self.options.hoverOutFunction($(this));
				});
			}else{
                $elem.addClass(this.hoverClass());
            }
			var elemCss = $.extend({}, {"margin-right": this.options.pixelsBetweenItems}, this.options.itemCss);
			$elem.css(elemCss);
			var self =this;
			
			var $link = $('<a></a>');
			$link.attr("href", this.options.profiles[key]);
			$link.height(this.options.size);
			$link.width(this.options.size);
			if (this.options.themeDirPath) {
				var $img = $('<img src = "' + this.socialIconPath(key) + '.' + this.options.iconFileExtension + '"/>');
				$link.append($img);
			}else {
				$link.addClass(key);
				$link.addClass(this.options.theme);
			}
			
			if (this.options.newTab) {
				$link.attr("target", "_blank");
			};
			if (this.options.showTooltips) {
				$link.addClass(this.options.tooltipClass);
				$link.attr("title", this.tooltip(key));
				$link.addClass(this.options.tooltipsTheme);
			};

			$elem.append($link);
			return $elem;
		}
		,hoverClass: function(){
			if (this.options.hoverEffect == 'fadeOut') {
				return '';
			};
			return this.options.hoverEffect;

		}
		,socialIconPath: function(key) {
			return this.options.themeDirPath + '/' + key;
		}
		,setHoverFucntions:function() {
			if(this.options.hoverOutFunction && this.options.hoverInFunction) {
				return;
			}
			if (this.options.hoverEffect == 'myFadeOut') {
				this.options.hoverInFunction = this.hoverFunctions[this.options.hoverEffect].inFunc;
				this.options.hoverOutFunction = this.hoverFunctions[this.options.hoverEffect].outFunc;
			};
			
		}
		,tooltip: function(key) {
			if(this.options.tooltips.hasOwnProperty(key)) {
				return this.options.tooltips[key];
			} 
			return key;
		}

	});	
	
	$.fn.sollist =function(options){
		this.each(function() {
			var elem = $(this);        	
			new Sollist(options,elem);	
	    });	
	}

})(jQuery);
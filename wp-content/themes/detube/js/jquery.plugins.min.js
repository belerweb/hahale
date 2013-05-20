/** 
 * jQuery ImagesLoaded Plugin v2.0.1
 *
 * A jQuery plugin that triggers a callback after all the selected/child 
 * images have been loaded. Because you can't do .load() on cached images.
 *
 * @license MIT License. by Paul Irish et al.
 * @link https://github.com/desandro/imagesloaded
 */
(function(c,n){var k="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";c.fn.imagesLoaded=function(l){function m(){var b=c(h),a=c(g);d&&(g.length?d.reject(e,b,a):d.resolve(e));c.isFunction(l)&&l.call(f,e,b,a)}function i(b,a){b.src===k||-1!==c.inArray(b,j)||(j.push(b),a?g.push(b):h.push(b),c.data(b,"imagesLoaded",{isBroken:a,src:b.src}),o&&d.notifyWith(c(b),[a,e,c(h),c(g)]),e.length===j.length&&(setTimeout(m),e.unbind(".imagesLoaded")))}var f=this,d=c.isFunction(c.Deferred)?c.Deferred():
0,o=c.isFunction(d.notify),e=f.find("img").add(f.filter("img")),j=[],h=[],g=[];e.length?e.bind("load.imagesLoaded error.imagesLoaded",function(b){i(b.target,"error"===b.type)}).each(function(b,a){var e=a.src,d=c.data(a,"imagesLoaded");if(d&&d.src===e)i(a,d.isBroken);else if(a.complete&&a.naturalWidth!==n)i(a,0===a.naturalWidth||0===a.naturalHeight);else if(a.readyState||a.complete)a.src=k,a.src=e}):m();return d?d.promise(f):f}})(jQuery);

/**
 * jQuery Cookie Plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options.expires=-1}var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000))}else{date=options.expires}expires='; expires='+date.toUTCString()}var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('')}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break}}}return cookieValue}};

/** 
 * deSelectMenu Plugin 1.0
 * 
 * Turn unordered list menu into dropdown select menu
 * Copyright (c) 2012 Cloud Stone (dedepress.com)
 * Dual licensed under the MIT and GPL licenses
 */
(function($){
$.fn.deSelectMenu = function( options ) {
	// Default options
	var defaults = {
		optionNone: 'Navigate to...'
	};
	settings = $.extend( {}, defaults, options );
	el = $(this);
		
	this.each(function(){
		// Create select menu
		el.after('<div class="select-div"><div class="select-wrap"><select class="select-menu"></select></div></div>');
		
		// Get select menu object
		var selectMenu = el.parents().find('.select-menu');
		
		// Option none
		$(selectMenu).append('<option value="">' + settings.optionNone + '</option>');
		
		// Build options
		$(el).find('li').each(function() {
			var href = $(this).children('a').attr('href');
			var selected = (href == window.location.href) ? ' selected="selected"' : '';
			var text = $(this).children('a').text();
			var depth = $(this).parents('ul').length;
			text = (depth > 1) ? '&mdash; ' + text : text;
			text = (depth > 2) ? '&nbsp;&nbsp;'.repeat(depth-1)+text : text;

			$(selectMenu).append('<option value="' + href + '"' + selected + '>' + text + '</option>');
		});
			
		// Change event on select element
		$(selectMenu).change(function() {
			location.href = this.options[this.selectedIndex].value;
		});
	});
};
})(jQuery);


/**
 * Responsive Carousel based on jCarousel plugin
 * Only for horizontal carousel
 * Cloud Stone (dedepress.com)
 */
(function($){
$.fn.deCarousel = function( options ) {
	// Default options
	var defaults = {
		animation: 600,
		easing: 'easeOutCubic',
		wrap: 'circular'
	};
		
	// Override defaults with specified option
	options = $.extend( {}, defaults, options );
		
	var $carousel = $(this),
		containerWidth = $carousel.find('.carousel-container').width(),
		itemWidth = $carousel.find('li').outerWidth(true),
		visibleItemCount = parseInt(containerWidth/itemWidth),
		exactClipWidth = visibleItemCount*itemWidth;
		
	$carousel.find('.carousel-clip').width(exactClipWidth);
		
	$carousel.find('.carousel-clip').jcarousel(options);
	$carousel.find('.carousel-prev').jcarouselControl({target: '-='+visibleItemCount});
	$carousel.find('.carousel-next').jcarouselControl({target: '+='+visibleItemCount});
		
	return false;
};
})(jQuery);
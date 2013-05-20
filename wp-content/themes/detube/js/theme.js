(function( $ ){

  "use strict";

  $.fn.fitVids = function( options ) {
    var settings = {
      customSelector: null
    };

    if ( options ) {
      $.extend( settings, options );
    }

    return this.each(function(){
      var selectors = [
        "iframe[src*='player.vimeo.com']",
        "iframe[src*='youtube.com']",
        "iframe[src*='youtube-nocookie.com']",
        "iframe[src*='kickstarter.com']",
        "object",
        "embed"
      ];

      if (settings.customSelector) {
        selectors.push(settings.customSelector);
      }

      var $allVideos = $(this).find(selectors.join(','));

      $allVideos.each(function(){
        var $this = $(this);
        if ((this.tagName.toLowerCase() === 'embed' && $this.parent('object').length) || $this.parents('.video-wrap').length) { 
			return; 
		}

        $this.wrap('<div class="video-wrap"></div>');
      });
    });
  };
})( jQuery );

jQuery(window).load(function(){
	jQuery("body").fitVids();
});

jQuery(document).ready(function($){



if(typeof themeSettings =='undefined')
	themeSettings = {};

/*= Misc
 *=================================*/

/*== HTML5 placeholder fallback */
$('input[type="text"]').each(function(){
	var placeholder = $(this).attr('placeholder');
		
	$(this).bind('focus', function(){
		if($(this).attr('value') == '')
			$(this).attr('value', '').attr('placeholder', '');
	}).bind('blur', function(){
		if($(this).attr('value') == '')
			$(this).attr('placeholder', placeholder);
	});
});

/* Resize Slide */
$(window).bind('load resize', function(){
	jQuery('.slide').each(function(){
		jQuery(this).width(jQuery(this).parents('.slider').width());
		jQuery(this).parents('.slides').height(jQuery(this).height());
	});
});

// Create repeat method if it is not exists
if (!String.repeat) {
	String.prototype.repeat = function(l) {
		return new Array(l + 1).join(this);
	}
}

/*= Responsive Navigation Menu */
$('#main-nav .menu').deSelectMenu({});

/*= Loop View Switcher
 *=================================*/
$('.loop-actions .view a').click(function(e) {
	e.preventDefault();
		
	var viewType = $(this).attr('data-type'),
		loop = $('.switchable-view'),
		loopView = loop.attr('data-view');
			
	if(viewType == loopView)
		return false;
			
	$(this).addClass('current').siblings('a').removeClass('current');

	loop.stop().fadeOut(100, function(){
		if(loopView)
			loop.removeClass(loopView);
			
		$(this).fadeIn().attr('data-view', viewType).addClass(viewType);
	});
	
	$('.loop-content .screen').remove();
	$('.loop-content .thumb').show();

	$.cookie('loop_view', viewType, { path: '/', expires : 999});

	return false;
});

// Change event on select element
$('.orderby-select').change(function() {
	location.href = this.options[this.selectedIndex].value;
});

/*= "More/less" Toggle
 *=================================*/
if(themeSettings.infoToggle) {
	var infoToggle = function(){
		var $this = this;
	
		var info = $('#info'),
			trueHeight = info.height(), 
			lessHeight = themeSettings.infoToggle,
			arrow = $('.info-arrow'),
			more = $('.info-more'),
			less = $('.info-less');

		if((trueHeight-lessHeight) > 50) {
			info.height(lessHeight);
			more.css('display', 'inline-block');
			arrow.css('display', 'inline-block');
		}
	
		$('.info-more').click(function(){
			$this.infoMore();
			return false;
		});
	
		$('.info-less').click(function(){
			$this.infoLess();
			return false;
		});
	
		$('.info-arrow').click(function(){
			if($(this).hasClass('info-arrow-more'))
				$this.infoMore();
			else
				$this.infoLess();
			
			return false;
		});	
	
		this.infoMore = function(){
			arrow.removeClass('info-arrow-more').addClass('info-arrow-less');
			more.hide();
			less.css('display', 'inline-block');
			info.stop().animate({'height':trueHeight}, 300);
		}
		
		this.infoLess = function(){
			arrow.removeClass('info-arrow-less').addClass('info-arrow-more');
			less.hide();
			more.css('display', 'inline-block');
			info.stop().animate({'height':lessHeight}, 300);
		}
	}
	var infoToggle = new infoToggle();
}
	
/*= Carousel
 *=================================*/	
if(jQuery().jcarousel) {
	// Featured Carousel - Horizontal 
	$(window).bind('load resize', function(){
		$('.fcarousel-6').deCarousel();
		$('.fcarousel-5').deCarousel();
	});
	
	// Setup the carousels.  
	var carouselStage = $('.home-featured .stage .carousel')
		.jcarousel({
			wrap: 'circular'
		});	
	if(themeSettings.autoScrollForHomeFeatured) {
		carouselStage.jcarouselAutoscroll({
			'interval': themeSettings.autoScrollForHomeFeatured
		});
	}
	
	var carouselNav = $('.home-featured .nav .carousel-clip')
		.jcarousel({
			vertical: true,
			wrap: 'circular'
		});
		
	// Setup controls for the stage carousel
	$('.prev-stage')
            .on('inactive.jcarouselcontrol', function() {
                $(this).addClass('inactive');
            })
            .on('active.jcarouselcontrol', function() {
                $(this).removeClass('inactive');
            })
            .jcarouselControl({
                target: '-=1'
            });

        $('.next-stage')
            .on('inactive.jcarouselcontrol', function() {
                $(this).addClass('inactive');
            })
            .on('active.jcarouselcontrol', function() {
                $(this).removeClass('inactive');
            })
            .jcarouselControl({
                target: '+=1'
            });

		
	// Setup controls for the navigation carousel
	$('.home-featured .carousel-prev').jcarouselControl({target: '-=4'});
	$('.home-featured .carousel-next').jcarouselControl({target: '+=4'});
		
	// We loop through the items of the navigation carousel and set it up
    // as a control for an item from the stage carousel.
	carouselNav.jcarousel('items').each(function() {
    var item = $(this);

    // This is where we actually connect to items.
     var target = carouselStage.jcarousel('items').eq(item.index());

    item
		.on('active.jcarouselcontrol', function() {
            carouselNav.jcarousel('scrollIntoView', this);
             item.addClass('active');
        })
        .on('inactive.jcarouselcontrol', function() {
			item.removeClass('active');
		})
		.jcarouselControl({
			target: target,
			carousel: carouselStage
       });
    });
}

/*= Ajax
 *=================================*/	
$('.home-featured .stage .item-video').each(function(){
	if($(this).find('.screen').length)
		$(this).find('.thumb, .caption').hide();
});
	/*== Ajax video for Featured Content with standard layout on Home Page */
if(!themeSettings.autoScrollForHomeFeatured && themeSettings.ajaxLoadForHomeFeatured) {
	$('.home-featured .stage .item-video .thumb a').on('click', function(e){
		e.preventDefault();
		
		var el = $(this), 
			pid = el.attr('data-id'), 
			thumb = el.parents('.thumb'),
			caption = el.parents('.item').find('.caption')
			slides = el.parents('.carousel-list'); 
		
		// Prevent duplicate clicks
		if(el.attr('data-clickable') == 'no') 
			return false;
		slides.find('.thumb a').attr('data-clickable', '');
		el.attr('data-clickable', 'no');
		
		slides.find('.screen').remove();
		slides.find('.caption').show();
		slides.find('.thumb').show().removeClass('loading');
		caption.hide();
		thumb.addClass('loading');
			
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {action:'ajax-inline-video', 'id':pid},
			dataType: 'html',
			error: function(){
				alert(ajaxerror);
				thumb.removeClass('loading');
				el.attr('data-clickable', '');
			},
			success: function(r){
				thumb.before('<div class="screen"></div>');
				var screen = thumb.prev('.screen');
				screen.hide().html(r);
					
				if(screen.find('iframe').length > 0) {
					screen.find('iframe').load(function(){
						screen.show();
						thumb.hide().removeClass('loading');
						el.attr('data-clickable', '');
					});
				} else {
					screen.show();
					thumb.hide().removeClass('loading');
					el.attr('data-clickable', '');
				}
				
				/* Eval Scripts
				var dom = $(r);
				dom.filter('script').each(function(){
					$.globalEval(this.text || this.textContent || this.innerHTML || '');
				});*/
			}
		});
		
		return false;
	});
}

	// Stop video playing when click thumbnail
	$('.home-featured .nav .carousel-list a').bind('click', function(e){
		e.preventDefault();
			
		$('.home-featured .stage .screen').remove();
		$('.home-featured .stage .thumb').show();
		$('.home-featured .stage .caption').show();
	});
	
	/*== Ajax video for Featured Content with Full Width layout on Home Page */
	$('.home-featured-full .carousel .item-video a').on('click', function(e){
		e.preventDefault();  
		
		var el = $(this), pid = el.attr('data-id'), video = $('#video');
			
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {action:'ajax-video', 'id':pid},
			dataType: 'html',
			error: function(){
				alert(ajaxerror);
			},
			success: function(r){
				video.html(r);

				// Re-initialize AddThis
				addthis.toolbox('.addthis_toolbox');
				addthis.counter(".addthis_counter");
				
				/* Eval Scripts
				var dom = $(r);
				dom.filter('script').each(function(){
					$.globalEval(this.text || this.textContent || this.innerHTML || '');
				});*/

				el.parents('li').addClass('current').siblings().removeClass('current');
			}
		});
		
		return false;
	});

	/*== Ajax video for 'List Large' view */
if(themeSettings.ajaxVideoForListLargeView) {
	$('.list-large .item-video .thumb a').on('click', function(e){
		e.preventDefault();
		
		var el = $(this), pid = el.attr('data-id'), thumb = el.parents('.thumb');
		
		// Prevent duplicate clicks
		if(el.attr('data-clickable') == 'no') 
			return false;
		$('.list-large .thumb a').attr('data-clickable', '');
		el.attr('data-clickable', 'no');
		
		$('.list-large .screen').remove();
		$('.list-large .thumb').show().removeClass('loading');
		thumb.addClass('loading');
			
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {action:'ajax-inline-video', 'id':pid},
			dataType: 'html',
			error: function(){
				alert(ajaxerror);
				thumb.removeClass('loading');
				el.attr('data-clickable', '');
			},
			success: function(r){
				thumb.before('<div class="screen"></div>');
				var screen = thumb.prev('.screen');
				screen.hide().html(r);
					
				if(screen.find('iframe').length) {
					screen.find('iframe').load(function(){
						screen.show();
						thumb.hide().removeClass('loading');
						el.attr('data-clickable', '');
					});
				} else {
					screen.show();
					thumb.hide().removeClass('loading');
					el.attr('data-clickable', '');
				}
				
				/* Eval Scripts
				var dom = $(r);
				dom.filter('script').each(function(){
					$.globalEval(this.text || this.textContent || this.innerHTML || '');
				});*/
			}
		});
		
		return false;
	});
}

});
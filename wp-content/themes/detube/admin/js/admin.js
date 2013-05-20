jQuery.noConflict();
jQuery(document).ready(function($) {
	/* Upload, Insert and Preview Image */
	var _ste = window.send_to_editor;
	$('.thickbox').live('click', function(){
		if($(this).hasClass('dp-upload-button')) {
			var preview = $(this).siblings('.dp-upload-preview'),
				text = $(this).siblings('.dp-upload-text');
		
			window.send_to_editor = function(html) {
				var imgurl = $(html).find('img').attr('src');
				if(!imgurl)
					imgurl = $(html).attr('src');
				if(imgurl) {
					text.val(imgurl);
					preview.html('<img src="'+imgurl+'" />');
				}
				window.send_to_editor = _ste;
				tb_remove();
			}
			
			return false;
		} else {
			window.send_to_editor = _ste;
		}
	});
	$('.dp-remove-button').live('click', function(e){
		e.preventDefault();
		preview = $(this).siblings('.dp-upload-preview');
		text = $(this).siblings('.dp-upload-text');
		text.val('');
		preview.empty();
	});
	$('.dp-upload-preview').each(function(){
		text = $(this).siblings('.dp-upload-text');
		if(text.val())
			$(this).html('<img src="'+text.val()+'" />');
	})
	
	/* Toggle and Reset Button */
	$(".toggel-all").click(function(){
		if($(".postbox").hasClass("closed")) {
			$(".postbox").removeClass("closed");
		} else {
			$(".postbox").addClass("closed");
		};
		postboxes.save_state(pagenow);
				
		return false;
	});
	$('.reset').click(function(){
		if (confirm("Are you sure you want to reset to default options?")) { 
			return true;
		} else { 
			return false; 
		}
	});		
	
	/* Color Picker */
	$('.dp-color-handle').each(function(){
		current_color = $(this).next('.dp-color-input').attr('value');
		$(this).css('backgroundColor', current_color);
		var c = $(this).ColorPicker({
			color: $(this).next('.dp-color-input').attr('value'),
			onChange: function (hsb, hex, rgb, el) {
				$(c).css('backgroundColor', '#' + hex);
				$(c).next('.dp-color-input').attr('value', '#' + hex);
			}
		});
	});
	
	
	/* Color Sheme Change */
	$('#dp-color-scheme').change(function(){
		if($(this).val() == 'custom') {
			$('.in-color-scheme').parents('tr').show();
		} else {
			$('.in-color-scheme').parents('tr').hide();
		}
	}).change();
	
	/* Pattern Change */
	$('#dp-preset-bgpat').change(function(){
		var pat = $(this).val();
		if(pat != '')
			$('.dp-preset-bgpat-preivew').css('background', 'url('+pat+')');
	}).change();

	/* Logo Type */
	$('#dp-logo-type').change(function(){
		if($(this).val() == 'text') {
			$('#dp-logo').parents('tr').hide();
		} else {
			$('#dp-logo').parents('tr').show();
		}
	}).change();

	/* Sortable List */
	if(jQuery().sortable) {
	$('.sortable-list').sortable({
		cursor: 'move'
	});
	}
	
	$('.handler .up').click(function(){
		var currentItem = $(this).parents('li');
		var prevItem = currentItem.prev('li');
		
		prevItem.before(currentItem);
	});
	
	$('.handler .down').click(function(){
		var currentItem = $(this).parents('li');
		var nextItem = currentItem.next('li');
		
		nextItem.after(currentItem);
	});
	
	/* Toggle Section boxes */
	$('.section-handlediv, .section-hndle').live('click', function(){
		$(this).parents('.section-box').find('.section-inside').toggle();
	});
});
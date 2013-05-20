/*
HTML Example:
<div class="item-list-container">
	<a href="#" class="add-new-item">Add New Slide</a>
	<ul class="item-list ui-sortable" id="mustxxx-item-list">
	</ul>
	<ul class="item-list-sample" id="mustxxx-item-list-sample">
		<li>
			// Do someting...
			<a href="#" class="delete-item">Delete Item</a>
		</li>
	</ul>
</div>
*/

jQuery(function($) {
function init_items() {
	var b = $('.item-list-container');
	b.each(function(){
		var b = $(this), c = b.find('.item-list'),f = $('#'+c.attr('id')+'-sample');
		f.appendTo('body');
		
		c.sortable({
			handle:".section-hndle",
			cursor:'move',
			placeholder: 'sortable-placeholder',
			start: function(e, ui) {
				height = ui.item.outerHeight();
				ui.placeholder.height(height);
			}
		});
		
		c.find('li').each( function() {
		if (!this.h) {
			var g = $(this), d = g.find(".delete-item");
			d.live('click', function(e) {
				e.preventDefault();
				
				if (confirm("Are you sure you want to delete this item?")) { 
					g.animate({opacity: 0.25},500,function(){g.remove();}); 
				} else { return false; }
			});
			
			this.h = true;
		}
		});
	});
}

function add_item() {
	var b = $('.item-list-container');
	b.each(function(){
		var b = $(this), c = b.find('.item-list'), t = $('#'+c.attr('id')+'-sample').children();
		
		b.find('.add-new-item').click(function(e) {
			e.preventDefault();
			var n = [];
			c.find('li').each( function() {
				var f = $(this);
				var r = f.attr('rel');
				n.push(parseInt(r));
			});
			var d = c.find('li').length;
			
			// if(d > 0) d -= 1;
			
			while ($.inArray(d, n) != -1 ) { d++; }

			var g = t.clone();
			if(t.attr('id'))
				g = g.attr('id',t.attr('id').replace('##',d));
			g = g.attr('rel',d).html(t.html().replace(/##/ig,d));
			
			if($(this).attr('data-position') == 'prepend')
				g.prependTo(c);
			else
				g.appendTo(c);
			
			init_items();
			
			return false;
		});
	});
}

	init_items();
	add_item();
});
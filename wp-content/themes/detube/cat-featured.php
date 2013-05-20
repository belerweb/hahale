<?php
/**
 * The template for displaying featured posts on category pages
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>

<?php
	$args = (array)get_option('dp_cat_featured');
	// If user set 'Post Number' to 0 or leave it empty in theme options, return.
	if(empty($args['posts_per_page']))
		return;
	$args['posts_per_page'] = 14;
	$args['current_cat'] = true;
	$args = dp_parse_query_args($args);
	$query = new WP_Query($args); 
	
	if($query->have_posts()):

		// Load scripts only when needed
		wp_enqueue_script('jquery-carousel');
		
		// Get items
		$items = '';
		$i = 0;
		while ($query->have_posts()) : $query->the_post(); 			
			$thumb_html = dp_thumb_html('custom-small', '', '', false);
			
			// Build classname
			$classes = array();
			$classes[] = is_video() ? 'item-video' : 'item-post';
			$class = implode(' ', $classes);
			
			$items .= '<li class="'.$class.'">'.$thumb_html.'</li>';
		endwhile; ?>
	
	<div class="cat-featured wall">
		<div class="carousel fcarousel fcarousel-5 wrap cf">
		<div class="carousel-container">
			<div class="carousel-clip">
				<ul class="carousel-list"><?php echo $items; ?></ul>
			</div><!-- end .carousel-clip -->
			
			<div class="carousel-prev"></div>
			<div class="carousel-next"></div>
		</div><!-- end .carousel-container -->
		</div><!-- end .carousel -->
	</div><!-- end .cat-featured -->

	<?php endif; wp_reset_query(); ?>
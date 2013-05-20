<?php
/**
 * The template for displaying featured posts on home page
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.1
 */
?>

<?php
	$args = (array)get_option('dp_home_featured');
	$args = dp_parse_query_args($args);
	$autoplay = !empty($args['autoplay']) ? true : false;
	$query = new WP_Query($args); 
?>
	
<?php if($query->have_posts()): ?>
	<?php
		/* Load scripts only when needed */
		wp_enqueue_script('jquery-carousel');
		wp_enqueue_script('jplayer'); 
	?>
		
	<div class="home-featured-full wall">
		
	<?php
		$items = ''; $i = 0;
		while ($query->have_posts()) : $query->the_post(); global $post; $i++;
		
			/* Output first post
			 *============================================*/
			if($i == 1) { ?>
				<div id="video" class="wrap cf">
					<div id="headline" class="cf">
						<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	
						<div id="actions">
							<?php dp_like_post(); ?>
							
							<div class="dropdown dp-share">
								<a class="dropdown-handle" href="#"><?php _e('Share', 'dp'); ?></a>
				
								<div class="dropdown-content">
									<?php dp_addthis(array('post_id'=>$post->ID)); ?>
								</div>
							</div>
						</div>
					</div><!-- end #headline -->
					
					
	
					<div id="screen"><div id="screen-inner">
						<?php
							if(is_video($post->ID)) {
								dp_video($post->ID, $autoplay); 
							} else {
								$thumb_size = 'custom-full';
								dp_thumb_html($thumb_size);
							}
						?>
					</div></div><!-- end #screen -->
				</div><!-- end #video -->
			<?php } 

			/* Get carousel items
			 *============================================*/
			
			// Get Thumbnail html
			$thumb_html = dp_thumb_html('custom-small', '', '', false);
			
			// Build classname
			$classes = array('item');
			$classes[] = ($i == 1) ? 'current' : ''; // Add 'current' class to first post
			$classes[] = is_video() ? 'item-video' : 'item-post'; // Add item form class
			$class = implode(' ', $classes);
			
			$items .= '<li class="'.$class.'">'.$thumb_html.'</li>';

		endwhile; 
	?>
		
		<?php // Output carousel ?>
		<div class="carousel fcarousel fcarousel-6 wrap cf">
		<div class="carousel-container">
			<div class="carousel-clip">
				<ul class="carousel-list"><?php echo $items; ?></ul>
			</div><!-- end .carousel-clip -->
			
			<a class="carousel-prev" href="#"></a>
			<a class="carousel-next" href="#"></a>
		</div><!-- end .carousel-container -->
		</div><!-- end .carousel -->
		
	</div><!-- end #wall -->
<?php endif; wp_reset_query(); ?>
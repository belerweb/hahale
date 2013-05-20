<?php
/**
 * The template for displaying featured posts on home page
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
?>
<div class="home-featured wall">

<div class="wrap cf">
			
<?php
	$args = (array)get_option('dp_home_featured');
	$args = dp_parse_query_args($args);
	$ajaxload = !empty($args['ajaxload']) ? true : false;
	$autoscroll = !empty($args['autoscroll']) ? true : false;
	if(!empty($args['autoslideshow']))
		$autoplay = false;
	else
		$autoplay = !empty($args['autoplay']) ? true : false;
	$query = new WP_Query($args);
?>
	
<?php if($query->have_posts()): ?>

	<?php
		/* Load scripts only when needed */
		wp_enqueue_script('jquery-carousel');
		wp_enqueue_script('jquery-slides'); 
		wp_enqueue_script('jplayer'); 
	?>

	<div class="stage">
		<div class="carousel">
		<div class="carousel-list">
			<?php $items = ''; $i = 0; while ($query->have_posts()) : $query->the_post(); global $post; $i++; ?>
			<div class="item <?php echo is_video() ? 'item-video' : 'item-post'; ?>" data-id="<?php the_ID(); ?>">
				<?php
					if(empty($args['autoscroll']) && is_video($post->ID) && $i == 1) {
						echo '<div class="screen">';
							dp_video($post->ID, $autoplay);
						echo '</div>';
					}

					$thumb_size = 'custom-large';
					dp_thumb_html($thumb_size);
				?>
			
				<div class="caption"<?php if(empty($args['autoscroll']) && is_video($post->ID) && $i == 1) echo 'style="display:none;"'; ?>>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf(__('Permalink to %s', 'dp'), get_the_title()); ?>"><?php the_title(); ?></a></h2>
				</div>
			</div><!-- end .item -->
			<?php endwhile; ?>
			</div><!-- end .carousel-list -->
		</div><!-- end .carousel -->
	</div><!-- end .stage -->
		
	<div class="nav">
	<div class="carousel">
		<div class="carousel-clip">
			<ul class="carousel-list">
				<?php $items = ''; $i = 0; while ($query->have_posts()) : $query->the_post(); global $post; ?>
				<li data-id="<?php the_ID(); ?>" class="<?php echo is_video() ? 'item-video' : 'item-post'; ?>">
				<div class="inner">
					<?php
					$thumb_size = 'custom-small';
					dp_thumb_html($thumb_size);
					?>
			
					<div class="data">
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf(__('Permalink to %s', 'dp'), get_the_title()); ?>"><?php the_title(); ?></a></h2>
			
						<p class="meta">
							<span class="time"><?php printf(__('%s ago', 'dp'), human_time(get_the_time('U'))); ?></span>
						</p>
					</div>
				</div>
				</li>
				<?php $i++; endwhile; ?>
			</ul>
		</div><!-- end .carousel-clip -->
		
		<a class="carousel-prev" href="#"></a>
		<a class="carousel-next" href="#"></a>
	</div><!-- end .carousel -->
	</div><!-- end .nav -->

<?php endif; wp_reset_query(); ?>

</div><!-- end .wrap -->
</div><!-- end #wall -->
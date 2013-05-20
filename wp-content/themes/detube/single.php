<?php
/**
 * The Template for displaying all single posts.
 *
 * @package deTube
 * @subpackage Template
 * @since deTbue 1.0
 */

global $post;

// Get video layout
$video_layout = get_post_meta($post->ID, 'dp_video_layout', true);
if(!$video_layout)
	$video_layout = get_option('dp_single_video_layout');
if(!$video_layout)
	$video_layout = 'standard';

// Check the current post is a video post and get template based on the video layout
if(is_video()) {
	if($video_layout == 'full-width')
		get_template_part('single-video-full-width'); 
	else
		get_template_part('single-video'); 
	
	return;
}

get_header(); ?>

<div id="main"><div class="wrap cf">
	
	<div id="headline" class="cf">
	<div class="inner">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	
		<div id="actions">
			<?php dp_like_post(); ?>
			
			<div class="dropdown dp-share">
				<a class="dropdown-handle" href="#"><?php _e('Share', 'dp'); ?></a>
				
				<div class="dropdown-content">
					<?php dp_addthis(); ?>
				</div>
			</div>
		</div>
	</div><!-- end #headline>.inner -->
	</div><!-- end #headline -->
	
	<div id="content" role="main">
		<?php while (have_posts()) : the_post(); global $post;?>
		
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">		
		
		<div id="details" class="section-box">
			
				<p class="entry-meta">
					<span class="author"><?php _e('Added by', 'dp'); ?> <?php the_author_posts_link(); ?></span>
					<span class="time"><?php _e('on', 'dp'); ?> <?php the_date(); ?></span>
					
					<?php edit_post_link(__('Edit', 'dp'), ' <span class="sep">/</span> '); ?>
				</p>
				
				<?php 
				// Thumbnail
				global $post;
				$post_type = get_post_type($post_type);
				$post_format = get_post_format($post->ID);
				if(!$post_format && $post_type == 'post' && get_option('dp_single_thumb')) {
					$thumb_url = dp_thumb_url('custom-large', '', $post->ID, false);
					if(!empty($thumb_url)) {
						echo '<div id="thumb" class="rich-content"><img src="'.$thumb_url.'" alt="'.esc_attr(get_the_title($post->ID)).'" /><span class="vertical-align"></div>';
					}
				}
				?>

				<div class="entry-content rich-content">
					<?php the_content(); ?>
					<?php wp_link_pages(array('before' => '<p class="entry-nav pag-nav"><span>'.__('Pages:', 'dp').'</span> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div><!-- end .entry-content -->
			
				<div id="extras">
					<h4><?php _e('Category:', 'dp'); ?></h4> <?php the_category(', '); ?>
					<?php the_tags('<h4>'.__('Tags:', 'dp').'</h4>', ', ', ''); ?>
				</div>
			
		</div><!--end #deatils-->
		</div><!-- end #post-<?php the_ID(); ?> -->
		
		<?php 
			dp_related_posts(array(
				'number'=>get_option('dp_related_posts'), 
				'view'=>get_option('dp_related_posts_view', 'grid-mini')
			)); 
		?>

        <?php comments_template('', true); ?>

		<?php endwhile; ?>
	</div><!-- end #content -->

	<?php get_sidebar(); ?>

</div></div><!-- end #main -->
	
<?php get_footer(); ?>
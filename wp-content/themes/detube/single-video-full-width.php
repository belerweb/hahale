<?php
/**
 * The Template for displaying all single video post with full width layout.
 *
 * @package deTube
 * @subpackage Template
 * @since deTbue 1.1
 */

get_header(); ?>

<div class="wall">
<div id="video" class="wrap cf">
	<div id="headline" class="cf">
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
	</div><!-- end #headline -->
	
	<div id="screen">
		<?php dp_video($post->ID, get_option('dp_single_video_autoplay')); ?>
	</div><!-- end #screen -->
</div><!-- end #video -->
</div><!-- end #wall -->

<div id="main"><div class="wrap cf">
	<div id="content" role="main">
		<?php while (have_posts()) : the_post(); global $post;?>
		
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<div id="details" class="section-box">
			<div class="section-header">
				<h2 class="section-title"><?php _e('Details', 'dp'); ?> <a href="#" class="info-arrow info-arrow-more"></a></h2>
			</div>
			
			<div class="section-content">
			<div id="info" data-height="150">
				<p class="entry-meta">
					<span class="author"><?php _e('Added by', 'dp'); ?> <?php the_author_posts_link(); ?></span>
					<span class="time"><?php _e('on', 'dp'); ?> <?php the_date(); ?></span>
					
					<?php edit_post_link(__('Edit', 'dp'), ' <span class="sep">/</span> '); ?>
				</p>

				<div class="entry-content rich-content">
					<?php the_content(); ?>
					<?php wp_link_pages(array('before' => '<p class="entry-nav pag-nav"><span>'.__('Pages:', 'dp').'</span> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div><!-- end .entry-content -->
			
				<div id="extras">
					<h4><?php _e('Category:', 'dp'); ?></h4> <?php the_category(', '); ?>
					<?php the_tags('<h4>'.__('Tags:', 'dp').'</h4>', ', ', ''); ?>
				</div>
			</div><!-- end #info -->
			</div><!-- end .section-content -->
			
			<div class="info-toggle">
				<a href="#" class="info-more"><?php _e('Show more', 'dp'); ?></a>
				<a href="#" class="info-less"><?php _e('Show less', 'dp'); ?></a>
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
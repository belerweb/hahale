<?php
/**
 * Template Name: Archives
 *
 * A template to use on pages that lists your categories, archives, and last posts.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.1
 */

get_header(); ?>

<div id="main"><div class="wrap cf">
	
	<div id="headline"><div class="inner">
		<h1 class="page-title"><?php the_title(); ?></h1>
	</div></div><!-- end #headline -->
	
	<div id="content" role="main">
	
		<?php while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			
			<div class="page-content rich-content">
				
				<?php
					// Recently Updated
					$args = array(
						'view' => 'list-small',
						'title' => __('Recently Added', 'dp'),
						'post_type' => 'post',
						'ignore_sticky_posts' => true,
						'posts_per_page' => 12,
						'orderby' => 'date'
					);
					dp_section_box($args);
					
					// Most Liked
					$args = array(
						'view' => 'grid-medium',
						'title' => __('Most Liked', 'dp'),
						'post_type' => 'post',
						'ignore_sticky_posts' => true,
						'posts_per_page' => 6,
						'orderby' => 'likes'
					);
					dp_section_box($args);
					
					// Most Viewed
					$args = array(
						'view' => 'grid-mini',
						'title' => __('Most Viewed', 'dp'),
						'post_type' => 'post',
						'ignore_sticky_posts' => true,
						'posts_per_page' => 12,
						'orderby' => 'views'
					);
					dp_section_box($args);
					
					// Most Commented
					$args = array(
						'view' => 'grid-small',
						'title' => __('Most Commented', 'dp'),
						'post_type' => 'post',
						'ignore_sticky_posts' => true,
						'posts_per_page' => 9,
						'orderby' => 'comments'
					);
					dp_section_box($args);
				?>

				<?php wp_link_pages(array('before' => '<p class="entry-nav pag-nav"><strong>'.__('Pages:', 'dp').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>
			
		</div><!--end .hentry-->
		<?php endwhile; ?>
		
	</div><!--end #content-->
	
	<div id="sidebar">
		<?php
			$widget_args = array(
				'before_title' => '<div class="widget-header"><h3 class="widget-title">',
				'after_title' => '</h3></div>',
			);
			
			the_widget('WP_Widget_Calendar', array('title'=>__('Calendar', 'dp')), $widget_args); 
			the_widget('WP_Widget_Categories', array('title'=>__('Category Archives', 'dp'), 'count'=>true), $widget_args); 
			the_widget('WP_Widget_Archives', array('title'=>__('Monthly Archives', 'dp')), $widget_args); 
			the_widget('WP_Widget_Tag_Cloud', array('title'=>__('Tag Archives', 'dp')), $widget_args); 
			the_widget('DP_Widget_Comments', array('title'=>__('Recent Comments', 'dp'), 'number'=>5), array_merge($widget_args, array('widget_id'=>'widget-comments-on-archives-page'))); 
		?>
	</div><!--end #sidebar-->

</div></div><!-- end #main -->

<?php get_footer(); ?>
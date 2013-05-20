<?php
/**
 * DP Related Posts Widget
 *
 * Display related posts on single post pages.
 * 
 * @package deTube
 * @subpackage Widgets
 * @since deTube 1.2.5
 */

class DP_Widget_Related_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget-posts widget-related-posts', 'description' => __( "Display related posts on single post pages.", 'dp') );
		parent::__construct('dp-widget-related-posts', __('DP Related Posts Widget', 'dp'), $widget_ops);
		$this->alt_option_name = 'alt_dp_widget_related_posts';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		if(!is_singular())
			return;
			
		$cache = wp_cache_get('dp_widget_related_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}
		
		$style = isset($instance['style']) ? $instance['style'] : 'list';
		
		extract($args);
		ob_start();

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'dp') : $instance['title'], $instance, $this->id_base);
		
		?>
		
		<?php echo $before_widget; ?>
		
		<?php $r = dp_related_posts(array('number'=>$instance['posts_per_page'], 'fields'=>'object')); 
		if(!$r || !is_object($r) || !$r->have_posts())
			return;
		?>
		
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		
		<ul class="<?php echo 'post-'.$style; ?>">
			<?php while ($r->have_posts()) : $r->the_post(); $item_format = is_video() ? 'video' : 'post'; ?>
			<li class="item cf <?php echo 'item-'.$item_format; ?>">
				<?php 
					$image_size = ($style == 'list-full') ? 'custom-medium' : 'custom-small';
					dp_thumb_html($image_size);
				?>
				
				<div class="data">
					<h4 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title()); ?>"><?php the_title(); ?></a></h4>
				
					<p class="meta">
						<span class="author"><?php _e('Added by', 'dp'); ?> <?php the_author_posts_link(); ?></span>
						<span class="time"><?php printf(__('%s ago', 'dp'), human_time(get_the_time('U'))); ?></span>
					</p>
					
					<p class="stats"><?php echo dp_get_post_stats(); ?></p>
				</div>
			</li>
			<?php endwhile; wp_reset_query(); ?>
		</ul>
		
		<?php echo $after_widget; ?>
		
		<?php

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('dp_widget_related_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$new_instance['title'] = strip_tags($new_instance['title']);
		$new_instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['alt_dp_widget_related_posts']) )
			delete_option('alt_dp_widget_related_posts');

		return $new_instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('dp_widget_related_posts', 'widget');
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __('Related Posts', 'dp'),
			'posts_per_page' => 6,
			'orderby' => 'date',
			'order' => 'desc',
			'style' => 'list',
			'cat' => '',
			'current_cat' => true,
			'current_author' => true,
			'post__in' => '',
			'style' => 'list', // list, list-full, grid-2 or grid-3
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 

		// Styles
		$styles = array( 
			'list' => __( 'List with Thumbnail', 'dp' ),
			'list-full' => __( 'List with Full Width Thumbnail', 'dp' ),
			'grid-2' => __( '2 Columns Grid', 'dp' ), 
			'grid-3' => __( '3 Columns Grid', 'dp' )
		);
		
		$dropdown_categories = wp_dropdown_categories(array(
			'echo' => 0, 
			'name' => $this->get_field_name( 'cat' ),
			'selected' => $instance['cat'],
			'show_option_all' => __('All', 'dp'),
			'class' => 'widefat'
		));
		
		$dropdown_sort_types = dp_dropdown_sort_types(array(
			'echo' => 0, 
			'name' => $this->get_field_name( 'orderby' ),
			'selected' => $instance['orderby'],
			'class' => 'widefat'
		));
	
		$dropdown_order_types = dp_dropdown_order_types(array(
			'echo' => 0, 
			'name' => $this->get_field_name( 'order' ),
			'selected' => $instance['order'],
			'class' => 'widefat'
		)); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'dp') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e('Number:', 'dp') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" value="<?php echo $instance['posts_per_page']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e('Style:', 'dp') ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
				<?php foreach ( $styles as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['style'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		
	<?php }
}

// Register Widget
add_action('widgets_init', 'register_dp_widget_related_posts');
function register_dp_widget_related_posts() {
	register_widget('DP_Widget_Related_Posts');
}
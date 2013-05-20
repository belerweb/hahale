<?php
/**
 * DP Posts Widget
 *
 * Display posts in various ways you'd like.
 * 
 * @package deTube
 * @subpackage Widgets
 * @since deTube 1.0
 */

class DP_Widget_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget-posts', 'description' => __( "Display posts in various ways you'd like.", 'dp') );
		parent::__construct('dp-widget-posts', __('DP Posts Widget', 'dp'), $widget_ops);
		$this->alt_option_name = 'alt_dp_widget_posts';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('dp_widget_posts', 'widget');

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
		
		$query_args = $instance;
		$query_args['no_found_rows'] = true;
		$query_args = dp_parse_query_args($query_args);
		
		$r = new WP_Query( apply_filters( 'dp_widget_posts_args', $query_args ) );
		
		if ($r->have_posts()) : ?>
		
		<?php echo $before_widget; ?>
		
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
			<?php endwhile; ?>
		</ul>
		
		<?php echo $after_widget; ?>
		
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('dp_widget_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$new_instance['title'] = strip_tags($new_instance['title']);
		$new_instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
		
		$new_instance['current_cat'] = isset($new_instance['current_cat']);
		$new_instance['current_author'] = isset($new_instance['current_author']);
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['alt_dp_widget_posts']) )
			delete_option('alt_dp_widget_posts');

		return $new_instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('dp_widget_posts', 'widget');
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __('Recent Posts', 'dp'),
			'posts_per_page' => 6,
			'orderby' => 'date',
			'order' => 'desc',
			'style' => 'list',
			'cat' => '',
			'current_cat' => true,
			'current_author' => true,
			'post__in' => '',
			'views_timing' => '',
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
		
		$views_timings = dp_views_timings();
		
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
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e('Orderby:', 'dp') ?></label> 
			<?php echo $dropdown_sort_types; ?>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e('Order:', 'dp') ?></label> 
				<?php echo $dropdown_order_types; ?>
		</p>
		
		<?php if(function_exists('baw_pvc_main')) { ?>
		<p>
		<label for="<?php echo $this->get_field_id( 'views_timing' ); ?>"><?php _e('Views Timing:', 'dp') ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'views_timing' ); ?>" name="<?php echo $this->get_field_name( 'views_timing' ); ?>">
				<?php foreach ( $views_timings as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['views_timing'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<?php } ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e('Category:', 'dp') ?></label> 
			<?php echo $dropdown_categories; ?>
		</p>
		<p><input id="<?php echo $this->get_field_id('current_cat'); ?>" name="<?php echo $this->get_field_name('current_cat'); ?>" type="checkbox" <?php checked(!empty($instance['current_cat']) ? $instance['current_cat'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('current_cat'); ?>"><?php _e('Limit posts by current category on category archive pages?', 'dp'); ?></label></p>
		
		<p><input id="<?php echo $this->get_field_id('current_author'); ?>" name="<?php echo $this->get_field_name('current_author'); ?>" type="checkbox" <?php checked(!empty($instance['current_author']) ? $instance['current_author'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('current_author'); ?>"><?php _e('Limit posts by current author on author archive pages?', 'dp'); ?></label></p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'post__in' ); ?>"><?php _e('Includes:', 'dp') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'post__in' ); ?>" name="<?php echo $this->get_field_name( 'post__in' ); ?>" value="<?php echo $instance['post__in']; ?>" />
		</p>
		<p class="description">
			<?php _e('If you want to display specific posts, enter post ids to here, separate ids with commas, (e.g. 1,2,3,4). <br />if this field is not empty, category will be ignored. <br/>If you want to display posts sort by the order of your enter IDs, set "Sort" field as <strong>None</strong>.', 'dp'); ?>
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
add_action('widgets_init', 'register_dp_widget_posts');
function register_dp_widget_posts() {
	register_widget('DP_Widget_Posts');
}
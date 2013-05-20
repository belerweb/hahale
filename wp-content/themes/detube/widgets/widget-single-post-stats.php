<?php
/**
 * DP Single Post Stats Widget
 *
 * Display the stats of a post.
 * 
 * @package deTube
 * @subpackage Widgets
 * @since deTube 1.0
 */

class DP_Widget_Single_Post_Stats extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget-single-post-stats', 'description' => __('Display the stats of a post.', 'dp'));
		
		parent::__construct('dp-single-post-stats', __('DP Single Post Stats', 'dp'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		
		if(!is_single())
			return;
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
		echo dp_get_post_stats();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		return $instance;
	}

	function form( $instance ) {
		$defaults = array('title' => __('Post Stats', 'dp'));
		$instance = wp_parse_args( (array) $instance, $defaults);
		
		$title = strip_tags($instance['title']); ?>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:", 'dp'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p class='description'><?php _e("This widget will only display on the single post page", 'dp'); ?></p>
	<?php }
}

// Register Widget
add_action('widgets_init', 'register_dp_widget_single_post_stats');
function register_dp_widget_single_post_stats() {
	register_widget('DP_Widget_Single_Post_Stats');
}
<?php
/**
 * DP Recent Comments Widget
 *
 * Display the most recent comments.
 * 
 * @package deTube
 * @subpackage Widgets
 * @since deTube 1.0
 */
 
class DP_Widget_Comments extends WP_Widget {
	
	function DP_Widget_Comments() {
	
		$widget_ops = array( 'classname' => 'widget-comments', 'description' => __('Display the most recent comments.', 'dp') );
		$control_ops = array( 'width' => 400, 'height' => 350, 'id_base' => "dp-comments" );
		$this->WP_Widget( "dp-comments", __('DP Recent Comments', 'dp'), $widget_ops, $control_ops );
		$this->alt_option_name = "dp_widget_comments";

		add_action( 'comment_post', array(&$this, 'flush_widget_cache') );
		add_action( 'transition_comment_status', array(&$this, 'flush_widget_cache') );
	}

	function flush_widget_cache() {
		wp_cache_delete("dp_widget_comments", 'widget');
	}
	
	function widget( $args, $instance ) {
		
		global $comments, $comment;

		$cache = wp_cache_get("dp_widget_comments", 'widget');

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

 		extract($args, EXTR_SKIP);
 		$output = '';
		$title = apply_filters('widget_title', $instance['title']);
		
		
 		$output .= $before_widget;
		if ( $title ) {
			$output .= $before_title . $title . $after_title;
		}
		$output .= dp_list_comments_widget($instance,false);
		$output .= $after_widget;

		echo $output;
		
		$cache[$args['widget_id']] = $output;
		wp_cache_set("dp_widget_comments", $cache, 'widget');
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = $new_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset($new_instance['show_date']) ? 1 : 0;
		$instance['show_avatar'] = isset($new_instance['show_avatar']) ? 1 : 0;
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions["dp_widget_comments"]) )
			delete_option("dp_widget_comments");

		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array( 
			'title' => __('Recent Comments', 'dp'), 
			'number' => 5,
			'show_avatar' => true,
			'show_date' => true,
			'comment_length' => 80
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		
		$comment_type = array( '' => __( 'All', 'dp' ), 'comment' => __( 'Comment', 'dp' ) , 'trackback' => __( 'Trackback', 'dp' ));
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'dp'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Comment Number:', 'dp'); ?></label>
			<input class="small-text" type="text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_avatar' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_avatar'], true ); ?> id="<?php echo $this->get_field_id( 'show_avatar' ); ?>" name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" /> <?php _e( 'Show Avatar?', 'dp' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_date'], true ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" /> <?php _e( 'Show Comment Date?', 'dp' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'comment_length' ); ?>"><?php _e('Comment Excerpt Length:', 'dp'); ?></label>
			<input size="4" type="text" id="<?php echo $this->get_field_id( 'comment_length' ); ?>" name="<?php echo $this->get_field_name( 'comment_length' ); ?>" value="<?php echo $instance['comment_length']; ?>" />
		</p>
		<?php
	}
}

/** 
 * Function to return the most recent comments
 *
 * @uses get_comments() return array List of comments
 */
function dp_list_comments_widget($args = '',$echo = false) {
	global $comments, $comment;
	
	$defaults = array( 
		'title' => __('Recent Comments', 'dp'), 
		'number' => 5,
		'show_date' => true ,
		'show_avatar' => true,
		'avatar_size' => 48,
		'comment_length' => 80
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	$comments =  get_comments(array(
		'number' => $number,
		'status' => 'approve',
		'type' => 'comment'
	));
		
	$output = '<ul'.($show_avatar ? ' class="has-avatar"' : '').'>';
	
	if($comments) {
		foreach ($comments as $comment) {
			$output .=  '<li>';
				
			if($show_avatar)
				$output .=  get_avatar($comment->comment_author_email, $avatar_size);
				
			$output .= '<div class="data">';	
			$output .= '<span class="author"><a href="'.get_comment_link().'">'.get_comment_author().'</a></span> ';
					
			if($show_date)
				$output .= '<span class="date">'.sprintf(__('%s ago', 'dp'), human_time(get_comment_time('U'))).'</span> ';
			
			$output .= ' <p class="excerpt">'.mb_strimwidth(strip_tags(apply_filters('comment_content', $comment->comment_content)), 0, $comment_length, "...").'</p>';
				
			$output .= '</div></li>';
		}
	}
	
	$output .= '</ul>';
	
	if($echo)
		echo $output;
	else
		return $output;
}

register_widget('DP_Widget_Comments');
?>
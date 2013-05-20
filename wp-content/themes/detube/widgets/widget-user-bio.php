<?php
/**
 * DP User Bio Widget
 *
 * Display the "Biographical Info" of a user.
 * 
 * @package deTube
 * @subpackage Widgets
 * @since deTube 1.0
 */

class DP_Widget_User_Bio extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget-user-bio', 'description' => __('Display the "Biographical Info" of a user.', 'dp'));
		$control_ops = array('width' => 400, 'height' => 350);
		
		parent::__construct('dp-user-bio', __('DP User Bio', 'dp'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		
		$user_id = $instance['user_id'];
		if(empty($user_id)) {
			if(is_author())
				$user_id = get_queried_object_id();
			else
				return;
		}
		
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$title = sprintf($instance['title'], '<span class="display-name">'.get_the_author_meta( 'display_name', $user_id ).'</span>');
		
		$bio = get_the_author_meta( 'description', $user_id );
		if($instance['strip_tags'])
			$bio = strip_tags($bio);
		if(function_exists('mb_strimwidth') && $bio_length = $instance['bio_length'])
			$bio = mb_strimwidth($bio, 0, $bio_length);
		
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
		echo '<div class="bio">'.wpautop( $bio ).'</div>';
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		extract($new_instance);
	
		$instance = $old_instance;
		$instance['title'] = strip_tags($title);
		$instance['user_id'] = !empty($user_id) && is_numeric($user_id) ? absint($user_id) : '';
		$instance['bio_length'] = !empty($bio_length) && is_numeric($bio_length) ? absint($bio_length) : '';
		$instance['strip_tags'] = isset($new_instance['strip_tags']);
		
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			 'title' => __('About %s', 'dp'), 
			 'user_id' => '',
			 'strip_tags' => false,
			 'bio_length' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults);
		extract($instance);
		
		$title = strip_tags($title); ?>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:", 'dp'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p class='description'><?php _e("'%s' in string will be replaced with user's name.", 'dp'); ?></p>
		
		<p><label for="<?php echo $this->get_field_id('user_id'); ?>"><?php _e('User ID:', 'dp'); ?></label>
		<input id="<?php echo $this->get_field_id('user_id'); ?>" name="<?php echo $this->get_field_name('user_id'); ?>" type="text" value="<?php echo $user_id; ?>" size="3" /></p>
		<p class='description'><?php _e("When viewing an author archive page, default user ID is the ID of current user. Set this field only if you want to show a specific user. <br /><strong>Note:</strong> By default, this widget is only displayed on the author archive page, if you set User ID, it will displayed on all pages.", 'dp'); ?></p>

		<p><label for="<?php echo $this->get_field_id('bio_length'); ?>"><?php _e('Bio Length:', 'dp'); ?></label>
		<input id="<?php echo $this->get_field_id('bio_length'); ?>" name="<?php echo $this->get_field_name('bio_length'); ?>" type="text" value="<?php echo $bio_length; ?>" size="3" /></p>
		<p class='description'><?php _e("Enter a length number if you want to limit bio length, e.g. 300.", 'dp'); ?></p>

		<p><input id="<?php echo $this->get_field_id('strip_tags'); ?>" name="<?php echo $this->get_field_name('strip_tags'); ?>" type="checkbox" <?php checked($strip_tags); ?> />&nbsp;<label for="<?php echo $this->get_field_id('strip_tags'); ?>"><?php _e('Strip HTML Tags?', 'dp'); ?></label></p>
	<?php }
}

// Register Widget
add_action('widgets_init', 'register_dp_widget_user_bio');
function register_dp_widget_user_bio() {
	register_widget('DP_Widget_User_Bio');
}
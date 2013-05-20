<?php
/*
Plugin Name: DP Post Likes
Description: Add "Like" feature for WordPress. Note: this is just an extension as a part of deTube theme now, maybe made into a true plugin in the future.
Author: Cloud Stone
Version: 1.0
Author URI: http://dedepress.com
*/

add_action('init', 'dp_create_post_likes_table');
add_action( 'wp_footer', 'dp_ajax_like_post_script' );
add_action( 'wp_ajax_nopriv_like_post', 'dp_ajax_like_post' );
add_action( 'wp_ajax_like_post', 'dp_ajax_like_post');

global $wpdb;
$wpdb->postlikes = $wpdb->prefix.'postlikes';


$settings = array(
	'login_required' => true,
	'labels' => array(
		'like' => __('Like?', 'dp'),
		'liked' => __('You Like!', 'dp'),
		'needs_login' => __('You must login first!', 'dp')
	)
);

/**
 * Retrieve the SQL for creating database table.
 */
function dp_create_post_likes_table() {
	global $wpdb;
	
	$charset_collate = '';	
	if ( ! empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( ! empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";
	
	/* Debugging
	$sql = "DROP TABLE IF EXISTS $wpdb->postlikes";
	$wpdb->query($sql);
	*/
	
	$sql = "CREATE TABLE IF NOT EXISTS $wpdb->postlikes (
			like_id bigint(20) unsigned NOT NULL auto_increment,
			post_id bigint(20) unsigned NOT NULL default '0',
			user_id bigint(20) unsigned NOT NULL default '0',
			like_ip varchar(100) NOT NULL default '',
			like_time datetime NOT NULL default '0000-00-00 00:00:00',
			PRIMARY KEY (like_id),
			KEY user_id (user_id),
			KEY post_id (post_id)
			) $charset_collate;";
	
	return $wpdb->query($sql);
}

/**
 * Display like post
 */
function dp_like_post($post_id = null, $echo = true) {
	global $post;
	
	if(!$post_id)
		$post_id = $post->ID;
	$post_id = (int)$post_id;
	
	// Get user settings
	$settings = get_option('dp_post_likes');
	$login_required = !empty($settings['login_required']) ? true : false;
	
	$r = '';

		$like_id = dp_is_user_liked_post($post_id);
		
		if($like_id) {
			$r .= '<a class="liked" href="javascript:void(0);" data-lid="'.$like_id.'" data-pid="'.$post_id.'">'.__('You Like!', 'dp').'</a>';
		} else {
			$r .= '<a class="like" href="javascript:void(0);" data-pid="'.$post_id.'">'.__('Like?', 'dp').'</a>';
		}
	

	$r = '<span class="dp-like-post">'.$r.'</span>';
		
	if($echo)
		echo $r;
	else
		return $r;
}

/**
 * Ajax action
 */
function dp_ajax_like_post() {
	$defaults = array(
		'post_id' => '',
		'user_id' => '',
		'like_id' => '',
		'action_type' => '',
		'label' => '',
		'error' => ''
	);
	$arr = wp_parse_args($_POST, $defaults);
	extract($arr);
	
	$r = array();
	
	// Check Ajax nonce
	check_ajax_referer( 'dp_like_post_nonce', 'nonce' );

	// Check "Login Required"
	$settings = get_option('dp_post_likes');
	$login_required = !empty($settings['login_required']) ? true : false;
	
	if($login_required && !is_user_logged_in()) {
		$r['error'] = __( 'You must login first.', 'dp' );
	} else {
		// Like
		if($action_type == 'like') {
			if(empty($user_id) && is_user_logged_in())
				$user_id = get_current_user_id();
			$arr['user_id'] = $user_id;
			
			$like_id = dp_insert_post_like($arr, true);
			
			if($like_id)
				$label = __('You Like!', 'dp');
			elseif(is_wp_error($like_id))
				$error = $like_id->get_error_message();
		} 
		
		// Remove Like
		elseif($action_type == 'remove_like') {
			dp_delete_post_like($like_id, $post_id);
			$label = __('Like?', 'dp');
		}
		
		$likes = dp_get_post_likes($post_id);
	
		$r = array(
			'likes' => $likes,
			'id' => $like_id,
			'label' => $label,
			'error' => $error
		);
	}
	
	$r = json_encode($r);
	die($r);
}

/**
 * Ajax script
 */
function dp_ajax_like_post_script() { ?>
<script type="text/javascript">
(function($) {
	$('.dp-like-post .like, .dp-like-post .liked').on('click', function() {
		el = $(this);

		actionType = el.hasClass('liked') ? 'remove_like' : 'like';
		
		var data = {
			action: 'like_post', 
			action_type: actionType, 
			like_id: el.attr('data-lid'),
			post_id: el.attr('data-pid'), 
			user_id: el.attr('data-uid'),
			label: el.text(),
			nonce: '<?php echo wp_create_nonce("dp_like_post_nonce"); ?>'
		};
		
		$.ajax({
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			type: 'POST',
			data: data,
			dataType: 'json',
			error: function(){
				alert('<?php _e('Something error. please try again later!', 'dp'); ?>');
				el.removeClass('liking');
			},
			beforeSend: function(){
				el.addClass('liking');
			},
			success: function(r){				
				if(r.error != '') {
					alert(r.error);
					return false;
				}
				
				if(actionType == 'like')
					el.stop().attr('data-lid', r.id).removeClass('like').addClass('liked');
				else if(actionType == 'remove_like')
					el.stop().removeAttr('data-lid').removeClass('liked').addClass('like');
				
				$('.dp-post-likes').each(function(){
					var count = $(this).find('.count');
					if(count.attr('data-pid') == el.attr('data-pid'))
						$(count).text(r.likes);
				});
				
				el.removeClass('liking').text(r.label);
			}
		});
		
		return false;
	});
})(jQuery);
</script>
<?php }

function dp_get_user_liked_posts($user_id, $fields = '') {
	global $wpdb;
	
	// Get user settings
	$settings = get_option('dp_post_likes');
	$login_required = !empty($settings['login_required']) ? true : false;
	
	/*if($login_required && !is_user_logged_in())
		return array();*/

	if(!$user_id)
		$user_id = get_current_user_id();
	
	if($user_id)
		$likes = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postlikes WHERE $wpdb->postlikes.user_id = %d", $user_id));
	else {
		$user_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] );
		$likes = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postlikes WHERE $wpdb->postlikes.user_id = 0 AND $wpdb->postlikes.like_ip = %d", $user_ip));
	}
	
	$_likes = array();
	if($fields == 'post_ids' && !empty($likes)) {
		foreach($likes as $like)
			$_likes[] = $like->post_id; 

		$likes = $_likes;
	}
	
	return $likes;
}

// Add Public Variables
add_filter('query_vars', 'dp_post_likes_query_vars');
function dp_post_likes_query_vars($query_vars) {
	$query_vars[] = 'filter_type';
	$query_vars[] = 'filter_user';

	return $query_vars;
}

### Function: Sort Views Posts
add_action('pre_get_posts', 'post_likes_sorting');
function post_likes_sorting($query) {	

	if($query->get('filter_type') == 'user_liked') {
		if(is_author() && is_main_query()) {
			$user_id = dp_get_queried_user_id();
			if(!get_query_var('filter_user'))
				$query->set('filter_user', $user_id);
			$query->set('author', '');
			$query->set('author_name', '');
		}
		
		add_filter('posts_join', 'post_likes_join', 10, 2);
		add_filter('posts_where', 'post_likes_where', 10, 2);
	} else {
		remove_filter('posts_join', 'post_likes_join');
		remove_filter('posts_where', 'post_likes_where');
	}
}

/**
 * Modify Default WordPress Listing To Make It Filter By User Liked
 */
function post_likes_join($content, $query) {
	global $wpdb;
	$content .= " INNER JOIN $wpdb->postlikes ON ($wpdb->postlikes.post_id = $wpdb->posts.ID)";
	return $content;
}
function post_likes_where($content, $query) {
	global $wpdb;
	
	$user_id = (int)$query->get('filter_user');
	$content .= " AND ($wpdb->postlikes.user_id = $user_id)";
	
	if($user_id == 0) {
		$user_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] );
		$content .= " AND ($wpdb->postlikes.like_ip = '$user_ip')";
	}
	
	if(is_author() && is_main_query())
		$query->set('author', $query->get('filter_user'));

	return $content;
}


/* Hook Functions
 *========================================================*/

/**
 * Add likes and set as 0 when publish post/page
 */
add_action('publish_post', 'dp_add_post_meta_likes');
add_action('publish_page', 'dp_add_post_meta_likes');
function dp_add_post_meta_likes($post_id) {
	if(wp_is_post_revision($post_id))
		return;
		
	$likes = (int)get_post_meta($post_id, 'likes', true);
	if($likes == '' || $likes < 0)
		update_post_meta($post_id, 'likes', 0);
}

/**
 * Delete likes when delete post/page
 */
add_action('delete_post', 'dp_delete_post_meta_likes');
function dp_delete_post_meta_likes($post_id) {
	if(wp_is_post_revision($post_id))
		return;
		
	delete_post_meta($post_id, 'likes'); // delete post meta 'likes' from $wpdb->postmeta
	dp_delete_post_likes($post_id); // delete all likes by this post from $wpdb->postlikes
}

/**
 * Delete a like from $wpdb->postlikes and update post meta if specify the post id
 */
function dp_delete_post_like($like_id, $post_id = '') {
	global $wpdb;
	
	if ( !$like = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->postlikes WHERE like_id = %d", $like_id)) )
		return $like;
		
	$wpdb->delete( $wpdb->postlikes, array( 'like_id' => $like_id ) );
	
	// update post meta if specify the post id
	if($post_id) {
		$likes = (int)get_post_meta($post_id, 'likes', true);
		update_post_meta($post_id, 'likes', $likes-1);
	}
}

/**
 * Delete all likes by a post from $wpdb->postlikes
 */
function dp_delete_post_likes($post_id) {
	global $wpdb;
		
	$wpdb->delete( $wpdb->postlikes, array( 'post_id' => $post_id ) );
}

/* Basic Functions
 *========================================================*/
/**
 * Insert a post like into database
 */
function dp_insert_post_like($arr, $wp_error = false) {
	global $wpdb;
	
	$defaults = array('post_id'=>'', 'user_id'=>0, 'like_ip'=>'', 'like_time'=>'');
	$arr = wp_parse_args($arr, $defaults);
	
	// export array as variables
	extract($arr, EXTR_SKIP);
	
	$like_id = 0;
	
	// Check post id
	if(!$post_id) {
		if($wp_error)
			return new WP_Error( 'invalid_post_id', __( 'Invaild post ID.', 'dp' ) );
		else
			return 0;
	}
	
	// Check liked
	$liked = dp_is_user_liked_post($post_id);
	if( $liked ) {
		if($wp_error)
			return new WP_Error( 'liked', __( 'You already liked.', 'dp' ) );
		else
			return 0;
	}
	
	// Get user ip
	$like_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] );
	
	// Get current time
	$like_time = current_time('mysql');
	
	// expected_slashed (everything!)
	$data = compact( array( 'post_id', 'user_id', 'like_ip', 'like_time'));
	$data = stripslashes_deep( $data );
	
	if( false === $wpdb->insert( $wpdb->postlikes, $data ) ) {
		if ( $wp_error )
			return new WP_Error('db_insert_error', __('Could not insert post like into the database', 'dp'), $wpdb->last_error);
		else
			return 0;
	}
	
	$like_id = (int)$wpdb->insert_id;
	
	// update post meta
	$likes = (int)get_post_meta($post_id, 'likes', true);
	update_post_meta($post_id, 'likes', $likes+1);
	
	return $like_id;
}

/**
 * Get post likes
 */
function dp_get_post_likes($post_id = '') {
	global $post;
	
	if(!$post_id)
		$post_id = $post->ID;
		
	$likes = get_post_meta($post_id, 'likes', true);
	
	if($likes == '')
		add_post_meta($post_id, 'likes', 0, true);
	
	$likes = absint($likes);
	$likes = short_number($likes);
	
	return $likes;
}

/**
 * Check if the current user has been like a post
 *
 * @return like_id if current user has been like the post, false if not
 */
function dp_is_user_liked_post($post_id, $user_id = false) {
	global $wpdb;
	
	// Get user settings
	$settings = get_option('dp_post_likes');
	$login_required = !empty($settings['login_required']) ? true : false;
	
	if(is_user_logged_in()) {
		// If no specified user id, get it
		if(!$user_id)
			$user_id = get_current_user_id();
		
		// Get like id
		$like_id = $wpdb->get_var( $wpdb->prepare("SELECT like_id FROM $wpdb->postlikes WHERE post_id = %d AND user_id = %s", $post_id, $user_id));
	} else {
		if($login_required)
			return false;
			
		// If user don't need to login
		$user_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] );
		
		$like_id = $wpdb->get_var( $wpdb->prepare("SELECT like_id FROM $wpdb->postlikes WHERE post_id = %d AND like_ip = %s", $post_id, $user_ip));
	}
	
	return $like_id;
}


/**
 * Add column 'dp_post_likes' into 'WP List Table' on admin edit view page
 */
add_filter("manage_edit-post_columns", "dp_post_likes_edit_columns");  
function dp_post_likes_edit_columns($columns){  
    $like_column = array(  
        "dp_post_likes" => __( 'Likes', 'dp' ),
    ); 
	$columns = $columns + $like_column;

    return $columns;  
}  
  
/**
 * Add columns into 'WP List Table' on admin edit view page
 *
 */
add_action("manage_posts_custom_column",  "dp_post_likes_custom_columns");
function dp_post_likes_custom_columns($column){  
    global $post;  
    switch ($column) {
		case 'dp_post_likes':
			printf(__('%s likes', 'dp'), dp_get_post_likes($post->ID));
		break;
    }  
}
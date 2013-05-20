<?php
/**
 * Author Template
 *
 * The template for displaying Author Profile pages.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */

/* Loads the "Author Filter Template" based on the query var "filter_type"
 * If current page has variable key "filter_type" in the query, load the appropriate template and return.
 */
$filter_type = get_query_var('filter_type');
if($filter_type == 'user_added') {
	get_template_part('author-added');
	return;
} elseif($filter_type == 'user_liked') {
	get_template_part('author-liked');
	return;
}

// Get the ID of current user so we can use it later.
$user_id = dp_get_queried_user_id();


get_header(); ?>

<div id="main"><div class="wrap cf">

	<div id="content" role="main">
		
		<?php // Author Box ?>
		<div class="author-box cf">
			<?php echo get_avatar($user_id, 64); ?>

			<div class="data">
				<h1 class="display-name"><?php the_author_meta( 'display_name', $user_id ); ?></h1>
			
				<div class="meta">
				<?php
					if($user_registered = get_the_author_meta( 'user_registered', $user_id ))
						echo '<span class="joined">'.sprintf(__('Joined %s ago', 'dp'), human_time(strtotime($user_registered))).'</span>';
					if($location = get_the_author_meta('location', $user_id))
						echo ' <span class="sep">/</span> <span class="location">'.$location.'</span>';
				?>
				</div>
			
				<div class="links">
				<?php
					if($twitter = get_the_author_meta('twitter', $user_id))
						echo '<a class="twitter" href="'.$twitter.'">'.__('Twitter', 'dp').'</a> ';
					if($facebook = get_the_author_meta('facebook', $user_id))
						echo '<a class="facebook" href="'.$facebook.'">'.__('Facebook', 'dp').'</a> ';
					if($website = get_the_author_meta('url', $user_id))
						echo '<a class="website" href="'.$website.'">'.preg_replace('[http://|https://]', '', $website).'</a> ';
				?>
				</div>
			</div><!-- #author-box .data -->
		</div><!-- #author-box -->
		
		<?php // Recently Added
			$link = add_query_arg('filter_type', 'user_added', get_author_posts_url($user_id));
			
			$args = array(
				'view' => 'grid-medium',
				'title' => __('Recently Added', 'dp'),
				'link' => $link,
				'author' => $user_id,
				'post_type' => 'post',
				'ignore_sticky_posts' => true,
				'posts_per_page' => 0,
				'hide_if_empty' => true
			);
			dp_section_box($args); 
		?>
		
		<?php // Liked Videos
			$link = add_query_arg('filter_type', 'user_liked', get_author_posts_url($user_id));
		
			$args = array(
				'view' => 'grid-small',
				'title' => __('Liked Videos', 'dp'),
				'link' => $link,
				'post_type' => 'post',
				'ignore_sticky_posts' => true,
				'posts_per_page' => 6,
				'filter_type' => 'user_liked',
				'filter_user' => $user_id
			);
			dp_section_box($args); 
		?>
	
	</div><!-- end #content -->

	<?php get_sidebar(); ?>

</div></div><!-- end #main -->

<?php get_footer(); ?>
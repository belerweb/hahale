<?php
/**
 * Template Name: Likes
 * 
 * A Page Template for displaying users/visitor's liked posts.
 *
 * @package deTube
 * @subpackage Page Template
 * @since deTube 1.1
 */
 
// Redirect to "Liked Videos" page using "author-liked.php" template for logged in users
if(is_user_logged_in()) {
	$user_id = get_current_user_id();
	$url = add_query_arg('filter_type', 'user_liked', get_author_posts_url($user_id));
	wp_redirect($url);
}

get_header(); 
?>

<div id="main"><div class="wrap cf">
	<div id="content" role="main">
		<div class="loop-header below-no-actions">
			<h1 class="loop-title"><?php the_title(); ?></h1>
		</div>
		
		<?php 
			$settings = get_option('dp_post_likes');
			$login_required = !empty($settings['login_required']) ? true : false;
			
			if($login_required && !is_user_logged_in()) {
				echo '<p class="must-login">'.sprintf(__('You must <a href="%1$s">register</a> and <a href="%2$s">login</a> to view your liked posts.', 'dp'),  site_url('wp-login.php?action=register', 'login'), wp_login_url()).'</p>';
			} else {
				$user_id = get_current_user_id();
				query_posts(array(
							'post_type' => 'post',
							'ignore_sticky_posts' => true,
							'filter_type' => 'user_liked',
							'filter_user' => $user_id
						));
						
				global $wp_query;

				if(have_posts()) : ?>
					<div class="loop-content switchable-view grid-small">
						<div class="nag cf">
						<?php 
						while (have_posts()) : the_post();
							get_template_part('item-video');
						endwhile; ?>
						</div>
					</div><!-- end .loop-content -->
			
					<?php get_template_part('loop-nav'); ?>
				<?php else : ?>
					<div id="post-0" class="post no-results not-found">
						<div class="entry-content rich-content">
							<p><?php echo 'You don\'t have any liked posts.'; ?></p>
						</div>
					</div><!-- end #post-0 -->
				<?php endif; ?>
		<?php } ?>
	</div><!-- end #content -->
	
	<?php get_sidebar(); ?>
</div></div><!-- end #main -->

<?php get_footer(); ?>
<?php 
/**
 * Author Liked Template
 *
 * The template for displaying Video Archive Liked by current author.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
 
// Get the ID of current user so we can use it later.
$user_id = dp_get_queried_user_id(); 

get_header(); ?>

<div id="main"><div class="wrap cf">
	<div id="content" role="main">
		
		<div class="loop-header">
			<h1 class="loop-title"><?php printf(__("%s / Liked Videos", 'dp'), '<a href="'.get_author_posts_url($user_id).'">'.get_the_author_meta( 'display_name', $user_id ).'</a>'); ?></h1>
		</div>
	
		<?php if (have_posts()) : global $loop_view; ?>
	
			<?php get_template_part('loop-actions'); ?>
		
			<div class="loop-content switchable-view <?php echo $loop_view; ?>" data-view="<?php echo $loop_view; ?>">
			<div class="nag cf">
				<?php while (have_posts()) : the_post();
					get_template_part('item-video');
				endwhile; ?>
			</div>
			</div><!-- end .loop-content -->
			
			<?php get_template_part('loop-nav'); ?>
		
		<?php else : ?>
			
			<?php get_template_part('loop-error'); ?>
			
		<?php endif; ?>
	</div><!-- end #content -->

	<?php get_sidebar(); ?>

</div></div><!-- end #main -->

<?php get_footer(); ?>
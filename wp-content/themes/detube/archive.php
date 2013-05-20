<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.0
 */
get_header(); ?>

<div id="main"><div class="wrap cf">
	
	<div id="content" role="main">
	
	<?php get_template_part('loop-header'); ?>
	
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
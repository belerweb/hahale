<?php 
/**
 * Template Name: Tag Cloud
 *
 * A template to use on pages that displays tag cloud with full width layout.
 *
 * @package deTube
 * @subpackage Template
 * @since deTube 1.1
 */

get_header(); ?>

<div id="main" class="full-width"><div class="wrap cf">

	<div id="content" role="main">
	
		<?php while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1 class="page-title"><?php the_title(); ?></h1>

			<div class="page-content rich-content">
				<div class="term-cloud post_tag-cloud tag-cloud">
					<?php wp_tag_cloud( array( 'number' => 200 ) ); ?>
				</div><!-- end .term-cloud -->
					
				<?php wp_link_pages(array('before' => '<p class="entry-nav pag-nav"><strong>'.__('Pages:', 'dp').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>
		</div><!--end .hentry-->
		<?php endwhile; ?>
		
	</div><!--end #content-->

</div></div><!-- end #main -->

<?php get_footer(); ?>